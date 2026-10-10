<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medium extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'mediums';
    
    protected $fillable = [
        'name',
        'status'
    ];
}
