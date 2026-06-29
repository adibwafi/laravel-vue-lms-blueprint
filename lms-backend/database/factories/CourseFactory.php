<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends BaseFactory
{
    /**
     * Indicate that the category is for prakerja course category type.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function prakerja()
    {
        return $this->state(function (array $attributes) {
            return [
                'prasyarat' => json_encode(
                    $this->faker->sentences($this->faker->numberBetween(2, 4)),
                ),
                'metode_absensi' => json_encode(
                    $this->faker->sentences($this->faker->numberBetween(2, 4)),
                ),
                'durasi_pelatihan' => $this->faker->text(100, 200),
                'isPaid' => true,
                'link_zoom' => $this->faker->url(),
            ];
        });
    }

    /**
     * Indicate that the category is non prakerja course category type.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function nonPrakerja()
    {
        return $this->state(function (array $attributes) {
            return [
                'what_learn' => json_encode(
                    $this->faker->sentences($this->faker->numberBetween(2, 4)),
                ),
                'skill' => json_encode(
                    $this->faker->sentences($this->faker->numberBetween(2, 4)),
                ),
                'requirements' => json_encode(
                    $this->faker->sentences($this->faker->numberBetween(2, 4)),
                ),
                'price' => $this->faker->randomElement([$this->faker->numberBetween(100000, 4000000), 0]),
                'isPaid' => $this->faker->boolean(),
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'banner' => $this->getRandomImagePath(1920, 1080),
            'name' => $this->faker->text(20),
            'description' => $this->faker->paragraph(4),
            'status' => $this->faker->randomElement(['released']),
            'order' => $this->faker->numberBetween(-50, 50),
        ];
    }
}
