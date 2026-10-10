<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'noteable_id',
        'noteable_type',
    ];

    public function noteable()
    {
        return $this->morphTo();
    }

}
