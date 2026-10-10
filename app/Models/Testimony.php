<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimony extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'text',
        'status',

        'testimonyable_id',
        'testimonyable_type',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function testimonyable()
    {
        return $this->morphTo();
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
