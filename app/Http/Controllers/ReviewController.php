<?php

namespace App\Http\Controllers;

use App\Jobs\ClearReviewsCacheJob;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function index(Request $request,Book $book){
        $page = $request->query("page",1);

        $reviews = Cache::remember('reviews_for_book_'.$book->id.'_page_'.$page, now()->addMinutes(60), function () use($book) {
            return $book->reviews()->with("user")->paginate(10);
        });

        return response()->json($reviews);
    }
    public function store(Request $request,Book $book){

        $validated = $request->validate([
            "rating" => "required|integer|between:1,5",
            "comment" => "nullable|string"
        ]);

        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->first();

        if ($existingReview) {

            $existingReview->update([
                "rating" => $validated["rating"],
                "comment" => $validated["comment"],
            ]);

            return response()->json([ "message" => "Review updated succesfully!", "review" => $existingReview ],200);
        }

        $review = $book->reviews()->create([
            "user_id" => $request->user()->id,
            "rating" => $validated["rating"],
            "comment" => $validated["comment"],
        ]);

        // clear reviews cahce
        ClearReviewsCacheJob::dispatch($book->id);

        return response()->json([ "message" => "Review created succesfully!", "review" => $review ],201);
    }
}
