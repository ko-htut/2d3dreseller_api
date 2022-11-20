<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\VoucherItem;

class VoucherResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'voucher_code' => $this->voucher_code,
            'note' => $this->note,
            'opened_at' => $this->opened_at,
            'closed_at' => $this->closed_at,
            'numbers' => VoucherItem::where('voucher_id', $this->id)->select('number', 'amount')->get(),
            'total' => get_total_sale_amount($this->id, false, false, false),
        ];
    }
}
