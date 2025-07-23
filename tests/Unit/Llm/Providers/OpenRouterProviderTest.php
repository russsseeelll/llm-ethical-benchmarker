<?php

namespace Tests\Unit\Llm\Providers;

use App\Llm\Providers\OpenRouterProvider;
use App\Support\CostEstimator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenRouterProviderTest extends TestCase
{
    use RefreshDatabase;
    public function test_send_uses_default_temperature(): void
    {
        Http::fake([
            'api.openrouter.ai/*' => Http::response([
                'choices' => [['message' => ['content' => 'Test response']]],
                'usage' => ['total_tokens' => 100]
            ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'Test prompt');

        Http::assertSent(function (Request $request) {
            return $request->data()['temperature'] === 0.7;
        });
    }

    public function test_send_throws_exception_for_unsupported_model(): void
    {
        $provider = new OpenRouterProvider();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported model key: invalid_model');

        $provider->send('invalid_model', 'Test prompt');
    }

    public function test_send_measures_latency(): void
    {
        Http::fake([
            'api.openrouter.ai/*' => Http::response([
                'choices' => [['message' => ['content' => 'Test response']]],
                'usage' => ['total_tokens' => 100]
            ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'Test prompt');

        $this->assertIsInt($result['latency_ms']);
        $this->assertGreaterThanOrEqual(0, $result['latency_ms']);
    }
} 