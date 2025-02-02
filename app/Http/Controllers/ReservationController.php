<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        // return response()->json($request->all());
        // Validate the request data
        $request->validate([
            'book_id' => 'required|exists:books,id',  // Ensure the book exists
            'rental_date' => ['required','date','after_or_equal:tomorrow'],
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
        // Check if the user already has an active reservation
        $existingReservation = Reservation::where('user_id', $request->user()->id)
            ->where('status', '!=', 'returned') // Active rentals only
            ->exists();

        if ($existingReservation) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'book_id' => ['You already have an active rental. Return the current book before renting another.']
                ]
            ], 422); // Unprocessable Entity (same as validation errors)
        }

        $rentalDate = \Carbon\Carbon::parse($request->rental_date);
        // Create the reservation
        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'book_id' => $request->book_id,
            'rental_date' => $request->rental_date,
            'rental_time' => $request->rental_time,
            'due_date' => $rentalDate->addDays(7)->toDateString(),
            'status' => 'pending',  // Default to pending
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Reservation created successfully.',
            'reservation' => $reservation
        ], 201); // 201 Created status
    }

    public function update(Request $request, Reservation $reservation)
    {
        // Validate the request data
        $request->validate([ // Optional: Only validate if provided
            'book_id' => 'sometimes|exists:books,id',  // Optional: Only validate if provided
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
        // set due date 7 days after rental date
        $rentalDate = \Carbon\Carbon::parse($request->rental_date);
        $reservation->due_date = $rentalDate->addDays(7)->toDateString();
        // Save the updated reservation
        $reservation->save();

        // Return a success response
        return response()->json([
            'message' => 'Reservation updated successfully.',
            'reservation' => $reservation
        ]);
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        // Soft delete the reservation
        $reservation->delete();

        // Return a success response
        return response()->json([
            'message' => 'Reservation deleted successfully.'
        ]);
    }
}
