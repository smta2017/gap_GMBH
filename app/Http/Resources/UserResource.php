<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserDetails;
use App\Models\Company;
use App\Models\Image;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'profile_image' => ($this->image) ? asset('images/users') . '/' . $this->image->file_name : asset('images/users/default-profile.jpg'),
            'player_id' => $this->player_id,
            'childs' => UserChildResource::collection($this->childs),
        ];
    }
}
