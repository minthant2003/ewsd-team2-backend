<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\User;
use Carbon\Carbon;
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
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponseClass::sendResponse(null, "Email or Password is not correct.", 404);
        }

        $token = $user->createToken("authToken")->plainTextToken;

        // return ApiResponseClass::sendResponse($user, "Login is successful.", 200);
        return ApiResponseClass::sendResponse([
            "user" => $this->formatCamelCase($user),
            "token" => $token
        ], "login successful", 200);
    }

    public function logout(Request $request){
        $user = $request->user();
        if(!$user){
            return ApiResponseClass::sendResponse(null,"Access Denied! There is no token or invalid token");
        }
        // revoke exact, single user's token
        // $request->user()->currentAccessToken()->delete();

        // revoke all related user's token
        $user->tokens()->delete();

        return ApiResponseClass::sendResponse(null,"Log out successful");

    }

    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'userName' => $obj->user_name,
            'email' => $obj->email,
            'phoneNo' => $obj->phone_no,
            'roleId' => $obj->role_id,
            'departmentId' => $obj->department_id,
            'remark' => $obj->remark,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
