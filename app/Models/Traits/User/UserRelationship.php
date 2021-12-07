<?php

namespace App\Models\Traits\User;

use App\Models\Register;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait UserRelationship
{
    /**
     * @return HasMany
     */
    public function registers()
    {
        return $this->hasMany(Register::class);
    }
}
