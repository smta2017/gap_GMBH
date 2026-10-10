<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestDestinationResource extends JsonResource
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
            'request_id' => $this->request_id,

            'city_id' => $this->city_id,
            'city_name' => ($this->city) ? $this->city->name : '',
            'city_letter_code' => ($this->city) ? $this->city->code : '',

            'city' => new CityResource2($this->city),
            
            'hotel_id' => $this->hotel_id,
            'hotel_name' => ($this->hotel) ? $this->hotel->name : '',

            'arrival_date' => $this->arrival_date,
            'departure_date' => $this->departure_date,

            // "products" => RequestProductResource::collection($this->products),
            "products" => RequestProductResource::collection($this->productsQuery()),
        ];
    }
}
