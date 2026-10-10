<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Season extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'code',
        'service_id',
        'start_date',
        'end_date',
        'color',
        'display',
        'peak_time_from',
        'peak_time_to'
    ];

    public function service()
    {
        return $this->belongsTo(ProductService::class);
    }

    public function lists()
    {
        return $this->hasMany(PriceList::class);
    }    
}
