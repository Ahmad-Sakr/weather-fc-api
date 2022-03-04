<?php

namespace App\Http\Resources\v1;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
        ];
    }
}
