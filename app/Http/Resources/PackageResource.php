<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price_cache' => $this->price_cache,
            'davinci_booking_code' => $this->davinci_booking_code,
            'fields' => FieldResource::collection($this->fields),
            'images' =>  $this->imagesFullDataURLEncode(),
            'main_image' => $this->get_main_image(),
            
        ];
    }
}
