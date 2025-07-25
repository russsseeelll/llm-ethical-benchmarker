<?php

namespace App\Support;

class CostEstimator
{
    public const RATES = [
        'openai_gpt4o'  => 0.00001,  // this is our rate for openai gpt4o per token
        'claude_sonnet' => 0.000009, // this is our rate for claude sonnet per token
        'deepseek_fp8'  => 0.000002, // this is our rate for deepseek fp8 per token
    ];

    public static function estimate(string $modelKey, int $totalTokens): float
    {
        // grab the rate for the model, or use a default if we don't know it
        $rate = self::RATES[$modelKey] ?? 0.00001;

        // calculate the cost for our token count
        return round(($totalTokens / 1000) * $rate, 6);
    }
}
