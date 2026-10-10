<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasicTranslationResource extends JsonResource
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
            'language_id' => $this->language_id,
            'language_name' => ($this->language) ? $this->language->name : '',
            'language_code' => ($this->language) ? $this->language->code : '',

            'locale' => $this->locale,
            'name' => $this->name,
        ];
    }
}
