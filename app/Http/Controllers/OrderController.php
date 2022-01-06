<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Carbon;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public $user;
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->user = auth('api')->user();
        $this->orderService = $orderService;
    }

    public function index()
    {
        return $this->user->orders()->with('orderhistories', 'orderinfo')->where('payment_status', 'paid')->latest()->get();
    }

    public function adminindex()
    {
        return OrderResource::collection(Order::with('orderhistories', 'orderinfo')->where('payment_status', 'paid')->latest()->paginate(20));
    }
    public function adminorderspending()
    {
        return Order::with('orderhistories', 'orderinfo')->where('status', 'pending')->where('payment_status', 'paid')->latest()->paginate(20);
    }
    public function adminordersassigned()
    {
        return Order::with('orderhistories', 'orderinfo')->where('status', 'assigned')->where('payment_status', 'paid')->latest()->paginate(20);
    }


    public function storeorders()
    {
        return auth('store_api')->user()->orders()->with('orderhistories', 'orderinfo')->latest()->get();
    }


    public function store(Request $request)
    {
        $name = $request->input('name') ? $request->input('name') : 'Order-' . rand(0000, 9999);

        return $this->orderService->create(
            $this->user,
            $name,
            $request->shipping ? $request->shipping : 0,
            $request->coupon,
            $request->commission,
            $request->discount ? $request->discount : 0,
            $request->shippingtype,
            $request->address,
            $request->city,
            $request->state,
            $request->pickupPoint,
            $request->phoneNumber,
            $request->extraInstruction,
            $request->paymentMethod,
            $request->title,
            $request->isScheduled,
            $request->schedule_time,
            $request->deliverymethod,
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {

        return $order->load('orderhistories', 'orderinfo');
    }
    public function adminshow(Order $order)
    {

        return $order->load('orderhistories', 'orderinfo');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function updateorderstatus(Request $request, Order $order)
    {
        $order->status = $request->status;
        if ($request->status === 'delivered') {
            $order->status = 'delivered';
        }


        $order->save();
        return $order->load('orderhistories', 'orderinfo');
    }
    public function assignlogistic(Request $request, Order $order)
    {

        if ($request->has('status') && $request->filled('status')) {
            $order->status = $request->status;
        }
        if ($request->has('logistic') && $request->filled('logistic')) {
            $order->logistic = $request->logistic;
        }
        if ($request->has('logistic_status') && $request->filled('logistic_status')) {
            $order->logistic_status = $request->logistic_status;
        }
        if ($request->logistic_status === 'delivered') {
            $order->status = 'delivered';
        }
        if ($request->has('view_at') && $request->filled('view_at')) {
            $order->view_at = Carbon::now();
        }

        $order->save();
        return $order->load('orderhistories', 'orderinfo');
    }

    public function queryorder(Order $order)
    {
        return $order;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        return $this->orderService->remove($order);
    }
}
