<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsMapResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "code"=> $this->code,
            "hotels_count"=> $this->hotels_count,
            "golf_courses_count"=> $this->golf_courses_count,
            "cities"=> StatisticsMapCitiesResource::collection($this->cities),
        ];
    }
}
