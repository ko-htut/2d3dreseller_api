<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
            'id' => $this->id,
            'note' => $this->note,
            'opened_at' => $this->opened_at,
            'closed_at' => $this->closed_at,
            'number' => new NumberResource($this->number),
            'total' => get_total_sale_amount($this->id, false, false, false),
        ];
    }
}
