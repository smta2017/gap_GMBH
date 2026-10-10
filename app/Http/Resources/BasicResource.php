<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasicResource extends JsonResource
{
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
            'name' => $this->name,
            'status' => $this->status,
            'icon'=>$this->icon,
            'icon_name'=>$this->icon_name,
            'font_type'=>$this->font_type,
            'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
        ];
    }
}
