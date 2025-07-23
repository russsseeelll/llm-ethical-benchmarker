<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    /** @use HasFactory<\Database\Factories\PersonaFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'prompt_template',
        'metadata',
        'md5_hash',
    ];

    // get all scenarios for this persona
    public function scenarios()
    {
        return $this->hasMany(Scenario::class);
    }

    // stop delete if persona has scenarios
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($persona) {
            if ($persona->scenarios()->count() > 0) {
                throw new \Exception('cannot delete persona with scenarios.');
            }
        });
    }
}
