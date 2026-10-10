<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectServiceProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'service_id',
        'service_property_id',
        'selected_option',
        'notes',
        'active'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
