<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'email' => 'admin@buckhill.co.uk',
            'is_admin' => 1,
            'password' => bcrypt('admin'), // password
            'avatar' => null,
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'is_marketing' => 0,
        ];
    }
}
