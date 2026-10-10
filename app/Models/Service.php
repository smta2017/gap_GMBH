<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes, HasFactory;
    
    public const HOTEL_SERVICES_LIST = ['Hotel-General','Hotel-Sport&Wellness','Hotel-Food&Drinks','Hotel-Golf&Highlights'];

    protected $fillable = [
        'name',
        'description',
        'type',

        'view_type',
        'options',

        'active',
        'sorted',
        
        'icon',
        'icon_name',
        'font_type',
    ];

    public function properties()
    {
        return $this->hasMany(ServiceProperty::class, 'service_id');
    }

    public function addons()
    {
        return $this->hasMany(ServiceAddon::class, 'service_id');
    }

    public function fees()
    {
        return $this->hasMany(ServiceFeeDetails::class, 'service_id');
    }

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
}
