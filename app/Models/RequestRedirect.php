<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestRedirect extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'request_id',
        'request_product_id',
        'request_tee_time_id',

        'subject',
        'body'
    ];
}
