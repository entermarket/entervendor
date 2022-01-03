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
    public function show( $brandId)
    {
        return Brand::with('category')->where('store_id', $brandId)->get();
    }
    public function store(Request $request)
    {

        $store = auth('store_api')->user();
        $brand = $store->brands()->create([
            'name' => $request->name,
            'category_id' => intval($request->id)
        ]);
        return $brand->load('category');
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
        $id =  $brand->id;
        if(auth('store_api')->id() != $brand->store_id) return response('Unauthorised', 401);
        $brand->delete();
        return response()->json([
            'id'=> $id
        ]);
    }
}
