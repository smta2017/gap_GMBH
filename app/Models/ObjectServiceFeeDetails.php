<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectServiceFeeDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'service_id',
        'service_addon_id',
        'service_fees_details_id',
        'qty',
        'fees',

        'unit',

        'notes'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
