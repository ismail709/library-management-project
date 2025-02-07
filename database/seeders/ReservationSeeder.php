<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            $randomBooks = $books->random(10);
            foreach ($randomBooks as $book) {
                $user->reservations()->create(Reservation::factory()->make([
                    "book_id"=> $book->id,
                ])->toArray());
            }
        }
    }
}
