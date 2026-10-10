<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProperty extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'description',

        'view_type',
        'options',

        'active',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
