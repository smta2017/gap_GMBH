<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Country extends Model
{
    use SoftDeletes, HasFactory;

    public const IMAGE_PATH = 'images/countries';

    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'region_id',
        'language_id',
        'currency_id',
        'status',
        'show_website',
        'published_at',
        'top',
        'related_countries',
        'updated_at'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function get_all()
    {
        $region_id = request()->input('region_id');
        $not_ignore_empty_cities = request()->input('not_ignore_empty_cities');

        $object = request()->input('object');

        $countries = $this;

        if(! ( isset($not_ignore_empty_cities) && $not_ignore_empty_cities == '1'))
        {
            $countries = $countries->whereHas('cities');
        }

        $countries = $countries->when($region_id, function($query) use ($region_id){
             return $query->where('region_id', $region_id);
        });

        if(isset($object) && in_array($object, ['GolfCourses', 'Hotels', 'DMCs', 'TravelAgencies', 'TourOperators', 'Services', 'Products', 'HotelProducts', 'GolfHolidays']))
        {
            $countries = $countries->whereHas($object);
        }

        if(isset(request()->show_website))
        {
            $countries = $countries->where('show_website', request()->show_website);
        }

        return $countries->get();
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
        return $this->hasMany(ProductService::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, ProductService::class, 'country_id', 'service_id');
    }

    public function HotelProducts()
    {
        return $this->hasManyThrough(HotelProduct::class, ProductService::class, 'country_id', 'service_id');
    }

    public function GolfHolidays()
    {
        return $this->hasManyThrough(GolfHoliday::class, ProductService::class, 'country_id', 'service_id');
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function imagesFullData()
    {
        return $this->images()->select('id', DB::raw("CONCAT('".asset('images/countries')."', '/', file_name) AS file_name"), 'is_main','alt','original_file_name', 'size', 'rank')->orderBy('rank');
    }

    public function get_main_image()
    {
        $image = $this->images()->where('is_main', '1')->first();
        if($image)
        {
            return asset('images/countries') . '/' . rawurlencode($image->file_name);
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
        return $this->belongsToMany(GolfCourse::class, 'country_featured_golf_course');
    }

    public function featuredHotels()
    {
        return $this->belongsToMany(Hotel::class, 'country_featured_hotel');
    }

    public function featuredCities()
    {
        return $this->belongsToMany(City::class, 'country_featured_city');
    }

    public function featuredProducts()
    {
        return $this->belongsToMany(Product::class, 'country_featured_product');
    }

    public function featuredHotelProducts()
    {
        return $this->belongsToMany(HotelProduct::class, 'country_featured_hotel_product');
    }

    public function featuredGolfHolidays()
    {
        return $this->belongsToMany(GolfHoliday::class, 'country_featured_golf_holiday');
    }

    public function get_golf_courses_number()
    {
        return GolfCourse::where('country_id', $this->id)->count();
    }

    public function get_hotels_number()
    {
        return Hotel::where('country_id', $this->id)->count();
    }

    public function get_products_number()
    {
        $services = ProductService::where('country_id', $this->id)->pluck('id')->toArray();
        return Product::whereIn('service_id', $services)->count();
    }

    public function get_hotel_products_number()
    {
        $services = ProductService::where('country_id', $this->id)->pluck('id')->toArray();
        return HotelProduct::whereIn('service_id', $services)->count();
    }

    public function get_golf_holidays_number()
    {
        $services = ProductService::where('country_id', $this->id)->pluck('id')->toArray();
        return GolfHoliday::whereIn('service_id', $services)->count();
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
        Country::find($this->id)->update([
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
