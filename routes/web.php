<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/colocations', [ColocationController::class, 'index'])->name('colocations.index');
    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('/my-colocation', [ColocationController::class, 'my'])->name('colocations.my');
    Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');
    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');
    Route::post('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');
    Route::post('/colocations/{colocation}/members/{user}/remove', [ColocationController::class, 'removeMember'])
        ->name('colocations.members.remove');
    Route::post('/colocations/{colocation}/transfer-ownership/{user}', [ColocationController::class, 'transferOwnership'])
        ->name('colocations.transfer-ownership');

    Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'store'])
        ->name('invitations.store');

    Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])
        ->name('invitations.accept');
    Route::get('/invitations/{token}/refuse', [InvitationController::class, 'refuse'])
        ->name('invitations.refuse');

    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])
        ->name('expenses.store');
    Route::post('/colocations/{colocation}/payments', [PaymentController::class, 'store'])
        ->name('payments.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/users/{user}/ban', [AdminController::class, 'ban'])->name('admin.ban');
    Route::post('/admin/users/{user}/unban', [AdminController::class, 'unban'])->name('admin.unban');
});

require __DIR__.'/auth.php';
