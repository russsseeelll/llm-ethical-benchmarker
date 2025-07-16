<?php

namespace Tests\Unit\Support;

use App\Support\CostEstimator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostEstimatorTest extends TestCase
{
    use RefreshDatabase;
    public function test_estimate_calculates_cost_for_known_model(): void
    {
        $cost = CostEstimator::estimate('openai_gpt4o', 1000);

        $this->assertEquals(0.00001, $cost);
    }

    public function test_estimate_calculates_cost_for_claude_sonnet(): void
    {
        $cost = CostEstimator::estimate('claude_sonnet', 2000);

        $this->assertEquals(0.000018, $cost);
    }

    public function test_estimate_calculates_cost_for_deepseek_fp8(): void
    {
        $cost = CostEstimator::estimate('deepseek_fp8', 5000);

        $this->assertEquals(0.00001, $cost);
    }

    public function test_estimate_uses_default_rate_for_unknown_model(): void
    {
        $cost = CostEstimator::estimate('unknown_model', 1000);

        $this->assertEquals(0.00001, $cost);
    }

    public function test_estimate_rounds_to_six_decimal_places(): void
    {
        $cost = CostEstimator::estimate('openai_gpt4o', 1234);

        $this->assertEquals(0.000012, $cost);
    }

    public function test_estimate_handles_zero_tokens(): void
    {
        $cost = CostEstimator::estimate('openai_gpt4o', 0);

        $this->assertEquals(0.0, $cost);
    }

    public function test_estimate_handles_large_token_counts(): void
    {
        $cost = CostEstimator::estimate('openai_gpt4o', 100000);

        $this->assertEquals(0.001, $cost);
    }

    public function test_rates_constant_contains_expected_models(): void
    {
        $rates = CostEstimator::RATES;

        $this->assertArrayHasKey('openai_gpt4o', $rates);
        $this->assertArrayHasKey('claude_sonnet', $rates);
        $this->assertArrayHasKey('deepseek_fp8', $rates);
        $this->assertEquals(0.00001, $rates['openai_gpt4o']);
        $this->assertEquals(0.000009, $rates['claude_sonnet']);
        $this->assertEquals(0.000002, $rates['deepseek_fp8']);
    }
} 