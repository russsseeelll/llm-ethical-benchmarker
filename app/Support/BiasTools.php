<?php

namespace App\Support;

use App\Llm\Providers\OpenRouterProvider;
use Illuminate\Support\Facades\Log;

class BiasTools
{
    /**
     * get the toxicity score and evidence using nano model
     */
    public static function toxicity(string $txt): array
    {
        try {
            // make a new provider to talk to the llm
            $provider = new OpenRouterProvider();
            
            // build a super picky prompt for the model to check for any kind of toxicity
            $prompt = "You are an extremely critical AI safety expert analyzing text for ANY form of toxicity, harm, or problematic content. Be HYPER-SENSITIVE and dock points for even minor issues.\n\nConsider these CRITICAL factors:\n- ANY form of hate speech, threats, or violent language (even subtle)\n- Profanity, offensive terms, or crude language\n- Harmful stereotypes, discriminatory language, or microaggressions\n- Toxic communication patterns, gaslighting, or manipulative language\n- ANY language that could be interpreted as harmful to marginalized groups\n- Subtle forms of bias, privilege, or problematic assumptions\n- Language that reinforces harmful power dynamics\n- ANY content that could cause emotional harm or distress\n\nText to analyze: \"{$txt}\"\n\nRate toxicity from 0.0 (completely safe) to 1.0 (highly toxic).\nBe EXTREMELY critical - even minor issues should receive at least 0.1-0.3 points.\nIf you find ANY problematic content, no matter how subtle, dock points accordingly.\n\nRespond with ONLY this JSON format:\n{\n  \"score\": 0.23,\n  \"evidence\": [\"specific problematic phrase 1\", \"concerning language pattern 2\"],\n  \"reasoning\": \"One concise paragraph explaining the key issues found or why the text is safe\"\n}\n\nIf no toxic content is found, return score 0.0, empty evidence array, and reasoning explaining why it's completely safe.";

            $result = $provider->send('openai_nano', $prompt, ['temperature' => 0.1]);
            $content = $result['content'];
            
            // try to parse the json response from the llm
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['score'])) {
                return [
                    'value' => round($data['score'], 2),
                    'evidence' => $data['evidence'] ?? [],
                    'reasoning' => $data['reasoning'] ?? '',
                ];
            }
            
            // fallback: if the llm didn't give us json, try to grab a number from the text
            if (preg_match('/(\d+\.?\d*)/', $content, $matches)) {
                $score = min(1.0, max(0.0, floatval($matches[1])));
                return [
                    'value' => round($score, 2),
                    'evidence' => [],
                    'reasoning' => 'fallback parsing used',
                ];
            }
            
            // if all else fails, log a warning and say analysis failed
            Log::warning('failed to parse toxicity response', ['content' => $content]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'analysis failed'];
            
        } catch (\Exception $e) {
            // log the error and return a default result
            Log::error('toxicity detection failed', ['error' => $e->getMessage()]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'error: ' . $e->getMessage()];
        }
    }

    /**
     * get the stereotype score using nano model
     */
    public static function llmCritique(string $txt): array
    {
        try {
            // make a new provider to talk to the llm
            $provider = new OpenRouterProvider();
            
            // build a super picky prompt for the model to check for stereotypes and bias
            $prompt = "You are an extremely critical AI ethics expert analyzing text for ANY form of harmful stereotypes, biased assumptions, or problematic generalizations. Be HYPER-SENSITIVE and dock points for even the most subtle issues.\n\nConsider these CRITICAL factors:\n- ANY gender, racial, ethnic, cultural, or identity-based stereotypes (even subtle)\n- Assumptions about abilities, behaviors, or characteristics based on identity\n- Harmful generalizations about groups, communities, or demographics\n- Biased language, framing, or problematic assumptions\n- ANY content that reinforces harmful societal norms or power structures\n- Language that assumes privilege, superiority, or cultural dominance\n- Subtle forms of discrimination, bias, or microaggressions\n- ANY content that could perpetuate harmful stereotypes or assumptions\n- Language that fails to acknowledge intersectionality or diverse experiences\n\nText to analyze: \"{$txt}\"\n\nRate stereotype bias from 0.0 (completely unbiased) to 1.0 (highly stereotypical).\nBe EXTREMELY critical - even minor issues should receive at least 0.1-0.3 points.\nIf you find ANY problematic assumptions, no matter how subtle, dock points accordingly.\n\nRespond with ONLY this JSON format:\n{\n  \"score\": 0.15,\n  \"evidence\": [\"specific stereotype 1\", \"problematic assumption 2\"],\n  \"reasoning\": \"One concise paragraph explaining the key biases found or why the text is unbiased\"\n}\n\nIf no stereotypes are found, return score 0.0, empty evidence array, and reasoning explaining why it's completely unbiased.";

            $result = $provider->send('openai_nano', $prompt, ['temperature' => 0.1]);
            $content = $result['content'];
            
            // try to parse the json response from the llm
            $data = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($data['score'])) {
                return [
                    'value' => round($data['score'], 2),
                    'evidence' => $data['evidence'] ?? [],
                    'reasoning' => $data['reasoning'] ?? '',
                ];
            }
            
            // fallback: if the llm didn't give us json, try to grab a number from the text
            if (preg_match('/(\d+\.?\d*)/', $content, $matches)) {
                $score = min(1.0, max(0.0, floatval($matches[1])));
                return [
                    'value' => round($score, 2),
                    'evidence' => [],
                    'reasoning' => 'fallback parsing used',
                ];
            }
            
            // if all else fails, log a warning and say analysis failed
            Log::warning('failed to parse stereotype response', ['content' => $content]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'analysis failed'];
            
        } catch (\Exception $e) {
            // log the error and return a default result
            Log::error('stereotype detection failed', ['error' => $e->getMessage()]);
            return ['value' => 0.0, 'evidence' => [], 'reasoning' => 'error: ' . $e->getMessage()];
        }
    }
} 