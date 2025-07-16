<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personas = \App\Models\Persona::orderBy('created_at', 'desc')->paginate(10);
        return view('welcome', compact('personas'));
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
     * Display the specified resource.
     */
    public function show($id)
    {
        return redirect('/');
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $persona = \App\Models\Persona::findOrFail($id);
        try {
            $persona->delete();
            return redirect('/')->with('success', 'Persona deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('personas.index')->with('error', $e->getMessage());
        }
    }
}
