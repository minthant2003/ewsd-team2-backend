<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\IdeaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DepartmentController;

// Authentication
Route::post("/login",[AuthController::class,"login"]);
Route::post("/logout",[AuthController::class,"logout"])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // User API
    Route::get('/user', [UserController::class, "getUser"]);

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

    // Categories API
    Route::get("get/categories",[CategoriesController::class,"getAllCategories"]);
    Route::get("get/category/{id}", [CategoriesController::class, 'getCategoryById']);
    Route::post("add/category",[CategoriesController::class,"createCategory"]);
    Route::put("update/category/{id}",[CategoriesController::class,"updateCategory"]);
    Route::delete("delete/category/{id}",[CategoriesController::class,"deleteCategory"]);

    // Academic Year API
    Route::get("get/academic-years",[AcademicYearController::class,"getAcademicYears"]);
    Route::get("get/academic-year/{id}", [AcademicYearController::class, 'getAcademicYearById']);
    Route::post("add/academic-year",[AcademicYearController::class,"createAcademicYear"]);
    Route::put("update/academic-year/{id}",[AcademicYearController::class,"updateAcademicYear"]);
    Route::delete("delete/academic-year/{id}",[AcademicYearController::class,"deleteAcademicYear"]);

    // Idea API
    // example form request body for submit/idea
    // {
    //      "key": "_",
    //      "files": [file obj] -> file obj arr
    // }
    // *
    //  To access files,
    //  1. php artisan storage:link
    //  2. use public file url -> `${ur_backend_domain}${publicFileUrl}` at frontend
    // *
    Route::post("submit/idea", [IdeaController::class, "submitIdea"]);
    Route::get("get/idea/{id}", [IdeaController::class, "getIdeaById"]);
    Route::get("get/ideas", [IdeaController::class, "getIdeas"]);
    Route::delete("delete/idea/{id}", [IdeaController::class, "deleteIdeaById"]);
});
