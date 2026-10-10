<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryDetailsResource extends JsonResource
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
            'phone_code' => $this->phone_code,
            'status' => $this->status,
            'region_id' => $this->region_id,
            'region_name' => ($this->region) ? $this->region->name : '',
            'language_id' => $this->language_id,
            'language_name' => ($this->language) ? $this->language->name : '',
            'currency_id' => $this->currency_id,
            'currency_name' => ($this->currency) ? $this->currency->name : '',
            'children_key' => 'cities',
            'cities' => CityResource::collection($this->cities),

            'region' => new RegionResource2($this->region),

            'images' => $this->imagesFullData,
            'main_image' => $this->get_main_image(),
            'fields' => FieldResource::collection($this->fields),
            'faqs' => FaqResource::collection($this->faqs),
            
            'testimonies' => TestimonyResource::collection($this->testimonies),
            'related_countries' => $this->related_countries,
            'featured_golf_courses' => BaseDataResource::collection($this->featuredGolfCourses),
            'featured_hotels' => BaseDataResource::collection($this->featuredHotels),
            'featured_cities' => BaseDataResource::collection($this->featuredCities),
            'featured_products' => BaseDataResource::collection($this->featuredProducts),
            'featured_hotel_products' => BaseDataResource::collection($this->featuredHotelProducts),
            'featured_golf_holidays' => BaseDataResource::collection($this->featuredGolfHolidays),

            'golf_courses_number' => $this->get_golf_courses_number(),
            'hotels_number' => $this->get_hotels_number(),
            'products_number' => $this->get_products_number(),
            'hotel_products_number' => $this->get_hotel_products_number(),
            'golf_holidays_number' => $this->get_golf_holidays_number(),
            'show_website' => $this->show_website,
            'top' => $this->top,

            'published_at' => $this->published_at,
            'updated_at' => $this->updated_at,
            'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
            'is_publish_required' => $this->isPublishRequired(),
            "publish" => $this->publishColumn()

        ];
    }
}
