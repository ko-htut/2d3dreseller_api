<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoDResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'set',
        'val',
        'time_type',
        'country',
        'date',
        'serial',
        'created_at',
        'updated_at'
    ];
}
