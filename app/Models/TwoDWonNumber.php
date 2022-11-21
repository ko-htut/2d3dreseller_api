<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoDWonNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'set',
        'val',
        'date',
        'time_type',
        'created_at',
        'updated_at'
    ];
}
