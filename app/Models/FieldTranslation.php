<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldTranslation extends Model
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
        return $this->belongsTo(Field::class, 'field_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
