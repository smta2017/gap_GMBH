<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BewotecDavinciService extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'booking_code_id',
        'booking_code_name',
        'requirement',
        'date_from',
        'date_to',
        'duration',
        'catalog_code',
        'catalog_name',
        'destination_name',
        'destination_code',
        'standard_meal_code',
        'package_service_id',
        'package_order',
        'package_type_of_assignment',
        'sync_last',
    ];

    public function ServiceTypes()
    {
        return $this->hasMany(BewotecDavinciServiceType::class, "service_id");
    }

    public function Children()
    {
        return $this->hasMany(BewotecDavinciService::class, "package_service_id");
    }
}
