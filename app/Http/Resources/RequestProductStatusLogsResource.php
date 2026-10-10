<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\RequestSubStatus;
use App\Models\RequestStatus;

class RequestProductStatusLogsResource extends JsonResource
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

            'status_id' => $this->get_status_from_properties($this->properties, 'attributes','status_id'),
            'status_name' => $this->get_status_from_properties($this->properties, 'attributes','status.name'),

            'is_system' => $this->is_system_action(),

            'description' => $this->description,
            'subject_id' => $this->subject_id,
            'log_name' => $this->log_name,

            'action_name' => $this->get_action_name(),

            'causer_type' => $this->causer_type,
            'causer_id' => $this->causer_id,

            'user' => ($this->userData()) ? $this->userData()->details->first_name . ' ' . $this->userData()->details->last_name : '',
            'user_company_type_id' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->type->name : '') : '',
            'user_company_id' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->id : '') : '',
            'user_company_name' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->name : '') : '',
            
            'created_at' => $this->created_at,

            'properties' => $this->properties,
        ];
    }

    public function is_system_action()
    {
        $subStatusName = $this->get_status_from_properties($this->properties, 'attributes','status.name');

        if (str_contains($subStatusName, 'Redirected') || str_contains($subStatusName, 'Confirmed'))
        { 
            return 1;
        }

        return 0;
    }

    public function userData()
    {
        $user = User::find($this->causer_id);

        if($user)
        {
            return $user;
        }

        return false;
    }

    public function get_action_name()
    {
        if($this->get_status_from_properties($this->properties, 'attributes','status_id'))
        {
            return "Status Changed";
        }

        if($this->subject_type == 'App\Models\RequestProduct' && $this->get_status_from_properties($this->properties, 'attributes','status_id') == null)
        {
            return 'Product Updated';
        }

        if($this->subject_type == 'App\Models\RequestProductTeeTime')
        {
            return 'Alternative Added';
        }
    }

    public function get_status_from_properties($properties, $attributeName, $key)
    {
        if(isset($properties[$attributeName]))
        {
            $attributes = $properties[$attributeName];
        
            if(isset($attributes[$key])){
    
                $attributeKey = $attributes[$key];
    
                return $attributeKey;
            }
        }
  
        return null;
    }
}
