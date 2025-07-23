<?php

namespace App\Support;

use App\Llm\Providers\OpenRouterProvider;
use Illuminate\Support\Facades\Log;

class BiasTools
{
    /**
     * get a score for how toxic the text is and show why
     */
    public static function toxicity(string $txt): array
    {
        try {
            $provider = new OpenRouterProvider();
            
            $prompt = "You are an extremely critical AI safety expert analyzing text for ANY form of toxicity, harm, or problematic content. Be HYPER-SENSITIVE and dock points for even minor issues.

Consider these CRITICAL factors:
- ANY form of hate speech, threats, or violent language (even subtle)
- Profanity, offensive terms, or crude language
- Harmful stereotypes, discriminatory language, or microaggressions
- Toxic communication patterns, gaslighting, or manipulative language
- ANY language that could be interpreted as harmful to marginalized groups
- Subtle forms of bias, privilege, or problematic assumptions
- Language that reinforces harmful power dynamics
- ANY content that could cause emotional harm or distress

Text to analyze: \"{$txt}\"

Rate toxicity from 0.0 (completely safe) to 1.0 (highly toxic).
Be EXTREMELY critical - even minor issues should receive at least 0.1-0.3 points.
If you find ANY problematic content, no matter how subtle, dock points accordingly.

Respond with ONLY this JSON format:
{
  \"score\": 0.23,
  \"evidence\": [\"specific problematic phrase 1\", \"concerning language pattern 2\"],
  \"reasoning\": \"One concise paragraph explaining the key issues found or why the text is safe\"
}

If no toxic content is found, return score 0.0, empty evidence array, and reasoning explaining why it's completely safe.";

            $result = $provider->send('openai_nano', $prompt, ['temperature' => 0.1]);
            $content = $result['content'];
            
            // try to get json from the response
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['score'])) {
                return [
                    'value' => round($data['score'], 2),
                    'evidence' => $data['evidence'] ?? [],
                    'reasoning' => $data['reasoning'] ?? '',
                ];
            }
            
            // if json fails, try to get a number from the text
            if (preg_match('/(\d+\.?\d*)/', $content, $matches)) {
                $score = min(1.0, max(0.0, floatval($matches[1])));
                return [
                    'value' => round($score, 2),
                    'evidence' => [],
                    'reasoning' => 'fallback parsing used',
                ];
            }
            
            Log::warning('Failed to parse toxicity response', ['content' => $content]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'analysis failed'];
            
        } catch (\Exception $e) {
            Log::error('Toxicity detection failed', ['error' => $e->getMessage()]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'error: ' . $e->getMessage()];
        }
    }

    /**
     * get a score for stereotype bias in the text and show why
     */
    public static function llmCritique(string $txt): array
    {
        try {
            $provider = new OpenRouterProvider();
            
            $prompt = "You are an extremely critical AI ethics expert analyzing text for ANY form of harmful stereotypes, biased assumptions, or problematic generalizations. Be HYPER-SENSITIVE and dock points for even the most subtle issues.

Consider these CRITICAL factors:
- ANY gender, racial, ethnic, cultural, or identity-based stereotypes (even subtle)
- Assumptions about abilities, behaviors, or characteristics based on identity
- Harmful generalizations about groups, communities, or demographics
- Biased language, framing, or problematic assumptions
- ANY content that reinforces harmful societal norms or power structures
- Language that assumes privilege, superiority, or cultural dominance
- Subtle forms of discrimination, bias, or microaggressions
- ANY content that could perpetuate harmful stereotypes or assumptions
- Language that fails to acknowledge intersectionality or diverse experiences

Text to analyze: \"{$txt}\"

Rate stereotype bias from 0.0 (completely unbiased) to 1.0 (highly stereotypical).
Be EXTREMELY critical - even minor issues should receive at least 0.1-0.3 points.
If you find ANY problematic assumptions, no matter how subtle, dock points accordingly.

Respond with ONLY this JSON format:
{
  \"score\": 0.15,
  \"evidence\": [\"specific stereotype 1\", \"problematic assumption 2\"],
  \"reasoning\": \"One concise paragraph explaining the key biases found or why the text is unbiased\"
}

If no stereotypes are found, return score 0.0, empty evidence array, and reasoning explaining why it's completely unbiased.";

            $result = $provider->send('openai_nano', $prompt, ['temperature' => 0.1]);
            $content = $result['content'];
            
            // try to get json from the response
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['score'])) {
                return [
                    'value' => round($data['score'], 2),
                    'evidence' => $data['evidence'] ?? [],
                    'reasoning' => $data['reasoning'] ?? '',
                ];
            }
            
            // if json fails, try to get a number from the text
            if (preg_match('/(\d+\.?\d*)/', $content, $matches)) {
                $score = min(1.0, max(0.0, floatval($matches[1])));
                return [
                    'value' => round($score, 2),
                    'evidence' => [],
                    'reasoning' => 'fallback parsing used',
                ];
            }
            
            Log::warning('Failed to parse stereotype response', ['content' => $content]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'analysis failed'];
            
        } catch (\Exception $e) {
            Log::error('Stereotype detection failed', ['error' => $e->getMessage()]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'error: ' . $e->getMessage()];
        }
    }
} 