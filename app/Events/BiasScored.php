<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class BiasScored implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public int   $testRunId,
        public string $modelKey,
        public array  $scores
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("test-run.{$this->testRunId}");
    }

    public function broadcastAs(): string
    {
        return 'llm.bias';
    }

    public function broadcastWith(): array
    {
        return [
            'model_key' => $this->modelKey,
            'scores'    => $this->scores,
        ];
    }
}
