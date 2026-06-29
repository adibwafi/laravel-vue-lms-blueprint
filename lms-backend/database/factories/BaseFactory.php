<?php

namespace Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class BaseFactory extends Factory
{
    /**
     * Get random image path from s3.
     *
     * @param integer $width
     * @param integer $height
     * @return string
     */
    protected function getRandomImagePath(int $width, int $height)
    {
        $random_image_path = sprintf("random-image/%dx%d", $width, $height);
        $files = Config::get($random_image_path);
        if (!$files) {
            $files = Storage::disk('s3')->allFiles($random_image_path);
            Config::set($random_image_path, $files);
        }

        if (count($files) == 0) {
            throw new Exception(sprintf("There is no files with width %d, height %d", $width, $height), 1);
        }
        return $this->faker->randomElement($files);
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    }
}
