<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\OrderContent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function getOrders(Request $request) {
        try {
            // get all the orders available and return success
            $orders = Order::all();
            return response()->json([
                'status' => true,
                'message' => 'Successful',
                'data' => $orders
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
    public function getOrder(Request $request, $orderRef) {
        try {
            // get a single orders using order reference and return success
            $order = Order::with('orderContent')->where('order_reference', $orderRef)->first();
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order with reference ' . $orderRef . ' not found'
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Successful',
                'data' => $order
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
    public function saveStatus(Request $request) {
        $data = array(
            'status' => $request->status,
            'orderRef' => $request->orderRef
        );
        // Validate the payload sent
        $validator = Validator::make($data, [
            'status' => 'required|string|in:pending,intransit,delivered,cancelled',
            'orderRef' => 'required|string'
        ]);
        if ($validator->fails()) {
            // Return error message on failed Validation
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            // get a single orders using order reference and return success
            $order = Order::where('order_reference', $data['orderRef'])->first();
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order with reference ' . $data['orderRef'] . ' not found'
                ]);
            }
            $order->status = $data['status'];
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'Successful'
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
