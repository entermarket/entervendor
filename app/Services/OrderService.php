<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\LgaPrice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderCreated;
use App\Http\Controllers\BankDetailController;

class OrderService
{

  public function generateUniqueCode()
  {
    $code = null;
    do {
      $code = random_int(10000000, 99999999);
    } while (Order::where("order_no", "=", $code)->first());

    return $code;
  }


  public function create(
    $user,
    $name,
    $shipping_charges,
    $promo,
    $discount,
    $commission,
    $allAddress,
    $pickup_location,
    $extra_instruction,
    $payment_method,
    $title,
    $delivery_method,

  ) {

    // try {
    return  DB::transaction(function () use (
      $user,
      $name,
      $shipping_charges,
      $promo,
      $discount,
      $commission,
      $allAddress,
      $pickup_location,
      $extra_instruction,
      $payment_method,
      $title,
      $delivery_method,

    ) {
      $cartservice = new CartService;
      $usercart =  $cartservice->getCart($user)['cart'];
      $isScheduled = false;

      if (!count($usercart)) {
        return response()->json(
          [
            'status' => false,
            'message' => 'Empty cart',

          ],
          401
        );
      }

      $total = ($cartservice->total($user)['total']) * count($allAddress);
      $weight = $cartservice->total($user)['weight'];
      $deliveryFee = collect($allAddress)->map(function ($a) {
        $lga =  LgaPrice::find($a['lga']);
        if ($a['shipping'] === 'standard') {
          return  $lga->standard_fee;
        }
        if ($a['shipping'] === 'express') {
          return  $lga->express_fee;
        }
        if ($a['shipping'] === 'scheduled') {
          return  $lga->scheduled_fee;
        }
      })->reduce(function ($a,$b){
        return $a + $b;
      });
      $order_no = $this->generateUniqueCode();
      $grand_total = (intval($total) + intval($deliveryFee)) - $discount;

      $items = $usercart->map(function ($a) {
        return $a['quantity'];
      })->reduce(function ($a, $b) {
        return $a + $b;
      });

      //create order
      foreach ($allAddress as $address) {

        if ($address['shipping'] === 'scheduled') {
          $isScheduled = true;
        }


        $order =  $user->orders()->create([
          'order_no' => $order_no,
          'name' => $name,
          'status' => 'pending',
          'sub_total' => $total,
          'total_amount' => $total,
          'commission' => $commission,
          'tax' => 0,
          'shipping_charges' => $deliveryFee,
          'promo' => $promo,
          'discount' => $discount,
          'grand_total' => $grand_total,
          'title' => $title,
          'isScheduled' => $isScheduled,
          'schedule_time' => $address['schedule_time'],
          'items' => $items,
          'shipping_method' => $address['shipping'],
          'weight' => $weight

        ]);

        $order->orderhistories()->createMany($usercart->toArray());



        $mappedarray = array_map(function ($a) use ($order_no) {
          $a['order_no'] = $order_no;
          return $a;
        }, $usercart->toArray());
        $user->storeorder()->createMany($mappedarray);
        $this->reducequantity($usercart);


        //update order information
        $order->orderinfo()->create([
          'user_id' => $user->id,
          'firstName' => $user->firstName,
          'lastName' => $user->lastName,
          'delivery_method' => $delivery_method,
          'shipping_method' => $address['shipping'],
          'shipping_address' => $address['address'],
          '$pickup_location' => $pickup_location,
          'email' => $user->email,
          'city' => 'city',
          'state' => 'state',
          'phoneNumber' =>  $address['phoneNumber'],
          'extra_instruction' => $extra_instruction,
          'payment_method' => $payment_method
        ]);


        //update user profile here

        $addresses = $user->address;

        array_push($addresses, [
          'address' => $address['address'],
          'lga' => $address['lga'],
          'phoneNumber' => $address['phoneNumber'],
          'contact_name' => $address['contact_name'],
          'shipping' => $address['shipping'],

        ]);
        $user->address =  $addresses;
        $user->save();
      }






      $myrequest = new Request();
      $myrequest->setMethod('POST');
      $myrequest->request->add([
        'amount' => $grand_total,
        'email' => $user->email,
        'order_id' => $order->id
      ]);


      $payment  = new BankDetailController();
      $payment_data = $payment->makepayment($myrequest);



      // clear cart
      // $cartservice->clearcart($user);


      return response()->json(
        [
          'status' => true,
          'message' => 'order created',
          'data' => $payment_data,
          'order' => $order
        ],
        201
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

  public function reducequantity($cartitems)
  {

    foreach ($cartitems as $item) {
      $product  = Product::find($item->product_id);
      $product->in_stock = $product->in_stock  - $item->quantity;
      $product->save();
    }
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
