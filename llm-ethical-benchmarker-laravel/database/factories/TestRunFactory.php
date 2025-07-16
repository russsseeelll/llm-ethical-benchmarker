<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestRun>
 */
class TestRunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'persona_id' => 1,
            'scenario_id' => 1,
            'started_by' => $this->faker->userName(),
            'status' => 'pending',
            'started_at' => $this->faker->dateTime(),
            'completed_at' => null,
        ];
    }
}
