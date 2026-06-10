<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
     public function store(Request $request)
    {
        //validation
         $validated = $request->validate([
            'name'=>'required|string|max:255',
            'slug' =>'required|string|unique:categories,slug',
            'image' =>'nullable|image|mimes:jpeg,png,jpg|max:2048',    
        ]);
        //proccesing
        $categories = Category::create($validated);
       //responce
       return response()->json([
      'status'=>'success',
      'data'=>$categories
       ],201);
 
    }
}

