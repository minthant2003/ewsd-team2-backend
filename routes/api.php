<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Login API
Route::post("login", [AuthController::class, "login"]);

// User API
Route::post("/createUser",[UserController::class, "createUser"]);
Route::get("/readUsers",[UserController::class, "readUsers"]);
Route::get("/readUserById/{id}",[UserController::class, "readUserById"]);
Route::post("/updateUser",[UserController::class, "updateUser"]);
Route::post("/deleteUser/{id}",[UserController::class, "deleteUserById"]);

// Role API
Route::get("get/roles",[RoleController::class,"getRoles"]);
Route::get("get/roles/{id}", [RoleController::class, 'getRoleById']);
Route::post("add/role",[RoleController::class,"createRole"]);
Route::put("update/role/{id}",[RoleController::class,"updateRole"]);
Route::delete("delete/role/{id}",[RoleController::class,"deleteRole"]);

// Department API
Route::get("get/departments",[DepartmentController::class,"getAllDepartments"]);
Route::get("get/department/{id}", [DepartmentController::class, 'getDepartmentById']);
Route::post("add/department",[DepartmentController::class,"createDepartment"]);
Route::put("update/department/{id}",[DepartmentController::class,"updateDepartment"]);
Route::delete("delete/department/{id}",[DepartmentController::class,"deleteDepartment"]);
