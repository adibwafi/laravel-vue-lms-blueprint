<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends BaseFactory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'fullname' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$Q9ttVBay4iMbAtUdZf0e7e6AD4wVFbgofhBlc/Eeyx2Y7fKWefWti',
            'status' => 'active',
            'phone' => '08' . $this->faker->numerify("###########"),
            'image' => $this->getRandomImagePath(1080, 1080),
            'remember_token' => Str::random(10),
            'is_blocked' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
