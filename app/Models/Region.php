<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status'
    ];

    public function get_all()
    {
        // return $this->whereHas('countries')->get();
        return $this->get();
    }

    public function get_all_active()
    {
        return $this->where('status', '1')->get();
    }

    public function countries()
    {
        // return $this->hasMany(Country::class)->whereHas('cities');
        return $this->hasMany(Country::class);
    }

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
}
