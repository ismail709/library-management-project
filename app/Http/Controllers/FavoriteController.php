<?php

namespace App\Http\Controllers;

use App\Jobs\ClearFavoritesCacheJob;
use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request,Book $book){
        $data = $request->user()->favorites()->toggle($book->id);

        // remove the cache
        ClearFavoritesCacheJob::dispatch($request->user()->id);
        
        $isFavorite = in_array($book->id, $data['attached']);

        return response()->json(['is_favorite' => $isFavorite]);
    }
    public function isFavorite(Request $request, Book $book)
    {
        $isFavorite = Favorite::where('user_id', $request->user()->id)
                          ->where('book_id', $book->id)
                          ->exists();

        return response()->json(['is_favorite' => $isFavorite]);
    }
}
