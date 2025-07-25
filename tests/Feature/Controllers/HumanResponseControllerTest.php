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

    public function test_store_validates_required_fields(): void
    {
        // try posting with nothing, should fail
        $response = $this->post('/questionnaire', []);

        $response->assertSessionHasErrors(['scenario_id', 'response', 'consent']);
    }

    public function test_store_validates_scenario_exists(): void
    {
        // try posting with a scenario id that doesn't exist
        $response = $this->post('/questionnaire', [
            'scenario_id' => 999, // not a real scenario
            'response' => 'test response',
            'consent' => 'on',
        ]);

        $response->assertSessionHasErrors(['scenario_id']);
    }
} 