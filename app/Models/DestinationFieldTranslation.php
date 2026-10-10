<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinationFieldTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'field_id',
        'language_id',
        'locale',
        'description',
    ];

    public function field()
    {
        return $this->belongsTo(DestinationField::class, 'field_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
