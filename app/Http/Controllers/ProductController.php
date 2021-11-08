<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{



    public function index()
    {
        return Product::with('store', 'category')->get();
    }

    public function storeproducts(Request $request)
    {
        return Product::with('store','category')->where('store_id', $request->store_id)->where('category_id', $request->category_id)->get();
    }

    public function allstoreproducts(Request $request)
    {
        return Product::with('store','category')->where('store_id', $request->store_id)->get();
    }

    public function show(Product $product)
    {
        return $product->load('store', 'category');
    }
    public function store(Request $request)
    {
      
        $store = auth('store_api')->user();
         $data = $request->all();
         $data['images'] =$request->images[0]['preview'];
         $data['product_no'] = rand(000000,999999);

         $product= $store->products()->create($data);
         return $product->load('store','category');
    }


    public function update(Request $request, Product $product)
    {
        return   $product->update($request->all());
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
        $id = $product->id;
        $product->delete();
        return response()->json(['id'=> $id,'message'=>'deleted']);
    }
}
