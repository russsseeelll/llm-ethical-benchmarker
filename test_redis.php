<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Queue;

echo "Testing Redis connection...\n";

try {
    Redis::ping();
    echo "✓ Redis connection successful\n";
} catch (Exception $e) {
    echo "✗ Redis connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Testing queue connection...\n";
echo "Queue connection: " . config('queue.default') . "\n";
echo "Redis client: " . config('database.redis.client') . "\n";

// Test pushing to queue
try {
    Queue::push(function() {
        echo "Test job executed!\n";
    }, [], 'llm');
    echo "✓ Job pushed to queue successfully\n";
} catch (Exception $e) {
    echo "✗ Failed to push job: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if job is in Redis
$keys = Redis::keys('*queue*');
echo "Queue keys in Redis: " . count($keys) . "\n";
foreach ($keys as $key) {
    echo "  - $key\n";
} 