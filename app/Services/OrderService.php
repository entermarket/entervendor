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


  public function create(
    $user,
    int $shipping_charges,
    string $promo,
    $commission,
    int $discount,
    $shipping_method,
    $shipping_address,
    $city,
    $state,
    $pickup_location,
    $phoneNumber,
    $extra_instruction,
    $payment_method,
    $title,
    $isScheduled,
    $schedule_time

  ) {

    // try {
    return  DB::transaction(function () use (
      $user,
      $shipping_charges,
      $promo,
      $discount,
      $shipping_method,
      $shipping_address,
      $city,
      $state,
      $phoneNumber,
      $extra_instruction,
      $payment_method,
      $pickup_location,
      $commission,
      $title,
      $isScheduled,
      $schedule_time
    ) {
      $cartservice = new CartService;
      $usercart =  $cartservice->getCart($user);
      $total = $cartservice->total($user);
      $order_no = $this->generateUniqueCode();
      $grand_total = ($total + $shipping_charges + $commission) - $discount;


      //create order
      $order =  $user->orders()->create([
        'order_no' => $order_no,
        'status' => 'pending',
        'sub_total' => $total,
        'total_amount' => $total,
        'commission' => $commission,
        'tax' => 0,
        'shipping_charges' => $shipping_charges,
        'promo' => $promo,
        'discount' => $discount,
        'grand_total' => $grand_total,
        'title' => $title,
        'isScheduled' => $isScheduled,
        'schedule_time' => $schedule_time
      ]);

      $order->orderhistories()->createMany($usercart->toArray());

      //update order information
      $order->orderinfo()->create([
        'user_id' => $user->id,
        'firstName' => $user->firstName,
        'lastName' => $user->lastName,
        'shipping_method' => $shipping_method,
        'shipping_address' => $shipping_address,
        '$pickup_location' => $pickup_location,
        'email' => $user->email,
        'city' => $city,
        'state' => $state,
        'phoneNumber' => $phoneNumber,
        'extra_instruction' => $extra_instruction,
        'payment_method' => $payment_method
      ]);

      //update user profile here
      $user->address = $shipping_address;
      $user->city = $city;
      $user->state =  $state;
      $user->phoneNumber =  $phoneNumber;
      $user->save();

      $cartservice->clearcart($user);

      return response()->json(
        [
          'status' => true,
          'message' => 'order created'
        ],
        200
      );
    });
    // } catch (\Throwable $th) {
    //   return response()->json(
    //     [
    //       'status' => false,
    //       'message' => $th
    //     ],
    //     200
    //   );
    // }
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
