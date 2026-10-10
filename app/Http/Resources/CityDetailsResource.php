<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityDetailsResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status,
            'country_id' => $this->country_id,
            'country_name' => ($this->country) ? $this->country->name : '',
            'region_id' => $this->country->region_id,
            'region_name' => ($this->region) ? $this->region->name : '',

            'images' => $this->imagesFullData,
            
            'main_image' => $this->get_main_image(),

            'show_website' => $this->show_website,
            
            'published_at' => $this->published_at,
            'is_publish_required' => $this->isPublishRequired(),
            
            'fields' => FieldResource::collection($this->fields),
            'faqs' => FaqResource::collection($this->faqs),
            
            'testimonies' => TestimonyResource::collection($this->testimonies),
            
            'region' => new RegionResource2($this->country->region),
            'country' => new CountryResource2($this->country),
            'related_regions' => $this->related_regions,
            'featured_golf_courses' => BaseDataResource::collection($this->featuredGolfCourses),
            'featured_hotels' => BaseDataResource::collection($this->featuredHotels),
            'featured_products' => BaseDataResource::collection($this->featuredProducts),
            'featured_hotel_products' => BaseDataResource::collection($this->featuredHotelProducts),
            'featured_golf_holidays' => BaseDataResource::collection($this->featuredGolfHolidays),

            'golf_courses_number' => $this->get_golf_courses_number(),
            'hotels_number' => $this->get_hotels_number(),
            'products_number' => $this->get_products_number(),
            'hotel_products_number' => $this->get_hotel_products_number(),
            'golf_holidays_number' => $this->get_golf_holidays_number(),
            'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
            
            'top' => $this->top,
            'areas' => AreaResource::collection($this->areas),
            'object_number' => $this->get_object_number(),
            "publish" => $this->publishColumn()

        ];
    }
}
