<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(){
        $books = Book::paginate(10);
        return response()->json($books);
    }
    public function search(Request $request)
    {
        $query = $request->query('search',"");
        $limit = $request->query('limit',true);
        $limit = filter_var($limit, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        // Search for books by title or author
        if($limit){
            $books = Book::where('title', 'like', "%{$query}%")
                     ->orWhere('author', 'like', "%{$query}%")
                     ->limit(10)
                     ->get();
            return response()->json($books);
        }else{
            $books = Book::where('title', 'like', "%{$query}%")
                     ->orWhere('author', 'like', "%{$query}%")
                     ->paginate(10);
            return response()->json(["data" => $books,"nextPage" => $books->hasMorePages() ? $books->currentPage() + 1 : null]);
        }
    }
    public function popular(Request $request){
        $books = Book::where('views', '>=', 100)->orderBy('views', 'desc')->paginate(10); // You can adjust the criteria based on your database
        return response()->json($books);
    }
    public function mostRented(Request $request){
        $books = Book::withCount('reservations')  // Count reservations related to each book
                    ->having('reservations_count', '>=', 10)
                    ->orderBy('reservations_count', 'desc')  // Order by the reservation count
                    ->paginate(10);  // Paginate the results

        return response()->json($books);
    }
    public function recent(Request $request){
        $books = Book::latest()->paginate(10); // Orders by created_at column in descending order
        return response()->json($books);
    }
    public function byCategory(Request $request,Category $category){
        // Assuming a book belongs to a category
        $books = $category->books()->paginate(10);
        return response()->json($books);
    }
    public function byCollection(Request $request,Collection $collection){
        // Assuming a book belongs to a collection
        $books = $collection->books()->paginate(10);
        return response()->json($books);
    }
    public function find(Request $request,Book $book){
        return response()->json($book);
    }
}
