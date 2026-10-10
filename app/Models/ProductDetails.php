<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetails extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'product_id',
        'golf_course_id',
        'type',
        'tee_time_id',
        'min_tee_time_id',
        'max_tee_time_id',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function golfcourse()
    {
        return $this->belongsTo(Golfcourse::class, 'golf_course_id');
    }

    public function teeTime()
    {
        return $this->belongsTo(TeeTime::class, 'tee_time_id');
    }

    public function minTeeTime()
    {
        return $this->belongsTo(TeeTime::class, 'min_tee_time_id');
    }

    public function maxTeeTime()
    {
        return $this->belongsTo(TeeTime::class, 'max_tee_time_id');
    }
}
