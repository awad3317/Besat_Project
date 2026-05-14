<?php

use App\Http\Controllers\API\AppSettingController;
use App\Http\Controllers\API\Auth\Driver\DriverAuthController;
use App\Http\Controllers\API\Auth\Driver\DriverForgetPasswordController;
use App\Http\Controllers\API\Auth\Driver\DriverOtpController;
use App\Http\Controllers\API\Auth\User\UserAuthController;
use App\Http\Controllers\API\Auth\User\UserController;
use App\Http\Controllers\API\Auth\User\UserForgetPasswordController;
use App\Http\Controllers\API\Auth\User\UserOtpController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\RatingController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\SpecialOrderController;
use App\Http\Controllers\API\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['auth.sanctum.api'])->group(function () {
        Route::apiResource('vehicles', VehicleController::class)->only(['index']);
});

// Routes For Users only
Route::middleware(['auth.sanctum.api', 'user'])->group(function () {
        Route::post('/user/logout',[UserAuthController::class,'logout']);
        Route::post('/user/upsertRating',[RatingController::class,'upsertRating']);
        Route::post('/user/calculatePrice',[RequestController::class,'calculatePrice']);
        Route::post('/user/specialOrder',[SpecialOrderController::class,'store']);
        Route::get('/profile', [UserController::class, 'index']);
});

// Routes For Drivers only
Route::middleware(['auth.sanctum.api', 'driver'])->group(function () {
        Route::post('/driver/logout',[DriverAuthController::class,'logout']);
        Route::post('/driver/updateDeviceToken',[DriverController::class,'updateDeviceToken']);
        Route::post('/driver/updateLocation',[DriverController::class,'updateLocation']);
        Route::post('/driver/updateOnlineStatus',[DriverController::class,'updateOnlineStatus']);
});
        //           Auth Route For User          //
Route::post('/user/register',[UserAuthController::class,'register']);
Route::post('/user/login',[UserAuthController::class,'login']);
Route::post('/user/verifyOtpAndLogin',[UserOtpController::class,'verifyOtpAndLogin']);
Route::post('/user/resendOTP',[UserOtpController::class,'resendOTP']);
Route::post('/user/check-existence', [UserAuthController::class, 'checkUserExistence']);
        //           Auth Route For Driver         //
Route::post('/driver/register',[DriverAuthController::class,'register']);
Route::post('/driver/login',[DriverAuthController::class,'login']);
Route::post('/driver/verifyOtpAndLogin',[DriverOtpController::class,'verifyOtpAndLogin']);
Route::post('/driver/resendOTP',[DriverOtpController::class,'resendOTP']);
        //       Forget Password Route For User       //
Route::post('/user/forgetPassword', [UserForgetPasswordController::class,'forgetPassword']);
Route::post('/user/resetPassword', [UserForgetPasswordController::class,'resetPassword']);
        //       Forget Password Route For Driver       //
Route::post('/driver/forgetPassword', [DriverForgetPasswordController::class,'forgetPassword']);
Route::post('/driver/resetPassword', [DriverForgetPasswordController::class,'resetPassword']);
        //       App Settings Route    //  
Route::get('/appSettings', [AppSettingController::class, 'index']); 


