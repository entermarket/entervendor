<?php

namespace App\Services;


class WishlistService
{
  public function createwishlist($request, $user)
  {
    return  $user->wishlist()->create([
      'store_name' => $request->nastore_nameme,
      'product_name'  => $request->product_name,
      'product_desc'  => $request->product_desc,

    ]);
  }

  public function userwishlist($user)
  {
    return $user->wishlist()->get();
  }
}
