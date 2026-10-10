<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaviniciCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'davinici_code',
        'codeable_id',
        'codeable_type',
    ];

    public function codeable()
    {
        return $this->morphTo();
    }

}
