<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'hotel_id' => $this->hotel_id,
            'hotel_name' => ($this->hotel) ? $this->hotel->name : '',
            'phone' => $this->phone,
            'fax' => $this->fax,
            'website' => $this->website,
            'email' => $this->email,
            'rank' => $this->rank,
            'contract' => $this->contract,
            'logo' => ($this->logo) ? asset('images/companies') . '/' . $this->logo->file_name : asset('images/companies/default-company-logo.jpg'),

            "booking_code" => $this->booking_code,
            "davinci_booking_code" => $this->davinci_booking_code,
            
            'delegate_name' => $this->delegate_name,
            'delegate_email' => $this->delegate_email,
            'delegate_mobile_number' => $this->delegate_mobile_number,
            'delegate_user_id' => $this->delegate_user_id,
            'delegate_user_name' => ($this->delegateuser) ? $this->delegateuser->details->first_name . ' ' . $this->delegateuser->details->last_name: '',
            'assigned_user_id' => $this->assigned_user_id,
            'assigned_user_name' => ($this->assignuser) ? $this->assignuser->details->first_name . ' ' . $this->assignuser->details->last_name: '',

            'company_type_id' => $this->company_type_id,
            'company_type_name' => ($this->type) ? $this->type->name : '',

            'region_id' => $this->region_id,
            'region_name' => ($this->region) ? $this->region->name : '',
            'country_id' => $this->country_id,
            'country_name' => ($this->country) ? $this->country->name : '',
            'country_code' => ($this->country) ? $this->country->code : '',
            'country_phone_code' => ($this->country) ? $this->country->phone_code : '',
            'city_id' => $this->city_id,
            'city_name' => ($this->city) ? $this->city->name : '',
            'postal_code' => $this->postal_code,
            'street' => $this->street,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_link' => $this->location_link,

            'has_childs' => $this->check_has_childs(),
            'childs_count' => $this->calc_childs_count(),

            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'facebook' => $this->facebook,
            'linkedin' => $this->linkedin,

            'region' => new RegionResource2($this->region),
            'country' => new CountryResource2($this->country),
            'city' => new CityResource2($this->city),
            'area' => new AreaResourceNew($this->area),
            'top' => $this->top,
            'lang'=>$this->lang
        ];
    }
}
