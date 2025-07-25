<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HumanResponse extends Model
{
    /** @use HasFactory<\Database\Factories\HumanResponseFactory> */
    // this lets us use the factory for this model
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'response',
        'consent',
    ];
}
