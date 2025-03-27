<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\ApiResponseClass;
use Carbon\Carbon;

class DepartmentController extends Controller
{
    // Create a new department
    public function createDepartment(Request $request)
    {
        try {
            $validationFailObj = $this->departmentValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $data = $this->requestDepartmentData($request);

            $department = Department::create($data);
            $camelObj = $this->formatCamelCase($department);
            return ApiResponseClass::sendResponse($camelObj, 'Department created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to create Department');
        }
    }

    // Delete a Department
    public function deleteDepartment($id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return ApiResponseClass::sendResponse(null, 'Department not found', 404);
            }

            if ($department->users()->exists()) {
                return ApiResponseClass::sendResponse(null, 'Cannot delete department. It is assigned to users.', 400);
            }

            $department->delete();

            return ApiResponseClass::sendResponse(null, 'Department deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to delete Department');
        }
    }

    // Update a Department
    public function updateDepartment($id, Request $request)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return ApiResponseClass::sendResponse(null, 'Department not found', 404);
            }

            $validationFailObj = $this->departmentValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $data = $this->requestDepartmentData($request);

            $department->update($data);
            $camelObj = $this->formatCamelCase($department);
            return ApiResponseClass::sendResponse($camelObj, 'Department updated successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to update Department');
        }
    }

    // Retrieve all Departments
    public function getAllDepartments()
    {
        try {
            $departments = Department::all();
            $formattedObjList = [];
            foreach ($departments as $department) {
                $formattedObjList[] = $this->formatCamelCase($department);
            }
            return ApiResponseClass::sendResponse($formattedObjList, 'Departments fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Departments');
        }
    }

    // Retrieve a specific department by ID
    public function getDepartmentById($id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return ApiResponseClass::sendResponse(null, 'Department not found', 404);
            }
            $formattedObj = $this->formatCamelCase($department);
            return ApiResponseClass::sendResponse($formattedObj, 'Department fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Department');
        }
    }

    // public function getTotalDepartmentCount()
    // {
    //     try {
    //         $count = Department::count();
    //         return ApiResponseClass::sendResponse($count, "Department count fetched successfully", 200);
    //     } catch (\Exception $e) {
    //         return ApiResponseClass::rollback($e, 'Failed to fetch Department Count.');
    //     }
    // }

    // Validate Department data
    private function departmentValidationCheck($request)
    {
        $departmentId = $request->route('id');
        $validator = Validator::make($request->all(), [
            'departmentName' => [
                'required',
                'unique:departments,department_name,' . $departmentId . ',id'
            ]
        ], [
            'departmentName.required' => "Department name is required",
            'departmentName.unique' => "Department name must be unique"
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
        }
        return null;
    }

    // Format Department data for database
    private function requestDepartmentData($request)
    {
        return [
            "department_name" => $request->departmentName,
            "remark" => $request->remark
        ];
    }

    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'departmentName' => $obj->department_name,
            'remark' => $obj->remark,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
