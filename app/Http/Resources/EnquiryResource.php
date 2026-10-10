<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EnquiryResource extends JsonResource
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
            'integration_source_name' => ($this->integration) ? $this->integration->name : (($this->company) ? $this->company->name:''),
            'source_id' => $this->source_id,
            'source' => ($this->source) ? $this->source->name:'',

            'medium_id' => $this->medium_id,
            'medium' => ($this->medium) ? $this->medium->name:'',

            'compaign' => $this->compaign,

            'target' => $this->target,
            'airport_name' => $this->airport_name,
            
            'arrival_date' => $this->arrival_date,
            'enquiry_date' => $this->created_at->format('Y-m-d'),

            'status_id' => $this->status_id,
            'status' => ($this->status) ? $this->status->name : '',

            'group_number' => $this->group_number,
            'number_of_nights' => $this->number_of_nights,
            'number_of_rounds' => $this->number_of_rounds,
            
            'flight' => $this->flight,
            'receive_offer' => $this->receive_offer,
            
            'is_schedule_datetime' => $this->is_schedule_datetime,
            'schedule_datetime' => ($this->schedule_datetime) ? Carbon::parse($this->schedule_datetime) : $this->schedule_datetime,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile_number' => $this->mobile_number,
            'email' => $this->email,
    
            'city_id' => $this->city_id,
            'city_name' => ($this->city) ? $this->city->name:'',

            'comments' => EnquiryCommentResource::collection($this->comments),
        ];
    }
}
