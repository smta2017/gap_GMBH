<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomField extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'room_field_type_id',
        'description',
        'is_html',
        'room_id',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function type()
    {
        return $this->belongsTo(RoomFieldType::class, 'room_field_type_id');
    }

    public function translations()
    {
        return $this->hasMany(RoomFieldTranslation::class);
    }
}
