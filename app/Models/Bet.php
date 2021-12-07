<?php

namespace App\Models;

use App\Models\Traits\Bet\BetRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory, BetRelationship;

    protected $fillable = ['ref', 'other', 'total', 'voucher'];

    protected $casts = [
        'other' => 'array'
    ];
}
