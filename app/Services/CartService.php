<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;

class CartService
{
  public function createCart($store_name, $product_name, $brand_name, $price, $quantity, $description, $image, $store_id, $product_id, $weight)
  {
    $price = floatval($price);
    $quantity = intval($quantity);

    return [

      'store_name' => $store_name,
      'product_name' => $product_name,
      'brand_name' => $brand_name,
      'price' => $price,
      'quantity' => $quantity,
      'description' => $description,
      'image' => $image,
      'store_id' => $store_id,
      'product_id' => $product_id,
      'weight'=>$weight
    ];
  }

  public function add($user, $store_name, $product_name, $brand_name, $price, $quantity, $description, $image, $store_id, $product_id, $weight)
  {
    $cartItems = $this->createCart($store_name, $product_name, $brand_name, $price, $quantity, $description, $image, $store_id, $product_id, $weight);

    $newcart = $user->cart()
      ->where([
        'product_id' => $cartItems['product_id'],
        'store_id' => $cartItems['store_id'],

      ])
      ->first();

    if (is_null($newcart)) {
      $item =  $user->cart()->create($cartItems);
      return response([
        'status' => 'success',
        'data' => $item
      ], 201);
    } else {
      $newcart->store_name = $store_name;
      $newcart->brand_name = $brand_name;
      $newcart->price = $price;
      $newcart->weight = $weight;
      $newcart->quantity =  $newcart->quantity + $quantity;
      $newcart->save();

      return response()->json([
        'status' => 'in_cart',
        'data' => $newcart
      ]);
    }
  }
  public function getCart($user)
  {
    $cart = $user->cart()->get();

    $mappedcart = $cart->map(function ($a) {
      $a->subtotal = $a->quantity * $a->price;
      return $a;
    });
    $total =  $mappedcart->reduce(function ($total, $item) {
      return $total += $item->subtotal;
    });

    $commission = 0;
    $shipping = 0;
    return [
      'cart' => $mappedcart,
      'total' => $total,
      'commission' => $commission,
      'shipping' => $shipping,
      'weight' => $this->totalweight($user)


    ];
  }
  public function update($action, $cart)
  {


    try {
      if ($action === 'plus') {
        $cart->quantity =  $cart->quantity + 1;
        $cart->save();
      }
      if ($action === 'subtract') {


        if ($cart->quantity > 1) {
          $cart->quantity =  $cart->quantity - 1;
          $cart->save();
        } else {
          $cart->delete();
        }
      }
      if ($cart) {
        return $cart;
      }
      return [];
    } catch (\Throwable $th) {
      return response()->json(
        [
          'status' => false,
          'message' => $th
        ],
        200
      );
    }
  }
  public function remove($cart)
  {
    try {
      if ($cart->quantity > 1) {
        $cart->quantity =  $cart->quantity - 1;
        $cart->save();
        return $cart;
      } else {
        $cart->delete();
        return;
      }
    } catch (\Throwable $th) {

      return response()->json(
        [
          'status' => false,
          'message' => 'no action'
        ],
        200
      );
    }
  }
  public function clearcart($user)
  {
    try {
      $user->cart()->delete();
      return response()->json(
        [
          'status' => true,
          'message' => 'cart cleared'
        ],
        200
      );
    } catch (\Throwable $th) {

      return response()->json(
        [
          'status' => false,
          'message' => 'no action'
        ],
        200
      );
    }
  }

  public function total($user)
  {
    $cart = $user->cart()->get();

    $mappedcart = $cart->map(function ($a) {
      $a->subtotal = $a->quantity * $a->price;
      return $a;
    });
    $total =  $mappedcart->reduce(function ($total, $item) {
      return $total += $item->subtotal;
    });

    $commission = 0;
    $shipping = 0;
    return [

      'total' => $total,
      'commission' => $commission,
      'shipping' => $shipping,
      'weight' => $this->totalweight($user)

    ];
  }
  public function totalweight($user)
  {
    $cart = $user->cart()->get();

    $mappedcart = $cart->map(function ($a) {
      $a->totalWeight = $a->quantity * $a->weight;
      return $a;
    });
    $total =  $mappedcart->reduce(function ($total, $item) {
      return $total += $item->totalWeight;
    });

    return $total;
  }
}
