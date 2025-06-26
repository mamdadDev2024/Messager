<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'image',
            'video',
            'document'
        ];
        return [
            'url' => $this->faker->url,
            'file_name' => $this->faker->name,
            'size' => $this->faker->numberBetween(1000 , 3000),
            'type' => $types[rand(0, count($types) -1)]
        ];
    }
}
