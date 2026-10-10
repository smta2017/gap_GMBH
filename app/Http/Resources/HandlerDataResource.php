<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HandlerDataResource extends JsonResource
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
            
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',

            'company_type_id' => ($this->company) ? $this->company->type->id : '',
            'company_type_name' => ($this->company) ? $this->company->type->name : '',

            // 'request_tee_times' => RequestTeeTimeResource::collection($this->teeTimes())
        ];
    }
}
