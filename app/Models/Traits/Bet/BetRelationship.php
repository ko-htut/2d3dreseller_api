<?php

namespace App\Models\Traits\Bet;

use App\Models\Customer;
use App\Models\Number;
use App\Models\Register;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BetRelationship
{
    /**
     * @return BelongsToMany
     */
    public function numbers()
    {
        return $this->belongsToMany(
            Number::class,
            'bet_numbers',
            'bet_id',
            'number_id'
        )->withPivot(['type', 'amount', 'id']);
    }

    /**
     * @return BelongsTo
     */
    public function register()
    {
        return $this->belongsTo(Register::class);
    }
}
