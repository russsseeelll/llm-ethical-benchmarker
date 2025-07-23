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
    public function test_scenario_can_be_created_with_valid_data(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario',
            'description' => 'A test scenario description',
            'persona_id' => $persona->id,
            'is_multiple_choice' => true,
            'choices' => '["A", "B", "C"]',
        ]);

        $this->assertDatabaseHas('scenarios', [
            'id' => $scenario->id,
            'title' => 'Test Scenario',
            'description' => 'A test scenario description',
            'persona_id' => $persona->id,
            'is_multiple_choice' => true,
            'choices' => '["A", "B", "C"]',
        ]);
    }

    public function test_scenario_has_persona_relationship(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create(['persona_id' => $persona->id]);

        $this->assertEquals($persona->id, $scenario->persona->id);
    }

    public function test_scenario_has_test_runs_relationship(): void
    {
        $scenario = Scenario::factory()->create();
        $testRun = TestRun::factory()->create(['scenario_id' => $scenario->id]);

        $this->assertTrue($scenario->testRuns->contains($testRun));
        $this->assertEquals(1, $scenario->testRuns->count());
    }

    public function test_scenario_generates_slug_from_title(): void
    {
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario Title',
            'slug' => null,
        ]);

        $this->assertEquals('TestScenarioTitle', $scenario->slug);
    }

    public function test_scenario_uses_existing_slug_if_provided(): void
    {
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario Title',
            'slug' => 'custom-slug',
        ]);

        $this->assertEquals('custom-slug', $scenario->slug);
    }

    public function test_scenario_has_md5_hash_generated(): void
    {
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario',
            'prompt_template' => 'You are helpful',
        ]);

        $expectedHash = md5('Test Scenario' . 'You are helpful');
        $this->assertEquals($expectedHash, $scenario->md5_hash);
    }

    public function test_scenario_can_have_real_life_outcome(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'persona_id' => $persona->id,
            'real_life_outcome' => 'Outcome text here.'
        ]);
        $this->assertEquals('Outcome text here.', $scenario->real_life_outcome);
    }
} 