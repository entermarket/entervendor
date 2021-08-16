<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;

class CartService
{
  public function createCart($store_name, $product_name, $brand_name, $price, $quantity, $description, $image)
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
      'image' => $image
    ];
  }

  public function add($user, $store_name, $product_name, $brand_name, $price, $quantity, $description, $image)
  {
    $cartItems = $this->createCart($store_name, $product_name, $brand_name, $price, $quantity, $description, $image);

    $newcart = $user->cart()
      ->where([
        'product_name' => $cartItems['product_name'],
        'store_name' => $cartItems['store_name'],
        'brand_name' => $cartItems['brand_name']
      ])
      ->first();

    if (is_null($newcart)) {
      $item =  $user->cart()->create($cartItems);
      return $item;
    } else {
      $newcart->store_name = $store_name;
      $newcart->brand_name = $brand_name;
      $newcart->price = $price;
      $newcart->quantity =  $newcart->quantity + $quantity;
      $newcart->save();

      return $newcart;
    }
  }
  public function getCart($user)
  {
    $cart = $user->cart()->get();
    $mappedcart = $cart->map(function ($a) {
      $a->subtotal = $a->quantity * $a->price;
      return $a;
    });
    return $mappedcart;
  }
  public function update($action, $cart)
  {

    try {
      if ($action === 'plus') {
        $cart->quantity =  $cart->quantity + 1;
        $cart->save();
      }
      if ($action === 'subtract') {
        $cart->quantity =  $cart->quantity - 1;
        if ($cart->quantity > 1) {
          $cart->save();
        } else {
          $cart->delete();
        }
      }

      return response()->json(
        [
          'status' => true,
          'message' => 'cart updated'
        ],
        200
      );
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
      $cart->delete();
      return response()->json(
        [
          'status' => true,
          'message' => 'item deleted'
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
    $cart  = $this->getCart($user);
    $total =  $cart->reduce(function ($total, $item) {
      return $total += $item->subtotal;
    });
    return $total;
  }
}
