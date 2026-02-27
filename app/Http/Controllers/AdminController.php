<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'colocations' => Colocation::count(),
            'expenses' => Expense::count(),
            'banned' => User::where('is_banned', true)->count(),
        ];

        $users = User::orderByDesc('created_at')->get();

        return view('admin.index', compact('stats', 'users'));
    }

    public function ban(User $user)
    {
        $user->update(['is_banned' => true]);

        return back()->with('success', 'User banned.');
    }

    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);

        return back()->with('success', 'User unbanned.');
    }
}
