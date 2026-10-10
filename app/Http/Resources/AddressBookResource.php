<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserDetails;
use App\Models\Company;
use App\Models\Image;

class AddressBookResource extends JsonResource
{

    public static $wrap = '';
    
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'department' => $this->department,
            'title' => $this->title,
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',

            'company_type_id' => ($this->company) ? $this->company->type->id : '',
            'company_type_name' => ($this->company) ? $this->company->type->name : '',

            'user_id' => $this->user_id
        ];
    }
}
