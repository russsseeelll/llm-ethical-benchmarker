<?php

namespace App\Http\Controllers;

use App\Models\Scenario;
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    // this controller handles all the stuff for our scenarios (the ethical situations)
    /**
     * show a list of all scenarios
     */
    public function index()
    {
        // get the latest scenarios, 3 per page
        $scenarios = \App\Models\Scenario::orderBy('created_at', 'desc')->paginate(3);
        return view('welcome', compact('scenarios'));
    }

    /**
     * show the form to make a new scenario
     */
    public function create()
    {
        // we don't use a separate create page, just go home
        return redirect('/');
    }

    /**
     * save a new scenario
     */
    public function store(Request $request)
    {
        // validate the input for our scenario
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
        // make a slug from the title (no spaces)
        $data['slug'] = str_replace(' ', '', $data['title']);
        // if choices is set, make sure it's valid json
        if (!empty($data['choices']) && is_string($data['choices'])) {
            json_decode($data['choices']); // validate json
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['choices' => 'invalid json'])->withInput();
            }
        }
        // make a hash for this scenario
        $data['md5_hash'] = md5($data['title'] . ($data['prompt_template'] ?? ''));
        \App\Models\Scenario::create($data);
        return redirect('/')->with('success', 'scenario created successfully.');
    }

    /**
     * show a single scenario
     */
    public function show($slug)
    {
        // get the scenario and all personas for the page
        $scenario = \App\Models\Scenario::where('slug', $slug)->with('persona')->firstOrFail();
        $allPersonas = \App\Models\Persona::all();
        $scenarios = collect([$scenario]);
        $personas = $allPersonas;
        $showConsentModal = false;
        return view('scenario', compact('scenario', 'allPersonas', 'scenarios', 'personas', 'showConsentModal'));
    }

    /**
     * show the form to edit a scenario (not used)
     */
    public function edit($id)
    {
        // we don't use a separate edit page, just go home
        return redirect('/');
    }

    /**
     * update a scenario
     */
    public function update(Request $request, $id)
    {
        // find the scenario by slug or id
        $scenario = \App\Models\Scenario::where('slug', $id)->orWhere('id', $id)->firstOrFail();
        // validate the input
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
        // update the slug
        $data['slug'] = str_replace(' ', '', $data['title']);
        // if choices is set, make sure it's valid json
        if (!empty($data['choices']) && is_string($data['choices'])) {
            json_decode($data['choices']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['choices' => 'invalid json'])->withInput();
            }
        }
        // update the hash
        $data['md5_hash'] = md5($data['title'] . ($data['prompt_template'] ?? ''));
        $scenario->update($data);
        // check if the edit was made from the scenario page or welcome page
        $referer = $request->header('HTTP_REFERER');
        if ($referer && str_contains($referer, '/scenario/')) {
            return redirect()->route('scenario.show', $data['slug'])->with('success', 'scenario updated successfully.');
        }
        return redirect('/')->with('success', 'scenario updated successfully.');
    }

    /**
     * delete a scenario
     */
    public function destroy($id)
    {
        // find the scenario and delete it
        $scenario = \App\Models\Scenario::where('slug', $id)->orWhere('id', $id)->firstOrFail();
        $scenario->delete();
        return redirect('/')->with('success', 'scenario deleted successfully.');
    }
}
