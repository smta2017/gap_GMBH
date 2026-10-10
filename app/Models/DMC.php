<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use DB;

class DMC extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "dmcs";

    protected $fillable = [

        "company_id",
        "is_parent",
        "name",
        "is_client",
        "ref_id",
        "has_hotels",
        "has_golf_courses",

        "active",

        "delegate_name",
        "delegate_email",
        "delegate_mobile_number",
        "delegate_user_id",
        "assigned_user_id",

        "region_id",
        "country_id",
        "city_id",
        "area_id",
        "street",
        "postal_code",
        "phone",
        "fax",
        "email",
        "website",

        "is_company_address",
        
        "reservation_email",
        
        "booking_code",
        "davinci_booking_code",
        
        "created_by",
        "updated_by",
        "deleted_by"
    ];

    public function get_pagination($filter)
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $searchQuery = request()->input('search');

        return $this->where($filter)->where(function ($query) use ($searchQuery){

            $columns = Schema::getColumnListing('dmcs');
            foreach($columns as $column)
            {
                $query->orWhere($column, 'LIKE', '%' . $searchQuery . '%');
            }
            
        })->paginate($pagination);
    }

    public function golfcourses()
    {
        return $this->belongsToMany(GolfCourse::class, 'dmc_golfcourse', 'dmc_id', 'golf_course_id');
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'dmc_hotel', 'dmc_id', 'hotel_id');
    }

    public function cities()
    {
        return $this->belongsToMany(City::class, 'dmc_city', 'dmc_id', 'city_id');
    }

    public function traveltypes()
    {
        return $this->belongsToMany(TravelType::class, 'dmc_travel_type', 'dmc_id', 'travel_type_id');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function imagesFullData()
    {
        return $this->images()->select('id', DB::raw("CONCAT('".asset('images/dmcs')."', '/', file_name) AS file_name"),'alt','original_file_name', 'size', 'rank')->orderBy('rank');
    }     

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function assignuser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function delegateuser()
    {
        return $this->belongsTo(User::class, 'delegate_user_id');
    }

    public function createdbyuser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function country()
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

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
    }
    
    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitieable');
    }

    public function requestProductHandler()
    {
        return $this->hasMany(RequestProduct::class, 'service_handler_id')->where('service_handler_type_id', '6');
    }

    public function teeTimesQuery()
    {
        $teeTimes = RequestProductTeeTime::join('request_products', 'request_products.id', 'request_product_tee_times.request_product_id')
                                        ->where('request_products.service_handler_type_id', '6')
                                        ->where('request_products.service_handler_id', $this->id)
                                        ->get();    
        return $teeTimes;                          
    }

    public function teeTimes()
    {
        return $this->hasManyThrough(RequestProductTeeTime::class, RequestProduct::class, 'service_handler_id', 'request_product_id')->where('request_products.service_handler_type_id', '6');
    }
}
