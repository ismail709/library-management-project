<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(){
        $categories = Cache::remember('categories', now()->addMinutes(60), function () {
            return Category::all();
        });

        return response()->json($categories);
    }
    public function find(Request $request,Category $category){
        return response()->json($category);
    }
    public function featured(){
        $featured_categories = Cache::remember('featured_categories', now()->addMinutes(60), function () {
            return Category::where("featured",true)->get();
        });
        
        return response()->json($featured_categories);
    }
}
