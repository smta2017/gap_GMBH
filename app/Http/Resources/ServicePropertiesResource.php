<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServicePropertiesResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->descriptio,
    
            'view_type' => $this->view_type,
            'options' => $this->options,
        ];
    }
}
