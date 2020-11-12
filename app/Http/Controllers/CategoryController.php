<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function getCategories() {
        try {
            // get all the categories available and return success
            $categories = Category::all();
            return response()->json([
                'status' => true,
                'message' => 'Successful',
                'data' => $categories
            ], 200);
        } catch (\Throwable $th) {
            // throw an error to the log and respond properly 
            \Log::info($th);
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    
    public function createCategory(Request $request) {
        // Collect all required parameter(s)
        $data = array(
            'name' => $request->name
        );
        // Validate the payload sent
        $validator = Validator::make($data, [
            'name' => 'required|unique:categories'
        ]);
        if ($validator->fails()) {
            // Return error message on failed Validation
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            // Create the new category
            $category = Category::create($data);
            // return the newly created category
            return response()->json([
                'status' => true,
                'message' => 'Successful',
                'data' => $category
            ], 200);
        } catch (\Throwable $th) {
            // throw an error to the log and respond properly 
            \Log::info($th);
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function updateCategory(Request $request) {
        // Collect all required parameter(s)
        $data = array(
            'id' => $request->id,
            'name' => $request->name
        );
        // Validate the payload sent
        $validator = Validator::make($data, [
            'id' => 'required',
            'name' => 'required|unique:categories'
        ]);
        if ($validator->fails()) {
            // return error message based on validation
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            // find the category requested
            $category = Category::find($data['id']);
            if ($category) { // update the category if it exist and return success
                $category->update([
                    'name' => $data['name']
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Successful',
                    'data' => $category
                ], 200);
            }
            // return error mesage as item isn't found
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 200);
        } catch (\Throwable $th) {
            // throw an error to the log and respond properly 
            \Log::info($th);
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function deleteCategory(Request $request) {
        // Collect all required parameter(s)
        $data = array(
            'id' => $request->id
        );
        // Validate the payload sent
        $validator = Validator::make($data, [
            'id' => 'required'
        ]);
        // return error message based on validation
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            $category = Category::find($data['id']);
            if ($category) { // delete the category if it exist and return success
                $category->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Successful'
                ], 200);
            }
            // return error mesage as item isn't found
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 200);
        } catch (\Throwable $th) {
            // throw an error to the log and respond properly 
            \Log::info($th);
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}
