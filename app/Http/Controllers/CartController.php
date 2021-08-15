<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $cartservice;
    public $user;

    public function __construct(CartService $cartservice)
    {
        $this->cartservice = $cartservice;
        $this->user = auth('api')->user();
    }
    public function index()
    {
        return $this->cartservice->getCart($this->user);
    }

    public function store(Request $request)
    {



        return  $this->cartservice->add(
            $this->user,
            $request->store_name,
            $request->product_name,
            $request->brand_name,
            $request->price,
            $request->quantity,
            $request->description,
            $request->image
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        return $cart;
    }

    public function gettotal()
    {

        return $this->cartservice->total($this->user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        return $this->cartservice->update($request->action, $cart);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        return  $this->cartservice->remove($cart);
    }
    public function destroyall()
    {
        return  $this->cartservice->clearcart($this->user);
    }
}
