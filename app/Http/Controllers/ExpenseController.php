<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Colocation;

class ExpenseController extends Controller
{
    
public function store(Colocation $colocation, Request $request)
{
    $user = $request->user();

    $isMember = $colocation->memberships()
        ->where('user_id', $user->id)
        ->whereNull('left_at')
        ->exists();

    abort_unless($isMember, 403);

    $data = $request->validate([
        'title' => ['required','string','max:255'],
        'amount' => ['required','numeric','min:0.01'],
        'expense_date' => ['required','date'],
        'category_id' => ['required','exists:categories,id']
    ]);

    Expense::create([
        'colocation_id' => $colocation->id,
        'category_id' => $data['category_id'],
        'payer_id' => $user->id,
        'title' => $data['title'],
        'amount' => $data['amount'],
        'expense_date' => $data['expense_date'],
    ]);

    return back()->with('success', 'Expense added successfully.');
}
}
