<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnquiryCommentResource extends JsonResource
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
            'enquiry_id' => $this->commentable_id,
            'user' => ($this->user) ? $this->user->details->first_name . ' ' . $this->user->details->last_name : '',
            'createt_at' => $this->created_at
        ];
    }
}
