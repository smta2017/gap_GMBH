<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
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
            'type_id' => $this->type_id,
            'type_name' => ($this->type) ? $this->type->name : '',
            'description' => $this->description,
            'is_html' => $this->is_html,
            'translations' => ($this->translations) ? FieldTranslationResource::collection($this->translations) : [],
        ];
    }
}
