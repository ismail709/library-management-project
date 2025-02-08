<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();
        
        foreach ($users as $user) {
            // choose 10 random books
            $randomBooks = $books->random(100)->all();
            // loop over them and create reviews for each book
            foreach ($randomBooks as $book) {
                if(!Review::where("book_id", $book->id)->where("user_id", $user->id)->exists()) {
                    $book->reviews()->create(Review::factory()->make(["user_id"=> $user->id])->toArray());
                }
            }
        }
    }
}
