<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BannerPromotionFactory extends BaseFactory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(10),
            'url' => $this->faker->url(),
            'image' => $this->getRandomImagePath(1920, 1080),
            'sorter' => $this->faker->numberBetween(1, 10),
        ];
    }
}
