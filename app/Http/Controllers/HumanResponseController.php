<?php

namespace App\Http\Controllers;

use App\Models\HumanResponse;
use Illuminate\Http\Request;

class HumanResponseController extends Controller
{
    public function create()
    {
        return view('human_questionnaire');
    }

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