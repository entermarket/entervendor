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

        return Category::get();
    }

    public function store(Request $request)
    {


        return Category::create([
            'name' => $request->name,
        ]);
    }


    public function update(Request $request, Category $category)
    {

        $category->name = $request->name;
        $category->save();
        return $category;
    }
    public function destroy(Category $category)
    {

        $id =  $category->id;

        $category->delete();
        return response()->json([
            'id'=> $id
        ]);
    }
}
