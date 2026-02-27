<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function store(Colocation $colocation, Request $request)
    {
        abort_unless($colocation->owner_id === $request->user()->id, 403);
        abort_if($colocation->status !== 'active', 422, 'Colocation is not active.');

        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns', 'max:255'],
        ]);

        $token = $this->generateUniqueToken();

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => strtolower($data['email']),
            'token' => $token,
            'status' => 'pending',
            'expires_at' => now()->addHour(),
        ]);

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation email sent successfully.');
    }

    public function accept(string $token, Request $request)
    {
        $user = $request->user();
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')->withErrors(['msg' => 'Invitation is no longer valid.']);
        }

        if (now()->greaterThan($invitation->expires_at)) {
            $invitation->update(['status' => 'expired']);

            return redirect()->route('dashboard')->withErrors(['msg' => 'Invitation expired.']);
        }

        if (strtolower($user->email) !== strtolower($invitation->email)) {
            abort(403, 'Invitation email does not match your account.');
        }

        $alreadyInActiveColocation = $user->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', fn ($query) => $query->where('status', 'active'))
            ->exists();

        if ($alreadyInActiveColocation) {
            return redirect()->route('dashboard')->withErrors([
                'msg' => 'You already have an active colocation.',
            ]);
        }

        DB::transaction(function () use ($invitation, $user) {
            $invitation->refresh();

            if ($invitation->status !== 'pending') {
                abort(422, 'Invitation is no longer valid.');
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
        });

        return redirect()->route('colocations.show', $invitation->colocation_id)
            ->with('success', 'Invitation accepted successfully.');
    }

    public function refuse(string $token, Request $request)
    {
        $user = $request->user();
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')->withErrors(['msg' => 'Invitation is no longer valid.']);
        }

        if (now()->greaterThan($invitation->expires_at)) {
            $invitation->update(['status' => 'expired']);

            return redirect()->route('dashboard')->withErrors(['msg' => 'Invitation expired.']);
        }

        if (strtolower($user->email) !== strtolower($invitation->email)) {
            abort(403, 'Invitation email does not match your account.');
        }

        $invitation->update(['status' => 'refused']);

        return redirect()->route('dashboard')->with('success', 'Invitation refused.');
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(40);
        } while (Invitation::where('token', $token)->exists());

        return $token;
    }
}
