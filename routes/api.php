<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;

// Login
Route::post("login", [AuthController::class, "login"]);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Get role
Route::get("get/roles",[RoleController::class,"getRoles"]);

// Get role by id
Route::get("get/roles/{id}", [RoleController::class, 'getRoleById']);


// Create role
Route::post("add/role",[RoleController::class,"createRole"]);

// Update role
Route::put("update/role/{id}",[RoleController::class,"updateRole"]);

// Delete role
Route::delete("delete/role/{id}",[RoleController::class,"deleteRole"]);



