<?php

namespace Tests\Feature;

use App\Llm\Providers\OpenRouterProvider;
use Tests\TestCase;

class OpenRouterLiveTest extends TestCase
{
    /** @test @group live */
    public function it_calls_openrouter_successfully(): void
    {
        $key = env('OPEN_ROUTER_API_KEY');

        if (blank($key) || str_contains($key, 'sk-or-') === false) {
            $this->markTestSkipped('No real OpenRouter key – skipping live test.');
        }

        $provider = app(OpenRouterProvider::class);

        $result = $provider->send(
            'openai_gpt4o',
            'One‑word smoke‑test: respond with "ok"',
            ['temperature' => 0.1]
        );

        $this->assertStringContainsStringIgnoringCase('ok', $result['content']);
    }
}
