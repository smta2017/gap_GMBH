<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource2 extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->translations) {
            $name_en = $this->translations[0]['name'];
            $name_de =$this->translations[1]['name'];
         }

        return [
            'id' => $this->id,
            'name_en' => $name_en,
            'name_de' => $name_de,
        ];
    }
}
