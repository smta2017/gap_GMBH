<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomFieldType extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
}
