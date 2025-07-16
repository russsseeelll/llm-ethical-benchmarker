<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
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
            'prompt_template' => $this->faker->sentence(),
            'metadata' => json_encode(['role' => $this->faker->jobTitle()]),
            'md5_hash' => md5($this->faker->unique()->uuid),
        ];
    }
}
