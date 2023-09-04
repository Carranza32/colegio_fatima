<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'last_name' => $this->faker->lastName(),
            'student_card' => $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'email' => $this->faker->unique()->safeEmail(),
            'course_id' => Course::all()->random()->id,
            'is_active' => 1
        ];
    }
}
