<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;

// Login
Route::post("login", [AuthController::class, "login"]);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//createUser
Route::post("/createUser",[UserController::class, "createUser"]);

//readAllUsers
Route::get("/readUsers",[UserController::class, "readUsers"]);

//readUserById
Route::get("/readUserById/{id}",[UserController::class, "readUserById"]);

//updateUser
Route::post("/updateUser",[UserController::class, "updateUser"]);

//deleteUser
Route::post("/deleteUser/{id}",[UserController::class, "deleteUserById"]);

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



// Get departments
Route::get("get/departments",[DepartmentController::class,"getAllDepartments"]);

// Get department by id
Route::get("get/department/{id}", [DepartmentController::class, 'getDepartmentById']);


// Create department
Route::post("add/department",[DepartmentController::class,"createDepartment"]);

// Update department
Route::put("update/department/{id}",[DepartmentController::class,"updateDepartment"]);

// Delete department
Route::delete("delete/department/{id}",[DepartmentController::class,"deleteDepartment"]);



