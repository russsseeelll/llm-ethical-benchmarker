<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    /** @use HasFactory<\Database\Factories\ScenarioFactory> */
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

    // get the persona for this scenario
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // make a slug if missing
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($scenario) {
            if (empty($scenario->slug) && !empty($scenario->title)) {
                $scenario->slug = str_replace(' ', '', $scenario->title);
            }
        });
    }
}
