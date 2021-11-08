<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{



    public function index()
    {
        return Product::with('store', 'category')->get();
    }

    public function storeproducts(Request $request)
    {
        return Product::with('store', 'category')->where('store_id', $request->store_id)->where('category_id', $request->category_id)->get();
    }

    public function allstoreproducts(Request $request)
    {
        return Product::with('store', 'category')->where('store_id', $request->store_id)->get();
    }

    public function show(Product $product)
    {
        return $product->load('store', 'category');
    }
    public function store(Request $request)
    {
        return $request->all();
        return Product::create($request->all());
    }


    public function update(Request $request, Product $product)
    {

        if ($request->filled('active')  && $request->has('active')) {

            $product->active = $request->active;
        }
        if (!empty($request->input('product_name')) && $request->filled('product_name')  && $request->has('product_name')) {
            $product->product_name = $request->product_name;
        }
        if (!empty($request->input('product_desc')) && $request->filled('product_desc')  && $request->has('product_desc')) {
            $product->product_desc = $request->product_desc;
        }
        if (!empty($request->input('in_stock')) && $request->filled('in_stock')  && $request->has('in_stock')) {
            $product->in_stock = $request->in_stock;
        }
        if (!empty($request->input('price')) && $request->filled('price')  && $request->has('price')) {
            $product->price = $request->price;
        }
        if (!empty($request->input('sales_price')) && $request->filled('sales_price')  && $request->has('sales_price')) {
            $product->sales_price = $request->sales_price;
        }
        if (!empty($request->input('image')) && $request->filled('image')  && $request->has('image')) {
            $product->image = $request->image;
        }
        if (!empty($request->input('product_no')) && $request->filled('product_no')  && $request->has('product_no')) {
            $product->product_no = $request->product_no;
        }


        $product->save();
        return $product;
    }

    public function getsimilarproducts($id)
    {
        $product = Product::find($id);
        $similar = Product::with('store', 'category')->where(strtolower('product_name'), 'like', '%' . strtolower($product->product_name) . '%')
            ->where('id',  '!=', $id)->get();
        return $similar;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json('deleted');
    }
}
