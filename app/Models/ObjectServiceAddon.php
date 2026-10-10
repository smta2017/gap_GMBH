<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectServiceAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'service_id',
        'service_addon_id',

        'qty',
        'fees',

        'selected_option',
        'notes',
        'active'
    ];

    protected $appends = [
        'fee_details'
    ];

    public function getFeeDetailsAttribute()
    {
        return ObjectServiceFeeDetails::where('service_addon_id', $this->service_addon_id)->select(['service_fees_details_id', 'service_addon_id', 'qty', 'fees', 'unit', 'notes'])->get();
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
