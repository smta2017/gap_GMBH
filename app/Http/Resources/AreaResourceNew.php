<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResourceNew extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->translations) {
            $name_en = $this->translations[0]['name'];
            $name_de =$this->translations[1]['name'];
         }
        return [
            'id' => $this->id,
            'name_en' => $name_en,
            'name_de' => $name_de,
            'code' => $this->code,
            'status' => $this->status,
            'city_id' => $this->city->id,
            // 'city_name' => ($this->city) ? $this->city->name : '',
            // 'country_id' => $this->city->country_id,
            // 'country_name' => ($this->city->country) ? $this->city->country->name : '',
            // 'region_id' => $this->city->country->region_id,
            // 'region_name' => ($this->city->country) ? $this->city->country->region->name : '',

            // 'region' => new RegionResource2($this->city->country->region),
            // 'country' => new CountryResource2($this->city->country),
            // 'city' => new CityResource2($this->city),

            // 'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
        ];
    }
}
