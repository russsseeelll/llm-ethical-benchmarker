<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    /**
     * show a list of all personas
     */
    public function index()
    {
        $personas = \App\Models\Persona::orderBy('created_at', 'desc')->paginate(10);
        return view('welcome', compact('personas'));
    }

    /**
     * show the form to make a new persona
     */
    public function create()
    {
        return redirect('/');
    }

    /**
     * save a new persona
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prompt_template' => 'nullable|string',
            'metadata' => 'nullable|string',
            'revision' => 'nullable|integer',
        ]);
        if (!empty($data['metadata']) && is_string($data['metadata'])) {
            json_decode($data['metadata']); // Validate JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['metadata' => 'Invalid JSON'])->withInput();
            }
        }
        $data['md5_hash'] = md5($data['name'] . ($data['prompt_template'] ?? ''));
        \App\Models\Persona::create($data);
        return redirect('/')->with('success', 'Persona created successfully.');
    }

    /**
     * show a single persona (not used)
     */
    public function show($id)
    {
        return redirect('/');
    }

    /**
     * show the form to edit a persona (not used)
     */
    public function edit($id)
    {
        return redirect('/');
    }

    /**
     * update a persona
     */
    public function update(Request $request, $id)
    {
        $persona = \App\Models\Persona::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'prompt_template' => 'nullable|string',
            'metadata' => 'nullable|string',
            'revision' => 'nullable|integer',
        ]);
        if (!empty($data['metadata']) && is_string($data['metadata'])) {
            json_decode($data['metadata']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['metadata' => 'Invalid JSON'])->withInput();
            }
        }
        $data['md5_hash'] = md5($data['name'] . ($data['prompt_template'] ?? ''));
        $persona->update($data);
        return redirect('/')->with('success', 'Persona updated successfully.');
    }

    /**
     * delete a persona
     */
    public function destroy($id)
    {
        $persona = \App\Models\Persona::findOrFail($id);
        try {
            $persona->delete();
            return redirect('/')->with('success', 'Persona deleted successfully.');
        } catch (\Exception $e) {
            return redirect('/')->with('error', $e->getMessage());
        }
    }
}
