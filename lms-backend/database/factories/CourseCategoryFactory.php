<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class CourseCategoryFactory extends BaseFactory
{
    /**
     * Indicate that the course category is prakerja.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function prakerja()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'prakerja',
                'prasyarat' => json_encode(
                    $this->faker->sentences($this->faker->numberBetween(2, 4)),
                ),
                'metode_absensi' => json_encode(
                    $this->faker->sentences($this->faker->numberBetween(2, 4)),
                ),
                'durasi_pelatihan' => $this->faker->text(100, 200),
                'price' => $this->faker->numberBetween(1000000, 9000000),
            ];
        });
    }

    /**
     * Indicate that the course category is non prakerja type.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function nonPrakerja()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->faker->randomElement(['msib', 'public_bootcamp']),
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
                'link_payment' => $this->faker->randomElement([null, $this->faker->url()]),
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
            'type' => $this->faker->randomElement(['prakerja', 'msib', 'public_bootcamp']),
            'color' => $this->faker->randomElement(["blue", "purple", "green", "orange", "red"]),
            'description' => $this->faker->paragraph(4),
            'status' => $this->faker->randomElement(['released']),
        ];
    }
}
