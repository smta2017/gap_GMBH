<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestDetailsTeeTimeViewResource extends JsonResource
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

            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',

            'travel_agency_id' => $this->travel_agency_id,
            'travel_agency_name' => ($this->travelAgency) ? $this->travelAgency->name : '',
            
            'tour_operator_id' => $this->tour_operator_id,
            'tour_operator_name' => ($this->tourOperator) ? $this->tourOperator->name : '',
            
            'ref_id' => $this->ref_id,
            'tui_ref_code' => $this->tui_ref_code,
            'group_code' => $this->group_code,
            'tui_params' => $this->tui_params,

            'phone' => $this->phone, 
            'fax' => $this->fax,
            'email' => $this->email,
    
            'type_id' => $this->type_id,
            'type_name' => ($this->type) ? $this->type->name : '',

            'status_id' => $this->status_id,
            'status_name' => ($this->status) ? $this->status->name : '',

            'sub_status_id' => $this->sub_status_id,
            'sub_status_name' => ($this->subStatus) ? $this->subStatus->name : '',

            'is_delegate' => $this->is_delegate,
            // 'delegate_client_id' => $this->delegate_client_id,
            'delegate_player_id' => $this->delegate_player_id,
            'delegate_player_token' => $this->get_delegate_player_token(),
            'is_client_submit' => $this->is_client_submit,

            'submit_date' => $this->submit_date,
            
            'notes' => $this->notes,
            
            'created_at' => $this->created_at,
            
            'number_of_destinations' => $this->destinations->count(),
            'number_of_clients' => $this->clients->count(),
            'number_of_players' => $this->players->count(),

            // 'booking_codes' => BookingCodeResource::collection($this->codes),
            
            // "destinations" => RequestDestinationResource::collection($this->destinations),
            // "clients" => RequestClientResource::collection($this->clients),
            // "players" => RequestPlayerResource::collection($this->players),
            // "comments" => RequestCommentResource::collection($this->comments),
            // "documents" => RequestDocumentResource::collection($this->documents),

            // "status_logs" => RequestStatusLogsResource::collection($this->statusLogs()),

            // 'user' => ($this->createdbyuser) ? $this->createdbyuser->details->first_name . ' ' . $this->createdbyuser->details->last_name : '',
            // 'user_company_type_id' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->type->id : '') : '',
            // 'user_company_type_name' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->type->name : '') : '',
            // 'user_company_id' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->id : '') : '',
            // 'user_company_name' => ($this->createdbyuser) ? (($this->createdbyuser->details->company) ? $this->createdbyuser->details->company->name : '') : '',
        ];
    }
}
