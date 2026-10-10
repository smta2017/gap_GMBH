<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\Console\Output\AnsiColorMode;

class RoomResource extends JsonResource
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
            'code' => $this->code,
           
            'hotel_id' => $this->hotel_id,
            
            'show_website' => $this->show_website,
            
            'status' => $this->status,

            'facilities' => FacilityResource::collection($this->facilities),


            'fields' => RoomFieldResource::collection($this->fields),
            
            'main_image' => $this->get_main_image(),
            
            'images' => $this->imagesFullData,

            'is_publish_required'=> $this->isPublishRequired()
        ];
    }
}
