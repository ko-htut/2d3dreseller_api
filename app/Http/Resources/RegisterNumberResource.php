<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterNumberResource extends JsonResource
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
            'number' => $this->number,
            'types' => collect(config('essentials.types'))->map(function($type) {
                return $this->getTypeTotal($this->number, $type);
            }),
            'total' => current_register_number_total_amount($this->number, false, false) ,
        ];
    }

    private function getTypeTotal($number, $type)
    {
        return [
            'type' => $type,
            'total' => current_register_number_total_amount($number, $type, false) ,
        ];
    }
}
