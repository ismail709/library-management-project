<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'rental_date' => 'required|date|after_or_equal:tomorrow|before:+1 week',
            'rental_time' => 'required|date_format:H:i',
        ]);

        $book = Book::find( $request->book_id );
        
        if($book->stock == 0){
            return response()->json([
                'message' => 'We cannot process your request.',
                'errors' => [
                    'book_id' => ['The book you are trying to rent is out of stock.']
                ]
            ], 422);
        }
        
        $existingReservation = Reservation::where('user_id', $request->user()->id)
            ->whereNotIn('status', ['returned','late'])
            ->exists();

        if ($existingReservation) {
            return response()->json([
                'message' => 'This operation is not allowed.',
                'errors' => [
                    'book_id' => ['You already have an active rental. Return the current book before renting another.']
                ]
            ], 422);
        }

        $rentalDate = \Carbon\Carbon::parse($request->rental_date);
        
        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'book_id' => $request->book_id,
            'rental_date' => $request->rental_date,
            'rental_time' => $request->rental_time,
            'due_date' => $rentalDate->addDays(7)->toDateString(),
            'status' => 'pending', 
        ]);

        return response()->json([
            'message' => 'Reservation created successfully.',
            'reservation' => $reservation
        ], 201);
    }

    public function update(Request $request, Reservation $reservation)
    {
        
        $request->validate([
            'book_id' => 'sometimes|exists:books,id',
            'rental_date' => 'sometimes|date',
            'rental_time' => 'sometimes|date_format:H:i',
        ]);

        if ($request->has('book_id')) {
            $reservation->book_id = $request->book_id;
        }
        if ($request->has('rental_date')) {
            $reservation->rental_date = $request->rental_date;
        }
        if ($request->has('rental_time')) {
            $reservation->rental_time = $request->rental_time;
        }
        
        $rentalDate = \Carbon\Carbon::parse($request->rental_date);
        $reservation->due_date = $rentalDate->addDays(7)->toDateString();
        
        $reservation->save();

        return response()->json([
            'message' => 'Reservation updated successfully.',
            'reservation' => $reservation
        ]);
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted successfully.'
        ]);
    }
}
