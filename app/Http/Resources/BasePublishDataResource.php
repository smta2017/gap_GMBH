<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasePublishDataResource extends JsonResource
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
            'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
            "publish" => $this->publishColumn()
        ];
    }
}
