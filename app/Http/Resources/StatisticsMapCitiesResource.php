<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsMapCitiesResource extends JsonResource
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
            'id'=> $this->id,
            'code'=> $this->code,
            "hotels"=> $this->Hotels->count(),
            "golf_courses"=> $this->GolfCourses->count(),
        ];
    }
}
