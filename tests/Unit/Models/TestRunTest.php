<?php

namespace Tests\Unit\Models;

use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use App\Models\LlmResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestRunTest extends TestCase
{
    use RefreshDatabase;
    public function test_test_run_can_be_created_with_valid_data(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create();
        
        $testRun = TestRun::factory()->create([
            'persona_id' => $persona->id,
            'scenario_id' => $scenario->id,
            'status' => 'queued',
            'started_by' => 1,
        ]);

        $this->assertDatabaseHas('test_runs', [
            'id' => $testRun->id,
            'persona_id' => $persona->id,
            'scenario_id' => $scenario->id,
            'status' => 'queued',
            'started_by' => 1,
        ]);
    }

    public function test_test_run_has_scenario_relationship(): void
    {
        $scenario = Scenario::factory()->create();
        $testRun = TestRun::factory()->create(['scenario_id' => $scenario->id]);

        $this->assertEquals($scenario->id, $testRun->scenario->id);
    }

    public function test_test_run_has_persona_relationship(): void
    {
        $persona = Persona::factory()->create();
        $testRun = TestRun::factory()->create(['persona_id' => $persona->id]);

        $this->assertEquals($persona->id, $testRun->persona->id);
    }

    public function test_test_run_has_llm_responses_relationship(): void
    {
        $testRun = TestRun::factory()->create();
        $llmResponse = LlmResponse::factory()->create(['test_run_id' => $testRun->id]);

        $this->assertTrue($testRun->llmResponses->contains($llmResponse));
        $this->assertEquals(1, $testRun->llmResponses->count());
    }

    public function test_test_run_datetime_fields_are_casted(): void
    {
        $testRun = TestRun::factory()->create([
            'started_at' => '2023-01-01 10:00:00',
            'completed_at' => '2023-01-01 11:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $testRun->started_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $testRun->completed_at);
    }

    public function test_test_run_can_be_updated_with_completion_data(): void
    {
        $testRun = TestRun::factory()->create(['status' => 'queued']);
        
        $testRun->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->assertEquals('completed', $testRun->status);
        $this->assertNotNull($testRun->completed_at);
    }
} 