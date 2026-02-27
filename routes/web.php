<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ExpenseController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations', [ColocationController::class, 'store'])->name('colocations.store');
});
Route::get('/my-colocation', [ColocationController::class, 'my'])->name('colocations.my')->middleware('auth');
Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show')->middleware('auth');
    Route::middleware('auth')->group(function () {
    Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');
});
Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])
    ->name('colocations.leave');
Route::post('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])
    ->name('colocations.cancel');

//invitation Route

Route::middleware('auth')->group(function () {
    Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'store'])
        ->name('invitations.store');

    Route::get('/invites/{token}', [InvitationController::class, 'show'])
        ->name('invitations.show');

    Route::post('/invites/{token}/accept', [InvitationController::class, 'accept'])
        ->name('invitations.accept');

    Route::post('/invites/{token}/refuse', [InvitationController::class, 'refuse'])
        ->name('invitations.refuse');
    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])
        ->name('expenses.store');
});




Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'store'])
    ->name('invitations.store');





require __DIR__.'/auth.php';
