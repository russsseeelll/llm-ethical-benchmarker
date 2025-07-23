<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class LlmResponseCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    // set up the event with test run id, model key, content, and latency
    public function __construct(
        public int    $testRunId,
        public string $modelKey,
        public string $content,
        public int    $latencyMs
    ) {}

    // what channel to broadcast on
    public function broadcastOn(): Channel
    {
        return new Channel("test-run.{$this->testRunId}");
    }

    // name of the event
    public function broadcastAs(): string
    {
        return 'llm.response';
    }

    // data to send with the event
    public function broadcastWith(): array
    {
        return [
            'model_key'   => $this->modelKey,
            'content'     => $this->content,
            'latency_ms'  => $this->latencyMs,
        ];
    }
}
