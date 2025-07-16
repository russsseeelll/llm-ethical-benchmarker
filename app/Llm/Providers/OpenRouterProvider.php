<?php

namespace App\Llm\Providers;

use App\Support\CostEstimator;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterProvider
{
    public function send(string $modelKey, string $prompt, array $options = []): array
    {
        $modelString = config("models.$modelKey");

        if (! $modelString) {
            throw new RuntimeException("Unsupported model key: $modelKey");
        }

        $temperature = $options['temperature'] ?? 0.7;

        $payload = [
            'model'     => $modelString,
            'messages'  => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => $temperature,
        ];

        $start = microtime(true);

        /** @var Response $response */
        $response = Http::withToken(env('OPEN_ROUTER_API_KEY'))
            ->withHeaders(config('openrouter.headers'))
            ->timeout(config('openrouter.timeout'))
            ->post(config('openrouter.base_url') . '/chat/completions', $payload);

        $latencyMs = (int) ((microtime(true) - $start) * 1000);

        if ($response->failed()) {
            throw new RuntimeException(
                "OpenRouter call failed: {$response->status()} {$response->body()}"
            );
        }

        $data         = $response->json();
        $content      = $data['choices'][0]['message']['content'] ?? '';
        $totalTokens  = $data['usage']['total_tokens'] ?? 0;
        $costEstimate = CostEstimator::estimate($modelKey, $totalTokens);

        return [
            'content'       => $content,
            'latency_ms'    => $latencyMs,
            'usage_tokens'  => $totalTokens,
            'cost_usd'      => $costEstimate,
            'raw_response'  => $data,
        ];
    }
}
