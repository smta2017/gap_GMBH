<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PriceListResource extends JsonResource
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
            'service_id' => $this->service_id,
            'service_name' => ($this->service) ? $this->service->name : '',

            'price_list_type_id' => $this->price_list_type_id,
            'price_list_type_name' => ($this->priceListType) ? $this->priceListType->name : '',

            'populate_list_id' => $this->populate_list_id,
            'populate_list_name' => ($this->populateList) ? $this->populateList->name : '',

            'markup' => $this->markup,
            'status' => $this->status,
        ];
    }
}
