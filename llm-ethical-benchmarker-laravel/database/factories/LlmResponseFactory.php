<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LlmResponse>
 */
class LlmResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'test_run_id' => 1,
            'provider' => $this->faker->company(),
            'model' => $this->faker->word(),
            'temperature' => $this->faker->randomFloat(2, 0, 1),
            'prompt' => $this->faker->sentence(),
            'response_raw' => $this->faker->paragraph(),
            'cost_usd' => $this->faker->randomFloat(4, 0, 1),
            'latency_ms' => $this->faker->numberBetween(100, 2000),
            'scores' => json_encode(['accuracy' => $this->faker->randomFloat(2, 0, 1)]),
        ];
    }
}
