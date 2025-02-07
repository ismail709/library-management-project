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
        $n_words = rand(2,6);
        return [
            "title" => $this->faker->words($n_words, true),
            "author" => $this->faker->name(),
            "editor" => $this->faker->company(),
            "edition_date" => $this->faker->date(),
            "stock" => $this->faker->numberBetween(1,1000),
            "views" => $this->faker->numberBetween(1,1000),
            "cover_image" => $this->faker->randomElement(['cover1.jpg','cover2.jpg','cover3.jpg','cover4.jpg','cover5.jpg','cover6.jpg','cover7.jpg','cover8.jpg','cover9.jpg','cover10.jpg','cover11.jpg','cover12.jpg','cover13.jpg','cover14.jpg','cover15.jpg','cover16.jpg','cover17.jpg','cover18.jpg','cover19.jpg','cover20.jpg']),
        ];
    }
}
