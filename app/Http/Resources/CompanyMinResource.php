<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyMinResource extends JsonResource
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
            'name' => $this->name,
            // 'hotel_id' => $this->hotel_id,
            // 'hotel_name' => ($this->hotel) ? $this->hotel->name : '',
            'phone' => $this->phone,
            'fax' => $this->fax,
            'website' => $this->website,
            'email' => $this->email,
            'rank' => $this->rank,
            'contract' => $this->contract,

            "booking_code" => $this->booking_code,
            "davinci_booking_code" => $this->davinci_booking_code,
            
            'company_type_id' => $this->company_type_id,
            'company_type_name' => ($this->type) ? $this->type->name : '',
 
          
            // 'has_childs' => $this->check_has_childs(),
            // 'childs_count' => $this->calc_childs_count(),
 

            'top' => $this->top,


        ];
    }
}
