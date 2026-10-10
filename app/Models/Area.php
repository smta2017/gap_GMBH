<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Area extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'region_id',
        'country_id',
        'city_id',
        'language_id',
        'status',
    ];

   
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function get_all()
    {
        $city_id = request()->input('city_id');
        $search = request()->input('search');
        
        $code = request()->input('code');

        $areas = $this;

        $areas = $areas->when(($city_id || $search), function($query) use ($city_id, $search){

            if($city_id)
            {
                $query = $query->where('city_id', $city_id);
            }
            if($search)
            {
                $query = $query->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('code', 'LIKE', '%' . $search . '%');
            }
            return $query;
        });

        if($code)
        {
            $areas = $areas->where('code', $code);
        }

        if(isset(request()->show_website))
        {
            $areas = $areas->where('show_website', request()->show_website);
        }

        return $areas->get();
    }
    
    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
}
