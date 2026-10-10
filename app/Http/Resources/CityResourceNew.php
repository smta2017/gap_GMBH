<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResourceNew extends JsonResource
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
            
            'country_id' => $this->country_id,
            // 'country_name' => ($this->country) ? $this->country->name : '',
            // 'region_id' => $this->country->region_id,
            // 'region_name' => ($this->region) ? $this->region->name : '',

            // 'main_image' => $this->get_main_image(),
            // 'show_website' => $this->show_website,
            
            // 'country' => new CountryResource2($this->country),
            // 'region' => new RegionResource2($this->country->region),
            // 'top' => $this->top,
            // 'published_at' => $this->published_at,
            // 'is_publish_required' => $this->isPublishRequired(),
            // 'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
            // 'areas' => AreaResource::collection($this->areas),
            // 'object_number' => $this->get_object_number(),

            // "publish" => $this->publishColumn()

        ];
    }
}
