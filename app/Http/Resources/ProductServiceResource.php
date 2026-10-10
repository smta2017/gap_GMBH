<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductServiceResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        if($this->provider()){
            $booking_code = $this->provider()->booking_code;
        }
        else if($this->company){
            $booking_code = $this->company->booking_code;
        }
        else{
            $booking_code = '';
        }
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
    
            'company_type_id' => $this->company_type_id,
            'company_type_name' => ($this->companyType) ? $this->companyType->name : '',

            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',

            'provider_id' => $this->provider_id,
            'provider_name' => ($this->provider()) ? $this->provider()->name : '',
    
            'country_id' => $this->country_id,
            'country_name' => ($this->country) ? $this->country->name : '',

            'city_id' => $this->city_id,
            'city_name' => ($this->city) ? $this->city->name : '',
    
            'letter_code' => $this->letter_code,
            
            'validity_from' => $this->validity_from,
            'validity_to' => $this->validity_to,

            'code' => $this->code,
            'ref_code' => $this->ref_code,
            'tui_code' => $this->tui_code,
            'booking_code' => $booking_code,
    
            'invoice_handler_id' => $this->invoice_handler_id,
            'invoice_handler_name' => ($this->invoiceHandler) ? $this->invoiceHandler->name : '',
    
            'service_handler_type_id' => $this->service_handler_type_id,
            'service_handler_type_name' => ($this->serviceHandlerType) ? $this->serviceHandlerType->name : '',


            'service_handler_id' => $this->service_handler_id,
            'service_handler_name' => ($this->serviceHandler) ? $this->serviceHandler->name : '',

            'booking_possible_for' => $this->booking_possible_for,
            'booking_from_id' => $this->booking_from_id,
    
            'cities' => CityResource2::collection($this->cities),
            'hotels' => HotelResource::collection($this->hotels),
            
            'country' => new CountryResource2($this->country),
            'active' => $this->active,
            
            'use_destination_hotel' => $this->use_destination_hotel,

        ];
    }
}
