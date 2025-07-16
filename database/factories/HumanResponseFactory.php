<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HumanResponse>
 */
class HumanResponseFactory extends Factory
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
            'participant_hash' => $this->faker->sha256(),
            'answer_text' => $this->faker->paragraph(),
            'created_at' => $this->faker->dateTime(),
        ];
    }

    public function withTimestamps()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => now(),
            ];
        });
    }
}
