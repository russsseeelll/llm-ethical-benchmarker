<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LlmResponse extends Model
{
    /** @use HasFactory<\Database\Factories\LlmResponseFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'provider',
        'model',
        'temperature',
        'prompt',
        'response_raw',
        'latency_ms',
        'cost_usd',
        'scores',
        'test_run_id',
    ];
}
