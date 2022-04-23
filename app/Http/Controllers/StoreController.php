<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use Validator;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Services\StoreService;
use Illuminate\Support\Facades\Http;

class StoreController extends Controller
{
    public $storeservice;
    public $user;

    public function __construct(StoreService $storeservice)
    {
        $this->storeservice = $storeservice;
        $this->user = auth('api')->user();
    }
    public function index()
    {
        return $this->storeservice->showallstores();
    }


    public function storegetproducts()
    {
        $store= auth('store_api')->user();
        return $store->products()->with('store','category','brand')->get();
    }
    public function getallstores(Request $request)
    {
        return $this->storeservice->getallstores($request);
    }

    public function store(Request $request)
    {
      
        $validator = Validator::make($request->all(), [

            'email' => 'bail|required|unique:stores|email:rfc,dns',
            'password' => 'required|min:6|alpha_dash',
            'image'=> 'required',
            'lga_id' => 'required'

        ]);



        return $this->storeservice->createstore($request);
    }

    public function show(Store $store)
    {
        return $store;
    }

    public function update(Request $request){
        $store = auth('store_api')->user();
       try {
        $store->name = $request->username;
        $store->save();
        return response('update successful');
       } catch (\Throwable $th) {
           throw $th;
       }
    }
    public function getstorecategories(Store $store)
    {
        return $store->categories()->get();
    }
    public function destroy(Store $store)
    {
        $store->delete();
        return $this->response_success('store removed');
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|email:rfc,dns',
            'password' => 'required|min:6|alpha_dash',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }

        $credentials = $request->only(["email", "password"]);
        $store = Store::where('email', $credentials['email'])->first();
        if ($store) {
            if (!Auth::guard('store')->attempt($credentials)) {
                $responseMessage = "invalid credentials";

                return response()->json([
                    "success" => false,
                    "message" => $responseMessage,
                    "error" => $responseMessage
                ], 422);
            }

        $user =  Auth::guard('store')->user();
            $accessToken =$user->createToken('authToken')->accessToken;
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
    public function getpayviametoken()
    {
        $data = [
            'email' => 'entermarket@payviame.com',
            'password' => 'almond.2',
        ];

        $response =  Http::post('https://apis.payviame.com/api/auth/login', $data);
        $payviame_token = $response->json()['access_token'];
        return $payviame_token;
    }


    public function logout()
    {

        //auth()->user()->logout();

        $user = Auth::guard("store_api")->user()->token();
        $user->revoke();
        $responseMessage = "successfully logged out";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }

    public function changepassword(Request $request)
    {
        $request->validate([
            'oldpassword' => 'required',
            'newpassword' => 'required',
        ]);

        $user = auth('store_api')->user();
        $oldpassword = $user->password;
        $checkpassword = Hash::check($request->oldpassword, $oldpassword);
        if (!$checkpassword) {
            return response()->json([
                "success" => false,
                "message" => 'incorrect old password'

            ], 401);
        }

        $user->password = Hash::make($request->newpassword);
        $user->save();
        return response()->json([
            "success" => true,
            "message" => 'password changed'

        ], 200);
    }

    public function searchstores(Request $request){

        return $this->storeservice->searchstores($request);

    }

}
