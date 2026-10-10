<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class RequestDocumentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => ($this->user) ? $this->user->details->first_name . ' ' . $this->user->details->last_name : '',
            '_date' => Carbon::parse($this->date),
            'file_name' => $this->file_name,
            'file_path' => asset('images/companies') . '/' . $this->file_name,
            'file_type' => $this->file_type
        ];
    }
}
