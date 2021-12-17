<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }

        $credentials = $request->only(["email", "password"]);
        $admin = Admin::where('email', $credentials['email'])->first();
        if ($admin) {
            if (!Auth::guard('admin')->attempt($credentials)) {
                $responseMessage = "invalid credentials";

                return response()->json([
                    "success" => false,
                    "message" => $responseMessage,
                    "error" => $responseMessage
                ], 422);
            }

            $user =  Auth::guard('admin')->user();
            $accessToken = $user->createToken('authToken')->accessToken;
            $responseMessage = "login successful";


            return $this->respondWithOnlyToken($accessToken, $responseMessage, $user);
        } else {
            $responseMessage = "invalid credentials";
            return response()->json([
                "success" => false,
                "message" => $responseMessage,
                "error" => $responseMessage
            ], 422);
        }
    }

    public function register(Request $request)
    {


        try {
            $validator = Validator::make($request->all(), [
                'email' => 'bail|required|unique:admins',
                'password' => 'required|min:6',
                'name' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'something went wrong',
                    'error' => $validator->messages()->toArray()
                ], 422);
            }

           Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)

            ]);
            $responseMessage = "registration successful";
            return response()->json([
                'success' => true,
                'message' => $responseMessage
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'something went wrong',
                'error' => $th
            ], 400);
        }
    }
}
