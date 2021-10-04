<?php

namespace App\Services;

use App\Models\Store;
use App\Models\Product;

class WishlistService
{
  public function createwishlist($request, $user)
  {

    $product = Product::find($request->product_id);
    $store = Store::find($product->store_id);
    return  $user->wishlist()->create([
      'store_id' => $product->store_id,
      'product_id' => $product->id,
      'store_name' => $store->name,
      'product_name'  => $product->product_name,
      'product_desc'  => $product->product_desc,

    ]);
  }

  public function userwishlist($user)
  {
    return $user->wishlist()->get();
  }
}
