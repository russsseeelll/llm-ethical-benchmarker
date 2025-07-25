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

    /**
     * Extract clean text content from response_raw JSON
     */
    public function parsed_content(): string
    {
        $raw = json_decode($this->response_raw, true);
        
        if (is_array($raw) && isset($raw['choices'][0]['message']['content'])) {
            return $raw['choices'][0]['message']['content'];
        }
        
        // Fallback: if it's already a string, return as is
        if (is_string($raw)) {
            return $raw;
        }
        
        // Last resort: return empty string
        return '';
    }
}
