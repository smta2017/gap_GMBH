<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use DB;

class Hotel extends Model
{
    use SoftDeletes, HasFactory;
    public const IMAGE_PATH = 'images/eggheads';
    
    protected $fillable = [
        "company_id",
        "name",
        "ref_id",
        'tui_ref_code',
        'giata_code',
        "letter_code",
        "number_of_rooms",
        "hotel_rating",
        "website_description",
        "internal_description",

        "active",
        "show_website",
        "direct_contract",
        "via_dmc",
        "handler_type_id",
        "handler_id",

        "delegate_name",
        "delegate_email",
        "delegate_mobile_number",
        "delegate_user_id",
        "assigned_user_id",

        "region_id",
        "country_id",
        "city_id",
        "street",
        "postal_code",
        "location_link",
        "latitude",
        "longitude",
        "phone",
        "fax",
        "email",

        "payee",
        "is_payee_only",
        "payee_key_created",
        "bank",
        "bank_location",
        "account_number",
        "swift_code",
        "iban",


        "leader_offer",
        "leader_offer_number",
        "leader_offer_notes",
        "pro_leader_offer",
        "pro_leader_offer_number",
        "pro_leader_offer_notes",
        "junior",
        "junior_ratio",
        "junior_notes",
        "travel_agent",
        "travel_agent_ratio",
        "travel_agent_notes",
        "president",
        "president_ratio",
        "president_notes",
        "pro",
        "pro_ratio",
        "pro_notes",
        
        "is_golf_globe",
        "is_company_address",
        
        "reservation_email",
        "booking_accounting_id",

        "has_golf_course",
        "golf_desk",
        "golf_shuttle",
        "storage_room",

        'published_at',
        'top',

        "booking_code",
        "davinci_booking_code",
        'area_id',
        'notes',
        "created_by",
        "updated_by",
        "deleted_by",
        "website"
    ];

    public function get_pagination()
    {

        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $order = (in_array(request()->order, ['ASC', 'DESC'])) ? request()->order : 'DESC';
        $orderBy = request()->input('order_by');

        $results = $this->query();

        if(request()->search)
        {
            $results = $results->where('id', request()->search)
                                ->orWhere('name', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('ref_id', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('tui_ref_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('giata_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('letter_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('website_description', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('booking_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('davinci_booking_code', 'LIKE', '%' . request()->search . '%')
                                ->orWhere('website_description', 'LIKE', '%' . request()->search . '%')
                                ->orWhereHas('company', function($query) {
                                    $query->where('name', 'LIKE', '%' . request()->search . '%');
                                })
                                ->orWhereHas('region.translations', function($query) {
                                    $query->where('name', 'LIKE', '%' . request()->search . '%');
                                })
                                ->orWhereHas('country.translations', function($query) {
                                    $query->where('name', 'LIKE', '%' . request()->search . '%');
                                })
                                ->orWhereHas('city.translations', function($query) {
                                    $query->where('name', 'LIKE', '%' . request()->search . '%');
                                });
        }

        if(isset(request()->publish))
        {
            if(request()->publish =='Unpublished'){
                $results->where('show_website',0);
            }
            if (request()->publish == 'Pending Publish') {
                $results->where(function ($query) {
                    $query->where(function ($query2) {
                        $query2->where('show_website', 1);
                        $query2->where('updated_at', '>', \DB::raw('published_at'));
                    })->orWhere(function ($query2) {
                        $query2->where('show_website', 1);
                        $query2->whereNull('published_at');
                    });
                });
            }
            if(request()->publish =='Published'){
                $results->where(function ($query){
                    $query->where('show_website',1);
                    $query->whereRaw('updated_at <= published_at');
                });
            }
        }

        if(request()->company_id)
        {
            $results = $results->where('company_id', request()->company_id);
        }

        if(request()->company_id_list)
        {
            $results = $results->whereIn('company_id', request()->company_id_list);
        }

        if(request()->region_id)
        {
            $results = $results->where('region_id', request()->region_id);
        }

        if(request()->region_id_list)
        {
            $results = $results->whereIn('region_id', request()->region_id_list);
        }

        if(request()->country_id)
        {
            $results = $results->where('country_id', request()->country_id);
        }

        if(request()->country_id_list)
        {
            $results = $results->whereIn('country_id', request()->country_id_list);
        }

        if(request()->city_id)
        {
            $results = $results->where('city_id', request()->city_id);
        }

        if(request()->area_id)
        {
            $results = $results->where('area_id', request()->area_id);
        }

        if(request()->city_id_list)
        {
            $results = $results->whereIn('city_id', request()->city_id_list);
        }

        if(isset(request()->is_golf_globe))
        {
            $results = $results->where('is_golf_globe', request()->is_golf_globe);
        }

        if(isset(request()->tui_ref_code))
        {
            $results = $results->where('tui_ref_code', request()->tui_ref_code);
        }

        if(isset(request()->giata_code))
        {
            $results = $results->where('giata_code', request()->giata_code);
        }

        if(isset(request()->ref_id))
        {
            $results = $results->where('ref_id', request()->ref_id);
        }

        if(request()->input('booking_code'))
        {
            $results = $results->where('booking_code',  request()->input('booking_code'));
        }

        if(isset(request()->giata_code))
        {
            $results = $results->where('giata_code', request()->giata_code);
        }

        if(isset(request()->show_website))
        {
            $results = $results->where('show_website', request()->show_website);
        }

        if(isset(request()->top))
        {
            $results = $results->where('top', request()->top);
        }

        if($orderBy == 'hotel_id')
        {
            $results->orderBy('id', $order);
        }

        if($orderBy == 'hotel_name')
        {
            $results->orderBy('name', $order);
        }

        if($orderBy == 'letter_code')
        {
            $results->orderBy('letter_code', $order);
        }

        if($orderBy == 'ref_id')
        {
            $results->orderBy('ref_id', $order);
        }

        if($orderBy == 'region_id')
        {
            $results->orderBy('region_id', $order);
        }

        if($orderBy == 'country_id')
        {
            $results->orderBy('country_id', $order);
        }

        if($orderBy == 'city_id')
        {
            $results->orderBy('city_id', $order);
        }
        // dd( $results->toSql());
        return $results->paginate($pagination);

        
        // $requestPagination = request()->input('pagination');
        // $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        // $searchQuery = request()->input('search');

        // return $this->where($filter)->where(function ($query) use ($searchQuery){

        //     $columns = Schema::getColumnListing('hotels');
        //     foreach($columns as $column)
        //     {
        //         $query->orWhere($column, 'LIKE', '%' . $searchQuery . '%');
        //     }
            
        // })->paginate($pagination);
    }

    public function get_hotel_for_request()
    {
        $request = request();

        $results = $this;

        if(isset($request->search))
        {
            $results = $results->where(function($q) use ($request){
                $q->where('name', 'LIKE', '%' . $request->search . '%')->orWhere('booking_code', 'LIKE', '%' . $request->search . '%')
                ->orWhere('davinci_booking_code', 'LIKE', '%' . $request->search . '%');
            });
        }

        if(!$request->travel_agency_id || !isset($request->city_id) || !isset($request->search)){
            return $results->whereNull('id')->get();
        }
          
        if(isset($request->city_id))
        {
            $results = $results->where('city_id', $request->city_id);
        }

        if($request->travel_agency_id)
        {
            $operators = \DB::table('travel_agency_tour_operator')->where('travel_agency_id', $request->travel_agency_id)->pluck('tour_operator_id')->toArray();
 
            $hotelIDs = \DB::table('hotel_tour_operator')->whereIn('tour_operator_id', $operators)->pluck('hotel_id')->toArray();
            
            $results = $results->whereIn('id', $hotelIDs);
        }
          
        return $results->get();

    }

    public function get_all()
    {
        $request = request();
        $filter = [];

        $results = $this;

        if(isset($request->search))
        {
            // array_push($filter, array('name', 'LIKE', '%' . $request->search . '%'));
            $results = $results->where(function($q) use ($request){
                $q->where('name', 'LIKE', '%' . $request->search . '%')->orWhere('booking_code', 'LIKE', '%' . $request->search . '%')
                ->orWhere('davinci_booking_code', 'LIKE', '%' . $request->search . '%');
            });
        }

        if(isset($request->city_id))
        {
            // array_push($filter, array('city_id', $request->city_id));
            $results = $results->where('city_id', $request->city_id);
        }

        if(isset($request->area_id))
        {
            array_push($filter, array('area_id', $request->area_id));
        }

        if(isset($request->country_id))
        {
            array_push($filter, array('country_id', $request->country_id));
        }

        if(isset($request->tui_ref_code))
        {
            array_push($filter, array('tui_ref_code', $request->tui_ref_code));
        }

        if(isset($request->giata_code))
        {
            array_push($filter, array('giata_code', $request->giata_code));
        }

        if(isset($request->ref_id))
        {
            array_push($filter, array('ref_id', $request->ref_id));
        }

        if(isset($request->is_golf_globe))
        {
            array_push($filter, array('is_golf_globe', $request->is_golf_globe));
        }

        if(isset($request->show_website))
        {
            array_push($filter, array('show_website', $request->show_website));
        }

        if(isset($request->city_id_list))
        {
            $results = $results->whereIn('city_id', $request->city_id_list);
        }
        
        if(request()->input('booking_code'))
        {
            $results = $results->where('booking_code',  request()->input('booking_code'));
        }
        
        // $user = request()->user();

        // if($user->details->company->company_type_id != '1')
        // {
        //     $childs = $user->childs->where('child_type_id', '4')->pluck('child_id')->toArray();
        //     $results = $results->whereIn('id', $childs);
        // }

        $results = $results->where($filter);

        if($request->travel_agency_id)
        {
            $operators = \DB::table('travel_agency_tour_operator')->where('travel_agency_id', $request->travel_agency_id)->pluck('tour_operator_id')->toArray();
 
            $hotelIDs = \DB::table('hotel_tour_operator')->whereIn('tour_operator_id', $operators)->pluck('hotel_id')->toArray();
            
            $results = $results->whereIn('id', $hotelIDs);
        }

        return $results->get();
    }

    public function servicesObject()
    {
        return $this->belongsToMany(Service::class, 'object_services', 'child_id', 'service_id')->withPivot('qty', 'fees', 'selected_option', 'notes');
    }

    public function services()
    {
        return $this->servicesObject()->whereIn('object_services.type', Service::HOTEL_SERVICES_LIST)->orderBy('sorted');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'hotel_id');
    }

    public function golfcourses()
    {
        return $this->belongsToMany(GolfCourse::class, 'hotel_golfcourse');
    }

    public function ownedgolfcourses()
    {
        return $this->golfcourses()->where('type', 'owned');
    }

    public function relatedgolfcourses()
    {
        return $this->golfcourses()->where('type', 'related');
    }

    public function touroperators()
    {
        return $this->belongsToMany(TourOperator::class, 'hotel_tour_operator', 'hotel_id', 'tour_operator_id');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'hotel_facility')->withPivot(["number"]);
    }

    public function boards()
    {
        return $this->belongsToMany(Board::class, 'hotel_board');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'hotel_tag');
    }

    public function fields()
    {
        return $this->morphMany(Field::class, 'fieldable');
    }
    
    public function relatedHotels()
    {
        return $this->belongsToMany(Hotel::class, 'related_hotels','hotel_id','related_hotel_id');
    }

    public function cityfeaturedHotels()
    {
        return $this->belongsToMany(City::class, 'city_featured_hotel');
    }

    public function countryfeaturedHotels()
    {
        return $this->belongsToMany(Hotel::class, 'country_featured_hotel');
    }

    public function productServiceHotels()
    {
        return $this->belongsToMany(Hotel::class, 'product_service_hotel');
    }
    
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function imagesFullData()
    {
        return $this->images()->select('id', DB::raw("CONCAT('".asset('images/eggheads')."', '/', file_name) AS file_name"), 'is_main','alt','original_file_name', 'size', 'rank')->orderBy('rank');
    }   

    public function get_main_image()
    {
        $image = $this->images()->where('is_main', '1')->first();
        if($image)
        {
            return asset('images/eggheads') . '/' . rawurlencode($image->file_name);
        }
    }

    public function imagesFullDataURLEncode()
    {
        $images = $this->imagesFullData;

        foreach($images as $image)
        {
            $imageParts = explode('/', $image->file_name);

            $imageNameEncode = $imageParts[count($imageParts) -1];

            $image->file_name = asset('images/eggheads') . '/' . rawurlencode($imageNameEncode);
        }

        return $images;
    }
    
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function links()
    {
        return $this->morphMany(Link::class, 'linkable');
    }

    public function linksHDImages()
    {
        return $this->links()->where('type', 'hd_images')->select('id', "link");
    }  

    public function linksLogoImages()
    {
        return $this->links()->where('type', 'logo_images')->select('id', "link");
    }  

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function bookingaccounting()
    {
        return $this->belongsTo(Company::class, 'booking_accounting_id');
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
    
    public function handlertype()
    {
        return $this->belongsTo(CompanyType::class, 'handler_type_id');
    }

    public function handler()
    {
        return $this->belongsTo(Company::class, 'handler_id');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitieable');
    }

    public function translations()
    {
        return $this->hasMany(HotelTranslation::class, 'hotel_id');
    }

    public function requestProductHandler()
    {
        return $this->hasMany(RequestProduct::class, 'service_handler_id')->where('service_handler_type_id', '4');
    }

    public function DaviniciCodes()
    {
        return $this->morphMany(DaviniciCode::class, 'codeable');
    }
    

    public function teeTimesQuery()
    {
        $teeTimes = RequestProductTeeTime::join('request_products', 'request_products.id', 'request_product_tee_times.request_product_id')
                                        ->where('request_products.service_handler_type_id', '4')
                                        ->where('request_products.service_handler_id', $this->id)
                                        ->get();    
        return $teeTimes;                          
    }

    public function teeTimes()
    {
        return $this->hasManyThrough(RequestProductTeeTime::class, RequestProduct::class, 'service_handler_id', 'request_product_id')->where('request_products.service_handler_type_id', '4');
    }

    public function isPublishRequired()
    {
        if($this->updated_at > $this->published_at)
        {
            return true;
        }
        return false;
    }

    public function updateUpdatedAt()
    {
        $new_date =  \Carbon\Carbon::now()->format('Y-m-d H:i:s') ;
        Hotel::find($this->id)->update([
            'updated_at' =>$new_date
        ]);
    }

    public function publishColumn()
    {
        $publish="";
        if (!$this->show_website) {
             $publish='Unpublished';
        }elseif ($this->show_website && $this->isPublishRequired()) {
            $publish='Pending Publish';
        }elseif($this->show_website && !$this->isPublishRequired()){
             $publish='Published';
        }
        return $publish;
    }

}
