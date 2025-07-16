<?php

namespace Tests\Unit\Support;

use App\Support\BiasTools;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiasToolsTest extends TestCase
{
    use RefreshDatabase;
    public function test_contains_slur_detects_slur_words(): void
    {
        $result = BiasTools::containsSlur('This text contains slurword1 and other content');

        $this->assertTrue($result['value']);
        $this->assertContains('slurword1', $result['evidence']);
    }

    public function test_contains_slur_returns_false_for_clean_text(): void
    {
        $result = BiasTools::containsSlur('This is clean text without any slurs');

        $this->assertFalse($result['value']);
        $this->assertEmpty($result['evidence']);
    }

    public function test_contains_slur_is_case_insensitive(): void
    {
        $result = BiasTools::containsSlur('This text contains SLURWORD1');

        $this->assertTrue($result['value']);
        $this->assertContains('slurword1', $result['evidence']);
    }

    public function test_toxicity_returns_structured_response(): void
    {
        // Mock the OpenRouterProvider to avoid actual API calls
        $mockProvider = \Mockery::mock('App\Llm\Providers\OpenRouterProvider');
        $mockProvider->shouldReceive('send')
            ->once()
            ->andReturn([
                'content' => json_encode([
                    'score' => 0.3,
                    'evidence' => ['problematic phrase'],
                    'reasoning' => 'Found some concerning language'
                ])
            ]);

        $this->app->instance('App\Llm\Providers\OpenRouterProvider', $mockProvider);

        $result = BiasTools::toxicity('This is some text to analyze');

        $this->assertEquals(0.3, $result['value']);
        $this->assertContains('problematic phrase', $result['evidence']);
        $this->assertEquals('Found some concerning language', $result['reasoning']);
    }

    public function test_toxicity_handles_fallback_parsing(): void
    {
        // Mock provider to return non-JSON response
        $mockProvider = \Mockery::mock('App\Llm\Providers\OpenRouterProvider');
        $mockProvider->shouldReceive('send')
            ->once()
            ->andReturn([
                'content' => 'The toxicity score is 0.45 for this text'
            ]);

        $this->app->instance('App\Llm\Providers\OpenRouterProvider', $mockProvider);

        $result = BiasTools::toxicity('This is some text to analyze');

        $this->assertEquals(0.45, $result['value']);
        $this->assertEmpty($result['evidence']);
        $this->assertEquals('Fallback parsing used', $result['reasoning']);
    }

    public function test_toxicity_handles_provider_exception(): void
    {
        // Mock provider to throw exception
        $mockProvider = \Mockery::mock('App\Llm\Providers\OpenRouterProvider');
        $mockProvider->shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('API error'));

        $this->app->instance('App\Llm\Providers\OpenRouterProvider', $mockProvider);

        $result = BiasTools::toxicity('This is some text to analyze');

        $this->assertEquals(0.0, $result['value']);
        $this->assertEmpty($result['evidence']);
        $this->assertStringContainsString('Error: API error', $result['reasoning']);
    }

    public function test_llm_critique_returns_structured_response(): void
    {
        // Mock the OpenRouterProvider
        $mockProvider = \Mockery::mock('App\Llm\Providers\OpenRouterProvider');
        $mockProvider->shouldReceive('send')
            ->once()
            ->andReturn([
                'content' => json_encode([
                    'score' => 0.2,
                    'evidence' => ['stereotype found'],
                    'reasoning' => 'Found some biased assumptions'
                ])
            ]);

        $this->app->instance('App\Llm\Providers\OpenRouterProvider', $mockProvider);

        $result = BiasTools::llmCritique('This is some text to analyze');

        $this->assertEquals(0.2, $result['value']);
        $this->assertContains('stereotype found', $result['evidence']);
        $this->assertEquals('Found some biased assumptions', $result['reasoning']);
    }

    public function test_llm_critique_handles_fallback_parsing(): void
    {
        // Mock provider to return non-JSON response
        $mockProvider = \Mockery::mock('App\Llm\Providers\OpenRouterProvider');
        $mockProvider->shouldReceive('send')
            ->once()
            ->andReturn([
                'content' => 'The stereotype score is 0.15 for this text'
            ]);

        $this->app->instance('App\Llm\Providers\OpenRouterProvider', $mockProvider);

        $result = BiasTools::llmCritique('This is some text to analyze');

        $this->assertEquals(0.15, $result['value']);
        $this->assertEmpty($result['evidence']);
        $this->assertEquals('Fallback parsing used', $result['reasoning']);
    }

    public function test_llm_critique_handles_provider_exception(): void
    {
        // Mock provider to throw exception
        $mockProvider = \Mockery::mock('App\Llm\Providers\OpenRouterProvider');
        $mockProvider->shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('API error'));

        $this->app->instance('App\Llm\Providers\OpenRouterProvider', $mockProvider);

        $result = BiasTools::llmCritique('This is some text to analyze');

        $this->assertEquals(0.0, $result['value']);
        $this->assertEmpty($result['evidence']);
        $this->assertStringContainsString('Error: API error', $result['reasoning']);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
} 