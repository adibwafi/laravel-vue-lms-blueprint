<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TutorFactory extends BaseFactory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'image' => $this->getRandomImagePath(1080, 1080),
            'job' => $this->faker->jobTitle(),
            'link_profile' => $this->faker->url(),
        ];
    }
}
