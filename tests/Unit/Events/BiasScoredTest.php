<?php

namespace Tests\Unit\Events;

use App\Events\BiasScored;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiasScoredTest extends TestCase
{
    use RefreshDatabase;
    public function test_event_has_correct_properties(): void
    {
        $scores = ['fairness_score' => 0.8, 'details' => ['toxicity' => 0.1]];
        
        $event = new BiasScored(
            testRunId: 123,
            modelKey: 'openai_gpt4o',
            scores: $scores
        );

        $this->assertEquals(123, $event->testRunId);
        $this->assertEquals('openai_gpt4o', $event->modelKey);
        $this->assertEquals($scores, $event->scores);
    }

    public function test_event_broadcasts_on_correct_channel(): void
    {
        $event = new BiasScored(123, 'openai_gpt4o', []);

        $channel = $event->broadcastOn();

        $this->assertEquals('test-run.123', $channel->name);
    }

    public function test_event_has_correct_broadcast_name(): void
    {
        $event = new BiasScored(123, 'openai_gpt4o', []);

        $this->assertEquals('llm.bias', $event->broadcastAs());
    }

    public function test_event_broadcasts_correct_data(): void
    {
        $scores = ['fairness_score' => 0.8, 'details' => ['toxicity' => 0.1]];
        
        $event = new BiasScored(123, 'openai_gpt4o', $scores);

        $broadcastData = $event->broadcastWith();

        $this->assertEquals([
            'model_key' => 'openai_gpt4o',
            'scores' => $scores,
        ], $broadcastData);
    }

    public function test_event_implements_should_broadcast_now(): void
    {
        $event = new BiasScored(123, 'openai_gpt4o', []);

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcastNow::class, $event);
    }
} 