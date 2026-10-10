<?php

namespace App\Http\Resources;

use App\Models\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsRequestResource extends JsonResource
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

        $activities =  json_decode($this->activities);

        // =====================================submited_at========================================

        $filtered_arr = array_filter(
            $activities,
            function($obj){ 
                if (isset($obj->properties->attributes->sub_status_id)) {
                    return $obj->properties->attributes->sub_status_id === Request::SUBMITED_STATUS;
                }else{ return '';}
            }
        );


        $submited_at ='';
        foreach ($filtered_arr as  $value) {
            $submited_at  =$value->created_at;
        }
        // =====================================approved_at========================================


        $filtered_arr = array_filter(
            $activities,
            function($obj){ 
                if (isset($obj->properties->attributes->sub_status_id)) {
                    return $obj->properties->attributes->sub_status_id === Request::APPROVED_STATUS;
                }else{ return '';}
            }
        );

        $approved_at ='';
        foreach ($filtered_arr as  $value) {
            $approved_at  =$value->created_at;
        }


        // ========================================confirmed_at=====================================

        $filtered_arr = array_filter(
            $activities,
            function($obj){ 
                if (isset($obj->properties->attributes->sub_status_id)) {
                    return $obj->properties->attributes->sub_status_id === Request::CONFIRMED_STATUS;
                }else{ return '';}
            }
        );

        $confirmed_at ='';
        foreach ($filtered_arr as  $value) {
            $confirmed_at  =$value->created_at;
        }

        return [
            'id' => $this->id,
            'travel_agency' => $this->travelAgency->name,
            'tui_code' => $this->tui_ref_code,
            'created_at' => $this->created_at,
            'submited_at' => $submited_at,
            'approved_at' => $approved_at,
            'confirmed_at' => $confirmed_at,
        ];

    }
}
