<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request,Book $book){
        $data = $request->user()->favorites()->toggle($book->id);
        $isFavorite = in_array($book->id, $data['attached']);

        return response()->json(['is_favorite' => $isFavorite]);
    }
    public function isFavorite(Request $request, Book $book)
    {
        // return response()->json(['message'=> $request->user()]);
        $isFavorite = Favorite::where('user_id', $request->user()->id)
                          ->where('book_id', $book->id)
                          ->exists();

        return response()->json(['is_favorite' => $isFavorite]);
    }
}
