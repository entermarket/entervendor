<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use Mail;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Str;


class UserController extends Controller
{


    protected $user;


    public function __construct()
    {

        $this->middleware("auth:api", ["except" => ["login", "register", "postEmail", "updatePassword"]]);
        $this->user = new User;
    }
    public function register(Request $request)
    {
        // try {

        $validated =  $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            'phoneNumber' => 'required|unique:users'
        ]);

        User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'address' => $request->address,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'phoneNumber' => $request->phoneNumber,
            'profileImage' => $request->profileImage,
            'password' => Hash::make($request->password)

        ]);


        $responseMessage = "registration successful";

        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'something went wrong'
        //     ], 200);
        // }
    }
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
        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            if (!auth()->attempt($credentials)) {
                $responseMessage = "invalid credentials";

                return response()->json([
                    "success" => false,
                    "message" => $responseMessage,
                    "error" => $responseMessage
                ], 422);
            }

            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            $responseMessage = "login successful";

            return $this->respondWithToken($accessToken, $responseMessage, auth()->user());
        } else {
            $responseMessage = "invalid credentials";
            return response()->json([
                "success" => false,
                "message" => $responseMessage,
                "error" => $responseMessage
            ], 422);
        }
    }


    public function postEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);



        $token = Str::random(40);

        DB::table('password_resets')->insert(
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
        );


        $credentials = $request->only(["email"]);
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {

            $responseMessage = "email error";

            return response()->json([
                "success" => false,
                "message" => $responseMessage,
                "error" => $responseMessage
            ], 422);
        }


        $maildata = [
            'title' => 'Password Reset',
            'url' => 'http://localhost:3000/reset-password/?token=' . $token . '&action=password_reset'
        ];

        Mail::to($credentials['email'])->send(new PasswordResetMail($maildata));
        return response()->json([
            "success" => true,
            "message" => 'email sent',

        ], 200);
    }
    public function updatePassword(Request $request)
    {


        $request->validate([
            // 'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6',
            'confirmPassword' => 'required',

        ]);

        $updatePassword = DB::table('password_resets')
            ->where(['token' => $request->token])
            ->first();

        if (!$updatePassword) {
            return response()->json([
                "success" => false,
                "message" => 'Invalid request'

            ], 200);
        }


        $user = User::where('email', $updatePassword->email)
            ->update(['password' => Hash::make($request->password)]);


        DB::table('password_resets')->where(['token' => $request->token])->delete();

        return response()->json([
            "success" => true,
            "message" => 'Your password has been changed'

        ], 200);
    }

    public function viewProfile()
    {
        $responseMessage = "user profile";
        $data = Auth::guard("api")->user();
        return response()->json([
            "success" => true,
            "message" => $responseMessage,
            "data" => $data
        ], 200);
    }

    public function logout()
    {

        //auth()->user()->logout();
        $user = Auth::guard("api")->user()->token();
        $user->revoke();
        $responseMessage = "successfully logged out";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }
}
