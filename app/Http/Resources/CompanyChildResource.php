<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyChildResource extends JsonResource
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
            
            "booking_code" => $this->booking_code,
            "davinci_booking_code" => $this->davinci_booking_code,
            
            'company_id' => $this->company_id,
            'company_type_id' => ($this->company) ? $this->company->company_type_id : '',

            'region_id' => $this->region_id,
            'region_name' => ($this->region) ? $this->region->name : '',
            'country_id' => $this->country_id,
            'country_name' => ($this->country) ? $this->country->name : '',
            'country_code' => ($this->country) ? $this->country->code : '',
            'country_phone_code' => ($this->country) ? $this->country->phone_code : '',
            'city_id' => $this->city_id,
            'city_name' => ($this->city) ? $this->city->name : '',

            'region' => new RegionResource2($this->region),
            'country' => new CountryResource2($this->country),
            'city' => new CityResource2($this->city),
            'area' => new AreaResource($this->area),
        ];
    }
}
