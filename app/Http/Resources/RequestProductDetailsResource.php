<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestProductDetailsResource extends JsonResource
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
            'request_product_id' => $this->request_product_id,
           
            'product_id' => $this->product_id,
            'product_name' => ($this->product) ? $this->product->name : '',

            'product_details_id' => $this->product_details_id,

            'type' => $this->type,

            'golf_course_id' => $this->golf_course_id,
            'golf_course_name' => ($this->golfcourse) ? $this->golfcourse->name : '',
            'hcp_men' => ($this->golfcourse) ? $this->golfcourse->hcp_men : '',
            'hcp_women' => ($this->golfcourse) ? $this->golfcourse->hcp_women : '',
            'start_time' => ($this->golfcourse) ? $this->golfcourse->start_time : '',
            'end_time' => ($this->golfcourse) ? $this->golfcourse->end_time : '',
            
            'company_id' => ($this->golfcourse) ? $this->golfcourse->company_id : '',
            'company_name' => ($this->golfcourse) ? $this->golfcourse->company->name : '',

            'tee_time_id' => $this->tee_time_id,
            'tee_time_name' => ($this->teeTime) ? $this->teeTime->name : '',

            'min_tee_time_id' => $this->min_tee_time_id,
            'min_tee_time_name' => ($this->minTeeTime) ? $this->minTeeTime->name : '',

            'max_tee_time_id' => $this->max_tee_time_id,
            'max_tee_time_name' => ($this->maxTeeTime) ? $this->maxTeeTime->name : '',
        ];
    }
}
