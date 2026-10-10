<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserDetails;
use App\Models\Company;
use App\Models\Image;

class PackagesServicesResource extends JsonResource
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
        // return parent::toArray($request);
        return [
            "id" => $this["id"], //: 2,
            "booking_code" => $this["booking_code"], //: "GMZ11104",
            "booking_code_id" => $this["booking_code_id"], //: 7806,
            "booking_code_name" => $this["booking_code_name"], //: "Jardin Tecina Golf Package (7N+5GF)",
            "requirement" => $this["requirement"], //: "H",
            "requirement_flag" => $this["requirement_flag"], //: 0,
            "date_from" => $this["date_from"], //: "2023-06-21",
            "date_to" => $this["date_to"], //: "2023-06-28",
            "duration" => $this["duration"], //: 1,
            "catalog_code" => $this["catalog_code"], //: "2223",
            "catalog_name" => $this["catalog_name"], //: "GOLF GLOBE ab 2022",
            "destination_name" => $this["destination_name"], //: "La Gomera",
            "destination_code" => $this["destination_code"], //: "GMZ",
            "standard_meal_code" => $this["standard_meal_code"], //: "H",
            "package_service_id" => $this["package_service_id"], //: 1,
            "package_order" => $this["package_order"], //: 1,
            "package_type_of_assignment" => $this["package_type_of_assignment"], //: 4,
            "sync_last" => $this["sync_last"], //: "2023-06-25 13:39:53",
            "created_at" => $this["created_at"], //: "2023-06-25T13:38:29.000000Z",
            // "updated_at" => $this["updated_at"], //: "2023-06-25T13:38:29.000000Z",
            "services" => PackagesServicesChildrenResource::collection($this['Children'])
        ];
    }
}
