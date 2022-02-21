<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->realTextBetween(10, 15);
        return [
            'uuid' => $this->faker->uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
        ];
    }
}
