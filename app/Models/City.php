<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class City extends Model
{
    use SoftDeletes, HasFactory;
    public const IMAGE_PATH = 'images/cities';

    protected $fillable = [
        'name',
        'code',
        'region_id',
        'country_id',
        'language_id',
        'status',
        'show_website',
        'published_at',
        'top',
        'related_regions',
        'updated_at'
    ];
 
   
    public function get_pagination($filter)
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;


        $searchQuery = request()->input('search');
        
        $results = $this->query();

          
        $user = request()->user();


        $results->where($filter);
        // $results->where(function ($query) {
        //     $query->where(function ($query2) {
        //         $query2->where('show_website', 1);
        //         $query2->where('updated_at', '>', \DB::raw('published_at'));
        //     })->orWhere(function ($query2) {
        //         $query2->where('show_website', 1);
        //         $query2->whereNull('published_at');
        //     });
          
        // });

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
            $results->orWhereHas('translations',function($q) use ($searchQuery){
                $q->where('name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orWhereHas('country.translations',function($q) use ($searchQuery){
                $q->where('name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orWhere('code', 'LIKE', '%' . $searchQuery . '%');
        }

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '3')->pluck('child_id')->toArray();
             $results->whereIn('id', $childs);
        }
        // dd($results->toSql());
        return $results->paginate($pagination); 
    }

    public function get_all()
    {
        $country_id = request()->input('country_id');
        $search = request()->input('search');
        
        $object = request()->input('object');

        $code = request()->input('code');

        $cities = $this;

        $cities = $cities->when(($country_id || $search), function($query) use ($country_id, $search){

            if($country_id)
            {
                $query = $query->where('country_id', $country_id);
            }
            if($search)
            {
                $query = $query->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('code', 'LIKE', '%' . $search . '%')
                                ->orWhereHas('country', function($s) use ($search){
                                    $s->where('name', 'LIKE', '%' . $search . '%');
                                });
            }
            return $query;
        });

        if(isset($object) && in_array($object, ['GolfCourses', 'Hotels', 'DMCs', 'TravelAgencies', 'TourOperators', 'Services', 'Products', 'HotelProducts', 'GolfHolidays']))
        {
            $cities = $cities->whereHas($object);
        }

        if($code)
        {
            $cities = $cities->where('code', $code);
        }

        if(isset(request()->show_website))
        {
            $cities = $cities->where('show_website', request()->show_website);
        }

        return $cities->get();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }


    public function GolfCourses()
    {
        return $this->hasMany(GolfCourse::class);
    }

    public function Hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function DMCs()
    {
        return $this->hasMany(DMC::class);
    }

    public function TravelAgencies()
    {
        return $this->hasMany(TravelAgency::class);
    }

    public function TourOperators()
    {
        return $this->hasMany(TourOperator::class);
    }

    public function Services()
    {
        $servicesData = \DB::table('product_service_city')->where('city_id', $this->id)->pluck('product_service_id')->toArray();
        return ProductService::whereIn('id', $servicesData)->count();
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function products()
    {
        $servicesData = \DB::table('product_service_city')->where('city_id', $this->id)->pluck('product_service_id')->toArray();
        return Product::whereIn('service_id', $servicesData)->count();
    }

    public function HotelProducts()
    {
        $servicesData = \DB::table('product_service_city')->where('city_id', $this->id)->pluck('product_service_id')->toArray();
        return HotelProduct::whereIn('service_id', $servicesData)->count();
    }

    public function GolfHolidays()
    {
        $servicesData = \DB::table('product_service_city')->where('city_id', $this->id)->pluck('product_service_id')->toArray();
        return GolfHoliday::whereIn('service_id', $servicesData)->count();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function imagesFullData()
    {
        return $this->images()->select('id', DB::raw("CONCAT('".asset('images/cities')."', '/', file_name) AS file_name"), 'is_main','alt' ,'original_file_name', 'size', 'rank')->orderBy('rank');
    }

    public function get_main_image()
    {
        $image = $this->images()->where('is_main', '1')->first();
        if($image)
        {
            return asset('images/cities') . '/' . rawurlencode($image->file_name);
        }
    }

    public function fields()
    {
        return $this->morphMany(Field::class, 'fieldable');
    }

    public function faqs()
    {
        return $this->morphMany(Faq::class, 'faqable');
    }

    public function testimonies()
    {
        return $this->morphMany(Testimony::class, 'testimonyable');
    }

    public function featuredGolfCourses()
    {
        return $this->belongsToMany(GolfCourse::class, 'city_featured_golf_course');
    }

    public function featuredHotels()
    {
        return $this->belongsToMany(Hotel::class, 'city_featured_hotel');
    }

    public function featuredProducts()
    {
        return $this->belongsToMany(Product::class, 'city_featured_product');
    }

    public function featuredHotelProducts()
    {
        return $this->belongsToMany(HotelProduct::class, 'city_featured_hotel_product');
    }

    public function featuredGolfHolidays()
    {
        return $this->belongsToMany(GolfHoliday::class, 'city_featured_golf_holiday');
    }

    public function get_golf_courses_number()
    {
        return GolfCourse::where('city_id', $this->id)->count();
    }

    public function get_hotels_number()
    {
        return Hotel::where('city_id', $this->id)->count();
    }

    public function get_products_number()
    {
        $services = ProductService::where('city_id', $this->id)->pluck('id')->toArray();
        return Product::whereIn('service_id', $services)->count();
    }

    public function get_hotel_products_number()
    {
        $services = ProductService::where('city_id', $this->id)->pluck('id')->toArray();
        return HotelProduct::whereIn('service_id', $services)->count();
    }

    public function get_golf_holidays_number()
    {
        $services = ProductService::where('city_id', $this->id)->pluck('id')->toArray();
        return GolfHoliday::whereIn('service_id', $services)->count();
    }

    public function get_object_number()
    {        
        $object = request()->input('object');

        if(isset($object) && in_array($object, ['GolfCourses', 'Hotels', 'DMCs', 'TravelAgencies', 'TourOperators']))
        {
            return $this->$object->count();
        }

        if(isset($object) && in_array($object, ['Products', 'Services', 'HotelProducts', 'GolfHolidays']))
        {
            return $this->$object();
        }

        return 0;
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
        City::find($this->id)->update([
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }

    public function translations()
    {
        return $this->morphMany(BasicTranslation::class, 'basicable');
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
