<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryHistory extends Model
{
    use HasFactory;

    protected $casts = [
        'open_at' => 'date',
    ];

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(LotteryLedger::class, 'lottery_ledger_id', 'id');
    }
}
