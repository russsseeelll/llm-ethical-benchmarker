<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LlmResponse extends Model
{
    /** @use HasFactory<\Database\Factories\LlmResponseFactory> */
    // this lets us use the factory for this model
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
        'scores' => 'array', // we store scores as an array
    ];

    /**
     * get the text from the response_raw json
     */
    public function parsed_content(): string
    {
        // try to decode the raw response as json
        $raw = json_decode($this->response_raw, true);
        
        // if it's an array and has the content, return it
        if (is_array($raw) && isset($raw['choices'][0]['message']['content'])) {
            return $raw['choices'][0]['message']['content'];
        }
        
        // fallback: if it's already a string, return as is
        if (is_string($raw)) {
            return $raw;
        }
        
        // last resort: return empty string
        return '';
    }
}
