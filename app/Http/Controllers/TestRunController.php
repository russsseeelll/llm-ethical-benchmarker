<?php

namespace App\Http\Controllers;

use App\Llm\Jobs\RunPromptJob;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class TestRunController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'scenario_id' => ['required', 'integer', 'exists:scenarios,id'],
            'persona_id'  => ['required', 'integer', 'exists:personas,id'],
            'model_key'   => ['required', Rule::in(array_keys(config('models')))],
            'temperature' => ['numeric', 'min:0', 'max:2'],
        ]);

        /** @var TestRun $testRun */
        $testRun = TestRun::create([
            'scenario_id'  => $data['scenario_id'],
            'persona_id'   => $data['persona_id'],
            'status'       => 'queued',
            'started_by'   => auth()->id(), // or null for guest
            'started_at'   => now(),
        ]);

        Log::info('TestRun created', [
            'test_run_id' => $testRun->id,
            'scenario_id' => $data['scenario_id'],
            'persona_id' => $data['persona_id'],
            'model_key' => $data['model_key'],
        ]);

        $job = new RunPromptJob(
            testRun:    $testRun,
            modelKey:   $data['model_key'],
            temperature: $data['temperature'] ?? 0.7,
        );

        Log::info('About to dispatch RunPromptJob', [
            'test_run_id' => $testRun->id,
            'queue' => 'llm',
            'queue_connection' => config('queue.default'),
        ]);

        dispatch($job)->onQueue('llm');

        Log::info('RunPromptJob dispatched', [
            'test_run_id' => $testRun->id,
        ]);

        return response()->json(['test_run_id' => $testRun->id], 202);
    }

    public function status(TestRun $testRun)
    {
        $latestResponse = $testRun->llmResponses()->latest()->first();
        
        $response = null;
        if ($latestResponse && $latestResponse->response_raw) {
            $rawData = json_decode($latestResponse->response_raw, true);
            $response = $rawData['choices'][0]['message']['content'] ?? null;
        }
        
        return response()->json([
            'status' => $testRun->status,
            'response' => $response,
            'scores' => $latestResponse ? $latestResponse->scores : null,
        ]);
    }
}
