<?php

namespace App\Models\Traits\Register;

use App\Models\Bet;
use App\Models\Number;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait RegisterRelationship
{
    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    /**
     * @return BelongsTo
     */
    public function number()
    {
        return $this->belongsTo(Number::class);
    }
}
