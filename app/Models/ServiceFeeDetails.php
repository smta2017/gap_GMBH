<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFeeDetails extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'service_id',
        'addon_id',

        'unit_type',
        'unit_options',

        'active',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function addon()
    {
        return $this->belongsTo(ServiceAddon::class);
    }
}
