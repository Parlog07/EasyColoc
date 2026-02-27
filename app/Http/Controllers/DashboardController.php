<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $activeMembership = $user->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', fn ($query) => $query->where('status', 'active'))
            ->with('colocation')
            ->first();

        $activeColocation = $activeMembership?->colocation;

        $monthlyTotal = 0.0;
        $recentExpenses = collect();
        $activeMembers = collect();

        if ($activeColocation) {
            $monthlyTotal = (float) Expense::query()
                ->where('colocation_id', $activeColocation->id)
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount');

            $recentExpenses = Expense::query()
                ->with(['category', 'payer', 'colocation'])
                ->where('colocation_id', $activeColocation->id)
                ->latest('expense_date')
                ->limit(10)
                ->get();

            $activeMembers = $activeColocation->users()
                ->wherePivotNull('left_at')
                ->get();
        }

        return view('dashboard', [
            'activeColocation' => $activeColocation,
            'monthlyTotal' => round($monthlyTotal, 2),
            'recentExpenses' => $recentExpenses,
            'activeMembers' => $activeMembers,
        ]);
    }
}
