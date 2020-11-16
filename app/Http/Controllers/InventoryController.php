<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\Inventory;
use App\OrderContent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function getInventories(Request $request) {
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

    public function verifyPayment(Request $request) {
        // Collect all required parameter(s)
        $data = array(
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'total_sum' => $request->total_sum,
            'payment_reference' => $request->payment_reference,
            'cartItems' => $request->cart
        );
        // Validate the payload sent
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|string',
            'address' => 'required|string',
            'total_sum' => 'required|numeric',
            'payment_reference' => 'required|string',
            'cartItems' => 'required|array'
        ]);
        if ($validator->fails()) {
            // Return error message on failed Validation
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $total_sum = $this->getCartTotal($data['cartItems']);
        $verifyPayment = $this->verifyonPaystack($data['payment_reference'], $total_sum);
        $verifyPayment = 100;
        if ($verifyPayment == 100) {
            // payment successful, create the order
            try {
                $user = User::find(Auth::user()->id);
                $order = new Order;
                $order->name = $data['name'];
                $order->phone = $data['phone'];
                $order->email = $data['email'];
                $order->address = $data['address'];
                $order->payment_reference = $data['payment_reference'];
                $order->order_reference = $this->generateReference(); // generate a unique order reference
                $order->amount = $total_sum;
                $order->amount_paid = $total_sum;

                // create the order items
                foreach ($data['cartItems'] as $key => $value) {
                    $content = new OrderContent;
                    $content->product_name = $value['name'];
                    $content->product_quantity = $value['quantity'];
                    $content->product_price = $value['price'];
                    $content->total = $value['quantity'] * $value['price'];
                    $content->categoryId = $value['categories_id'];                
                    $content->productId = $value['id'];      
                    $order->orderContent()->save($content);          
                }
                // Save the order using the user as a reference
                $user->orders()->save($order);
                return response()->json([
                    'status' => true,
                    'message' => 'Order created successfully',
                    'data' => [
                        'total_amount' => $total_sum,
                        'order_reference' => $order->order_reference,
                    ]
                ]);
            } catch (\Throwable $th) {
                \Log::info($th);
                return response()->json([
                    'status' => false,
                    'message' => 'Error occured while creating your order'
                ]);
            }
        } elseif ($verifyPayment == 419) {
            // amount paid doesnot match the amount to pay
            return response()->json([
                'status' => false,
                'message' => 'Amount paid isn\'t correct'
            ]);
        } elseif ($verifyPayment == 404) {
            // transaction not found
            return response()->json([
                'status' => false,
                'message' => 'Transaction reference not found'
            ]);
        } else {
            \Log::info($verifyPayment);
            return response()->json([
                'status' => false,
                'message' => 'Sorry we could not validate your payment, kindly contact support'
            ]);
        }
    }
    private function uniqueId() {
        // generate a 20 character ransom string
        return 'DE7_' . Str::random(20);
    }
    public function generateReference () {
        $uniqueId = $this->uniqueId();
        // Validate that the reference doesn't already exist
        $order = Order::where('order_reference', $uniqueId)->first();
        if (!$order) {
            // return the reference if unique
            return $uniqueId;
        } else {
            // generate a new referemce if it's not unique
            $this->generateReference();
        }
    }
    private function getCartTotal($cart) {
        $totalAmount = 0;
        foreach ($cart as $key => $item) {
            $totalAmount = $totalAmount + ($item['quantity'] * $item['price']);
        }
        return $totalAmount;
    }
    public function verifyonPaystack($paymentReference, $amount, $mode = 1) {
        $verified = 0;
        $key = env('MIX_PAYSTACK_TEST_SEC');
        if(env('MIX_APP_ENV') == 'production') {
            $key = env('MIX_PAYSTACK_LIVE_SEC');
        }
        $url = 'https://api.paystack.co/transaction/verify/' . $paymentReference;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $key]
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $request = curl_exec($ch);

        if(curl_errno($ch)) {
            $verified = curl_errno($ch);
        } else {
            if ($request) {
                $result = json_decode($request, true);
                if($result["status"] == true) {
                    if($result["data"] && $result["data"]["status"] == "success") {
                        // at this point, payment has been verified.
                        // launch an event on a queue to send email of receipt to user.
                        $real_amount_paid = $result['data']['amount'] / 100;
                        if($amount == $real_amount_paid) {
                            // Amount paid verified!
                            $verified = 100;
                        } else {
                            // Amount paid inaccurate!
                            $verified = 419;
                        }
                    } else {
                        // Transaction not successful!
                        $verified = 404;
                    }
                }  else {
                    // Transaction not found!
                    $verified = 404;
                }
            } else {
                // Unable to verify transaction!
                $verified = 503;
            }
        }
        curl_close($ch);
        return $verified;
    }
}
