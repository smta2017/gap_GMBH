<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class RequestLogsResource extends JsonResource
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
            'log_name' => $this->log_name,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,

            'causer_type' => $this->causer_type,
            'causer_id' => $this->causer_id,

            'user' => ($this->userData()) ? $this->userData()->details->first_name . ' ' . $this->userData()->details->last_name : '',
            'user_company_type_id' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->type->name : '') : '',
            'user_company_id' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->id : '') : '',
            'user_company_name' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->name : '') : '',
            
            'created_at' => $this->created_at,

            'properties' => $this->properties,
        ];
    }

    public function userData()
    {
        $user = User::find($this->causer_id);

        if($user)
        {
            return $user;
        }

        return false;
    }
}
