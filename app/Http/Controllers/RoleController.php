<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\ApiResponseClass;
use Carbon\Carbon;

class RoleController extends Controller
{
    // retrieve all role
    public function getRoles()
    {
        try {
            $roles = Role::all();
            $camelObjList = [];
            foreach ($roles as $role) {
                $camelObjList[] = $this->formatCamelCase($role);
            }
            return ApiResponseClass::sendResponse($camelObjList, 'Roles fetched successfully');
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
            $camelObj = $this->formatCamelCase($role);
            return ApiResponseClass::sendResponse($camelObj, 'Role fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Role');
        }
    }

    // create role
    public function createRole(Request $request)
    {
        try {
            $validationFailObj = $this->roleValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $data = $this->requestRoleData($request);

            $role = Role::create($data);
            $camelObj = $this->formatCamelCase($role);
            return ApiResponseClass::sendResponse($camelObj, 'Role created successfully', 201);
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

            $validationFailObj = $this->roleValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $data = $this->requestRoleData($request);

            $role->update($data);
            $camelObj = $this->formatCamelCase($role);
            return ApiResponseClass::sendResponse($camelObj, 'Role updated successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to update Role');
        }
    }

    // validate role data
    private function roleValidationCheck($request)
    {
        $roleId = $request->route('id');
        $validator = Validator::make($request->all(), [
            'roleName' => [
                'required',
                'unique:roles,role_name,' . $roleId . ',id'
            ]
        ], [
            'roleName.required' => "Role name is required",
            'roleName.unique' => "Role name must be unique"
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
        }
        return null;
    }

    // request role data (change to array format)
    private function requestRoleData($request){
        return[
            "role_name" => $request->roleName,
            "remark" => $request->remark
        ];
    }

    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'roleName' => $obj->role_name,
            'remark' => $obj->remark,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

}
