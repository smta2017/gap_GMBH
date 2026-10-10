<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\RequestProductTeeTime;

class TeeTimeDatesResource extends JsonResource
{

    public static $wrap = '';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'date' => $this->the_date,
            'views' => $this->views,

            // 'request_tee_times' => RequestTeeTimeViewResource::collection(RequestProductTeeTime::where('parent_id', null)->whereDate('created_at', $this->date)->get())
        ];
    }
}
