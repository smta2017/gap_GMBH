<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CompanyDocumentResource extends JsonResource
{
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
            'title' => $this->title,
            'document_type_id' => $this->document_type_id,
            'document_type_name' => ($this->documenttype) ? $this->documenttype->name : '',
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',
            'expire_date' => Carbon::parse($this->expire_date),
            'file_name' => $this->file_name,
            'file_path' => asset('images/companies') . '/' . $this->file_name,
            'file_type' => $this->file_type,
            'is_notify' => $this->is_notify,
        ];
    }
}
