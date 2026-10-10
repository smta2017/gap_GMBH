<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaderType extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'has_company',
        'has_hcp',
        'status',
    ];
}
