<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Llm\Jobs\RunPromptJob;
use App\Models\TestRun;
use Illuminate\Support\Facades\Log;

// Create a test run
$testRun = TestRun::create([
    'scenario_id' => 1,
    'persona_id' => 1,
    'status' => 'queued',
    'started_by' => null,
    'started_at' => now(),
]);

echo "Created TestRun ID: " . $testRun->id . "\n";

// Dispatch the job
$job = new RunPromptJob(
    testRun: $testRun,
    modelKey: 'openai_gpt4o',
    temperature: 0.7,
);

echo "About to dispatch job...\n";
dispatch($job)->onQueue('llm');
echo "Job dispatched!\n";

// Check if job is in the database
$jobCount = \DB::table('jobs')->count();
echo "Jobs in database: " . $jobCount . "\n"; 