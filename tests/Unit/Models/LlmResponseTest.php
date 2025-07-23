<?php

namespace Tests\Unit\Models;

use App\Models\LlmResponse;
use App\Models\TestRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LlmResponseTest extends TestCase
{
    use RefreshDatabase;
    public function test_llm_response_can_be_created_with_valid_data(): void
    {
        $testRun = TestRun::factory()->create();
        
        $llmResponse = LlmResponse::factory()->create([
            'test_run_id' => $testRun->id,
            'provider' => 'openrouter',
            'model' => 'gpt-4',
            'temperature' => 0.7,
            'prompt' => 'Test prompt',
            'response_raw' => json_encode(['choices' => [['message' => ['content' => 'Test response']]]]),
            'latency_ms' => 1000,
            'cost_usd' => 0.001,
        ]);

        $this->assertDatabaseHas('llm_responses', [
            'id' => $llmResponse->id,
            'test_run_id' => $testRun->id,
            'provider' => 'openrouter',
            'model' => 'gpt-4',
            'temperature' => 0.7,
            'prompt' => 'Test prompt',
            'latency_ms' => 1000,
            'cost_usd' => 0.001,
        ]);
    }

    public function test_llm_response_scores_are_casted_to_array(): void
    {
        $scores = ['fairness_score' => 0.8, 'details' => ['toxicity' => 0.1]];
        $llmResponse = LlmResponse::factory()->create(['scores' => $scores]);

        $this->assertIsArray($llmResponse->scores);
        $this->assertEquals(0.8, $llmResponse->scores['fairness_score']);
    }

    public function test_parsed_content_extracts_content_from_json_response(): void
    {
        $responseRaw = json_encode([
            'choices' => [
                [
                    'message' => [
                        'content' => 'This is the extracted content'
                    ]
                ]
            ]
        ]);

        $llmResponse = LlmResponse::factory()->create(['response_raw' => $responseRaw]);

        $this->assertEquals('This is the extracted content', $llmResponse->parsed_content());
    }

    public function test_parsed_content_returns_empty_string_for_invalid_json(): void
    {
        $llmResponse = LlmResponse::factory()->create([
            'response_raw' => 'invalid json'
        ]);

        $this->assertEquals('', $llmResponse->parsed_content());
    }

    public function test_parsed_content_returns_empty_string_for_missing_content(): void
    {
        $responseRaw = json_encode(['choices' => [['message' => []]]]);
        $llmResponse = LlmResponse::factory()->create(['response_raw' => $responseRaw]);

        $this->assertEquals('', $llmResponse->parsed_content());
    }
} 