<?php

namespace App\Http\Controllers;

use App\Events\TransactionSuccessful;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BankDetailController extends Controller
{
    public $user;
    public $api_key;
    public function __construct()
    {
        $this->user = auth('api')->user();
        $this->api_key = config('services.paystack.sk');
    }


    public function store($request)
    {
        $request->validate(
            [
                'name' => 'required',
                'bank_name' => 'required',
                'account_no' => 'required',
                'bank_code' => 'required'

            ]
        );

        return   DB::transaction(function () use ($request) {
            $accountverification =  $this->verifyaccountnumber($request->account_no, $request->bank_code);
            if (!$accountverification) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot verify account details'
                ]);
            }

            $bank_name = $request->bank_name;
            $account_no = $request->account_no;
            $bank_code = $request->bank_code;

            $accountdetail = $this->user->accountdetail()->create([
                'account_no' => $account_no,
                'bank_name' => $bank_name,
                'bank_code' => $bank_code,

            ]);
        });
    }

    public function verifyaccountnumber($account_no, $bank_code)
    {

        $verify = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,

        ])->get(
            'https://api.paystack.co/bank/resolve',
            [
                'account_number' => $account_no,
                'bank_code' => $bank_code,

            ]
        );
        return $verify->json()['status'];
    }

    public function getbanks()
    {

        $response = Http::get('https://api.paystack.co/bank?coutry=nigeria');
        return  $bankdata = $response->json()['data'];
    }
    public function getbankdetail()
    {
        return $this->user->accountdetail()->first();
    }
    public function makepayment(Request $request)
    {

        return DB::transaction(function () use ($request) {
            $request->validate([

                'email' => 'required',
                'amount' => 'required',
                'order_id' => 'required'

            ]);

            $email = $request->email;
            $amount = $request->amount * 100;
            $order_id = $request->order_id;


            $body = [
                'email' => $email,
                'amount' => $amount,

            ];

            $response =  Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,
            ])->post(
                'https://api.paystack.co/transaction/initialize',
                $body
            );
            $data = $response->json()['data'];

            $result =   $this->user->transactions()->create([
                'reference' => $data['reference'],
                'message' => 'pending',
                'status' => 'pending',
                'trxref' =>  $data['access_code'],
                'redirecturl' =>  $data['authorization_url'],
                'order_id' => $order_id,
                'amount' => $amount,
                'mode' => 'paysatck',
                'type' => 'online'

            ]);

            return $data;
        });
    }

    public function verifytransaction($reference)
    {
        return  DB::transaction(function () use ($reference) {
            $response =  Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,
            ])->get(
                'https://api.paystack.co/transaction/verify/' . $reference
            );


            if ($response->json()['status'] && strtolower($response->json()['message']) == 'verification successful') {

                $order = Transaction::where('reference', $reference)->first();
                if (!$order) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid reference'
                    ]);
                }
                $order->message = $response->json()['message'];
                $order->status = $response->json()['status'];
                $order->save();




                return response()->json([
                    'status' => true,
                    'message' => 'Verification successful'
                ]);
            }
        });
    }

    public function transactionevent(Request $request)
    {
        return  DB::transaction(function () use ($request) {

            if ($request->event == 'charge.success') {

                $order = Order::where('reference', $request->reference)->first();
                if (!$order) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid reference'
                    ]);
                }
                $order->message = $request->gateway_response;
                $order->status = $request->status;
                $order->save();



                $result = [
                    'status' => true,
                    'message' => 'Transaction successful'
                ];
                broadcast(new TransactionSuccessful($result));
                return $result;
            }

            $result = [
                'status' => false,
                'message' => 'Transaction failed'
            ];
            broadcast(new TransactionSuccessful($result));
            return $result;
        });
    }
}
