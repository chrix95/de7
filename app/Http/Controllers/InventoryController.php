<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function getInventories() {
        try {
            // get all the inventory available and return success
            $inventory = Inventory::all();
            return response()->json([
                'status' => true,
                'message' => 'Successful',
                'data' => $inventory
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
    
    public function createInventory(Request $request) {
        // Collect all required parameter(s)
        $data = array(
            'name' => $request->name,
            'categories_id' => $request->categories_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description
        );
        // Validate the payload sent
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'categories_id' => 'required|integer',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'description' => 'required|string'
        ]);
        if ($validator->fails()) {
            // Return error message on failed Validation
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            // Create the new inventory
            $inventory = Inventory::create($data);
            // return the newly created inventory
            return response()->json([
                'status' => true,
                'message' => 'Successful',
                'data' => $inventory
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

    public function updateInventory(Request $request) {
        // Collect all required parameter(s)
        $data = array(
            'id' => $request->id,
            'name' => $request->name,
            'categories_id' => $request->categories_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description
        );
        // Validate the payload sent
        $validator = Validator::make($data, [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'categories_id' => 'required|integer',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'description' => 'required|string'
        ]);
        if ($validator->fails()) {
            // Return error message on failed Validation
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            // find the inventory requested
            $inventory = Inventory::find($data['id']);
            if ($inventory) { // update the inventory if it exist and return success
                $inventory->update([
                    'name' => $data['name'],
                    'categories_id' => $data['categories_id'],
                    'price' => $data['price'],
                    'quantity' => $data['quantity'],
                    'description' => $data['description']
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Successful',
                    'data' => $inventory
                ], 200);
            }
            // return error mesage as item isn't found
            return response()->json([
                'status' => false,
                'message' => 'Invenntory not found'
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
    
    public function deleteInventory(Request $request) {
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
            $inventory = Inventory::find($data['id']);
            if ($inventory) { // delete the inventory if it exist and return success
                $inventory->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Successful'
                ], 200);
            }
            // return error mesage as item isn't found
            return response()->json([
                'status' => false,
                'message' => 'Inventory not found'
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
