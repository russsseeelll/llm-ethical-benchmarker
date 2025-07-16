<?php

namespace Database\Factories;

use App\Models\Persona;
use App\Models\Scenario;
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
            'persona_id' => Persona::factory(),
            'scenario_id' => Scenario::factory(),
            'started_by' => $this->faker->userName(),
            'status' => 'pending',
            'started_at' => $this->faker->dateTime(),
            'completed_at' => null,
        ];
    }
}
