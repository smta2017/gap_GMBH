<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BewotecDavinciServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        "service_id",
        "service_type_code",
        "service_type_name",
        "participants",
        "adults",
        "price",
        "price_avg",
        "price_booking_related",
        "currency",
        "availability",
        "availability_detailed",
        "occupation_minimum",
        "occupation_maximum",
        "adults_minimum",
        "adults_maximum",
        "childs_maximum",
        "babys_maximum",
        "sync_last",
    ];

    function Service()
    {
        return $this->belongsTo(BewotecDavinciService::class, "service_id");
    }
}
