<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\ApiResponseClass;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    // Create a new department
    public function createDepartment(Request $request)
    {
        try {
            $this->departmentValidationCheck($request);
            $data = $this->requestDepartmentData($request);

            $department = Department::create($data);

            return ApiResponseClass::sendResponse($department, 'Department created successfully', 201);
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

            $this->departmentValidationCheck($request);
            $data = $this->requestDepartmentData($request);

            $department->update($data);

            return ApiResponseClass::sendResponse($department, 'Department updated successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to update Department');
        }
    }

    // Retrieve all Departments
    public function getAllDepartments()
    {
        try {
            $department = Department::all();

            return ApiResponseClass::sendResponse($department, 'Departments fetched successfully');
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

            return ApiResponseClass::sendResponse($department, 'Department fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Department');
        }
    }

    // Validate Department data
    private function departmentValidationCheck($request)
    {
        $validator = Validator::make($request->all(), [
            'departmentName' => [
                'required',
                'unique:departments,department_name,' . ($request->departmentId ?? 'NULL') . ',id'
            ]
        ], [
            'departmentName.required' => "Department name is required",
            'departmentName.unique' => "Department name must be unique"
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    // Format Department data for database
    private function requestDepartmentData($request)
    {
        return [
            "department_name" => $request->departmentName,
            "remark" => $request->remark
        ];
    }
}
