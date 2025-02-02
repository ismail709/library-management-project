<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();
        return response()->json($categories);
    }
    public function find(Request $request,Category $category){
        return response()->json($category);
    }
    public function featured(){
        $featured_categories = Category::where("featured",true)->get();
        return response()->json($featured_categories);
    }
}
