<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GolfCourseMiniResource extends JsonResource
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
            'name' => $this->name,
            // 'region' => new RegionResource2($this->region),
            'country' => new CountryResource2($this->country),
            'city' => new CityResource2($this->city),
            // 'area' => new AreaResource($this->area),
            "publish" => $this->publishColumn()
            
        ];
    }
}
