<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HandlerGCResource extends JsonResource
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
            
            'company_id' => $this->Company->id,
            'company_name' => $this->Company->name,

            'company_type_id' => $this->Company->company_type_id,
            'company_type_name' => ($this->Company->type) ? $this->Company->type->name : '',

            // 'request_tee_times' => RequestTeeTimeResource::collection($this->teeTimes())
        ];
    }
}
