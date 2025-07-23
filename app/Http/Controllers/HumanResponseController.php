<?php

namespace App\Http\Controllers;

use App\Models\HumanResponse;
use Illuminate\Http\Request;

class HumanResponseController extends Controller
{
    // show the form to answer the questionnaire
    public function create()
    {
        return view('human_questionnaire');
    }

    // save a new human response
    public function store(Request $request)
    {
        $data = $request->validate([
            'scenario_id' => 'required|exists:scenarios,id',
            'response' => 'required|string',
            'consent' => 'accepted',
        ]);
        $data['consent_timestamp'] = now();
        HumanResponse::create($data);
        return redirect('/')->with('success', 'Thank you for your response!');
    }
} 