<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserDetails;
use App\Models\Company;
use App\Models\Image;

class PackagesServicesChildrenResource extends JsonResource
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
        return parent::toArray($request);
    }
}
