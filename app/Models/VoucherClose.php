<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherClose extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

      protected $fillable = [
        'user_id',
        'voucher_code',
        'note',
        'extra_attributes',
        'is_close',
        'date'
    ];
}