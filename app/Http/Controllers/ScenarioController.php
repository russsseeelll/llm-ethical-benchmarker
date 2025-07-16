<?php

namespace App\Http\Controllers;

use App\Models\Scenario;
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scenarios = \App\Models\Scenario::orderBy('created_at', 'desc')->paginate(3);
        return view('welcome', compact('scenarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     */
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
        ]);
        $data['slug'] = str_replace(' ', '', $data['title']);
        if (!empty($data['choices']) && is_string($data['choices'])) {
            json_decode($data['choices']); // Validate JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['choices' => 'Invalid JSON'])->withInput();
            }
        }
        $data['md5_hash'] = md5($data['title'] . ($data['prompt_template'] ?? ''));
        \App\Models\Scenario::create($data);
        return redirect('/')->with('success', 'Scenario created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $scenario = \App\Models\Scenario::where('slug', $slug)->with('persona')->firstOrFail();
        $allPersonas = \App\Models\Persona::all();
        $scenarios = collect([$scenario]);
        $personas = $allPersonas;
        $showConsentModal = false;
        return view('scenario', compact('scenario', 'allPersonas', 'scenarios', 'personas', 'showConsentModal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     */
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
        // Check if the edit was made from the scenario page or welcome page
        $referer = $request->header('HTTP_REFERER');
        if ($referer && str_contains($referer, '/scenario/')) {
            return redirect()->route('scenario.show', $data['slug'])->with('success', 'Scenario updated successfully.');
        }
        return redirect('/')->with('success', 'Scenario updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $scenario = \App\Models\Scenario::where('slug', $id)->orWhere('id', $id)->firstOrFail();
        $scenario->delete();
        return redirect('/')->with('success', 'Scenario deleted successfully.');
    }
}
