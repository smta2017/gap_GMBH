<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'comment' => $this->comment,
            'user' => ($this->user) ? $this->user->details->first_name . ' ' . $this->user->details->last_name : '',
            'user_company_type_id' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->user) ? (($this->user->details->company) ? $this->user->details->company->type->name : '') : '',
            'createt_at' => $this->created_at
        ];
    }
}
