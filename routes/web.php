<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\requestcontroller;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SpecialOrderController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\vehiclePricingController;


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
    Route::resource('users', UserController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('request',requestcontroller::class);
    Route::resource('Coupon',CouponController::class);
    Route::resource('Vehicle',VehicleController::class);
    Route::resource('vehiclePricing',vehiclePricingController::class);
    Route::resource('specialOrder',SpecialOrderController::class);
    Route::resource('systems',SystemSettingsController::class);
    Route::resource('notifications',NotificationController::class);
    Route::resource('admins',AdminController::class)->middleware(['superAdmin']);
    Route::resource('log',ActivityLogController::class)->only(['index']);
    Route::post('/system-settings/auto-assign', [SystemSettingsController::class, 'updateAutoAssignSetting'])
        ->name('system-settings.auto-assign.update');

        
    Route::get('/system-settings/auto-assign', [SystemSettingsController::class, 'getAutoAssignSetting'])
        ->name('system-settings.auto-assign.get');
    Route::patch('/firebase/token', [FirebaseController::class, 'updateToken'])->name('firebase.token');
    Route::post('/firebase/validate-token', [FirebaseController::class, 'validateToken'])->name('firebase.validate-token');

});

require __DIR__.'/auth.php';