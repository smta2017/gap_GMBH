<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'color' => $this->color,

            'start_recur' => $this->start_recur,
            'end_recur' => $this->end_recur,
            
            'duration' => $this->duration,
            'days_of_week' => ($this->days_of_week) ? explode(',', $this->days_of_week) : [],
            'is_recurring' => $this->is_recurring,

            'type_id' => $this->type_id,
            'type_name' => ($this->type) ? $this->type->name : '',
        ];
    }
}
