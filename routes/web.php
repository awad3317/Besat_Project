<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
use App\Http\Controllers\drivercontroller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('users', usercontroller::class);
    Route::resource('drivers', drivercontroller::class);
});

require __DIR__.'/auth.php';