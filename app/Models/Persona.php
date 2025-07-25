<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    /** @use HasFactory<\Database\Factories\PersonaFactory> */
    // this lets us use the factory for this model
    use HasFactory;

    protected $fillable = [
        'name',
        'prompt_template',
        'metadata',
        'md5_hash',
    ];

    public function scenarios()
    {
        // this gets all scenarios for this persona
        return $this->hasMany(Scenario::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($persona) {
            // don't let us delete if there are scenarios for this persona
            if ($persona->scenarios()->count() > 0) {
                throw new \Exception('cannot delete persona with attributed scenarios.');
            }
        });
    }
}
