<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceFeeDetailsResource extends JsonResource
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
            'service_id' => $this->service_id,
            'service_name' => ($this->service) ? $this->service->name : '',
            'addon_id' => $this->addon_id,
            'addon_name' => ($this->addon) ? $this->addon->name : '',
    
            'unit_type' => $this->unit_type,
            'unit_options' => $this->unit_options,
        ];
    }
}
