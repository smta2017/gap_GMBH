<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'api_key' => $this->api_key,
            'status' => $this->status,
            'expiry_date' => $this->expiry_date,
            'created_at' => $this->created_at,
    
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',
        ];
    }
}
