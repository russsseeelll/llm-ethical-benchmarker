<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    /** @use HasFactory<\Database\Factories\ScenarioFactory> */
    // this lets us use the factory for this model
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'prompt_template',
        'is_multiple_choice',
        'choices',
        'revision',
        'md5_hash',
        'persona_id',
        'real_life_outcome',
    ];

    public function persona()
    {
        // this gets the persona for this scenario
        return $this->belongsTo(Persona::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($scenario) {
            // if slug is empty, make it from the title (no spaces)
            if (empty($scenario->slug) && !empty($scenario->title)) {
                $scenario->slug = str_replace(' ', '', $scenario->title);
            }
        });
    }
}
