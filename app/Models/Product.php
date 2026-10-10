<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\HotelResource;
use App\Http\Resources\CityResource;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',

        'is_package',
        
        'service_id',
        'golf_course_id',

        'code',
        'ref_code',
        'tui_code',

        'tee_time_id',
        'hole_id',

        'validity_from',
        'validity_to',

        'junior',
        'multi_players_only',
        'buggy',

        'use_service_configurations',

        'invoice_handler_id',

        'service_handler_type_id',
        'service_handler_id',

        'booking_possible_for',
        'booking_from_id',
        
        'additional_information',
        "booking_code",
        "davinci_booking_code",
        'status',
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
                                ->orWhere('ref_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('booking_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('davinci_booking_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('ref_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('tui_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('validity_from', request()->search)
                                ->orWhere('validity_to', request()->search)
                                ->orWhereHas('golfcourse', function($query) {
                                    $query->where('name', 'LIKE', '%' . request()->search . '%');
                                });
        }

        if(request()->service_id)
        {
            $results = $results->where('service_id', request()->service_id);
        }

        if(request()->booking_code)
        {
            $results = $results->where('booking_code', request()->booking_code);
        }

        if(request()->davinci_booking_code)
        {
            $results = $results->where('davinci_booking_code', request()->davinci_booking_code);
        }

        if(request()->status)
        {
            $results = $results->where('status', request()->status);
        }

        return $results->paginate($pagination);
    }

    public function get_all()
    {
        $request = request();
        $filter = [];

        $results = $this;

        if($request->search)
        {if(!$request->city_id && !$request->hotel_id){
            $results->whereNull('id');
        }
            // array_push($filter, array('name', 'LIKE', '%' . $request->search . '%'));
            $results = $results->where(function($q) use($request){
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('booking_code', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('davinci_booking_code', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('code', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('ref_code', 'LIKE', '%' . $request->search . '%')
                            ->orWhere('tui_code', 'LIKE', '%' . $request->search . '%');
            });

            if(!$request->city_id && !$request->hotel_id){
                $results->whereNull('id');
            }
        }

        if($request->service_id)
        {
            array_push($filter, array('service_id', $request->service_id ));
        }

        if($request->tui_code)
        {
            array_push($filter, array('tui_code', $request->tui_code ));
        }

        if($request->booking_code)
        {
            array_push($filter, array('booking_code', $request->booking_code ));
        }

        if($request->davinci_booking_code)
        {
            array_push($filter, array('davinci_booking_code', $request->davinci_booking_code ));
        }

        if($request->status)
        {
            array_push($filter, array('status', $request->status ));
        }

        $results = $results->where($filter);

        if($request->city_id)
        {
            $servicesData = \DB::table('product_service_city')->where('city_id', $request->city_id)->pluck('product_service_id')->toArray(); 
           
            $results = $results->where(function(Builder $query) use($servicesData){
                $query->whereIn('service_id', $servicesData);
                $query->where('booking_possible_for', 'Region');
            });
        }

        if($request->hotel_id)
        {
            $results = $results->orWhere(function(Builder $query) use($request) {
                $query->where('booking_possible_for', 'Hotel')->whereHas('hotels',function(Builder $query) use($request){
                $query->where('hotels.id' ,$request->hotel_id);
            });
        });
        }
            
        if($request->country_id)
        {
            $cities = City::where('country_id', $request->country_id)->pluck('id')->toArray();
            $servicesData = \DB::table('product_service_city')->whereIn('city_id', $cities)->pluck('product_service_id')->toArray(); 
            $results = $results->whereIn('service_id', $servicesData);
        }

        if($request->arrival_date && $request->departure_date)
        {
            $servicesDataObj = ProductService::where('validity_from', '<=', $request->arrival_date)->where('validity_to', '>=', $request->departure_date)->pluck('id')->toArray(); 
            $results = $results->whereIn('service_id', $servicesDataObj);
        }

        if($request->travel_agency_id)
        {
            $operators = \DB::table('travel_agency_tour_operator')->where('travel_agency_id', $request->travel_agency_id)->pluck('tour_operator_id')->toArray();
 
            $hotelIDs = \DB::table('hotel_tour_operator')->whereIn('tour_operator_id', $operators)->pluck('hotel_id')->toArray();
            
            // $results = $results->whereIn('booking_from_id', $hotelIDs);
        }


        if($request->company_id)
        {
            $results->whereHas('service',function($q) use($request){
                $q->whereCompanyId($request->company_id);
            });
        }

        
        
        return $results->get();
    }

    public function service()
    {
        return $this->belongsTo(ProductService::class, 'service_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function golfcourse()
    {
        return $this->belongsTo(GolfCourse::class, 'golf_course_id');
    }

    public function teeTime()
    {
        return $this->belongsTo(TeeTime::class, 'tee_time_id');
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

    public function hole()
    {
        return $this->belongsTo(Hole::class, 'hole_id');
    }

    public function hotel_data()
    {
        if($this->booking_possible_for == 'Hotel' && $this->booking_from_id != null && Hotel::find($this->booking_from_id) != null)
        {
            return new HotelResource(Hotel::find($this->booking_from_id));
        }else{
            return [];
        }
    }

    public function city_data()
    {
        if($this->booking_possible_for == 'City' && $this->booking_from_id != null && City::find($this->booking_from_id) != null)
        {
            return new CityResource(City::find($this->booking_from_id));
        }else{
            return [];
        }
    }

    public function details()
    {
        return $this->hasMany(ProductDetails::class, 'product_id');
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'product_hotel', 'product_id', 'hotel_id');
    }
}
