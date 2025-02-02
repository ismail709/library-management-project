<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" => $this->faker->sentence,
            "author" => $this->faker->name(),
            "editor" => $this->faker->company(),
            "edition_date" => $this->faker->date(),
            "stock" => $this->faker->numberBetween(1,1000),
            "views" => $this->faker->numberBetween(1,1000),
            "cover_image" => $this->faker->randomElement(['cover1.jpg','cover2.jpg','cover3.jpg','cover4.jpg','cover5.jpg']),
        ];
    }
}
