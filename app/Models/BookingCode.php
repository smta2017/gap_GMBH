<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingCode extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'booking_code',
        'codeable_id',
        'codeable_type',
    ];

    public function codeable()
    {
        return $this->morphTo();
    }

}
