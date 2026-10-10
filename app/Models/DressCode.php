<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DressCode extends Model
{
    use HasFactory;

    public $translatedAttributes = ['name'];

    protected $fillable = [
        'name',
        'status'
    ];

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
}
