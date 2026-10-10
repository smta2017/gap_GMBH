<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PriceResource extends JsonResource
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
            'service_id' => $this->service_id,
            'service_name' => ($this->service) ? $this->service->name : '',

            'price_list_id' => $this->price_list_id,
            'price_list_name' => ($this->priceList) ? $this->priceList->name : '',

            'product_id' => $this->product_id,
            'product_name' => ($this->product) ? $this->product->name : '',

            'season_id' => $this->season_id,
            'season_name' => ($this->season) ? $this->season->name : '',

            'price' => $this->price,
        ];
    }
}
