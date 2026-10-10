<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'seen' => $this->seen,
            'created_at' => $this->created_at,

            'user' => ($this->user) ? $this->user->details->first_name . ' ' . $this->user->details->last_name : '',
            'user_company_type_id' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->type->name : '') : '',
            'user_company_id' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->id : '') : '',
            'user_company_name' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->name : '') : '',
        ];
    }
}
