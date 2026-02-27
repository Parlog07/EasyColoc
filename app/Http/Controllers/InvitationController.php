<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function store(Colocation $colocation, Request $request)
    {
        abort_unless($colocation->owner_id === $request->user()->id, 403);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => $data['email'],
            'token' => Str::random(40),
            'status' => 'pending',
            'expires_at' => now()->addHour(),
        ]);

        Mail::to($data['email'])->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation sent.');
    }

    public function show(string $token, Request $request)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        if ($invitation->status === 'pending' && now()->greaterThan($invitation->expires_at)) {
        $invitation->update(['status' => 'expired']);
        }

        return view('invitations.show', compact('invitation'));
    }

    public function accept(string $token, Request $request)
    {
        $user = $request->user();
        $invitation = Invitation::where('token', $token)->firstOrFail();

        // Basic checks
        if ($invitation->status !== 'pending') {
            return back()->withErrors(['msg' => 'Invitation is no longer valid.']);
        }

        if (now()->greaterThan($invitation->expires_at)) {
            $invitation->update(['status' => 'expired']);
            return back()->withErrors(['msg' => 'Invitation expired.']);
        }

        if ($user->email !== $invitation->email) {
            abort(403); // email mismatch
        }

        $hasActive = $user->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', fn($q) => $q->where('status', 'active'))
            ->exists();

        if ($hasActive) {
            return back()->withErrors(['msg' => 'You already have an active colocation.']);
        }

        Membership::create([
            'user_id' => $user->id,
            'colocation_id' => $invitation->colocation_id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $invitation->update([
            'status' => 'accepted',
            'accepted_by_user_id' => $user->id,
        ]);

        return redirect()->route('colocations.show', $invitation->colocation_id)
            ->with('success', 'Invitation accepted.');
    }

    public function refuse(string $token, Request $request)
    {
        $user = $request->user();
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status !== 'pending') {
            return back()->withErrors(['msg' => 'Invitation is no longer valid.']);
        }

        if ($user->email !== $invitation->email) {
            abort(403);
        }

        $invitation->update(['status' => 'refused']);

        return redirect()->route('dashboard')->with('success', 'Invitation refused.');
    }
}