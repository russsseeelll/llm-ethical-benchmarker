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

    protected $casts = [
        'scores' => 'array',
    ];

    // get the main text from the response_raw json
    public function parsed_content(): string
    {
        $raw = json_decode($this->response_raw, true);
        
        if (is_array($raw) && isset($raw['choices'][0]['message']['content'])) {
            return $raw['choices'][0]['message']['content'];
        }
        
        // if it's already a string, just return it
        if (is_string($raw)) {
            return $raw;
        }
        
        // if nothing else, return empty string
        return '';
    }
}
