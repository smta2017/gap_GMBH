<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'imageable_id',
        'imageable_type',
        'is_main',
        'alt',
        'original_file_name',
        'size',
        'rank',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
