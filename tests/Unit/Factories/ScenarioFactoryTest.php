<?php

namespace Tests\Unit\Factories;

use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioFactoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_scenario_factory_creates_valid_scenario(): void
    {
        $scenario = Scenario::factory()->create();

        $this->assertInstanceOf(Scenario::class, $scenario);
        $this->assertNotEmpty($scenario->title);
        $this->assertNotEmpty($scenario->slug);
        $this->assertNotNull($scenario->md5_hash);
        $this->assertDatabaseHas('scenarios', ['id' => $scenario->id]);
    }

    public function test_scenario_factory_creates_with_custom_data(): void
    {
        $scenario = Scenario::factory()->create([
            'title' => 'Custom Scenario',
            'description' => 'Custom description',
            'prompt_template' => 'Custom prompt',
            'is_multiple_choice' => true,
            'choices' => '["A", "B", "C"]',
        ]);

        $this->assertEquals('Custom Scenario', $scenario->title);
        $this->assertEquals('CustomScenario', $scenario->slug);
        $this->assertEquals('Custom description', $scenario->description);
        $this->assertEquals('Custom prompt', $scenario->prompt_template);
        $this->assertTrue($scenario->is_multiple_choice);
        $this->assertEquals('["A", "B", "C"]', $scenario->choices);
    }

    public function test_scenario_factory_creates_multiple_scenarios(): void
    {
        $scenarios = Scenario::factory()->count(3)->create();

        $this->assertCount(3, $scenarios);
        $this->assertCount(3, Scenario::all());
    }

    public function test_scenario_factory_generates_unique_titles(): void
    {
        $scenario1 = Scenario::factory()->create();
        $scenario2 = Scenario::factory()->create();

        $this->assertNotEquals($scenario1->title, $scenario2->title);
    }

    public function test_scenario_factory_creates_with_persona(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create(['persona_id' => $persona->id]);

        $this->assertEquals($persona->id, $scenario->persona_id);
        $this->assertEquals($persona->id, $scenario->persona->id);
    }

    public function test_scenario_factory_creates_with_test_runs(): void
    {
        $scenario = Scenario::factory()
            ->has(TestRun::factory()->count(2))
            ->create();

        $this->assertCount(2, $scenario->testRuns);
        $this->assertInstanceOf(TestRun::class, $scenario->testRuns->first());
    }

    public function test_scenario_factory_generates_slug_from_title(): void
    {
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario Title',
            'slug' => null,
        ]);

        $this->assertEquals('TestScenarioTitle', $scenario->slug);
    }
} 