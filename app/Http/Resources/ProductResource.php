<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProductDetails;
use App\Models\Hotel;
use App\Models\City;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    //test
    public function toArray($request)
    {
        
        return [
            
            'id' => $this->id,
            'name' => $this->name,

            'is_package' => $this->is_package,

            'service_id' => $this->service_id,
            'service_name' => ($this->service) ? $this->service->name : '',

            'service' =>  ($this->service) ?  new ProductServiceResource($this->service):'',
            'service_company_id' => ($this->service) ? $this->service->company_id : '',
            'service_company_name' => ($this->service) ? ($this->service->company) ? $this->service->company->name : '' : '',

            'golf_course_id' => $this->golf_course_id,
            'golf_course_name' => ($this->golfcourse) ? $this->golfcourse->name : '',
    
            'code' => $this->code,
            'ref_code' => $this->ref_code,
            'tui_code' => $this->tui_code,
    
            'tee_time_id' => $this->tee_time_id,
            'tee_time_name' => ($this->teeTime) ? $this->teeTime->name : '',

            'hole_id' => $this->hole_id,
            'hole_name' => ($this->hole) ? $this->hole->name : '',
    
            'validity_from' => $this->validity_from,
            'validity_to' => $this->validity_to,
            
            'junior' => $this->junior,
            'multi_players_only' => $this->multi_players_only,
            'buggy' => $this->buggy,

            'use_service_configurations' => $this->use_service_configurations,
            
            'invoice_handler_id' => $this->invoice_handler_id,
            'invoice_handler_name' => ($this->invoiceHandler) ? $this->invoiceHandler->name : '',
    
            'service_handler_type_id' => $this->service_handler_type_id,
            'service_handler_type_name' => ($this->serviceHandlerType) ? $this->serviceHandlerType->name : '',

            'service_handler_id' => $this->service_handler_id,
            'service_handler_name' => ($this->serviceHandler) ? $this->serviceHandler->name : '',

            'booking_possible_for' => $this->booking_possible_for,
            'booking_from_id' => $this->booking_from_id,

            'additional_information' => $this->additional_information,
            
            'hotel' => $this->hotel_data(),
            'city' => $this->city_data(),
       
            'hotels' => HotelResource::collection($this->hotels),

            'tags' =>  TagResource::collection($this->tags),
            
            'booking_code' =>[
                'booking_code'=>$this->booking_code,
                'type'=> $this->service->companyType->name,
                'name'=> $this->service->company->name,
            ], 


            'status' =>  $this->status,
            'use_destination_hotel' => $this->use_destination_hotel,
            
            'details' => ProductDetailsResource::collection($this->details)
        ];
    }
}
