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
    public function test_send_makes_correct_api_request(): void
    {
        Http::fake([
            'api.openrouter.ai/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Test response'
                        ]
                    ]
                ],
                'usage' => [
                    'total_tokens' => 150
                ]
            ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'Test prompt', ['temperature' => 0.7]);

        Http::assertSent(function (Request $request) {
            return $request->url() === config('openrouter.base_url') . '/chat/completions'
                && $request->method() === 'POST'
                && $request->data()['model'] === config('models.openai_gpt4o')
                && $request->data()['messages'][0]['content'] === 'Test prompt'
                && $request->data()['temperature'] === 0.7;
        });

        $this->assertEquals('Test response', $result['content']);
        $this->assertArrayHasKey('latency_ms', $result);
        $this->assertArrayHasKey('usage_tokens', $result);
        $this->assertArrayHasKey('cost_usd', $result);
        $this->assertArrayHasKey('raw_response', $result);
    }

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

    public function test_send_throws_exception_for_api_failure(): void
    {
        Http::fake([
            'api.openrouter.ai/*' => Http::response('API Error', 500)
        ]);

        $provider = new OpenRouterProvider();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OpenRouter call failed: 500 API Error');

        $provider->send('openai_gpt4o', 'Test prompt');
    }

    public function test_send_calculates_cost_correctly(): void
    {
        Http::fake([
            'api.openrouter.ai/*' => Http::response([
                'choices' => [['message' => ['content' => 'Test response']]],
                'usage' => ['total_tokens' => 1000]
            ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'Test prompt');

        $expectedCost = CostEstimator::estimate('openai_gpt4o', 1000);
        $this->assertEquals($expectedCost, $result['cost_usd']);
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

    public function test_send_handles_missing_content_in_response(): void
    {
        Http::fake([
            'api.openrouter.ai/*' => Http::response([
                'choices' => [['message' => []]],
                'usage' => ['total_tokens' => 100]
            ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'Test prompt');

        $this->assertEquals('', $result['content']);
    }

    public function test_send_handles_missing_usage_in_response(): void
    {
        Http::fake([
            'api.openrouter.ai/*' => Http::response([
                'choices' => [['message' => ['content' => 'Test response']]]
            ], 200)
        ]);

        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'Test prompt');

        $this->assertEquals(0, $result['usage_tokens']);
        $this->assertEquals(0.0, $result['cost_usd']);
    }
} 