<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProductDetails;
use App\Models\Hotel;
use App\Models\City;

class GolfHolidayResource extends JsonResource
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

            'service_id' => $this->service_id,
            'service_name' => ($this->service) ? $this->service->name : '',

            'hotel_id' => $this->hotel_id,
            'hotel_name' => ($this->hotel) ? $this->hotel->name : '',
    
            'code' => $this->code,
            'ref_code' => $this->ref_code,
            'tui_code' => $this->tui_code,
    
            'room_type_id' => $this->room_type_id,
            'room_type_name' => ($this->roomType) ? $this->roomType->name : '',
    
            'room_view_id' => $this->room_view_id,
            'room_view_name' => ($this->roomView) ? $this->roomView->name : '',
    
            'room_board_id' => $this->room_board_id,
            'room_board_name' => ($this->roomBoard) ? $this->roomBoard->name : '',

    
            'validity_from' => $this->validity_from,
            'validity_to' => $this->validity_to,
            
            'number_of_nights' => $this->number_of_nights,
            'number_of_guests' => $this->number_of_guests,
            'number_of_rounds' => $this->number_of_rounds,
            'number_of_golf_courses' => $this->number_of_golf_courses,
    
            'unlimited_rounds' => $this->unlimited_rounds,

            'use_service_configurations' => $this->use_service_configurations,
            
            'invoice_handler_id' => $this->invoice_handler_id,
            'invoice_handler_name' => ($this->invoiceHandler) ? $this->invoiceHandler->name : '',
    
            'service_handler_type_id' => $this->service_handler_type_id,
            'service_handler_type_name' => ($this->serviceHandlerType) ? $this->serviceHandlerType->name : '',

            'service_handler_id' => $this->service_handler_id,
            'service_handler_name' => ($this->serviceHandler) ? $this->serviceHandler->name : '',

            'booking_possible_for' => $this->booking_possible_for,
            'booking_from_id' => $this->booking_from_id,

            'hotel' => $this->hotel_data(),
            'city' => $this->city_data(),
            
            'area_id' => $this->area_id,
            'area_name' => ($this->area) ? $this->area->name : '',
            
            'products' =>  ProductResource::collection($this->products),
            'hotels' =>  HotelResource::collection($this->hotels),
            'tags' =>  TagResource::collection($this->tags),
            'status' =>  $this->status,
            'area' => new AreaResource($this->area),
            'use_destination_hotel' => $this->use_destination_hotel,
        ];
    }
}
