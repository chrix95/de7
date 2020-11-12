<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric|unique:users',
            'password' => 'required|min:3|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            $user = new User;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Created'
            ], 201);
        } catch (\Throwable $th) {
            \Log::info($th);
            return response()->json([
                'status' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json([
                'status' => true,
                'data' => [
                    'user' => Auth::user(),
                    'token' => $token
                ]
            ], 200)->header('Authorization', $token);
        }
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout() {
        $this->guard()->logout();
        return response()->json([
            'status' => true,
            'message' => 'Logged out Successfully.',
        ], 200);
    }

    public function user(Request $request) {
        $user = User::find(Auth::user()->id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $user,
        ]);
    }

    public function refresh () {
        if ($token = $this->guard()->refresh()) {
            return response()->json([
                'status' => true,
                'message' => 'Token has been refreshed'
            ], 200)->header('Authorization', $token);
        }
        return response()->json([
            'status' => false,
            'message' => 'Token refresh failed'
        ], 401);
    }

    private function guard () {
        return Auth::guard();
    }

}
