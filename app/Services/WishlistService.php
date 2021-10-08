<?php

namespace App\Services;

use App\Models\Store;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;

class WishlistService
{
  public function createwishlist($request, $user)
  {


    $user->wishlist()->create([
      'name' => $request->name,

    ]);
    return  $user->wishlist()->with('wishlistitems')->get();
  }

  public function createwishlistitem($request, $user)
  {

    $wishlist = Wishlist::find($request->wishlist_id);
    $product = Product::find($request->product_id);

    $wishlist->wishlistitems()->create([
      'store_id' => $product->store_id,
      'product_id' => $product->id,
    ]);
    return $user->wishlist()->with('wishlistitems')->get();
  }

  public function userwishlist($user)
  {
    return $user->wishlist()->with('wishlistitems')->get();
  }
}
