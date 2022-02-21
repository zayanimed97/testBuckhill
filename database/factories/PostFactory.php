<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->realTextBetween(10, 30);
        return [
            'uuid' => $this->faker->uuid(),
            'title' => $title,
            'title' => Str::slug($title),
            'content' => $this->faker->realTextBetween(120, 300),
            'metadata' => '{"image": "a4b25233-cf6b-3864-b357-dc43e70a3b78", "author": "'.$this->faker->name().'"}'
        ];
    }
}
