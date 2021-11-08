<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Store;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return Brand::all();
    }
    public function show(Brand $brand)
    {

        return Brand::where('store_id', $brand->store_id)->get();
    }
    public function store(Request $request)
    {
       
        $store = Store::find($request->store_id);
        return $store->brands()->create([
            'name' => $request->name
        ]);
    }
    public function update( Request $request, Brand $brand)
    {
        if(auth('store_api')->id() != $brand->store_id) return response('Unauthorised', 401);
       if ($request->has('name') && $request->filled('name')) {
        $brand->name = $request->name;
       }
        $brand->save();
        return $brand;
    }
    public function destroy(Brand $brand)
    {
        if(auth('store_api')->id() != $brand->store_id) return response('Unauthorised', 401);
        $brand->delete();
        return response()->json('deleted');
    }
}
