<?php

namespace App\Http\Resources;

use App\Models\LotteryLedger;
use Illuminate\Http\Resources\Json\JsonResource;

class LotteryHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
//            'year' => $this->year,
//            'month' => $this->month,
            'first' => $this->first,
            'first_near' => $this->first_near,
            'second' => $this->second,
            'third' => $this->third,
            'fourth' => $this->fourth,
            'fifth' => $this->fifth,
            'first_three_digit' => $this->first_three_digit,
            'last_three_digit' => $this->last_three_digit,
            'last_two_digit' => $this->last_two_digit,
        ];
    }
}