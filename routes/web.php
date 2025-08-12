<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Frontend\Welcome;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserManager;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/users', UserManager::class)->name('users');
});

require __DIR__ . '/auth.php';
