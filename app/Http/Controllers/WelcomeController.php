<?php

namespace App\Http\Controllers;

use App\Models\Scenario;
use App\Models\Persona;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $scenarios = \App\Models\Scenario::orderBy('created_at', 'desc')->paginate(3, ['*'], 'scenarios_page');
        $personas = \App\Models\Persona::orderBy('created_at', 'desc')->paginate(3, ['*'], 'personas_page');
        $allPersonas = \App\Models\Persona::orderBy('name')->get();
        $showConsentModal = !$request->session()->get('gdpr_consented', false);
        return view('welcome', compact('scenarios', 'personas', 'allPersonas', 'showConsentModal'));
    }
} 