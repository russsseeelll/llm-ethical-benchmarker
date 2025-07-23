<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HumanResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'response',
        'consent',
    ];
}
