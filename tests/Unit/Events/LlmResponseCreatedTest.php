<?php

namespace Tests\Unit\Events;

use App\Events\LlmResponseCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LlmResponseCreatedTest extends TestCase
{
    use RefreshDatabase;
    public function test_event_has_correct_properties(): void
    {
        $event = new LlmResponseCreated(
            testRunId: 123,
            modelKey: 'openai_gpt4o',
            content: 'This is a test response',
            latencyMs: 1000
        );

        $this->assertEquals(123, $event->testRunId);
        $this->assertEquals('openai_gpt4o', $event->modelKey);
        $this->assertEquals('This is a test response', $event->content);
        $this->assertEquals(1000, $event->latencyMs);
    }

    public function test_event_broadcasts_on_correct_channel(): void
    {
        $event = new LlmResponseCreated(123, 'openai_gpt4o', 'content', 1000);

        $channel = $event->broadcastOn();

        $this->assertEquals('test-run.123', $channel->name);
    }

    public function test_event_has_correct_broadcast_name(): void
    {
        $event = new LlmResponseCreated(123, 'openai_gpt4o', 'content', 1000);

        $this->assertEquals('llm.response', $event->broadcastAs());
    }

    public function test_event_broadcasts_correct_data(): void
    {
        $event = new LlmResponseCreated(123, 'openai_gpt4o', 'This is a test response', 1000);

        $broadcastData = $event->broadcastWith();

        $this->assertEquals([
            'model_key' => 'openai_gpt4o',
            'content' => 'This is a test response',
            'latency_ms' => 1000,
        ], $broadcastData);
    }

    public function test_event_implements_should_broadcast_now(): void
    {
        $event = new LlmResponseCreated(123, 'openai_gpt4o', 'content', 1000);

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcastNow::class, $event);
    }
} 