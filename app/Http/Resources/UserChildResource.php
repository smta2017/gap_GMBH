<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserChildResource extends JsonResource
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
            'child_id' => $this->child_id,
            'child_name' => $this->get_child_name(),

            'child_type_id' => $this->child_type_id,
            'child_type_name' => $this->childType ? $this->childType->name : '',
        ];
    }
}
