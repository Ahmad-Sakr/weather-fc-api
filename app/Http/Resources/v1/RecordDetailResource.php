<?php

namespace App\Http\Resources\v1;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordDetailResource extends JsonResource
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
            'hour'          => $this->hour,
            'weather'       => $this->cloud,
            'temp'          => $this->temp,
            'min_temp'      => $this->min_temp,
            'max_temp'      => $this->max_temp,
            'pressure'      => $this->pressure,
            'sea_level'     => $this->sea_level,
            'humidity'      => $this->humidity,
            'wind_speed'    => $this->wind_speed
        ];
    }
}
