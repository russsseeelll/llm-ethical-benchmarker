<?php

namespace App\Http\Controllers;

use App\Jobs\RunPromptJob;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        dispatch(new RunPromptJob(
            testRun:    $testRun,
            modelKey:   $data['model_key'],
            temperature: $data['temperature'] ?? 0.7,
        ))->onQueue('llm');

        return response()->json(['test_run_id' => $testRun->id], 202);
    }
}
