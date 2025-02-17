<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|string|email',
            "password" => "required|string"
        ]);
        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), "Validation Error", 400);
        }

        $user = User::where("email", $request->email)->first();
        if (!$user) {
            return ApiResponseClass::sendResponse(null, "Email is not correct.", 404);
        }
        if (!Hash::check($request->password, $user->password)) {
            return ApiResponseClass::sendResponse(null, "Password is not correct.", 404);
        }
        return ApiResponseClass::sendResponse($user, "Login is successful.", 200);
    }
}
