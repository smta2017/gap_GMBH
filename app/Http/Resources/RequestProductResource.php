<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestProductResource extends JsonResource
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
            'request_destination_id' => $this->request_destination_id,
            'product_id' => $this->product_id,
            
            'name' => $this->name,
            
            'golf_course_id' => $this->golf_course_id,
            'golf_course_name' => ($this->golfcourse) ?  $this->golfcourse->name : '',

            'is_package' => $this->is_package,
            'code' => $this->code,
            'ref_code' => $this->ref_code,
            'tui_code' => $this->tui_ref_code,

            'tee_time_id' => $this->tee_time_id,
            'tee_time_name' => ($this->teeTime) ? $this->teeTime->name : '',

            'hole_id' => $this->hole_id,
            'hole_name' => ($this->hole) ? $this->hole->name : '',

            'junior' => $this->junior,
            'multi_players_only' => $this->multi_players_only,
            'buggy' => $this->buggy,

            'invoice_handler_id' => $this->invoice_handler_id,
            'invoice_handler_name' => ($this->invoiceHandler) ? $this->invoiceHandler->name : '',
    
            'service_handler_type_id' => $this->service_handler_type_id,
            'service_handler_type_name' => ($this->serviceHandlerType) ? $this->serviceHandlerType->name : '',

            // 'service_handler_id' => $this->service_handler_id,
            // 'service_handler_name' => ($this->serviceHandler) ? $this->serviceHandler->name : '',
            'service_handler_id' => ($this->get_service_handler_info()) ? $this->get_service_handler_info()->id : null,
            'service_handler_name' => ($this->get_service_handler_info()) ? $this->get_service_handler_info()->name : null,
            'service_handler_id_db' => $this->service_handler_id,

            'booking_possible_for' => $this->booking_possible_for,
            'booking_from_id' => $this->booking_from_id,

            'additional_information' => $this->additional_information,
    
            'number_of_players' => $this->number_of_players,
            'notes' => $this->notes,

            'configure_players_with_tee_times' => $this->configure_players_with_tee_times,

            'status_id' => $this->status_id,
            'status_name' => ($this->status) ? $this->status->name : '',

            'redirect_count' => $this->get_redirect_count(),
            
            'hotel' => $this->hotel_data(),
            'city' => $this->city_data(),

            'created_at' => $this->created_at,
            
            'user' => ($this->createdbyuser) ? $this->createdbyuser->details->first_name . ' ' . $this->createdbyuser->details->last_name : '',
            'user_company_type_id' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->type->name : '') : '',
            'user_company_id' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->id : '') : '',
            'user_company_name' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->name : '') : '',
            'booking_code' =>[
                'booking_code'=>($this->product) ? $this->product->booking_code : '',
                'type'=> $this->service->companyType->name,
            ], 
            "destination" => new RequestDestinationDataResource($this->destination),

            "status_logs" => RequestProductStatusLogsResource::collection($this->statusLogs()),

            'details' => RequestProductDetailsResource::collection($this->details),
            
            "request_tee_times" => RequestTeeTimeResource::collection($this->requestTeeTimesQuery()->where('parent_id', null)),

        ];
    }
}
