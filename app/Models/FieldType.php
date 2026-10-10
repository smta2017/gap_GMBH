<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldType extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function scopeActive($query){
        $query->whereStatus(1);
    }

    public function scopeCountryFields($query)
    {
        $query->whereCategoryId('country');
    }

    public function scopeCityFields($query)
    {
        $query->whereCategoryId('city');
    }

    public function scopeGolfCourseFields($query)
    {
        $query->whereCategoryId('golf_course');
    }


    public function scopeHotelFields($query)
    {
        $query->whereCategoryId('hotel');
    }
    
    public function scopePackageFields($query)
    {
        $query->whereCategoryId('package');
    }

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
}