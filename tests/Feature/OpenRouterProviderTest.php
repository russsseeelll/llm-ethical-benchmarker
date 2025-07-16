<?php

namespace Tests\Feature;

use App\Llm\Providers\OpenRouterProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenRouterProviderTest extends TestCase
{
    public function test_it_returns_clean_response_structure(): void
    {
        Http::fake([
            'openrouter.ai/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Hello world!']],
                ],
                'usage' => ['total_tokens' => 42],
            ], 200),
        ]);

        $provider = app(OpenRouterProvider::class);

        $result = $provider->send('openai_gpt4o', 'Say hello', ['temperature' => 0.2]);

        $this->assertSame('Hello world!', $result['content']);
        $this->assertEquals(42, $result['usage_tokens']);
        $this->assertGreaterThanOrEqual(0, $result['latency_ms']);
        $this->assertGreaterThanOrEqual(0, $result['cost_usd']);
    }
}
