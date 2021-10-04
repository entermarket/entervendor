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

    public function show(Category $category)
    {

        return $category->load('products');
    }

    public function store(Request $request)
    {
        $store = Store::find($request->store_id);
        return $store->categories()->create([
            'name' => $request->name
        ]);
    }


    public function update()
    {
        return Category::all();
    }
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json('deleted');
    }
}
