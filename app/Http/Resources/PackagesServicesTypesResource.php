<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserDetails;
use App\Models\Company;
use App\Models\Image;

class PackagesServicesTypesResource extends JsonResource
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
            "id" => $this->id,
            "service_id" => $this->service_id,
            "service_type_code" => $this->service_type_code,
            "service_type_name" => $this->service_type_name,
            "participants" => $this->participants,
            "adults" => $this->adults,
            "price" => $this->price,
            "price_avg" => $this->price_avg,
            "price_booking_related" => $this->price_booking_related,
            "currency" => $this->currency,
            "availability" => $this->availability,
            "availability_detailed" => $this->availability_detailed,
            "occupation_minimum" => $this->occupation_minimum,
            "occupation_maximum" => $this->occupation_maximum,
            "adults_minimum" => $this->adults_minimum,
            "adults_maximum" => $this->adults_maximum,
            "childs_maximum" => $this->childs_maximum,
            "babys_maximum" => $this->babys_maximum,
            "sync_last" => $this->sync_last,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
