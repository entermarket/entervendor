<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function respondWithToken($token, $payviame_token, $responseMessage, $data)
    {
        return \response()->json([
            "success" => true,
            "message" => $responseMessage,
            "data" => $data,
            "token" => $token,
            'payviame_token' => $payviame_token,
            "token_type" => "bearer",
        ], 200);
    }
    public function respondWithOnlyToken($token,$responseMessage, $data)
    {
        return \response()->json([
            "success" => true,
            "message" => $responseMessage,
            "data" => $data,
            "token" => $token,
            "token_type" => "bearer",
        ], 200);
    }

    public function response_success($data)
    {
        return response()->json([
            'message' => 'success',
            'status' => true,
            'data' => $data
        ]);
    }
}
