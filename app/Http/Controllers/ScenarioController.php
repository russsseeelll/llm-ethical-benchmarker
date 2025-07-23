<?php

namespace App\Http\Controllers;

use App\Models\Scenario;
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    // show all scenarios
    public function index()
    {
        $scenarios = \App\Models\Scenario::orderBy('created_at', 'desc')->paginate(3);
        return view('welcome', compact('scenarios'));
    }

    // show the form to make a new scenario
    public function create()
    {
        return redirect('/');
    }

    // save a new scenario
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'persona_id' => 'required|exists:personas,id',
            'description' => 'nullable|string',
            'prompt_template' => 'nullable|string',
            'is_multiple_choice' => 'nullable|boolean',
            'choices' => 'nullable|string',
            'revision' => 'nullable|integer',
            'real_life_outcome' => 'nullable|string',
        ]);
        $data['slug'] = str_replace(' ', '', $data['title']);
        if (!empty($data['choices']) && is_string($data['choices'])) {
            json_decode($data['choices']); // check if json is valid
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['choices' => 'Invalid JSON'])->withInput();
            }
        }
        $data['md5_hash'] = md5($data['title'] . ($data['prompt_template'] ?? ''));
        \App\Models\Scenario::create($data);
        return redirect('/')->with('success', 'Scenario created successfully.');
    }

    // show a single scenario
    public function show($slug)
    {
        $scenario = \App\Models\Scenario::where('slug', $slug)->with('persona')->firstOrFail();
        $allPersonas = \App\Models\Persona::all();
        $scenarios = collect([$scenario]);
        $personas = $allPersonas;
        $showConsentModal = false;
        return view('scenario', compact('scenario', 'allPersonas', 'scenarios', 'personas', 'showConsentModal'));
    }

    // show the form to edit a scenario
    public function edit($id)
    {
        return redirect('/');
    }

    // update a scenario
    public function update(Request $request, $id)
    {
        $scenario = \App\Models\Scenario::where('slug', $id)->orWhere('id', $id)->firstOrFail();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'persona_id' => 'required|exists:personas,id',
            'description' => 'nullable|string',
            'prompt_template' => 'nullable|string',
            'is_multiple_choice' => 'nullable|boolean',
            'choices' => 'nullable|string',
            'revision' => 'nullable|integer',
            'real_life_outcome' => 'nullable|string',
        ]);
        $data['slug'] = str_replace(' ', '', $data['title']);
        if (!empty($data['choices']) && is_string($data['choices'])) {
            json_decode($data['choices']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['choices' => 'Invalid JSON'])->withInput();
            }
        }
        $data['md5_hash'] = md5($data['title'] . ($data['prompt_template'] ?? ''));
        $scenario->update($data);
        // check if the edit was made from the scenario page or welcome page
        $referer = $request->header('HTTP_REFERER');
        if ($referer && str_contains($referer, '/scenario/')) {
            return redirect()->route('scenario.show', $data['slug'])->with('success', 'Scenario updated successfully.');
        }
        return redirect('/')->with('success', 'Scenario updated successfully.');
    }

    // delete a scenario
    public function destroy($id)
    {
        $scenario = \App\Models\Scenario::where('slug', $id)->orWhere('id', $id)->firstOrFail();
        $scenario->delete();
        return redirect('/')->with('success', 'Scenario deleted successfully.');
    }
}
