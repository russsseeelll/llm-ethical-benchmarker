<?php

namespace Tests\Feature\Jobs;

use App\Events\LlmResponseCreated;
use App\Jobs\BiasScoringJob;
use App\Llm\Jobs\RunPromptJob;
use App\Llm\Providers\OpenRouterProvider;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use App\Models\LlmResponse;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class RunPromptJobTest extends TestCase
{
    use DatabaseMigrations;
    public function test_job_processes_test_run_successfully(): void
    {
        Event::fake();
        Queue::fake();
        
        $persona = Persona::factory()->create([
            'name' => 'Test Persona',
            'prompt_template' => 'You are a helpful assistant.',
        ]);
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario',
            'description' => 'A test scenario',
            'persona_id' => $persona->id,
        ]);
        $testRun = TestRun::factory()->create([
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
            'status' => 'queued',
        ]);

        $mockProvider = Mockery::mock(OpenRouterProvider::class);
        $mockProvider->shouldReceive('send')
            ->once()
            ->andReturn([
                'content' => 'This is a test response',
                'latency_ms' => 1000,
                'usage_tokens' => 150,
                'cost_usd' => 0.001,
                'raw_response' => [
                    'choices' => [
                        [
                            'message' => [
                                'content' => 'This is a test response'
                            ]
                        ]
                    ]
                ],
            ]);

        $this->app->instance(OpenRouterProvider::class, $mockProvider);

        $job = new RunPromptJob($testRun, 'openai_gpt4o', 0.7);
        $job->handle($mockProvider);

        $testRun->refresh();
        $this->assertEquals('completed', $testRun->status);
        $this->assertNotNull($testRun->completed_at);

        $llmResponse = $testRun->llmResponses()->first();
        $this->assertNotNull($llmResponse);
        $this->assertEquals('openrouter', $llmResponse->provider);
        $this->assertEquals(0.7, $llmResponse->temperature);
        $this->assertEquals(1000, $llmResponse->latency_ms);
        $this->assertEquals(0.001, $llmResponse->cost_usd);

        Event::assertDispatched(LlmResponseCreated::class, function ($event) use ($testRun) {
            return $event->testRunId === $testRun->id
                && $event->modelKey === 'openai_gpt4o'
                && $event->content === 'This is a test response'
                && $event->latencyMs === 1000;
        });

        Queue::assertPushedOn('scoring', BiasScoringJob::class);
    }

    public function test_job_handles_llm_provider_failure(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create(['persona_id' => $persona->id]);
        $testRun = TestRun::factory()->create([
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
            'status' => 'queued',
        ]);

        $mockProvider = Mockery::mock(OpenRouterProvider::class);
        $mockProvider->shouldReceive('send')
            ->once()
            ->andThrow(new \RuntimeException('LLM provider failed'));

        $this->app->instance(OpenRouterProvider::class, $mockProvider);

        $job = new RunPromptJob($testRun, 'openai_gpt4o', 0.7);
        $job->handle($mockProvider);

        $testRun->refresh();
        $this->assertEquals('failed', $testRun->status);
        $this->assertNull($testRun->completed_at);
    }

    public function test_job_has_correct_tags(): void
    {
        $testRun = TestRun::factory()->create();
        $job = new RunPromptJob($testRun, 'openai_gpt4o', 0.7);

        $tags = $job->tags();

        $this->assertContains("test-run:{$testRun->id}", $tags);
        $this->assertContains('model:openai_gpt4o', $tags);
    }

    public function test_job_uses_correct_queue(): void
    {
        $testRun = TestRun::factory()->create();
        $job = new RunPromptJob($testRun, 'openai_gpt4o', 0.7);

        $this->assertEquals('llm', $job->queue);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 