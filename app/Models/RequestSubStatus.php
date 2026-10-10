<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestSubStatus extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'request_status_id',
        'status',
    ];
    
}