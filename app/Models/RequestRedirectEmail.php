<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestRedirectEmail extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'request_redirect_id',
        'email',
        'type'
    ];
}
