<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes, HasFactory;

    public $translatedAttributes = ['name'];
    
    protected $fillable = [
        'name',
        'status',
        'type',
    ];

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
}
