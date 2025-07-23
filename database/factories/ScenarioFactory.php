<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scenario>
 */
class ScenarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->unique()->slug(),
            'persona_id' => Persona::factory(),
            'description' => $this->faker->paragraph(),
            'prompt_template' => $this->faker->sentence(),
            'is_multiple_choice' => $this->faker->boolean(),
            'choices' => json_encode($this->faker->randomElements(['A', 'B', 'C', 'D'], $this->faker->numberBetween(2, 4))),
            'real_life_outcome' => $this->faker->optional()->sentence(),
            'revision' => 1,
            'md5_hash' => md5($this->faker->unique()->uuid),
        ];
    }
}
