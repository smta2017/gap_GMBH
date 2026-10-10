<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\HotelResource;
use App\Http\Resources\CityResource;

class HotelProduct extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',

        'service_id',
        'hotel_id',

        'code',
        'ref_code',
        'tui_code',

        'room_type_id',
        'room_view_id',
        'room_board_id',

        'validity_from',
        'validity_to',

        'use_service_configurations',

        'invoice_handler_id',

        'service_handler_type_id',
        'service_handler_id',

        'booking_possible_for',
        'booking_from_id',

        'status',
        'use_destination_hotel',
    ];

    public function get_pagination($filter)
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
                                ->orWhere('validity_from', request()->search)
                                ->orWhere('validity_to', request()->search)
                                ->orWhereHas('hotel', function($query) {
                                    $query->where('name', 'LIKE', '%' . request()->search . '%');
                                });
        }

        return $results->paginate($pagination);
    }

    public function get_all()
    {
        $request = request();
        $filter = [];

        $results = $this;

        if($request->search)
        {
            array_push($filter, array('name', 'LIKE', '%' . $request->search . '%'));
        }

        if($request->service_id)
        {
            array_push($filter, array('service_id', $request->service_id ));
        }

        if($request->status)
        {
            array_push($filter, array('status', $request->status ));
        }

        $results = $results->where($filter);

        if($request->city_id)
        {
            $servicesData = \DB::table('product_service_city')->where('city_id', $request->city_id)->pluck('product_service_id')->toArray(); 
            $results = $results->whereIn('service_id', $servicesData);
        }

        if($request->country_id)
        {
            $cities = City::where('country_id', $request->country_id)->pluck('id')->toArray();
            $servicesData = \DB::table('product_service_city')->whereIn('city_id', $cities)->pluck('product_service_id')->toArray(); 
            $results = $results->whereIn('service_id', $servicesData);
        }

        return $results->get();
    }

    public function service()
    {
        return $this->belongsTo(ProductService::class, 'service_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function roomView()
    {
        return $this->belongsTo(RoomView::class, 'room_view_id');
    }

    public function roomBoard()
    {
        return $this->belongsTo(RoomBoard::class, 'room_board_id');
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
}
