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
        'revision',
        'md5_hash',
    ];

    public function scenarios()
    {
        return $this->hasMany(Scenario::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($persona) {
            if ($persona->scenarios()->count() > 0) {
                throw new \Exception('Cannot delete persona with attributed scenarios.');
            }
        });
    }
}
