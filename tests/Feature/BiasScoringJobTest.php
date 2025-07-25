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

        // make a test run for us to use
        $testRun = TestRun::factory()->create();

        // make a fake llm response with some sample text
        $llmResponse = LlmResponse::factory()->create([
            'test_run_id' => $testRun->id,
            'response_raw' => json_encode([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'this is a sample response that should be analyzed for bias.'
                        ]
                    ]
                ]
            ]),
            'scores' => null,
        ]);

        // run the job right now (not queued)
        BiasScoringJob::dispatchSync($llmResponse);

        // refresh the model so we get the new data
        $llmResponse->refresh();

        // check that scores were actually calculated
        $this->assertNotNull($llmResponse->scores);
        $this->assertArrayHasKey('fairness_score', $llmResponse->scores);
        $this->assertArrayHasKey('details', $llmResponse->scores);

        // make sure the fairness score is between 0 and 1
        $fairnessScore = $llmResponse->scores['fairness_score'];
        $this->assertGreaterThanOrEqual(0, $fairnessScore);
        $this->assertLessThanOrEqual(1, $fairnessScore);

        // check the details structure
        $details = $llmResponse->scores['details'];
        $this->assertArrayHasKey('toxicity', $details);
        $this->assertArrayHasKey('stereotype_conf', $details);

        // make sure the BiasScored event was sent
        Event::assertDispatched(BiasScored::class, function ($event) use ($testRun, $llmResponse) {
            return $event->testRunId === $testRun->id
                && $event->modelKey === $llmResponse->model
                && $event->scores === $llmResponse->scores;
        });
    }

    public function test_bias_scoring_job_handles_empty_response(): void
    {
        Event::fake();

        // make a test run and a response with no content
        $testRun = TestRun::factory()->create();
        $llmResponse = LlmResponse::factory()->create([
            'test_run_id' => $testRun->id,
            'response_raw' => json_encode([]),
            'scores' => null,
        ]);

        BiasScoringJob::dispatchSync($llmResponse);

        $llmResponse->refresh();

        // should still calculate a score even if the content is empty
        $this->assertNotNull($llmResponse->scores);
        $this->assertArrayHasKey('fairness_score', $llmResponse->scores);
        $this->assertGreaterThanOrEqual(0, $llmResponse->scores['fairness_score']);
        $this->assertLessThanOrEqual(1, $llmResponse->scores['fairness_score']);
    }
} 