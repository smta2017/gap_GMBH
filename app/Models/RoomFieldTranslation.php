<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomFieldTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'room_field_id',
        'language_id',
        'locale',
        'description',
    ];

    public function field()
    {
        return $this->belongsTo(RoomField::class, 'room_field_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
