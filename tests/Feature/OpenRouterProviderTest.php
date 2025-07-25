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
        $this->assertArrayHasKey('latency_ms', $result);
        $this->assertArrayHasKey('usage_tokens', $result);
        $this->assertArrayHasKey('cost_usd', $result);
        $this->assertArrayHasKey('raw_response', $result);
    }
}
