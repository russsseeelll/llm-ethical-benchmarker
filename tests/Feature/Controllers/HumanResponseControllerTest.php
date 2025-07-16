<?php

namespace Tests\Feature\Controllers;

use App\Models\HumanResponse;
use App\Models\Scenario;
use App\Models\Persona;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class HumanResponseControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_store_creates_human_response(): void
    {
        // Create required scenario and persona
        $scenario = Scenario::factory()->create();
        $persona = Persona::factory()->create();

        $data = [
            'scenario_id' => $scenario->id,
            'response' => 'This is my ethical response',
            'consent' => 'on',
        ];

        $response = $this->post('/questionnaire', $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('human_responses', [
            'scenario_id' => $scenario->id,
            'response' => 'This is my ethical response',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post('/questionnaire', []);

        $response->assertSessionHasErrors(['scenario_id', 'response', 'consent']);
    }

    public function test_store_validates_scenario_exists(): void
    {
        $response = $this->post('/questionnaire', [
            'scenario_id' => 999, // Non-existent scenario
            'response' => 'Test response',
            'consent' => 'on',
        ]);

        $response->assertSessionHasErrors(['scenario_id']);
    }
} 