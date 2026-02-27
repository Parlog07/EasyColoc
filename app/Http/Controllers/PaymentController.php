<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Colocation $colocation, Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'from_user_id' => ['required', 'exists:users,id'],
            'to_user_id'   => ['required', 'exists:users,id'],
            'amount'       => ['required', 'numeric', 'min:0.01'],
        ]);

        abort_unless($user->id == $data['from_user_id'], 403);

        $isFromMember = $colocation->memberships()
            ->where('user_id', $data['from_user_id'])
            ->whereNull('left_at')
            ->exists();

        $isToMember = $colocation->memberships()
            ->where('user_id', $data['to_user_id'])
            ->whereNull('left_at')
            ->exists();

        abort_unless($isFromMember && $isToMember, 403);

        $amount = round((float) $data['amount'], 2);

        if ($amount <= 0 || $amount > 99999999.99) {
            return back()->withErrors(['msg' => 'Invalid amount.']);
        }

        $already = Payment::where('colocation_id', $colocation->id)
            ->where('from_user_id', $data['from_user_id'])
            ->where('to_user_id', $data['to_user_id'])
            ->where('amount', $amount)
            ->whereNotNull('paid_at')
            ->exists();

        if ($already) {
            return back()->withErrors(['msg' => 'This payment is already recorded.']);
        }

        Payment::create([
            'colocation_id' => $colocation->id,
            'from_user_id'  => $data['from_user_id'],
            'to_user_id'    => $data['to_user_id'],
            'amount'        => $amount,
            'paid_at'       => now(),
        ]);

        return back()->with('success', 'Payment recorded.');
    }
}