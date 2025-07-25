<?php

namespace Tests\Unit\Llm\Providers;

use App\Llm\Providers\OpenRouterProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenRouterProviderTest extends TestCase
{
    public function test_send_uses_default_temperature(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'fake response']],
                ],
                'usage' => ['total_tokens' => 10],
            ], 200),
        ]);
        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'prompt');
        $this->assertArrayHasKey('content', $result);
    }

    public function test_send_throws_exception_for_unsupported_model(): void
    {
        $this->expectException(\RuntimeException::class);
        $provider = new OpenRouterProvider();
        $provider->send('not_a_real_model', 'prompt');
    }

    public function test_send_measures_latency(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'fake response']],
                ],
                'usage' => ['total_tokens' => 10],
            ], 200),
        ]);
        $provider = new OpenRouterProvider();
        $result = $provider->send('openai_gpt4o', 'prompt');
        $this->assertArrayHasKey('latency_ms', $result);
    }
} 