<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function create()
    {
        return view('colocations.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $hasActiveColoc = $user->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', function ($q) {
                $q->where('status', 'active');
            })
            ->exists();

        if ($hasActiveColoc) {
            return back()->withErrors([
                'name' => 'You already have an active colocation. Leave it before creating a new one.',
            ]);
        }

        DB::transaction(function () use ($user, $data, &$colocation) {
            // Create colocation
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

        return redirect()->route('colocations.show', $colocation)->with('success', 'Colocation created successfully.');
    }
    public function my(Request $request)
    {
        $user = $request->user();

        $membership = $user->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', fn($q) => $q->where('status', 'active'))
            ->with('colocation')
            ->first();

        if (!$membership) {
            return redirect()->route('colocations.create');
        }

        return redirect()->route('colocations.show', $membership->colocation);
    }

    public function show(Colocation $colocation, Request $request)
    {
        $userId = $request->user()->id;

        $isMember = $colocation->memberships()
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->exists();
        $pendingInvites = $colocation->invitations()
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->get();

        abort_unless($isMember, 403);

        $members = $colocation->users()
            ->wherePivotNull('left_at')
            ->get();
        $expenses = $colocation->expenses()
            ->with(['payer', 'category'])
            ->orderByDesc('expense_date')
            ->get();

        $activeMembers = $colocation->users()
        ->wherePivotNull('left_at')
        ->get();

        $totalExpenses = $expenses->sum('amount');

        $memberCount = $activeMembers->count();

        $share = $memberCount > 0 ? $totalExpenses / $memberCount : 0;

        $balances = [];

        foreach ($activeMembers as $member) {
            $paid = $expenses
                ->where('payer_id', $member->id)
                ->sum('amount');

            $balance = $paid - $share;

            $balances[] = [
                'user' => $member,
                'paid' => $paid,
                'balance' => $balance,
            ];
        }

            $creditors = [];
        $debtors = [];

        foreach ($balances as $b) {
            if ($b['balance'] > 0.01) {
                $creditors[] = [
                    'user' => $b['user'],
                    'amount' => $b['balance']
                ];
            } elseif ($b['balance'] < -0.01) {
                $debtors[] = [
                    'user' => $b['user'],
                    'amount' => abs($b['balance'])
                ];
            }
        }

        $settlements = [];

        foreach ($debtors as &$debtor) {
            foreach ($creditors as &$creditor) {

                if ($debtor['amount'] == 0) continue;
                if ($creditor['amount'] == 0) continue;

                $payAmount = min($debtor['amount'], $creditor['amount']);

                $settlements[] = [
                    'from' => $debtor['user'],
                    'to' => $creditor['user'],
                    'amount' => $payAmount
                ];

                $debtor['amount'] -= $payAmount;
                $creditor['amount'] -= $payAmount;
            }
        }

        return view('colocations.show', compact('colocation', 'members', 'pendingInvites', 'expenses', 'balances', 'share', 'totalExpenses', 'settlements'));    }
    public function leave(Colocation $colocation, Request $request)
    {
        $user = $request->user();

        $membership = $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        abort_unless($membership, 403);
        abort_if($membership->role === 'owner', 403);

        $membership->update([
            'left_at' => now(),
        ]);

        return redirect()->route('colocations.create')
            ->with('success', 'You left the colocation.');
    }

    public function cancel(Colocation $colocation, Request $request)
    {
        abort_unless($colocation->owner_id === $request->user()->id, 403);

        DB::transaction(function () use ($colocation) {
            $colocation->update(['status' => 'cancelled']);

            $colocation->memberships()
                ->whereNull('left_at')
                ->update(['left_at' => now()]);
        });

        return redirect()->route('colocations.create')
            ->with('success', 'Colocation cancelled successfully.');
    }
}