<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Store;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return Brand::with('category')->get();
    }
    public function show( $brandId)
    {
        return Brand::with('category')->get();
    }
    public function store(Request $request)
    {


        $brand = Brand::create([
            'name' => $request->name,
            'category_id' => intval($request->id),
            'store_id' =>1
        ]);
        return $brand->load('category');
    }
    public function update( Request $request, Brand $brand)
    {

       if ($request->has('name') && $request->filled('name')) {
        $brand->name = $request->name;
       }
        $brand->save();
        return $brand;
    }
    public function destroy(Brand $brand)
    {
        $id =  $brand->id;

        $brand->delete();
        return response()->json([
            'id'=> $id
        ]);
    }
}
