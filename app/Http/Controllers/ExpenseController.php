<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

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
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'category_id' => ['nullable', 'string'],
            'category_name' => ['nullable', 'string', 'max:100'],
        ]);

        $categoryId = null;
        $selectedCategory = (string) ($data['category_id'] ?? '');
        $newCategoryName = trim((string) ($data['category_name'] ?? ''));

        if ($newCategoryName !== '') {
            $category = Category::firstOrCreate([
                'colocation_id' => $colocation->id,
                'name' => $newCategoryName,
            ]);

            $categoryId = $category->id;
        } elseif ($selectedCategory !== '' && $selectedCategory !== '__new__') {
            if (! ctype_digit($selectedCategory)) {
                return back()->withErrors([
                    'category_id' => 'Invalid category selection.',
                ])->withInput();
            }

            $categoryBelongsToColocation = Category::where('id', (int) $selectedCategory)
                ->where('colocation_id', $colocation->id)
                ->exists();

            if (! $categoryBelongsToColocation) {
                return back()->withErrors([
                    'category_id' => 'Selected category is invalid for this colocation.',
                ])->withInput();
            }

            $categoryId = (int) $selectedCategory;
        }

        if (! $categoryId) {
            return back()->withErrors([
                'category_id' => 'Select an existing category or type a new one.',
            ])->withInput();
        }

        Expense::create([
            'colocation_id' => $colocation->id,
            'category_id' => $categoryId,
            'payer_id' => $user->id,
            'title' => $data['title'],
            'amount' => $data['amount'],
            'expense_date' => $data['expense_date'],
        ]);

        return back()->with('success', 'Expense added successfully.');
    }
}
