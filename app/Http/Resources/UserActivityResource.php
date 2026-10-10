<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserDetails;
use App\Models\Company;
use App\Models\Image;
use Carbon\Carbon;

class UserActivityResource extends JsonResource
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
            'first_name' => $this->user->details->first_name,
            'last_name' => $this->user->details->last_name,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'player_id' => $this->player_id,
            'ip'=>$this->ip,
            'geoip_city_name'=>$this->geoip_city_name,
            'browser_name'=>$this->browser_name,
            'created_at'=> $this->created_at->format('Y-m-d')
        ];
    }
}
