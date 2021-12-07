<?php

namespace App\Models;

use App\Models\Traits\Register\RegisterRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    use HasFactory, RegisterRelationship;

    protected $fillable = [
        'note', 'user_id', 'opened_at', 'closed_at', 'number_id', 'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
