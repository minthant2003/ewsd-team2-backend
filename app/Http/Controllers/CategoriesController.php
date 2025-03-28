<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CategoriesController extends Controller
{
    // Retrieve all categories
    public function getAllCategories(){
        try{
            $categories= Categories::all();
            $formattedObjList=[];
            foreach ($categories as $category) {
                $formattedObjList[] = $this->formatCamelCase($category);
            }
            return ApiResponseClass::sendResponse($formattedObjList, 'Categories fetched successfully');
        }
        catch(\exception $e){
            return ApiResponseClass::rollback($e, 'Failed to fetch Categories');
        }

    }

    // Retrieve a specific category by ID
    public function getCategoryById($id)
    {
        try {
            $category = Categories::find($id);

            if (!$category) {
                return ApiResponseClass::sendResponse(null, 'Category not found', 404);
            }
            $formattedObj = $this->formatCamelCase($category);
            return ApiResponseClass::sendResponse($formattedObj, 'Category fetched successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to fetch Category');
        }
    }

    // Create a new category
    public function createCategory(Request $request)
    {
        try {
            $validationFailObj = $this->categoryValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $data = $this->requestCategoryData($request);

            $category = Categories::create($data);
            $camelObj = $this->formatCamelCase($category);
            return ApiResponseClass::sendResponse($camelObj, 'Category created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to create Category');
        }
    }

    // Delete a Category
    public function deleteCategory($id)
    {
        try {
            $category = Categories::find($id);

            if (!$category) {
                return ApiResponseClass::sendResponse(null, 'Category not found', 404);
            }

            if ($category->ideas()->exists()) {
                return ApiResponseClass::sendResponse(null, 'Cannot delete category. It is assigned to ideas.', 400);
            }

            $category->delete();

            return ApiResponseClass::sendResponse(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to delete Category');
        }
    }


    // Update a Category
    public function updateCategory($id, Request $request)
    {
        try {
            $category = Categories::find($id);

            if (!$category) {
                return ApiResponseClass::sendResponse(null, 'Category not found', 404);
            }

            $validationFailObj = $this->categoryValidationCheck($request);
            if ($validationFailObj) {
                return $validationFailObj;
            }
            $data = $this->requestCategoryData($request);

            $category->update($data);
            $camelObj = $this->formatCamelCase($category);
            return ApiResponseClass::sendResponse($camelObj, 'Category updated successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e, 'Failed to update Category');
        }
    }

    // Validate Category data
    private function categoryValidationCheck($request)
    {
        $categoryId = $request->route('id');
        $validator = Validator::make($request->all(), [
            'categoryName' => [
                'required',
                'unique:categories,category_name,' . $categoryId . ',id'
            ]
        ], [
            'categoryName.required' => "Category name is required",
            'categoryName.unique' => "Category name must be unique"
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendResponse($validator->errors(), "Validation errors", 400);
        }
        return null;
    }

    // Format Category data for database
    private function requestCategoryData($request)
    {
        return [
            "category_name" => $request->categoryName,
            "remark" => $request->remark
        ];
    }

    private function formatCamelCase($obj)
    {
        return [
            'id' => $obj->id,
            'categoryName' => $obj->category_name,
            'remark' => $obj->remark,
            'createdAt' => Carbon::parse($obj->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($obj->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
