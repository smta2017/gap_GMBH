<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResourceTable extends JsonResource
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
            // 'phone_code' => $this->phone_code,
            'status' => $this->status,
            // 'region_id' => $this->region_id,
            // 'region_name' => ($this->region) ? $this->region->name : '',
            // 'language_id' => $this->language_id,
            // 'language_name' => ($this->language) ? $this->language->name : '',
            // 'currency_id' => $this->currency_id,
            // 'currency_name' => ($this->currency) ? $this->currency->name : '',
            // 'main_image' => $this->get_main_image(),
            // 'show_website' => $this->show_website,
            
            'region' => new RegionResource2($this->region),

            'published_at' => $this->published_at,
            'top' => $this->top,
            // 'updated_at' => $this->updated_at,
            // 'is_publish_required' => $this->isPublishRequired(),

            // 'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
            
            'children_key' => 'cities',
            "publish" => $this->publishColumn()

        ];
    }
}
