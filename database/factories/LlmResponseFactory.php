<?php

namespace Database\Factories;

use App\Models\TestRun;
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
            'test_run_id' => TestRun::factory(),
            'provider' => $this->faker->company(),
            'model' => $this->faker->word(),
            'temperature' => $this->faker->randomFloat(2, 0, 1),
            'prompt' => $this->faker->sentence(),
            'response_raw' => json_encode([
                'choices' => [
                    [
                        'message' => [
                            'content' => $this->faker->paragraph()
                        ]
                    ]
                ]
            ]),
            'cost_usd' => $this->faker->randomFloat(4, 0, 1),
            'latency_ms' => $this->faker->numberBetween(100, 2000),
            'scores' => null,
        ];
    }
}
