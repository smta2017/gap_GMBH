<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HandlerResource extends JsonResource
{

    public static $wrap = '';
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
            'name' => $this->name,
            
            'company_id' => $this->id,
            'company_name' => $this->name,

            'company_type_id' => $this->company_type_id,
            'company_type_name' => ($this->type) ? $this->type->name : '',

            // 'request_tee_times' => RequestTeeTimeResource::collection($this->teeTimes())
        ];
    }
}
