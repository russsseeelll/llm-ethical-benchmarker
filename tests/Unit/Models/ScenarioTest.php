<?php

namespace Tests\Unit\Models;

use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioTest extends TestCase
{
    use RefreshDatabase;
    // this test checks if our scenario model does what we want
    public function test_scenario_can_be_created_with_valid_data(): void
    {
        // make a persona and a scenario with some data
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'title' => 'test scenario',
            'description' => 'a test scenario description',
            'persona_id' => $persona->id,
            'is_multiple_choice' => true,
            'choices' => '["A", "B", "C"]',
        ]);

        // check that the scenario is in the database
        $this->assertDatabaseHas('scenarios', [
            'id' => $scenario->id,
            'title' => 'test scenario',
            'description' => 'a test scenario description',
            'persona_id' => $persona->id,
            'is_multiple_choice' => true,
            'choices' => '["A", "B", "C"]',
        ]);
    }

    public function test_scenario_has_persona_relationship(): void
    {
        // make a persona and a scenario
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create(['persona_id' => $persona->id]);

        // check that the scenario's persona is right
        $this->assertEquals($persona->id, $scenario->persona->id);
    }

    public function test_scenario_generates_slug_from_title(): void
    {
        // make a scenario with a title and no slug
        $scenario = Scenario::factory()->create([
            'title' => 'test scenario title',
            'slug' => null,
        ]);

        // check that the slug was generated
        $this->assertEquals('TestScenarioTitle', $scenario->slug);
    }

    public function test_scenario_uses_existing_slug_if_provided(): void
    {
        // make a scenario with a custom slug
        $scenario = Scenario::factory()->create([
            'title' => 'test scenario title',
            'slug' => 'custom-slug',
        ]);

        // check that the slug is what we set
        $this->assertEquals('custom-slug', $scenario->slug);
    }

    public function test_scenario_can_have_real_life_outcome(): void
    {
        // make a scenario with a real life outcome
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'persona_id' => $persona->id,
            'real_life_outcome' => 'outcome text here.'
        ]);
        // check that the outcome is set
        $this->assertEquals('outcome text here.', $scenario->real_life_outcome);
    }
} 