<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestTeeTimeResource extends JsonResource
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
            'parent_id' => $this->parent_id,

            'request_product_id' => $this->request_product_id,
            'request_destination_id' => ($this->requestProduct) ?   $this->requestProduct->request_destination_id  : '',
            'request_product_handler_name' => ($this->requestProduct) ? ($this->requestProduct->serviceHandler) ?  $this->requestProduct->serviceHandler->name   : ''  : '',
            'request_product_details_id' => $this->request_product_details_id,

            'golf_course_id' => $this->golf_course_id,
            'golf_course_name' => ($this->golfcourse) ? $this->golfcourse->name : '',
            'golf_course_phone' => ($this->golfcourse) ? $this->golfcourse->phone : '',
            'golf_course_address' => ($this->golfcourse) ? $this->golfcourse->street : '',
            'golf_course' => ($this->golfcourse) ? new GolfCourseResource($this->golfcourse) : null,
            
            'golf_course_company_id' => ($this->golfcourse) ? $this->golfcourse->company_id : '',
            'golf_course_company_name' => ($this->golfcourse) ? ($this->golfcourse->company) ? $this->golfcourse->company->name : '' : '',

            "request_player_id" => $this->request_player_id,

            'type' => $this->type,

            'tee_time_id' => $this->tee_time_id,
            'tee_time_name' => ($this->teeTime) ? $this->teeTime->name : '',

            'min_tee_time_id' => $this->min_tee_time_id,
            'min_tee_time_name' => ($this->minTeeTime) ? $this->minTeeTime->name : '',

            'max_tee_time_id' => $this->max_tee_time_id,
            'max_tee_time_name' => ($this->maxTeeTime) ? $this->maxTeeTime->name : '',
            
            'date' => $this->date,

            'created_at' => $this->created_at,
            
            'user' => ($this->createdbyuser) ? $this->createdbyuser->details->first_name . ' ' . $this->createdbyuser->details->last_name : '',
            'user_company_type_id' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->type->name : '') : '',
            'user_company_id' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->id : '') : '',
            'user_company_name' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->name : '') : '',
            
            'time_from' => $this->time_from,
            'time_to' => $this->time_to,
            'pref_time' => $this->pref_time,
            'time_margin' => $this->time_margin,

            'conf_time' => $this->conf_time,

            'status_id' => $this->status_id,
            'status_name' => ($this->status) ? $this->status->name : '',

            "status_logs" => RequestTeeTimeStatusLogsResource::collection($this->statusLogs()),

            'voucher_code' => $this->voucher_code,

            'redirect_count' => $this->get_redirect_count(),
            
            'confirmed_alternative_id' => $this->get_confirmed_alternative(),

            'user' => ($this->user) ? $this->user->details->first_name . ' ' . $this->user->details->last_name : '',
            'user_company_type_id' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->type->name : '') : '',
            'user_company_id' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->id : '') : '',
            'user_company_name' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->name : '') : '',
            
            'request_id' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->id : null,
            'request_created_at' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->created_at : null,
            'request_stauts_id' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->status_id : null,
            'request_stauts_name' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->status->name : null,
            'request_sub_stauts_id' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->sub_status_id : null,
            'request_sub_stauts_name' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->subStatus->name : null,
            
            'request_travel_agency_id' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->travel_agency_id : null,
            'request_travel_agency_name' => ($this->requestProduct->destination->request) ? ($this->requestProduct->destination->request->travelAgency) ? $this->requestProduct->destination->request->travelAgency->name : null : null,
            'travel_agency' => ($this->requestProduct->destination->request) ? new TravelAgencyDetailsResource($this->requestProduct->destination->request->travelAgency) : null,

            'request_tour_operator_id' => ($this->requestProduct->destination->request) ? $this->requestProduct->destination->request->tour_operator_id : null,
            'request_tour_operator_name' => ($this->requestProduct->destination->request) ? ($this->requestProduct->destination->request->tourOperator) ? $this->requestProduct->destination->request->tourOperator->name : null : null,

            // 'alternatives' => RequestAlternativeTeeTimeResource::collection($this->alternatives),
            'alternatives' => RequestAlternativeTeeTimeResource::collection($this->alternativesQuery()),
            
        ];
    }
}
