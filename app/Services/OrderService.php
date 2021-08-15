<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class OrderService
{

  public function generateUniqueCode()
  {
    do {
      $code = random_int(10000000, 99999999);
    } while (Order::where("order_no", "=", $code)->first());

    return $code;
  }


  public function create($user, int $tax, int $shipping_charges, string $promo, int $discount)
  {
    try {
      return  DB::transaction(function () use ($user,  $tax,  $shipping_charges,  $promo,  $discount) {
        $cartservice = new CartService;
        $usercart =  $cartservice->getCart($user);
        $total = $cartservice->total($user);
        $order_no = $this->generateUniqueCode();
        $grand_total = ($total + $shipping_charges + $tax) - $discount;

        $order =  $user->orders()->create([
          'order_no' => $order_no,
          'status' => 'pending',
          'sub_total' => $total,
          'total_amount' => $total,
          'tax' => $tax,
          'shipping_charges' => $shipping_charges,
          'promo' => $promo,
          'discount' => $discount,
          'grand_total' => $grand_total,
        ]);

        $order->orderhistories()->createMany($usercart->toArray());
        $cartservice->clearcart($user);

        return response()->json(
          [
            'status' => true,
            'message' => 'order created'
          ],
          200
        );
      });
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

  public function update()
  {
  }

  public function remove($order)
  {
    $order->delete();
    return response()->json(
      [
        'status' => true,
        'message' => 'order deleted'
      ],
      200
    );
  }
}
