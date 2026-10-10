<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'hotel_id',
        'language_id',
        'locale',
        'name',
        'website_description',
        'internal_description'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
