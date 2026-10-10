<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{

    public static $wrap = '';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'service_id' => $this->service_id,
            'service_name' => ($this->service) ? $this->service->name : '',
            'service' => new ProductServiceResource($this->service),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'color' => $this->color,
            'display' => $this->display,
            'peak_time_from' => $this->peak_time_from,
            'peak_time_to' => $this->peak_time_to,
        ];
    }
}
