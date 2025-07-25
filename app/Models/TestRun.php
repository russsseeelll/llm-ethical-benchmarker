<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRun extends Model
{
    /** @use HasFactory<\Database\Factories\TestRunFactory> */
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
        return $this->belongsTo(Scenario::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function llmResponses()
    {
        return $this->hasMany(LlmResponse::class);
    }
}
