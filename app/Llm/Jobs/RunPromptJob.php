<?php

namespace App\Llm\Jobs;

use App\Events\LlmResponseCreated;
use App\Jobs\BiasScoringJob;
use App\Llm\Providers\OpenRouterProvider;
use App\Models\TestRun;
use App\Support\PromptBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RunPromptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public TestRun $testRun,
        public string  $modelKey,          // e.g. "openai_gpt4o"
        public float   $temperature = 0.7,
    ) {
        $this->onQueue('llm');
    }

    public function handle(OpenRouterProvider $provider): void
    {
        Log::info('RunPromptJob::handle started', [
            'test_run_id' => $this->testRun->id,
            'model_key' => $this->modelKey,
            'temperature' => $this->temperature,
        ]);

        // build the prompt using the scenario and persona
        $prompt = PromptBuilder::fromTestRun($this->testRun);

        try {
            $result = $provider->send($this->modelKey, $prompt, [
                'temperature' => $this->temperature,
            ]);
        } catch (RuntimeException $e) {
            Log::error('LLM call failed', [
                'test_run'  => $this->testRun->id,
                'model_key' => $this->modelKey,
                'error'     => $e->getMessage(),
            ]);

            $this->testRun->update(['status' => 'failed']);
            return;
        }

        // save the response
        $llmResponse = $this->testRun->llmResponses()->create([
            'provider'     => 'openrouter',
            'model'        => config("models.{$this->modelKey}"),
            'temperature'  => $this->temperature,
            'prompt'       => $prompt,
            'response_raw' => json_encode($result['raw_response']),
            'latency_ms'   => $result['latency_ms'],
            'cost_usd'     => $result['cost_usd'],
            'scores'       => null,
        ]);

        // send the answer to the ui
        event(new LlmResponseCreated(
            testRunId: $this->testRun->id,
            modelKey:  $this->modelKey,
            content:   $result['content'],
            latencyMs: $result['latency_ms'],
        ));

        // start bias scoring
        BiasScoringJob::dispatch($llmResponse)->onQueue('scoring');

        // mark the test run as done
        $this->testRun->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function tags(): array
    {
        return [
            "test-run:{$this->testRun->id}",
            "model:{$this->modelKey}",
        ];
    }
}
