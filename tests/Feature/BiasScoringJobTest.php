<?php

namespace Tests\Feature;

use App\Events\BiasScored;
use App\Jobs\BiasScoringJob;
use App\Models\LlmResponse;
use App\Models\TestRun;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BiasScoringJobTest extends TestCase
{
    use DatabaseMigrations;

    public function test_bias_scoring_job_calculates_fairness_score(): void
    {
        Event::fake();

        // Create a test run
        $testRun = TestRun::factory()->create();

        // Create a fake LLM response with sample text
        $llmResponse = LlmResponse::factory()->create([
            'test_run_id' => $testRun->id,
            'response_raw' => json_encode([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'This is a sample response that should be analyzed for bias.'
                        ]
                    ]
                ]
            ]),
            'scores' => null,
        ]);

        // Dispatch the job synchronously
        BiasScoringJob::dispatchSync($llmResponse);

        // Refresh the model to get updated data
        $llmResponse->refresh();

        // Assert that scores were calculated
        $this->assertNotNull($llmResponse->scores);
        $this->assertArrayHasKey('fairness_score', $llmResponse->scores);
        $this->assertArrayHasKey('details', $llmResponse->scores);

        // Assert fairness score is between 0 and 1
        $fairnessScore = $llmResponse->scores['fairness_score'];
        $this->assertGreaterThanOrEqual(0, $fairnessScore);
        $this->assertLessThanOrEqual(1, $fairnessScore);

        // Assert details structure
        $details = $llmResponse->scores['details'];
        $this->assertArrayHasKey('toxicity', $details);
        $this->assertArrayHasKey('stereotype_conf', $details);

        // Assert BiasScored event was dispatched
        Event::assertDispatched(BiasScored::class, function ($event) use ($testRun, $llmResponse) {
            return $event->testRunId === $testRun->id
                && $event->modelKey === $llmResponse->model
                && $event->scores === $llmResponse->scores;
        });
    }

    public function test_bias_scoring_job_handles_empty_response(): void
    {
        Event::fake();

        $testRun = TestRun::factory()->create();
        $llmResponse = LlmResponse::factory()->create([
            'test_run_id' => $testRun->id,
            'response_raw' => json_encode([]),
            'scores' => null,
        ]);

        BiasScoringJob::dispatchSync($llmResponse);

        $llmResponse->refresh();

        $this->assertNotNull($llmResponse->scores);
        $this->assertArrayHasKey('fairness_score', $llmResponse->scores);
        
        // Should still calculate a score even with empty content
        $this->assertGreaterThanOrEqual(0, $llmResponse->scores['fairness_score']);
        $this->assertLessThanOrEqual(1, $llmResponse->scores['fairness_score']);
    }
} 