<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public
    function random_strings($length_of_string)
    {

        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Shuffle the $str_result and returns substring
        // of specified length
        return substr(
            str_shuffle($str_result),
            0,
            $length_of_string
        );
    }

    public function index()
    {
        return Coupon::all();
    }

    public function show( $code)
    {
        $user = auth('api')->user();
        $coupon = Coupon::where('code', $code)->firstOrFail();
        if ($coupon->status === 'active') {
            $coupon_user = CouponUser::where('user_id',$user->id)->where('coupon_id', $coupon->id)->first();
            if (is_null($coupon_user)) {
                return response([
                    'status' => true,
                    'discount' => $coupon->discount/100
                ], 200);
            } else {
                return response([
                    'status' => false,
                    'message' => 'already used'
                ], 422);
            }
        }
        return response([
            'status' => false,
            'message' => 'invalid'
        ], 422);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'available' => 'required| numeric',
            'start' => 'required',
            'end' => 'required',
            'discount' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }

        $coupon = new Coupon();
        $coupon->available = $request->available;
        $coupon->start = $request->start;
        $coupon->end = $request->end;
        $coupon->discount = $request->discount;
        $coupon->code = $this->random_strings(5);
        $coupon->save();

        return $coupon;
    }

    public function getactive()
    {
        return Coupon::where('status', 'active')->get();
    }

    public function getpending()
    {
        return Coupon::where('status', 'pending')->get();
    }
    public function getexpired()
    {
        return Coupon::where('status', 'expired')->get();
    }


    public function update(Request $request, Coupon $coupon)
    {

        if ($request->has('status') && $request->filled('status')) {
            $coupon->status = $request->status;
        }
        if ($request->has('start') && $request->filled('start')) {
            $coupon->start = $request->start;
        }
        if ($request->has('end') && $request->filled('end')) {
            $coupon->end = $request->end;
        }
        if ($request->has('available') && $request->filled('available')) {
            $coupon->available = $request->available;
        }
        if ($request->has('code') && $request->filled('code')) {
            $coupon->code = $request->code;
        }


        return $coupon->save();
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response(['status' => true], 200);
    }
}
