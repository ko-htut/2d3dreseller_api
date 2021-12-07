<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetNumber extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }

    /**
     * @return BelongsTo
     */
    public function number()
    {
        return $this->belongsTo(Number::class);
    }
}
