<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{


    public function storeproducts(Request $request)
    {
        return Product::with('store')->where('store_id', $request->store_id)->where('category_id', $request->category_id)->get();
    }

    public function allstoreproducts(Request $request)
    {
        return Product::with('store')->where('store_id', $request->store_id)->get();
    }

    public function show(Product $product)
    {
        return $product->load('store', 'category');
    }
    public function store(Request $request)
    {
        return Product::create($request->all());
    }


    public function update(Requet $request, Product $product)
    {
        return   $product->update($request->all());
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json('deleted');
    }
}
