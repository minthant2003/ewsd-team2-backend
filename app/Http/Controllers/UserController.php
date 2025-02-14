<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\ApiResponseClass;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{
    //
    public function createUser(Request $req)
    {
        try{
        $this->validUser($req);
        $user = User::create([
            'user_name' => $req->user_name,
            'password' => $req->password,
            'email' => $req->email,
            'phone_no' => $req->phone_no,
            'role_id' => $req->role_id,
            'department_id' => $req->department_id,
            'remark' => $req->remark
        ]);
    } catch (\Exception $err) {
        return ApiResponseClass::rollback($err, 'Failed to create user');
    
    }
}
  
    //Read all users
    public function readUsers()
    {
        try {
            $users = User::all();

            return ApiResponseClass::sendResponse($users, 'Success User Lists');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to read users');
        }
    }

    //Read user by id
    public function readUserById($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return ApiResponseClass::sendResponse(null, 'User not found', 404);
            }

            return ApiResponseClass::sendResponse($user, 'Success user');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to read user');
        }
    }

    //Update user
    public function updateUser(Request $req)
    {
        try {
            $id = $req->id;
            $this->validUser($req);
            $success = User::where("id",$id)->update([
                'user_name' => $req->user_name,
                'password' => $req->password,
                'email' => $req->email,
                'phone_no' => $req->phone_no,
                'role_id' => $req->role_id,
                'department_id' => $req->department_id,
                'remark' => $req->remark
            ]);

            return ApiResponseClass::sendResponse($success, 'User is successfully Updated');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to update user');
        }
    }

    //Delete user
    public function deleteUserById($id)
    {
        try {
 
            $success = User::where("id",$id)->delete();

            return ApiResponseClass::sendResponse($success, 'User deleted successfully');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to delete user');
        }
    }

    public function validUser($user){
        $isValid = Validator::make($user->all(), [
            'user_name' => 'required',
            'password' => 'required',
            'email' => [
                'required',
                'unique:users,email,' . ($user->email ?? 'NULL') . ',id'
            ],
            'phone_no' => 'required',
            'role_id' => 'required',
            'department_id' => 'required',

        ], [
            'user_name.required' => "User Name is required",
            'pawword.required' => "Password is required",
            'email.required' => "Email is required",
            'email.unique' => "Email is already taken",
            'phone_no.required' => "Phone Number is required",
            'role_id.required' => "Role is required",
            'department_id.required' => "Department is required"
        ]);

        if ($isValid->fails()) {
            throw new ValidationException($isValid);
        }

    }

 }

