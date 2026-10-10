<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use DB;

class GolfCourse extends Model
{
    use SoftDeletes, HasFactory;
    
    public const GOLF_COURSE_SERVISE_TYPE = 'Golf Course';
    public const IMAGE_PATH = 'images/eggheads';

    
    protected $fillable = [
        "company_id",
        "hotel_id",
        "name",
        "ref_id",
        'tui_ref_code',
        'giata_code',
        "letter_code",
        "golf_course_style_id",
        "website_description",
        "internal_description",
        "designer",
        "active",
        "show_website",
        "direct_contract",
        "via_dmc",
        "via_hotel",
        "handler_type_id",
        "handler_id",
        "length_men",
        "length_women",
        "par_men",
        "par_women",
        "holes",
        "course_rating",
        "club_rating",
        "slope_from",
        "slope_to",
        "academy",
        "pros",
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

        "is_company_address",

        "payee_key_created",
        "bank",
        "bank_location",
        "account_number",
        "swift_code",
        "iban",

        "start_frequency",
        "start_gift",

        "membership",
        "hcp_men",
        "hcp_women",
        'start_time',
        'end_time',
        'published_at',
        'top',

        "booking_code",
        "davinci_booking_code",
        'area_id',
        "created_by",
        "updated_by",
        "updated_at",
        "deleted_by",
        "website_link"
    ];

    public function get_pagination($filter)
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        


        $searchQuery = request()->input('search');
        
        $results = $this->query();

          
        $user = request()->user();




        $results->where($filter);

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
        
        if ($searchQuery) {
            $results->orWhereHas('country.translations',function($q) use ($searchQuery){
                $q->where('name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orWhereHas('city.translations',function($q) use ($searchQuery){
                $q->where('name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orWhereHas('region.translations',function($q) use ($searchQuery){
                $q->where('name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orWhereHas('area.translations',function($q) use ($searchQuery){
                $q->where('name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orWhere(function ($query) use ($searchQuery){
                $columns = Schema::getColumnListing('golf_courses');
                foreach($columns as $column)
                {
                    $query->orWhere($column, 'LIKE', '%' . $searchQuery . '%');
                }
            });
        }

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '3')->pluck('child_id')->toArray();
             $results->whereIn('id', $childs);
        }

        // dd($results->toSql());
        return $results->paginate($pagination); 
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'golf_course_facility')->withPivot(["number"]);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'golf_course_tag');
    }

    public function servicesObject()
    {
        return $this->belongsToMany(Service::class, 'object_services', 'child_id', 'service_id')->withPivot('qty', 'fees', 'selected_option', 'notes');
    }

    public function relatedGolfCourses()
    {
        return $this->belongsToMany(GolfCourse::class, 'related_golf_courses','golf_course_id','related_golf_course_id');
    }

    public function services()
    {
        return $this->servicesObject()->where('object_services.type', 'Golf Course');
    }

    public function trainings()
    {
        return $this->servicesObject()->where('object_services.type', 'Training');
    }

    public function difficulties()
    {
        return $this->belongsToMany(Difficulty::class, 'golf_course_difficulty')->where('status',1);
    }

    public function terrains()
    {
        return $this->belongsToMany(Terrain::class, 'golf_course_terrain');
    }

    public function playables()
    {
        return $this->belongsToMany(Playable::class, 'golf_course_playable');
    }

    public function dresses()
    {
        return $this->belongsToMany(DressCode::class, 'golf_course_dress_code');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function fields()
    {
        return $this->morphMany(Field::class, 'fieldable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function DaviniciCodes()
    {
        return $this->morphMany(DaviniciCode::class, 'codeable');
    }

    public function imagesFullData()
    {
        return $this->images()->select('id', DB::raw("CONCAT('".asset('images/eggheads')."', '/', file_name) AS file_name"), 'is_main','alt','original_file_name', 'size', 'rank')->orderBy('rank');
    }

    public function cityfeaturedGolfCourses()
    {
        return $this->belongsToMany(City::class, 'city_featured_golf_course');
    }

    public function countryfeaturedGolfCourses()
    {
        return $this->belongsToMany(GolfCourse::class, 'country_featured_golf_course');
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
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
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function style()
    {
        return $this->belongsTo(GolfCourseStyle::class, 'golf_course_style_id');
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
        return $this->hasMany(GolfCourseTranslation::class, 'golf_course_id');
    }

    public function requestProductHandler()
    {
        return $this->hasMany(RequestProduct::class, 'service_handler_id')->where('service_handler_type_id', '3');
    }

    public function teeTimesQuery()
    {
        $teeTimes = RequestProductTeeTime::join('request_products', 'request_products.id', 'request_product_tee_times.request_product_id')
                                        ->where('request_products.service_handler_type_id', '3')
                                        ->where('request_products.service_handler_id', $this->id)
                                        ->get();    
        return $teeTimes;                          
    }

    public function teeTimes()
    {
        return $this->hasManyThrough(RequestProductTeeTime::class, RequestProduct::class, 'service_handler_id', 'request_product_id')->where('request_products.service_handler_type_id', '3');
    }

    public function get_teeTimes()
    {
        // return $this->hasManyThrough(RequestProductTeeTime::class, RequestProduct::class, 'service_handler_id', 'request_product_id')->where('request_products.service_handler_type_id', '3')->get();
        return $this->hasMany(RequestProductTeeTime::class,  'golf_course_id')->whereHas('requestProduct',function($q){
            $q->where('service_handler_type_id',3)->whereHas('destination.request');
        })->get();
    }

    public function handledTeeTimes()
    {
        return $this->hasMany(RequestProductTeeTime::class,  'golf_course_id')->whereHas('requestProduct',function($q){
            $q->where('service_handler_type_id',3);
        });
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
        GolfCourse::find($this->id)->update([
            'updated_at' => \Carbon\Carbon::now()
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
