<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GolfCourseDetailsResource extends JsonResource
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
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',
            'hotel_id' => $this->hotel_id,
            'hotel_name' => ($this->hotel) ? $this->hotel->name : '',
            'ref_id' => $this->ref_id,
            'davinici_codes' => DaviniciCodeResource::collection($this->DaviniciCodes),
            'tui_ref_code' => $this->tui_ref_code,
            'giata_code' => $this->giata_code,
            'letter_code' => $this->letter_code,
            'golf_course_style_id' => $this->golf_course_style_id,
            'golfcourse_style_name' => ($this->style) ? new BaseDataResource($this->style) :'',
            'active' => $this->active,
            'show_website' => $this->show_website,
            'direct_contract' => $this->direct_contract,
            'via_dmc' => $this->via_dmc,
            'via_hotel' => $this->via_hotel,

            'is_company_address' => $this->is_company_address,

            'delegate_name' => $this->delegate_name,
            "delegate_email" => $this->delegate_email,
            "delegate_mobile_number" => $this->delegate_mobile_number,
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
            'city_letter_code' => ($this->city) ? $this->city->code : '',

            'main_image' => $this->get_main_image(),
            
            'created_by_user_id' => $this->created_by,
            'created_by_user_name' => ($this->createdbyuser) ? $this->createdbyuser->details->first_name . ' ' . $this->createdbyuser->details->last_name: '',
            'created_at' => $this->created_at,

            'published_at' => $this->published_at,
            'is_publish_required' => $this->isPublishRequired(),
            
            'translations' => ($this->translations) ? GolfCourseTranslationResource::collection($this->translations) : [],

            "street" => $this->street,
            "postal_code" => $this->postal_code,
            "location_link" => $this->location_link,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "phone" => $this->phone,
            "fax" => $this->fax,
            "email" => $this->email,

            "website_link" => $this->website_link,
            "website_description" => $this->website_description,
            "internal_description" => $this->internal_description,
            "designer" => $this->designer,
            
            "handler_type_id" => $this->handler_type_id,
            "handler_type_name" => ($this->handlertype) ? $this->handlertype->name : '',
            "handler_id" => $this->handler_id,
            "handler_name" => ($this->handler) ? $this->handler->name : '',
            "length_men" => $this->length_men,
            "length_women" => $this->length_women,
            "par_men" => $this->par_men,
            "par_women" => $this->par_women,
            "holes" => $this->holes,
            "course_rating" => $this->course_rating,
            "club_rating" => $this->club_rating,
            "slope_from" => $this->slope_from,
            "slope_to" => $this->slope_to,
            "academy" => $this->academy,
            "pros" => $this->pros,

            "payee" => $this->payee,
            "is_payee_only" => $this->is_payee_only,
            "payee_key_created" => $this->payee_key_created,
            "bank" => $this->bank,
            "bank_location" => $this->bank_location,
            "account_number" => $this->account_number,
            "swift_code" => $this->swift_code,
            "iban" => $this->iban,

            "start_frequency" => $this->start_frequency,
            "start_gift" => $this->start_gift,
            "booking_code" => $this->booking_code,
            "davinci_booking_code" => $this->davinci_booking_code,
            'area_id' => $this->area_id,
            'area_name' => ($this->area) ? $this->area->name : '',
            "membership" => $this->membership,
            "hcp_men" => $this->hcp_men,
            "hcp_women" => $this->hcp_women,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'related_golf_courses' => BasePublishDataResource::collection($this->relatedGolfCourses),
            'facilities' => FacilityResource::collection($this->facilities),
            'services' => GolfCourseServiceResource::collection($this->services),
            'trainings' => GolfCourseServiceResource::collection($this->trainings),
            'notes' => NoteResource::collection($this->notes),
            'fields' => FieldResource::collection($this->fields),
            'difficulties' =>  BaseDataResource::collection($this->difficulties),
            'tags' =>  TagResource::collection($this->tags),
            'terrains' =>  BaseDataResource::collection($this->terrains),
            'playables' =>  BaseDataResource::collection($this->playables),
            'dresses' =>  BaseDataResource::collection($this->dresses),
            'images' => $this->imagesFullDataURLEncode(),
            'links_hd_images' => $this->linksHDImages,
            'links_logo_images' => $this->linksLogoImages,
            'company' => new CompanyResource($this->company),
            'region' => new RegionResource2($this->region),
            'country' => new CountryResource2($this->country),
            'city' => new CityResource2($this->city),
            'area' => new AreaResource($this->area),
            'top' => $this->top,
            "publish" => $this->publishColumn()
        ];
    }
}
