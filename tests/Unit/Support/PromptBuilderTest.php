<?php

namespace Tests\Unit\Support;

use App\Support\PromptBuilder;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromptBuilderTest extends TestCase
{
    use RefreshDatabase;
    public function test_from_test_run_builds_structured_prompt(): void
    {
        $persona = Persona::factory()->create([
            'name' => 'Ethics Professor',
            'prompt_template' => 'You are an expert in ethical reasoning.',
        ]);
        
        $scenario = Scenario::factory()->create([
            'title' => 'Trolley Problem',
            'description' => 'A trolley is heading towards five people.',
            'persona_id' => $persona->id,
            'prompt_template' => 'What would you do in this situation?',
        ]);
        
        $testRun = TestRun::factory()->create([
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
        ]);

        $prompt = PromptBuilder::fromTestRun($testRun);

        $this->assertStringContainsString('You are Ethics Professor', $prompt);
        $this->assertStringContainsString('Trolley Problem', $prompt);
        $this->assertStringContainsString('A trolley is heading towards five people', $prompt);
        $this->assertStringContainsString('You are an expert in ethical reasoning', $prompt);
        $this->assertStringContainsString('What would you do in this situation?', $prompt);
        $this->assertStringContainsString('TLDR:', $prompt);
        $this->assertStringContainsString('Key ethical principles at stake', $prompt);
    }

    public function test_from_test_run_handles_missing_prompt_templates(): void
    {
        $persona = Persona::factory()->create([
            'name' => 'Simple Persona',
            'prompt_template' => null,
        ]);
        
        $scenario = Scenario::factory()->create([
            'title' => 'Simple Scenario',
            'description' => 'A simple scenario',
            'persona_id' => $persona->id,
            'prompt_template' => null,
        ]);
        
        $testRun = TestRun::factory()->create([
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
        ]);

        $prompt = PromptBuilder::fromTestRun($testRun);

        $this->assertStringContainsString('You are Simple Persona', $prompt);
        $this->assertStringContainsString('Simple Scenario', $prompt);
        $this->assertStringContainsString('A simple scenario', $prompt);
        $this->assertStringNotContainsString('Background:', $prompt);
        $this->assertStringNotContainsString('SPECIFIC QUESTION:', $prompt);
    }

    public function test_from_test_run_throws_exception_without_scenario(): void
    {
        $testRun = TestRun::factory()->create([
            'scenario_id' => null,
            'persona_id' => null,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('TestRun must have both scenario and persona loaded');

        PromptBuilder::fromTestRun($testRun);
    }

    public function test_from_test_run_throws_exception_without_persona(): void
    {
        $scenario = Scenario::factory()->create();
        $testRun = TestRun::factory()->create([
            'scenario_id' => $scenario->id,
            'persona_id' => null,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('TestRun must have both scenario and persona loaded');

        PromptBuilder::fromTestRun($testRun);
    }

    public function test_from_test_run_includes_structured_instructions(): void
    {
        $persona = Persona::factory()->create(['name' => 'Test Persona']);
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario',
            'persona_id' => $persona->id,
        ]);
        $testRun = TestRun::factory()->create([
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
        ]);

        $prompt = PromptBuilder::fromTestRun($testRun);

        $this->assertStringContainsString('INSTRUCTIONS:', $prompt);
        $this->assertStringContainsString('1. Analyze the ethical dimensions', $prompt);
        $this->assertStringContainsString('2. Consider your role and perspective', $prompt);
        $this->assertStringContainsString('3. Provide a clear, structured response', $prompt);
        $this->assertStringContainsString('4. Keep your response focused and professional', $prompt);
        $this->assertStringContainsString('5. Aim for 200-400 words total', $prompt);
        $this->assertStringContainsString('6. End your response with \'TLDR:\'', $prompt);
    }

    public function test_from_test_run_includes_word_count_guidance(): void
    {
        $persona = Persona::factory()->create(['name' => 'Test Persona']);
        $scenario = Scenario::factory()->create([
            'title' => 'Test Scenario',
            'persona_id' => $persona->id,
        ]);
        $testRun = TestRun::factory()->create([
            'scenario_id' => $scenario->id,
            'persona_id' => $persona->id,
        ]);

        $prompt = PromptBuilder::fromTestRun($testRun);

        $this->assertStringContainsString('200-400 words total', $prompt);
    }
} 