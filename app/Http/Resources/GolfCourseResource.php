<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GolfCourseResource extends JsonResource
{

    public static $wrap = '';
    
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
            'email' => $this->email,
            'ref_id' => $this->ref_id,
            'tui_ref_code' => $this->tui_ref_code,
            'giata_code' => $this->giata_code,
            'letter_code' => $this->letter_code,
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',
            'hotel_id' => $this->hotel_id,
            'hotel_name' => ($this->hotel) ? $this->hotel->name : '',
            'golf_course_style_id' => $this->golf_course_style_id,
            'golfcourse_style_name' => ($this->style) ? $this->style->name:'',
            'active' => $this->active,
            'show_website' => $this->show_website,
            'holes' => $this->holes,
            'direct_contract' => $this->direct_contract,
            'via_dmc' => $this->via_dmc,
            'via_hotel' => $this->via_hotel,
            'is_company_address' => $this->is_company_address,
            'delegate_name' => $this->delegate_name,
            'delegate_user_id' => $this->delegate_user_id,
            'delegate_user_name' => ($this->delegateuser) ? $this->delegateuser->details->first_name . ' ' . $this->delegateuser->details->last_name: '',
            'assigned_user_id' => $this->assigned_user_id,
            'assigned_user_name' => ($this->assignuser) ? $this->assignuser->details->first_name . ' ' . $this->assignuser->details->last_name: '',
            'region_id' => $this->region_id,
            'region_name' => ($this->region) ? $this->region->name : '',
            'country_id' => $this->country_id,
            'country_name' => ($this->country) ? $this->country->name : '',
            'city_id' => $this->city_id,
            'city_name' => ($this->city) ? $this->city->name : '',
            'area_id' => $this->area_id,
            'area_name' => ($this->area) ? $this->area->name : '',
            'city_letter_code' => ($this->city) ? $this->city->code : '',

            'region' => new RegionResource2($this->region),
            'country' => new CountryResource2($this->country),
            'city' => new CityResource2($this->city),
            'area' => new AreaResource($this->area),
            
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "booking_code" => $this->booking_code,
            "top" => $this->top,
            "davinci_booking_code" => $this->davinci_booking_code,

            'main_image' => $this->get_main_image(),
            
            'created_by_user_id' => $this->created_by,
            'created_by_user_name' => ($this->createdbyuser) ? $this->createdbyuser->details->first_name . ' ' . $this->createdbyuser->details->last_name: '',
            'created_at' => $this->created_at,
            'published_at' => $this->published_at,
            'is_publish_required' => $this->isPublishRequired(),
            
            'translations' => ($this->translations) ? GolfCourseTranslationResource::collection($this->translations) : [],
            
            "publish" => $this->publishColumn()
            
        ];
    }
}
