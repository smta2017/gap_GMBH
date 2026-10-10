<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ObjectServiceProperty;
use App\Models\ObjectServiceAddon;
use App\Models\ObjectServiceFeeDetails;

class GolfCourseServiceResource extends JsonResource
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
            'service_id' => $this->id,
            'type' => $this->type,
            'qty' =>  $this->whenPivotLoaded('object_services', function () {
                return $this->pivot->qty;
            }),
            'fees' =>  $this->whenPivotLoaded('object_services', function () {
                return $this->pivot->fees;
            }),
            'selected_option' =>  $this->whenPivotLoaded('object_services', function () {
                return $this->pivot->selected_option;
            }),
            'notes' =>  $this->whenPivotLoaded('object_services', function () {
                return $this->notes;
            }),
            'active' =>  $this->whenPivotLoaded('object_services', function () {
                return $this->active;
            }),

            'sorted'=>$this->sorted,
            'icon'=>$this->icon,
            'icon_name'=>$this->icon_name,
            'font_type'=>$this->font_type,

            'properties' => ObjectServiceProperty::where('service_id', $this->id)->select(['service_property_id', 'selected_option', 'notes'])->get(),
            'addons' => ObjectServiceAddon::where('service_id', $this->id)->select(['service_addon_id', 'qty', 'fees', 'selected_option', 'notes'])->get(),
            'fee_details' => ObjectServiceFeeDetails::where('service_id', $this->id)->where('service_addon_id', null)->select(['service_fees_details_id', 'service_addon_id', 'qty', 'fees', 'unit', 'notes'])->get(),
            
            'translations' => ($this->translations) ? BasicTranslationResource::collection($this->translations) : [],
        ];
    }
}
