<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestStatus extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function subStatuses()
    {
        return $this->hasMany(RequestSubStatus::class, 'request_status_id');
    }
}
