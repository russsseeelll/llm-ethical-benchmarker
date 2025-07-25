<?php

namespace Tests\Feature\Controllers;

use App\Events\LlmResponseCreated;
use App\Jobs\BiasScoringJob;
use App\Llm\Jobs\RunPromptJob;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use App\Models\LlmResponse;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TestRunControllerTest extends TestCase
{
    use DatabaseMigrations;
    public function test_store_creates_test_run_and_dispatches_job(): void
    {
        Queue::fake();
        
        // make a persona and scenario for our test
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create();
        
        $data = [
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
            'model_key' => 'openai_gpt4o',
            'temperature' => 0.8,
        ];

        // try to create a test run
        $response = $this->post('/test-runs', $data);

        $response->assertStatus(202);
        $response->assertJsonStructure(['test_run_id']);
        
        // check the test run was made right
        $testRun = TestRun::find($response->json('test_run_id'));
        $this->assertNotNull($testRun);
        $this->assertEquals($scenario->id, $testRun->scenario_id);
        $this->assertEquals($persona->id, $testRun->persona_id);
        $this->assertEquals('queued', $testRun->status);
        $this->assertNotNull($testRun->started_at);
        
        // make sure the job was pushed to the right queue
        Queue::assertPushedOn('llm', RunPromptJob::class);
    }

    public function test_store_validates_required_fields(): void
    {
        // try posting with nothing, should fail
        $response = $this->post('/test-runs', []);

        $response->assertSessionHasErrors(['scenario_id', 'persona_id', 'model_key']);
    }

    public function test_store_validates_scenario_exists(): void
    {
        // try posting with a scenario id that doesn't exist
        $persona = Persona::factory()->create();
        
        $response = $this->post('/test-runs', [
            'scenario_id' => 999,
            'persona_id' => $persona->id,
            'model_key' => 'openai_gpt4o',
        ]);

        $response->assertSessionHasErrors(['scenario_id']);
    }

    public function test_store_validates_persona_exists(): void
    {
        // try posting with a persona id that doesn't exist
        $scenario = Scenario::factory()->create();
        
        $response = $this->post('/test-runs', [
            'scenario_id' => $scenario->id,
            'persona_id' => 999,
            'model_key' => 'openai_gpt4o',
        ]);

        $response->assertSessionHasErrors(['persona_id']);
    }

    public function test_store_validates_model_key(): void
    {
        // try posting with a model key that isn't allowed
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create();
        
        $response = $this->post('/test-runs', [
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
            'model_key' => 'invalid_model',
        ]);

        $response->assertSessionHasErrors(['model_key']);
    }

    public function test_store_validates_temperature_range(): void
    {
        // try posting with a temperature that's too high
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create();
        
        $response = $this->post('/test-runs', [
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
            'model_key' => 'openai_gpt4o',
            'temperature' => 3.0, // way too high
        ]);

        $response->assertSessionHasErrors(['temperature']);
    }

    public function test_status_returns_test_run_status_without_response(): void
    {
        // make a test run with no response yet
        $testRun = TestRun::factory()->create(['status' => 'queued']);

        $response = $this->get("/test-runs/{$testRun->id}/status");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'queued',
            'response' => null,
            'tldr' => null,
            'scores' => null,
        ]);
    }

    public function test_status_returns_response_with_tldr_extraction(): void
    {
        // make a test run and a response with a tldr
        $testRun = TestRun::factory()->create(['status' => 'completed']);
        $llmResponse = LlmResponse::factory()->create([
            'test_run_id' => $testRun->id,
            'response_raw' => json_encode([
                'choices' => [
                    [
                        'message' => [
                            'content' => "this is a long response with analysis.\n\ntldr: this is the summary"
                        ]
                    ]
                ]
            ]),
            'scores' => ['fairness_score' => 0.8],
        ]);

        $response = $this->get("/test-runs/{$testRun->id}/status");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'completed',
            'response' => "this is a long response with analysis.\n\ntldr: this is the summary",
            'tldr' => null,
            'scores' => ['fairness_score' => 0.8],
        ]);
    }

    public function test_status_returns_response_without_tldr(): void
    {
        // make a test run and a response with no tldr
        $testRun = TestRun::factory()->create(['status' => 'completed']);
        $llmResponse = LlmResponse::factory()->create([
            'test_run_id' => $testRun->id,
            'response_raw' => json_encode([
                'choices' => [
                    [
                        'message' => [
                            'content' => "this is a response without tldr"
                        ]
                    ]
                ]
            ]),
        ]);

        $response = $this->get("/test-runs/{$testRun->id}/status");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'completed',
            'response' => 'this is a response without tldr',
            'tldr' => null,
        ]);
    }

    public function test_status_returns_404_for_nonexistent_test_run(): void
    {
        // try to get a test run that doesn't exist
        $response = $this->get('/test-runs/999/status');

        $response->assertStatus(404);
    }
} 