<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRun extends Model
{
    /** @use HasFactory<\Database\Factories\TestRunFactory> */
    // this lets us use the factory for this model
    use HasFactory;

    protected $fillable = [
        'persona_id',
        'scenario_id',
        'started_by',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function scenario()
    {
        // this gets the scenario for this test run
        return $this->belongsTo(Scenario::class);
    }

    public function persona()
    {
        // this gets the persona for this test run
        return $this->belongsTo(Persona::class);
    }

    public function llmResponses()
    {
        // this gets all llm responses for this test run
        return $this->hasMany(LlmResponse::class);
    }
}
