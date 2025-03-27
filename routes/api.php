<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\ReportedIdeaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReactionController;
use App\Models\ReportedIdea;
use App\Http\Controllers\SystemReportController;

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
    Route::get('academic-year/{id}/ideas-csv', [AcademicYearController::class, 'downloadIdeasCsv']);
    Route::get('academic-year/{id}/submitted-files', [AcademicYearController::class, 'downloadSubmittedFiles']);

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
    Route::get("get/ideas/user/{userId}", [IdeaController::class, "getIdeasByUserId"]);
    Route::get("get/idea/{id}/details", [IdeaController::class, "getIdeaWithComments"]);
    Route::delete("delete/idea/{id}", [IdeaController::class, "deleteIdeaById"]);
    Route::post("report/idea/{id}", [IdeaController::class, "reportIdea"]);
    Route::post("view/idea/{id}/", [IdeaController::class, "increaseViewCount"]);

    //Reaction API
    Route::post("/createReaction",[ReactionController::class,"createReaction"]);
    Route::get("/readReactions",[ReactionController::class,"readReactions"]);
    Route::get("/readReactionByIdeaId/{ideaId}",[ReactionController::class,"readReactionByIdeaId"]);
   // Route::post("/deleteReactionById/{id}",[ReactionController::class,"deleteReactionById"]);
    Route::get("/getTotalLike/{ideaId}",[ReactionController::class,"getTotalLike"]);
    Route::get("/getTotalUnLike/{ideaId}",[ReactionController::class,"getTotalUnLike"]);

    // Comment API
    Route::post("add/ideas/{ideaId}/comment",[CommentController::class,"addComment"]);
    Route::get("get/ideas/{ideaId}/comment",[CommentController::class,"getCommentsByIdea"]);
    Route::delete("delete/comments/{id}",[CommentController::class,"deleteComment"]);
    
    // Block API
    Route::post("/blockUser/{id}", [UserController::class, "blockUser"]);
    Route::post("/unblockUser/{id}", [UserController::class, "unblockUser"]);
    Route::get("get/blockedUsers", [UserController::class, "getBlockedUsers"]);
    Route::get("get/notBlockedUsers", [UserController::class, "getNotblockedUsers"]);

    // ReportedIdea API
    Route::get("get/reportedIdeas", [ReportedIdeaController::class, "getReportedIdeas"]);
    Route::get("get/reportedIdeaByUserId/{userId}", [ReportedIdeaController::class, "getReportedIdeasByUserId"]);
    Route::post("report/idea", [ReportedIdeaController::class, "createReportedIdea"]);
    Route::delete("delete/reportedIdea/{id}", [ReportedIdeaController::class, "deleteReportedIdea"]);

    // System Report API
    Route::get("/getTopActiveUserByDepartment/{departmentId}", [SystemReportController::class, "getTopActiveUserByDepartment"]);
});
