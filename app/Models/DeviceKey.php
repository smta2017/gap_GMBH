<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceKey extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "device_keys";
    
    protected $fillable = [
        'user_id',
        'device_key',
        'active'
    ];
}
