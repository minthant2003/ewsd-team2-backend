<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    // retrieve all role
    public function getRoles()
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            return response()->json([
                'status' => 404,
                'data'=> null,
                'message' => 'No roles found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Roles retrieved successfully',
            'data' => $roles
        ], 200);
    }

    // Retrieve a single role by ID
    public function getRoleById($id)
    {
        $role = Role::find($id);

        if ($role === null) {
            return response()->json([
                'status' => 404,
                'data'=> null,
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Role retrieved successfully',
            'data' => $role
        ], 200);
    }

    // create role
    public function createRole(Request $request)
    {
        $this->roleValidationCheck($request);
        $data = $this->requestRoleData($request);

        $role = Role::create($data);

        return response()->json([
            'status' => 201,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    // delete role
    public function deleteRole($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status' => 404,
                'message' => 'Role not found'
            ], 404);
        }

        $role->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Role deleted successfully'
        ], 200);
    }

    // update role
    public function updateRole($id, Request $request)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'status' => 404,
                'data'=> null,
                'message' => 'Role not found'
            ], 404);
        }

        $this->roleValidationCheck($request);
        $data = $this->requestRoleData($request);

        $role->update($data);

        return response()->json([
            'status' => 200,
            'message' => 'Role updated successfully',
            'data' => $role
        ], 200);
    }

    // validate role data
    private function roleValidationCheck($request){

        Validator::make($request->all(),[
            'roleName'=>'required|unique:roles,role_name'.$request->roleId // variable roleId

        ],[
            'roleName.required'=>"User role need to be selected",
            'roleName.unique' => "Role name must be unique"
        ])->validate();
    }

    // request role data (change to array format)
    private function requestRoleData($request){
        return[
            "role_name" => $request->roleName,
            "remark" => $request->remark
        ];
    }

}
