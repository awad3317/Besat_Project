<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Routes For Users only
Route::middleware(['auth.sanctum.api', 'user'])->group(function () {
   
});

// Routes For Drivers only
Route::middleware(['auth.sanctum.api', 'driver'])->group(function () {
  
});
