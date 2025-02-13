<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\ApiResponseClass;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    // retrieve all role
    public function getRoles()
    {
        try {
            $role = Role::all();

            return ApiResponseClass::sendResponse($role, 'Roles fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Roles');
        }
    }

    // Retrieve a single role by ID
    public function getRoleById($id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return ApiResponseClass::sendResponse(null, 'Role not found', 404);
            }

            return ApiResponseClass::sendResponse($role, 'Role fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Role');
        }
    }

    // create role
    public function createRole(Request $request)
    {
        try {
            $this->roleValidationCheck($request);
            $data = $this->requestRoleData($request);

            $role = Role::create($data);

            return ApiResponseClass::sendResponse($role, 'Role created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to create Role');
        }
    }

    // delete role
    public function deleteRole($id)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return ApiResponseClass::sendResponse(null, 'Role not found', 404);
            }

            $role->delete();

            return ApiResponseClass::sendResponse(null, 'Role deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to delete Role');
        }
    }

    // update role
    public function updateRole($id, Request $request)
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return ApiResponseClass::sendResponse(null, 'Role not found', 404);
            }

            $this->roleValidationCheck($request);
            $data = $this->requestRoleData($request);

            $role->update($data);

            return ApiResponseClass::sendResponse($role, 'Role updated successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to update Role');
        }
    }

    // validate role data
    private function roleValidationCheck($request)
    {
        $validator = Validator::make($request->all(), [
            'roleName' => [
                'required',
                'unique:roles,role_name,' . ($request->roleId ?? 'NULL') . ',id'
            ]
        ], [
            'roleName.required' => "Role name is required",
            'roleName.unique' => "Role name must be unique"
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    // request role data (change to array format)
    private function requestRoleData($request){
        return[
            "role_name" => $request->roleName,
            "remark" => $request->remark
        ];
    }

}
