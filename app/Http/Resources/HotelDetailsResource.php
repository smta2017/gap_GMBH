<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelDetailsResource extends JsonResource
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
            'ref_id' => $this->ref_id,
            'tui_ref_code' => $this->tui_ref_code,
            'giata_code' => $this->giata_code,
            'letter_code' => $this->letter_code,
            'number_of_rooms' => $this->number_of_rooms,
            'hotel_rating' => $this->hotel_rating,
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',
            'active' => $this->active,
            'show_website' => $this->show_website,
            'direct_contract' => $this->direct_contract,
            'via_dmc' => $this->via_dmc,
            'davinici_codes' => DaviniciCodeResource::collection($this->DaviniciCodes),

            'is_company_address' => $this->is_company_address,

            'is_golf_globe' => $this->is_golf_globe,
            
            'main_image' => $this->get_main_image(),
            
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
            'created_by_user_id' => $this->created_by,
            'created_by_user_name' => ($this->createdbyuser) ? $this->createdbyuser->details->first_name . ' ' . $this->createdbyuser->details->last_name: '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'published_at' => $this->published_at,
            'is_publish_required' => $this->isPublishRequired(),
            
            'translations' => ($this->translations) ? HotelTranslationResource::collection($this->translations) : [],

            "street" => $this->street,
            "postal_code" => $this->postal_code,
            "location_link" => $this->location_link,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "phone" => $this->phone,
            "fax" => $this->fax,
            "email" => $this->email,

            "website_description" => $this->website_description,
            "internal_description" => $this->internal_description,
            
            "handler_type_id" => $this->handler_type_id,
            "handler_type_name" => ($this->handlertype) ? $this->handlertype->name : '',
            "handler_id" => $this->handler_id,
            "handler_name" => ($this->handler) ? $this->handler->name : '',

            "payee" => $this->payee,
            "is_payee_only" => $this->is_payee_only,
            "payee_key_created" => $this->payee_key_created,
            "bank" => $this->bank,
            "bank_location" => $this->bank_location,
            "account_number" => $this->account_number,
            "swift_code" => $this->swift_code,
            "iban" => $this->iban,

            "leader_offer" => $this->leader_offer,
            "leader_offer_number" => $this->leader_offer_number,
            "leader_offer_notes" => $this->leader_offer_notes,

            "pro_leader_offer" => $this->pro_leader_offer,
            "pro_leader_offer_number" => $this->pro_leader_offer_number,
            "pro_leader_offer_notes" => $this->pro_leader_offer_notes,

            "junior" => $this->junior,
            "junior_ratio" => $this->junior_ratio,
            "junior_notes" => $this->junior_notes,

            "travel_agent" => $this->travel_agent,
            "travel_agent_ratio" => $this->travel_agent_ratio,
            "travel_agent_notes" => $this->travel_agent_notes,

            "president" => $this->president,
            "president_ratio" => $this->president_ratio,
            "president_notes" => $this->president_notes,

            "pro" => $this->pro,
            "pro_ratio" => $this->pro_ratio,
            "pro_notes" => $this->pro_notes,

            "reservation_email" => $this->reservation_email,
            "booking_accounting_id" => $this->booking_accounting_id,
            "booking_accounting_name" => ($this->bookingaccounting) ? $this->bookingaccounting->name : '',
            'top' => $this->top,
    
            // "has_golf_course" => $this->has_golf_course,
            // "golf_desk" => $this->golf_desk,
            // "golf_shuttle" => $this->golf_shuttle,
            // "storage_room" => $this->storage_room,

            "booking_code" => $this->booking_code,
            "davinci_booking_code" => $this->davinci_booking_code,
            "notes" => $this->notes,
            'area_id' => $this->area_id,
            'area_name' => ($this->area) ? $this->area->name : '',
            'facilities' => FacilityResource::collection($this->facilities),
            'services' => GolfCourseServiceResource::collection($this->services),
            'boards' => BaseDataResource::collection($this->boards),
            // 'notes' => NoteResource::collection($this->notes),
            'related_hotels' => BasePublishDataResource::collection($this->relatedHotels),
            'tags' =>  TagResource::collection($this->tags),
            'fields' => FieldResource::collection($this->fields),
            'images' => $this->imagesFullDataURLEncode(),
            'links_hd_images' => $this->linksHDImages,
            'links_logo_images' => $this->linksLogoImages,
            'golfcourses' => BasePublishDataResource::collection($this->ownedgolfcourses),
            'related_golfcourses' => BaseDataResource::collection($this->relatedgolfcourses),
            'touroperators' => BaseDataResource::collection($this->touroperators),
            'rooms' => RoomResource::collection($this->rooms),
            'company' => new CompanyResource($this->company),
            'region' => new RegionResource2($this->region),
            'country' => new CountryResource2($this->country),
            'city' => new CityResource2($this->city),
            'area' => new AreaResource($this->area),
            'website'=> $this->website,
            "publish" => $this->publishColumn()

        ];
    }
}
