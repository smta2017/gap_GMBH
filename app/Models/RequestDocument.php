<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestDocument extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'request_id',
        'date',
        'file_name',
        'file_type',
        'user_id',
        
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
