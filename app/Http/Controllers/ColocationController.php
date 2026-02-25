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

        abort_unless($isMember, 403);

        $members = $colocation->users()
            ->wherePivotNull('left_at')
            ->get();

        return view('colocations.show', compact('colocation', 'members'));
    }
}