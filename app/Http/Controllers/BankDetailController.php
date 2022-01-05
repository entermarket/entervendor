<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StoreOrder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderCreated;
use App\Notifications\NewOrderAlert;
use Illuminate\Support\Facades\Http;
use App\Events\TransactionSuccessful;

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


            $email = $this->user->email;
            $amount = $request['amount'] * 100;
            $order_id = $request['order_id'];


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
            $responsedata = $response->json()['data'];

            $result =   $this->user->transactions()->create([
                'reference' => $responsedata['reference'],
                'message' => 'pending',
                'status' => 'pending',
                'trxref' =>  $responsedata['access_code'],
                'redirecturl' =>  $responsedata['authorization_url'],
                'order_id' => $order_id,
                'amount' => $amount,
                'mode' => 'paystack',
                'type' => 'online'

            ]);

            return $responsedata;
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

                $transaction = Transaction::where('reference', $reference)->first();
                $payment = Payment::where('reference', $reference)->first();
                if ($transaction) {
                    if(strtolower($transaction->message) === 'verification successful'){
                        return response()->json([
                            'status' => true,
                            'message' => 'Verification successful',
                            'data' => $transaction->load('order'),
                            'type' => 'order'
                        ]);
                    }
                    $transaction->message = $response->json()['message'];
                    $transaction->status = $response->json()['status'];
                    $transaction->save();

                    if ($response->json()['status'] == 'success') {
                        $order = Order::find($transaction->order_id);
                        $order->payment_status = 'paid';
                        $order->save();

                        StoreOrder::where('order_no', $order->order_no)->update(['payment_status'=> 'paid']);
                        $cartservice = new CartService;
                        $user = User::find($order->user_id);
                        $cartservice->clearcart($user);
                        $detail = [
                            'message' => 'Your order has been created and is being processed',
                            'url' => 'http://entermarket.net/profile?showing=4'
                        ];

                        $details = [
                            'message' => 'There is a new pending order',
                            'url' => 'http://admin.entermarket.net/orders/pending'
                        ];
                        $admin = Admin::find(1);

                        $user->notify(new OrderCreated($detail));
                        $admin->notify(new NewOrderAlert($details));
                    } else {
                        $order = Order::find($transaction->order_id);
                        $order->payment_status = 'failed';
                        $order->save();
                        StoreOrder::where('order_no', $order->order_no)->update(['payment_status'=> 'failed']);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Verification successful',
                        'data' => $transaction->load('order'),
                        'type' => 'order'
                    ]);
                } else if ($payment) {


                    if ($payment->status === 'pending') {


                        if ($payment->type === 'airtime') {
                            $body = [
                                'network' => strtoupper($payment->network),
                                'amount' => $payment->amount * 100,
                                'mobile_number' => $payment->number
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/buy-airtime',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                        if ($payment->type === 'data') {
                            $body = [
                                'paymentCode' => $payment->paymentCode,
                                'amount' => $payment->amount * 100,
                                'mobile_number' => $payment->number
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/buy-data-2',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata && $responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                        if ($payment->type === 'electricity') {
                            $body = [
                                'customerid' => $payment->customerid,
                                'amount' => $payment->amount * 100,
                                'paymentCode' => $payment->paymentCode
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/buy-electricity',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata && $responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                        if ($payment->type === 'internet') {
                            $body = [
                                'customerid' => $payment->customerid,
                                'amount' => $payment->amount * 100,
                                'paymentCode' => $payment->paymentCode
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/buy-internet',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata && $responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                        if ($payment->type === 'cable') {

                            $body = [
                                'customerid' => $payment->customerid,
                                'amount' => $payment->amount * 100,
                                'paymentCode' => $payment->paymentCode
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/buy-tv',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata && $responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                        if ($payment->type === 'betting') {
                            $body = [
                                'customerid' => $payment->customerid,
                                'amount' => $payment->amount * 100,
                                'paymentCode' => $payment->paymentCode
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/bet',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata && $responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                        if ($payment->type === 'software') {
                            $body = [
                                'network' => strtoupper($payment->network),
                                'amount' => $payment->amount * 100,
                                'mobile_number' => $payment->number
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/buy-data',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata && $responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                        if ($payment->type === 'education') {
                            $body = [
                                'network' => strtoupper($payment->network),
                                'amount' => $payment->amount * 100,
                                'mobile_number' => $payment->number
                            ];

                            $response =  Http::withHeaders([
                                'Authorization' => 'Bearer ' . $payment->token,
                            ])->post(
                                'https://api.payviame.com/api/buy-data',
                                $body
                            );
                            $responsedata = $response->json();
                            if ($responsedata && $responsedata['status'] === 'success') {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            } else {
                                $payment->status = $response->json()['status'];
                                $payment->message = $response->json()['message'];
                                $payment->transactionRef = $responsedata['transactionRef'];
                                $payment->save();
                            }
                        }
                    } else {
                        return response()->json([
                            'status' => true,
                            'message' => $payment->status,
                            'data' => $payment,
                            'type' => 'payment',

                        ]);
                    }
                    $newToken = $this->refreshtoken($payment->token);
                    return response()->json([
                        'status' => true,
                        'message' => 'Verification successful',
                        'new_token' => $newToken,
                        'data' => $payment,
                        'type' => 'payment',
                        'response' => $responsedata
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid reference'
                    ]);
                }
            }
            $result = [
                'status' => false,
                'message' => 'Transaction failed'
            ];
        });
    }

    public function refreshtoken($token)
    {

        $response =  Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->post('https://api.payviame.com/api/auth/refresh');
        return  $responsedata = $response->json()['access_token'];
    }

    public function transactionevent(Request $request)
    {
        return  DB::transaction(function () use ($request) {

            if ($request->event == 'charge.success') {

                $transaction = Transaction::where('reference', $request->reference)->first();
                if (!$transaction) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid reference'
                    ]);
                }
                $transaction->message = $request->gateway_response;
                $transaction->status = $request->status;
                $transaction->save();

                if ($request->status == 'success') {
                    $order = Order::find($transaction->order_id);
                    $order->payment_status = 'paid';
                    $order->save();
                } else {
                    $order = Order::find($transaction->order_id);
                    $order->payment_status = 'failed';
                    $order->save();
                }



                $result = [
                    'status' => true,
                    'message' => 'Transaction successful',

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

    public function paybypayviame(Request $request)
    {


        return DB::transaction(function () use ($request) {
            $payment = $this->user->payments()->create([
                'type' => $request->type,
                'text' => $request->type.' payment',
                'amount' => $request->amount,
                'service' => $request->service,
                'network' => $request->network,
                'number' => $request->number,
                'service_id' => $request->service_id,
                'status' => 'pending',
                'token' => $request->token,
                'paymentCode' => $request->paymentCode,
                'customerid' => $request->customerid

            ]);

            $email = $this->user->email;
            $amount = $request['amount'] * 100;

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
            $responsedata = $response->json()['data'];
            $payment->reference =  $responsedata['reference'];
            $payment->save();

            return $responsedata;
        });
    }
}
