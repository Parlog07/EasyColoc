<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function create()
    {
        return view('colocations.create');
    }


    public function index(Request $request)
    {
        $user = $request->user();

        $memberships = $user->memberships()
            ->with('colocation')
            ->orderByDesc('joined_at')
            ->get();

        $activeMembership = $memberships->first(function ($membership) {
            return is_null($membership->left_at)
                && $membership->colocation
                && $membership->colocation->status === 'active';
        });

        $activeColocation = $activeMembership?->colocation;

        $historyMemberships = $memberships->filter(function ($membership) use ($activeColocation) {
            return ! $activeColocation || $membership->colocation_id !== $activeColocation->id;
        });

        return view('colocations.index', [
            'activeColocation' => $activeColocation,
            'activeMembership' => $activeMembership,
            'historyMemberships' => $historyMemberships,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $hasActiveColoc = $user->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', function ($query) {
                $query->where('status', 'active');
            })
            ->exists();

        if ($hasActiveColoc) {
            return back()->withErrors([
                'name' => 'You already have an active colocation. Leave it before creating a new one.',
            ]);
        }

        $colocation = null;

        DB::transaction(function () use ($user, $data, &$colocation) {
            $colocation = Colocation::create([
                'name' => $data['name'],
                'status' => 'active',
                'owner_id' => $user->id,
            ]);

            Membership::create([
                'user_id' => $user->id,
                'colocation_id' => $colocation->id,
                'role' => 'owner',
                'joined_at' => now(),
            ]);
        });

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation created successfully.');
    }

    public function my(Request $request)
    {
        $user = $request->user();

        $membership = $user->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', fn ($query) => $query->where('status', 'active'))
            ->with('colocation')
            ->first();

        if (! $membership) {
            return redirect()->route('colocations.create');
        }

        return redirect()->route('colocations.show', $membership->colocation);
    }

    public function show(Colocation $colocation, Request $request, BalanceService $balanceService)
    {
        $user = $request->user();

        $isMember = $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->exists();

        abort_unless($isMember, 403);

        $snapshot = $balanceService->buildSnapshot($colocation);
        $members = $snapshot['members'];
        $payments = $snapshot['payments'];
        $balances = $snapshot['balances'];
        $share = $snapshot['share'];
        $totalExpenses = $snapshot['totalExpenses'];
        $settlements = $snapshot['settlements'];

        $pendingInvites = $colocation->invitations()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->get();

        $validatedFilter = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $expensesQuery = $colocation->expenses()
            ->with(['payer', 'category'])
            ->orderByDesc('expense_date');

        if (! empty($validatedFilter['month'])) {
            $expensesQuery->whereMonth('expense_date', $validatedFilter['month']);
        }

        if (! empty($validatedFilter['year'])) {
            $expensesQuery->whereYear('expense_date', $validatedFilter['year']);
        }

        $expenses = $expensesQuery->get();

        return view('colocations.show', compact(
            'colocation',
            'members',
            'pendingInvites',
            'expenses',
            'payments',
            'balances',
            'share',
            'totalExpenses',
            'settlements'
        ));
    }

    public function leave(Colocation $colocation, Request $request, BalanceService $balanceService)
    {
        $user = $request->user();

        $membership = $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        abort_unless($membership, 403);
        abort_if($membership->role === 'owner', 403);

        $balance = $balanceService->userBalance($colocation, $user->id);

        DB::transaction(function () use ($membership, $user, $balance) {
            if ($balance < -0.01) {
                $user->decrement('reputation');
            } else {
                $user->increment('reputation');
            }

            $membership->update(['left_at' => now()]);
        });

        return redirect()->route('colocations.create')
            ->with('success', 'You left the colocation.');
    }

    public function cancel(Colocation $colocation, Request $request, BalanceService $balanceService)
    {
        abort_unless($colocation->owner_id === $request->user()->id, 403);

        $hasOtherActiveMembers = $colocation->memberships()
            ->whereNull('left_at')
            ->where('user_id', '!=', $colocation->owner_id)
            ->exists();

        if ($hasOtherActiveMembers) {
            return back()->withErrors([
                'msg' => 'You cannot cancel while other active members are still in the colocation.',
            ]);
        }

        DB::transaction(function () use ($colocation, $balanceService) {
            $activeMembers = $colocation->users()->wherePivotNull('left_at')->get();
            $expenses = $colocation->expenses()->get();
            $payments = $colocation->payments()->get();

            foreach ($activeMembers as $member) {
                $balance = $balanceService->userBalanceFromData($activeMembers, $expenses, $payments, $member->id);

                if ($balance < -0.01) {
                    $member->decrement('reputation');
                } else {
                    $member->increment('reputation');
                }
            }

            $colocation->update(['status' => 'cancelled']);

            $colocation->memberships()
                ->whereNull('left_at')
                ->update(['left_at' => now()]);
        });

        return redirect()->route('colocations.create')
            ->with('success', 'Colocation cancelled successfully.');
    }

    public function transferOwnership(Colocation $colocation, User $user, Request $request)
    {
        abort_unless($request->user()->id === $colocation->owner_id, 403);
        abort_if($user->id === $colocation->owner_id, 422, 'User is already owner.');

        $newOwnerMembership = $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        abort_unless($newOwnerMembership, 422, 'Target user must be an active member.');

        $currentOwnerMembership = $colocation->memberships()
            ->where('user_id', $colocation->owner_id)
            ->whereNull('left_at')
            ->firstOrFail();

        DB::transaction(function () use ($colocation, $user, $newOwnerMembership, $currentOwnerMembership) {
            $colocation->update([
                'owner_id' => $user->id,
            ]);

            $newOwnerMembership->update([
                'role' => 'owner',
            ]);

            $currentOwnerMembership->update([
                'role' => 'member',
            ]);
        });

        return back()->with('success', 'Ownership transferred successfully.');
    }

    public function removeMember(Colocation $colocation, User $user, Request $request, BalanceService $balanceService)
    {
        abort_unless($request->user()->id === $colocation->owner_id, 403);
        abort_if($user->id === $colocation->owner_id, 403);

        $membership = $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->firstOrFail();

        DB::transaction(function () use ($colocation, $user, $membership, $balanceService) {
            $activeMembers = $colocation->users()->wherePivotNull('left_at')->get();
            $expenses = $colocation->expenses()->get();
            $payments = $colocation->payments()->get();

            $memberBalance = $balanceService->userBalanceFromData($activeMembers, $expenses, $payments, $user->id);

            if ($memberBalance < -0.01) {
                $user->decrement('reputation');

                // Debt transfer: member debt is moved to owner in balance computation.
                Payment::create([
                    'colocation_id' => $colocation->id,
                    'from_user_id' => $user->id,
                    'to_user_id' => $colocation->owner_id,
                    'amount' => round(abs($memberBalance), 2),
                    'paid_at' => now(),
                ]);
            } else {
                $user->increment('reputation');
            }

            $membership->update(['left_at' => now()]);
        });

        return back()->with('success', 'Member removed.');
    }
}
