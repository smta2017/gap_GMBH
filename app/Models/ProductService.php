<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductService extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'product_services';
    
    protected $fillable = [
        'name',
        'type',

        'company_type_id',
        
        'company_id',

        'provider_id',

        'country_id',
        'city_id',

        'letter_code',

        'code',
        'ref_code',
        'tui_code',

        'validity_from',
        'validity_to',

        'invoice_handler_id',

        "booking_code",
        "davinci_booking_code",
        
        'service_handler_type_id',
        'service_handler_id',

        'booking_possible_for',
        'booking_from_id',

        'active',
        'use_destination_hotel',
    ];

    public function get_pagination()
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $results = $this;

        if(request()->search)
        {
            $results = $results->where('id', request()->search)
                                ->orWhere('name', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('ref_code', 'LIKE', '%' . request()->search . '%');
        }

        if(request()->code)
        {
            $results = $results->where('code', 'LIKE', '%' . request()->code . '%');
        }
        if(request()->ref_code)
        {
            $results = $results->where('ref_code', 'LIKE', '%' . request()->ref_code . '%');
        }
        if(request()->validity_from)
        {
            $results = $results->where('validity_from', request()->validity_from);
        }
        if(request()->validity_to)
        {
            $results = $results->where('validity_to', request()->validity_to);
        }
        if(request()->type)
        {
            $results = $results->where('type', request()->type);
        }
        if(isset(request()->company_id))
        {
            $results = $results->where('company_id', request()->company_id);
        }
        if(request()->booking_code)
        {    
            $hotelsBooking = Hotel::where('booking_code', request()->booking_code)->pluck('company_id')->toArray();
            $coursesBooking = GolfCourse::where('booking_code', request()->booking_code)->pluck('company_id')->toArray();
            $dmcsBooking = DMC::where('booking_code', request()->booking_code)->pluck('company_id')->toArray();
            $companiesBooking = Company::where('booking_code', request()->booking_code)->pluck('id')->toArray();

            $arrData = array_unique(array_merge($hotelsBooking,$coursesBooking, $dmcsBooking, $companiesBooking), SORT_REGULAR);
            $results = $results->whereIn('company_id', $arrData);
        }
        if(isset(request()->active))
        {
            $results = $results->where('active', request()->active);
        }

        if(request()->city_id)
        {
            $servicesData = \DB::table('product_service_city')->where('city_id', request()->city_id)->pluck('product_service_id')->toArray();

            $results = $results->whereIn('id', $servicesData);
        }

        return $results->paginate($pagination);
    }

    public function get_all()
    {
        $results = $this;

        if(request()->search)
        {
            $results = $results->where('id', request()->search)
                                ->orWhere('name', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('ref_code', 'LIKE', '%' . request()->search . '%');
        }

        if(request()->city_id)
        {
            $servicesData = \DB::table('product_service_city')->where('city_id', request()->city_id)->pluck('product_service_id')->toArray();
    
            $results = $results->whereIn('id', $servicesData);
        }

        if(request()->type)
        {    
            $results = $results->where('type', request()->type);
        }

        if(request()->company_id)
        {    
            $results = $results->where('company_id', request()->company_id);
        }
        if(request()->booking_code)
        {    
            // if(request()->booking_code_type=='company'){
            //     $companiesBooking = Company::where('booking_code', request()->booking_code)->pluck('id')->toArray();
            //     $arrData =  $companiesBooking  ;//array_unique(array_merge($companiesBooking), SORT_REGULAR);
            //     $results = $results->whereNull('provider_id');
            // }else{   
            //     $hotelsBooking = Hotel::where('booking_code', request()->booking_code)->pluck('company_id')->toArray();
            //     $coursesBooking = GolfCourse::where('booking_code', request()->booking_code)->pluck('company_id')->toArray();
            //     $dmcsBooking = DMC::where('booking_code', request()->booking_code)->pluck('company_id')->toArray();
            //     $arrData = array_unique(array_merge($hotelsBooking,$coursesBooking, $dmcsBooking), SORT_REGULAR);
            //     $results = $results->whereNotNull('provider_id');
            // }

            $results = $results->where('booking_code', request()->booking_code);
        }

        if(isset(request()->active))
        {    
            $results = $results->where('active', request()->active);
        }

        return $results->get();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class, 'company_type_id');
    }

    public function provider()
    {
        if($this->company_type_id)
        {
            switch ($this->company_type_id) {
                case "2":
                    $provider = 'App\Models\TravelAgency';
                    break;
                case "3":
                    $provider = 'App\Models\GolfCourse';
                    break;
                case "4":
                    $provider = 'App\Models\Hotel';
                    break;
                case "5":
                    $provider = 'App\Models\TourOperator';
                    break;
                case "6":
                    $provider = 'App\Models\DMC';
                    break;
                default:
            }

            if(isset($provider))
            {
                return $provider::find($this->provider_id);
            }
            return false;
        }

        return false;
    }

    public function invoiceHandler()
    {
        return $this->belongsTo(Company::class, 'invoice_handler_id');
    }

    public function serviceHandlerType()
    {
        return $this->belongsTo(CompanyType::class, 'service_handler_type_id');
    }

    public function serviceHandler()
    {
        return $this->belongsTo(Company::class, 'service_handler_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'service_id');
    }
    public function seasons()
    {
        return $this->hasMany(Season::class, 'service_id');
    }

    public function lists()
    {
        return $this->hasMany(PriceList::class, 'service_id');
    }

    public function cities()
    {
        return $this->belongsToMany(City::class, 'product_service_city', 'product_service_id', 'city_id');
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'product_service_hotel', 'product_service_id', 'hotel_id');
    }
}
