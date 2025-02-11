<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login
Route::post("login", [AuthController::class, "login"]);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

