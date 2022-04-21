<?php

namespace App\Http\Controllers;

use App\Models\LgaPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LgaPriceController extends Controller
{

    public function index()
    {
        return LgaPrice::all();
    }
    public function show(LgaPrice $lga_price)
    {
        return $lga_price;
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'lga' => 'required',
            'standard_fee' => 'required|numeric',
            'express_fee' => 'required|numeric',
            'scheduled_fee' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }


      return  LgaPrice::create(
            [
                'lga'=> $request->lga,
                'standard_fee' => intval($request->standard_fee),
                'express_fee' => intval($request->express_fee),
                'scheduled_fee' => intval($request->scheduled_fee),
            ]
        );
    }
    public function update(Request $request, LgaPrice $lga_price){

        if ($request->has('standard_fee') && $request->filled('standard_fee')) {
            $lga_price->standard_fee =  $request->standard_fee;
        }
        if ($request->has('express_fee') && $request->filled('express_fee')) {
            $lga_price->express_fee =  $request->express_fee;
        }
        if ($request->has('scheduled_fee') && $request->filled('scheduled_fee')) {
            $lga_price->scheduled_fee =  $request->scheduled_fee;
        }


        $lga_price->save();
        return $lga_price;

    }
    public function destroy(Request $request, LgaPrice $lga_price)
    {
        $lga_price->delete();
        return response('ok');
    }
}
