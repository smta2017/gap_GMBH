<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectService extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'service_id',
        'type',
        'qty',
        'fees',
        'selected_option',
        'notes',
        'active'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
