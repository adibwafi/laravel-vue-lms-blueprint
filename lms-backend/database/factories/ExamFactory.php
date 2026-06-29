<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends BaseFactory
{
    /**
     * Indicate that the exam is quiz.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function quiz()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_quiz' => true,
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
            'name' => $this->faker->text(20),
            'max_attempt' => $this->faker->randomElement([null, 1, 3]),
            'kkm' => $this->faker->randomElement([null, 50, 70, 80]),
        ];
    }
}
