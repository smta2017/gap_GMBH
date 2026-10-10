<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Company extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'hotel_id',
        'phone',
        'fax',
        'website',
        'email',

        'delegate_name',
        'delegate_email',
        'delegate_mobile_number',
        "delegate_user_id",
        "assigned_user_id",

        'rank',
        'contract',
 
        'company_type_id',
        'user_id',
        
        'region_id',
        'country_id',
        'city_id',
        'area_id',
        'postal_code',
        'street',
        'latitude',
        'longitude',
        'location_link',

        "booking_code",
        "davinci_booking_code",
        
        'instagram',
        'twitter',
        'facebook',
        'linkedin',
        'lang'
    ];

    public function type()
    {
        return $this->belongsTo(CompanyType::class, 'company_type_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    // public function theHotel()
    // {
    //     return $this->hasMany(Hotel::class);
    // }

    // public function dmc()
    // {
    //     return $this->hasMany(DMC::class);
    // }
    public function assignuser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function delegateuser()
    {
        return $this->belongsTo(User::class, 'delegate_user_id');
    }

    public function logo()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class);
    }
    public function get_all()
    {
        return $this->get();
    }

    public function get_pagination($filter)
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $searchQuery = request()->input('search');

        $results = $this;

        if(request()->input('booking_code'))
        {
            $results = $results->where('booking_code',  request()->input('booking_code'));
        }

        return $results->where($filter)->where(function ($query) use ($searchQuery){

            $columns = Schema::getColumnListing('companies');
            foreach($columns as $column)
            {
                $query->orWhere($column, 'LIKE', '%' . $searchQuery . '%');
            }
            
        })->paginate($pagination);
    }

    public function check_has_childs($company_id = null)
    {
        if($company_id == null)
        {
            $company_id = $this->id;
        }
        return ($this->calc_childs_count($company_id) ) ? true: false;
    }

    public function calc_childs_count($company_id = null)
    {

        if($company_id == null)
        {
            $company_id = $this->id;
        }
        
        $objectsArr = [GolfCourse::class,Hotel::class,DMC::class,TravelAgency::class,TourOperator::class];

        $count = 0;

        foreach($objectsArr as $obj)
        {
            $count += $obj::where('company_id', $company_id)->count();
        }

        return $count;
    }

    public function requestProductHandler()
    {
        return $this->hasMany(RequestProduct::class, 'service_handler_id');
    }

    public function teeTimesQuery()
    {
        $teeTimes = RequestProductTeeTime::join('request_products', 'request_products.id', 'request_product_tee_times.request_product_id')
                                        ->where('request_products.service_handler_id', $this->id)
                                        ->get();    
        return $teeTimes;                          
    }

    public function teeTimes()
    {
        
        $requestProductIDs = RequestProduct::where('service_handler_id', $this->id)
                                            ->orWhere(function($query){
                                                $query->where('service_handler_id', null)->whereHas('destination', function($q){
                                                    $q->whereHas('hotel', function($sub){
                                                        $sub->where('company_id', $this->id);
                                                    });
                                                });
                                            })
                                            ->pluck('id')->toArray();
                                            
        $teeTimes = RequestProductTeeTime::whereIn('request_product_id', $requestProductIDs)->get();

        return $teeTimes;


        // return $this->hasManyThrough(RequestProductTeeTime::class, RequestProduct::class, 'service_handler_id', 'request_product_id');
    }
    
    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }

}
