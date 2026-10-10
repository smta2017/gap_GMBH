<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserDetails;
use App\Models\User;
use App\Models\Company;
use App\Models\Image;

class UserFullDataResource extends JsonResource
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
            $this->mergeWhen(true, new UserResource(User::find($this->id))),
            $this->mergeWhen(true, new UserDetailsResource(UserDetails::where('user_id', $this->id)->first())),
        ];
    }
}
