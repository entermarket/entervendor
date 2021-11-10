<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function show( $categoryId)
    {

        return Category::where('store_id', $categoryId)->get();
    }

    public function store(Request $request)
    {
        if(!auth('store_api')->id()) return response('Unauthorised', 401);
        $store =auth('store_api')->user();
        return $store->categories()->create([
            'name' => $request->name
        ]);
    }


    public function update(Request $request, Category $category)
    {
        if(auth('store_api')->id() != $category->store_id) return response('Unauthorised', 401);
        $category->name = $request->name;
        $category->save();
        return $category;
    }
    public function destroy(Category $category)
    {
       
        $id =  $category->id;
        if(auth('store_api')->id() != $category->store_id) return response('Unauthorised', 401);
        $category->delete();
        return response()->json([
            'id'=> $id
        ]);
    }
}
