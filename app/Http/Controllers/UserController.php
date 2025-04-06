<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\ApiResponseClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function createUser(Request $req)
    {
        try{
            $validationFailObj = $this->validUser($req);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $user = User::create([
                'user_name' => $req->userName,
                'password' => Hash::make($req->password),
                'email' => $req->email,
                'phone_no' => $req->phoneNo,
                'role_id' => $req->roleId,
                'department_id' => $req->departmentId,
                'last_login' => now(),
                'remark' => $req->remark
            ]);
            $camelObj = $this->formatCamelCase($user);
            return ApiResponseClass::sendResponse($camelObj, "New User has been successfully created.", 201);
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to create user');

        }
    }

    //Read all users
    public function readUsers()
    {
        try {
            $users = User::all();
            $camelObjList = [];
            foreach ($users as $user) {
                $camelObjList[] = $this->formatCamelCase($user);
            }
            return ApiResponseClass::sendResponse($camelObjList, 'Success User Lists');
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
            $camelObj = $this->formatCamelCase($user);
            return ApiResponseClass::sendResponse($camelObj, 'Success user');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to read user');
        }
    }

    //Update user
    public function updateUser(Request $req)
    {
        try {
            $id = $req->id;
            $user = User::find($id);
            if (!$user) {
                return ApiResponseClass::sendResponse(null, 'User not found', 404);
            }
            $validationFailObj = $this->validUserForUpdate($req);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $success = User::where("id",$id)->update([
                'user_name' => $req->userName,
                'email' => $req->email,
                'phone_no' => $req->phoneNo,
                'role_id' => $req->roleId,
                'department_id' => $req->departmentId,
                'last_login' => now(),
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
            $user = User::find($id);
            if (!$user) {
                return ApiResponseClass::sendResponse(null, 'User not found', 404);
            }
            $success = User::where("id",$id)->delete();

            return ApiResponseClass::sendResponse($success, 'User deleted successfully');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to delete user');
        }
    }

    public function validUser($request){
        $userId = $request->id;
        $isValid = Validator::make($request->all(), [
            'userName' => 'required',
            'password' => 'required',
            'email' => [
                'required',
                'unique:users,email,' . $userId . ',id'
            ],
            'phoneNo' => 'required',
            'roleId' => 'required',
            'departmentId' => 'required',
        ], [
            'userName.required' => "User Name is required",
            'password.required' => "Password is required",
            'email.required' => "Email is required",
            'email.unique' => "Email is already taken",
            'phoneNo.required' => "Phone Number is required",
            'roleId.required' => "Role is required",
            'departmentId.required' => "Department is required"
        ]);

        if ($isValid->fails()) {
            return ApiResponseClass::sendResponse($isValid->errors(), "Validation errors", 400);
        }
        return null;
    }

    public function validUserForUpdate($request){
        $userId = $request->id;
        $isValid = Validator::make($request->all(), [
            'userName' => 'required',
            'email' => [
                'required',
                'unique:users,email,' . $userId . ',id'
            ],
            'phoneNo' => 'required',
            'roleId' => 'required',
            'departmentId' => 'required',
        ], [
            'userName.required' => "User Name is required",
            'email.required' => "Email is required",
            'email.unique' => "Email is already taken",
            'phoneNo.required' => "Phone Number is required",
            'roleId.required' => "Role is required",
            'departmentId.required' => "Department is required"
        ]);

        if ($isValid->fails()) {
            return ApiResponseClass::sendResponse($isValid->errors(), "Validation errors", 400);
        }
        return null;
    }

    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'userName' => $obj->user_name,
            'email' => $obj->email,
            'phoneNo' => $obj->phone_no,
            'isDisable' => $obj->is_disable,
            'roleId' => $obj->role_id,
            'departmentId' => $obj->department_id,
            'remark' => $obj->remark,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    //Get current logged in user
    public function getUser(Request $request){
        $user = request()->user()->load('role');
        $camelObjUser = $this->formatCamelCase($user);
        $camelObjUser["roleName"] = $user->role->role_name ?? null;
        $userTimezone = $request->input('timezone', 'UTC');
        if ($user->last_login) {
            $camelObjUser["lastLogin"] = $user->last_login
            ->setTimezone($userTimezone)
            ->format('Y-m-d H:i:s');
            $camelObjUser["message"] = $user->last_login->format('Y-m-d H:i:s');
        } else {          
            $camelObjUser["lastLogin"] = null;
            $camelObjUser["message"] = "First time here? Welcome to Synergy";
        }
        if ($user->last_login === null) {
            $user->last_login = now();
            $user->save();
        }
        return ApiResponseClass::sendResponse($camelObjUser, "Received user successfully", 200);
    }

    //Block a user
    public function blockUser(Request $req){
        try {
            $userId = $req->id;
            $user = User::find($userId);
            if (!$user) {
                return ApiResponseClass::sendResponse(null, 'User not found', 404);
            }

            $user->is_disable = true;
            $user->save();

            return ApiResponseClass::sendResponse($this->formatCamelCase($user), 'User has been blocked successfully');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to block user');
        }
    }

    //Unblock a user
    public function unblockUser(Request $req){
        try {
            $userId = $req->id;
            $user = User::find($userId);
            if (!$user) {
                return ApiResponseClass::sendResponse(null, 'User not found', 404);
            }

            $user->is_disable = false;
            $user->save();

            return ApiResponseClass::sendResponse($this->formatCamelCase($user), 'User has been unblocked successfully');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to unblock user');
        }
    }

    //Get blocked users
    public function getBlockedUsers(){
        try {
            $users = User::where('is_disable', true)->get();
            if ($users->isEmpty()) {
                return ApiResponseClass::sendResponse([], 'No blocked users found', 200);
            }

            $camelObjList = [];
            foreach ($users as $user) {
                $camelObjList[] = $this->formatCamelCase($user);
            }
            return ApiResponseClass::sendResponse($camelObjList, 'List of blocked users fetched successfully');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to fetch blocked users');
        }
    }

    //Get users who have not been blocked
    public function getNotblockedUsers(){
        try {
            $users = User::where('is_disable', false)->get();
            if ($users->isEmpty()) {
                return ApiResponseClass::sendResponse([], 'No users who have not been blocked found', 200);
            }

            $camelObjList = [];
            foreach ($users as $user) {
                $camelObjList[] = $this->formatCamelCase($user);
            }
            return ApiResponseClass::sendResponse($camelObjList, 'List of users who have not been blocked fetched successfully');
        } catch (\Exception $err) {
            return ApiResponseClass::rollback($err, 'Failed to fetch users who have not been blocked');
        }
    }
}
