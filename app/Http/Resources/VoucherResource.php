<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Number;

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
            'numbers' => NumberResource::collection(Number::where('voucher_id', $this->id)->get()),
            'total' => get_total_sale_amount($this->id, false, false, false),
        ];
    }
}