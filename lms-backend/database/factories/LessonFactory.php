<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends BaseFactory
{
    /**
     * Indicate that the lesson is quiz.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function quiz()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_quiz' => true,
                'using_zoom' => false,
            ];
        });
    }

    /**
     * Indicate that the lesson is using zoom.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function usingZoom()
    {
        return $this->state(function (array $attributes) {
            return [
                'using_zoom' => true,
                'learn_time' => random_int(1800, 7200)
            ];
        });
    }

    /**
     * Indicate that the lesson is not using zoom.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function notUsingZoom()
    {
        return $this->state(function (array $attributes) {
            return [
                'using_zoom' => false,
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
            'using_zoom' => $this->faker->boolean(),
        ];
    }
}
