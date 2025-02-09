<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Favorite;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        
        $books = Cache::remember('all_books_'.$page,now()->addMinutes(60),function(){
            return Book::paginate(10);
        });

        return response()->json($books);
    }
    public function search(Request $request)
    {
        $query = $request->query('search', "");
        $page = $request->query('page', 1);
        $limit = $request->query('limit', true);
        $limit = filter_var($limit, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($limit) {
            $books = Cache::remember('searchbar_' . $query, now()->addMinutes(5), function () use ($query) {
                return Book::where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->limit(10)
                    ->get();
            });
            return response()->json($books);
        } else {
            $books = Cache::remember('searchpage_' . $query . "_page_" . $page, now()->addMinutes(5), function () use ($query) {
                return Book::where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->paginate(10);
            });
            return response()->json($books);
        }
    }
    public function popular(Request $request)
    {
        $page = $request->query('page', 1);
        
        $books = Cache::remember("popular_books_".$page,now()->addMinutes(60),function(){
            return Book::where('views', '>=', 100)->orderBy('views', 'desc')->paginate(10);
        });

        return response()->json($books);
    }
    public function mostRented(Request $request)
    {
        $page = $request->query('page', 1);
        
        $books = Cache::remember("most_rented_books_".$page, now()->addMinutes(60), function() {
            return Book::withCount('reservations')
            ->orderBy('reservations_count', 'desc')
            ->paginate(10);
        });

        return response()->json($books);
    }
    public function recent(Request $request)
    {
        $page = $request->query('page', 1);

        $books = Cache::remember("recent_books_" . $page, now()->addMinutes(60), function () {
            return Book::latest()->paginate(10);
        });

        return response()->json($books);
    }
    public function byCategory(Request $request, Category $category)
    {
        $page = $request->query('page', 1);

        $books = Cache::remember("category_".$category->id."_books_" . $page, now()->addMinutes(60), function () use ($category) {
            return $category->books()->paginate(10);
        });

        return response()->json($books);
    }
    public function byCollection(Request $request, Collection $collection)
    {
        $page = $request->query('page', 1);

        $books = Cache::remember("collection_".$collection->id."_books_" . $page, now()->addMinutes(60), function () use ($collection) {
            return $collection->books()->paginate(10);
        });

        return response()->json($books);
    }
    public function find(Request $request, Book $book)
    {
        $isViewed = $request->query('view', false);
        $isViewed = filter_var($isViewed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($isViewed) {

            $book->increment('views');

        }

        if (auth("sanctum")->check()) {

            $isFavorite = Favorite::where('user_id', auth("sanctum")->user()->id)
                ->where('book_id', $book->id)
                ->exists();
            
            return response()->json(["book" => $book, "is_favorite" => $isFavorite]);

        } else {

            return response()->json($book);

        }
    }

    public function recommended(Request $request, Book $book)
    {

        $sameBookRenters = Reservation::where('book_id', $book->id)
            ->when(auth('sanctum')->check(), function ($query) {
                $query->where('user_id', "!=", auth('sanctum')->user()->id);
            })
            ->pluck('user_id');

        if ($sameBookRenters->isEmpty()) {

            return response()->json([]);

        }

        $books = Book::whereHas('reservations', function ($query) use ($sameBookRenters, $book) {
                $query->whereIn('user_id', $sameBookRenters)
                    ->where('book_id', '!=', $book->id);
            })
                ->distinct()
                ->paginate(10);

        return response()->json($books);
    }
}
