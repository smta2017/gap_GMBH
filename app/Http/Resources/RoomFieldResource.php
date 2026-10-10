<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomFieldResource extends JsonResource
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
            'room_field_type_id' => $this->room_field_type_id,
            'room_field_type_name' => ($this->type) ? $this->type->name : '',
            'description' => $this->description,
            'is_html' => $this->is_html,
            'translations' => ($this->translations) ? FieldTranslationResource::collection($this->translations) : [],
        ];
    }
}
