<?php

namespace App\Support;

class CostEstimator
{
    public const RATES = [
        'openai_gpt4o'  => 0.00001,  
        'claude_sonnet' => 0.000009, 
        'deepseek_fp8'  => 0.000002,  
    ];

    public static function estimate(string $modelKey, int $totalTokens): float
    {
        $rate = self::RATES[$modelKey] ?? 0.00001;

        return round(($totalTokens / 1000) * $rate, 6);
    }
}
