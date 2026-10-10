<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TravelAgencyDetailsResource extends JsonResource
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
            'has_gfp_request' => $this->has_gfp_request,
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',
            'active' => $this->active,

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
            'created_by_user_id' => $this->created_by,
            'created_by_user_name' => ($this->createdbyuser) ? $this->createdbyuser->details->first_name . ' ' . $this->createdbyuser->details->last_name: '',
            'created_at' => $this->created_at,

            "street" => $this->street,
            "postal_code" => $this->postal_code,
            "phone" => $this->phone,
            "fax" => $this->fax,
            "email" => $this->email,
            "website" => $this->website,

            'notes' => NoteResource::collection($this->notes),

            'travel_types' => BaseDataResource::collection($this->traveltypes),

            'touroperators' => BaseDataResource::collection($this->touroperators),
            'golfcourses' => BaseDataResource::collection($this->golfcourses),

            'cities' => BaseDataResource::collection($this->cities),
            
            'hotels' => BaseDataResource::collection($this->hotels),
            'company' => new CompanyResource($this->company),
            'region' => new RegionResource2($this->region),
            'country' => new CountryResource2($this->country),
            'city' => new CityResource2($this->city),
            'area' => new AreaResource($this->area),
        ];
    }
}
