<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rentalDate = $this->faker->dateTimeBetween('-5 years', 'now');
        $rentalTime = $this->faker->time('H:i');
        $dueDate = Carbon::parse($rentalDate)->addDays(7);

        $isReturned = array_rand([true, false]);
        $returnDate = null;
        $status = "rented";

        if ($isReturned) {
            $maxReturnDate = Carbon::parse($rentalDate)->addDays(14);
            $returnDate = $this->faker->dateTimeBetween($rentalDate, $maxReturnDate);
            if ($returnDate > $dueDate) {
                $status = "late";
            } else {
                $status = "returned";
            }
        }
        return [
            'rental_date' => $rentalDate->format('Y-m-d'),
            'rental_time' => $rentalTime,
            'due_date' => $dueDate->format('Y-m-d'),
            'return_date' => $returnDate,
            'status' => $status,
        ];
    }
}
