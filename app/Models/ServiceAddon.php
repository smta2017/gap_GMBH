<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceAddon extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'type',
        
        'view_type',
        'options',

        'active',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function fees()
    {
        return $this->hasMany(ServiceFeeDetails::class, 'addon_id');
    }
}
