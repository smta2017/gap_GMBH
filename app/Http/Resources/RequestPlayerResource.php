<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestPlayerResource extends JsonResource
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

            'request_id' => $this->request_id,

            'client_id' => $this->client_id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'gender' => $this->gender,
            'groups' => $this->groups,
            'hcp' => $this->hcp,

            'additional_information' => $this->additional_information,
            
            'is_client' => $this->is_client,
            'is_leader' => $this->is_leader,

            'leader_type_id' => $this->leader_type_id,
            'leader_type_name' => ($this->leaderType) ? $this->leaderType->name : '',

            'leader_company_id' => $this->leader_company_id,
            'leader_company_name' => ($this->leaderCompany) ? $this->leaderCompany->name : '',

            "destinations" => RequestDestinationDataResource::collection($this->destinations)
        ];
    }
}
