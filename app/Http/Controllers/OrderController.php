<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

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
        return $this->user->orders()->with('orderhistories', 'orderinfo')->latest()->get();
    }
    
    public function storeorders()
    {
        return auth('store_api')->user()->orders()->with('orderhistories', 'orderinfo')->latest()->get();
    }


    public function store(Request $request)
    {



        return $this->orderService->create(
            $this->user,
            $request->name,
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
        return $order;
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
    public function update(Request $request, Order $order)
    {
        //
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
