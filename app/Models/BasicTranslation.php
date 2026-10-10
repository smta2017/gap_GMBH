<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'basicable_id',
        'basicable_type',
        'language_id',
        'locale',
        'name',
    ];

    public function basicable()
    {
        return $this->morphTo();
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
