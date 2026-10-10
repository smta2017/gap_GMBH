<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
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

            'type' => $this->type,
            
            'product_id' => $this->product_id,
            'product_name' => ($this->product) ? $this->product->name : '',

            'golf_course_id' => $this->golf_course_id,
            'golf_course_name' => ($this->golfcourse) ? $this->golfcourse->name : '',
            
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
