<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Country;
use App\Models\Image;
use App\Models\Language;
use App\Models\GolfCourse;
use App\Models\Hotel;
use App\Models\Product;
use App\Models\HotelProduct;
use App\Models\GolfHoliday;
use App\Models\DestinationFieldType;
use App\Http\Resources\BasicResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\CityDetailsResource;
use DB;
use File;

class CityController extends Controller
{
    public function index()
    {
        $country_id = request()->input('country_id');

        $cities = new City();

        
        $citiesData = CityResource::collection($cities->get_all());

        return response()->json([
            'status' => true,
            'cities' => $citiesData
        ]);
    }

    public function show($id)
    {
        $city = City::findOrFail($id);

        $cityData = new CityDetailsResource($city);

        return response()->json([
            'status' => true,
            'city' => $cityData,
        ]);
    }

    public function get_field_types()
    {
        $types = BasicResource::collection(DestinationFieldType::where('status', '1')->get());

        return response()->json([
            'status' => true,
            'field_types' => $types
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'status' => 'required|in:1,0',
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',

            'fields' => 'array',
            'fields.*.translations' => 'array',
            'fields.*.translations.*.language_id' => 'required|exists:languages,id',

            'faqs' => 'array',
            'faqs.*.question' => 'required',
            'faqs.*.answer' => 'required',
            
            'testimonies' => 'array',
            'testimonies.*.name' => 'required',
            'testimonies.*.text' => 'required',

            'featured_golf_courses' => 'array',
            'featured_golf_courses.*' => 'exists:golf_courses,id',

            'featured_hotels' => 'array',
            'featured_hotels.*' => 'exists:hotels,id',

            'featured_products' => 'array',
            'featured_products.*' => 'exists:products,id',

            'featured_hotel_products' => 'array',
            'featured_hotel_products.*' => 'exists:hotel_products,id',

            'featured_golf_holidays' => 'array',
            'featured_golf_holidays.*' => 'exists:golf_holidays,id',
        ]);

        try {
            DB::beginTransaction();

            $city = City::create([
                'name' => $request->name,
                'code' => $request->code,
                'status' => $request->status,
                'region_id' => $request->region_id,
                'country_id' => $request->country_id,
            ]);

            if(is_array($request->fields) && count($request->fields) > 0)
            {
                foreach($request->fields as $fieldData)
                {
                    $field = $city->fields()->create($fieldData);

                    if(isset($fieldData['translations']) && is_array($fieldData['translations']) && count($fieldData['translations']) > 0)
                    {
                        foreach($fieldData['translations'] as $translation)
                        {
                            $language = Language::findOrFail($translation['language_id']);
        
                            $translateDescription = (isset($translation['description'])) ? $translation['description'] : null;
        
                            $field->translations()->create([
                                'language_id' => $language->id,
                                'locale' => $language->code,
                                'description' => $translateDescription,
                            ]);
                        }
                    }
                }
            }

            if(is_array($request->faqs) && count($request->faqs) > 0)
            {
                foreach($request->faqs as $faqData)
                {
                    $faq = $city->faqs()->create($faqData);
                }
            }

            if(is_array($request->featured_golf_courses) && count($request->featured_golf_courses) > 0)
            {
                $courses = GolfCourse::whereIn('id', $request->featured_golf_courses)->get();

                foreach($courses as $course)
                {
                    $city->featuredGolfCourses()->save($course);
                }
            }

            if(is_array($request->featured_hotels) && count($request->featured_hotels) > 0)
            {
                $hotels = Hotel::whereIn('id', $request->featured_hotels)->get();

                foreach($hotels as $hotel)
                {
                    $city->featuredHotels()->save($hotel);
                }
            }

            if(is_array($request->featured_products) && count($request->featured_products) > 0)
            {
                $products = Product::whereIn('id', $request->featured_products)->get();

                foreach($products as $p)
                {
                    $city->featuredProducts()->save($p);
                }
            }

            if(is_array($request->featured_hotel_products) && count($request->featured_hotel_products) > 0)
            {
                $products = HotelProduct::whereIn('id', $request->featured_hotel_products)->get();

                foreach($products as $p)
                {
                    $city->featuredHotelProducts()->save($p);
                }
            }

            if(is_array($request->featured_golf_holidays) && count($request->featured_golf_holidays) > 0)
            {
                $products = GolfHoliday::whereIn('id', $request->featured_golf_holidays)->get();

                foreach($products as $p)
                {
                    $city->featuredGolfHolidays()->save($p);
                }
            }

            if ($request->hasFile('main_image')) {

                $imageName = \Str::random(6) . time().'.'.$request->main_image->extension();  
         
                $request->main_image->move(public_path('images/cities'), $imageName);
    
                $image = new Image;
                $image->file_name = $imageName;
    
                $city->images()->create(['file_name' => $imageName, 'is_main' => '1']);
                
            }

            if ($request->hasFile('images')) {
            
                foreach($request->file('images') as $image)
                {
    
                    $imageName = \Str::random(6) . time().'.'.$image->extension();  
         
                    $image->move(public_path('images/counties'), $imageName);
        
                    $image = new Image;
                    $image->file_name = $imageName;
        
                    $city->images()->create(['file_name' => $imageName]);
                }
            }

            DB::commit();

            $cityData = new CityDetailsResource($city);

            return response()->json([
                'status' => true,
                'city' => $cityData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_testimonies($id, Request $request)
    {
        $city = City::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required',
            'text' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = $request->user();

            $data['created_by'] = $user->id;

            $test = $city->testimonies()->create($data);

            if ($request->hasFile('image')) 
            {
                $imageName = \Str::random(6) . time().'.'.$request->image->extension();  
         
                $request->image->move(public_path('images/testimonies'), $imageName);
    
                $test->image()->create(['file_name' => $imageName]);
                
            }
            DB::commit();

            $cityData = new CityDetailsResource($city);

            return response()->json([
                'status' => true,
                'city' => $cityData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_testimonies_bulk($id, Request $request)
    {
        $city = City::findOrFail($id);

        $validated = $request->validate([
            'testimonies' => 'array|min:1',
            'testimonies.*.name' => 'required',
            'testimonies.*.text' => 'required',
        ]);

        try {
            DB::beginTransaction();

            foreach($request->testimonies as $testimony)
            {
                $test = $city->testimonies()->create($testimony);

                $requestData = new Illuminate\Http\Request($testimony);

                if ($requestData->hasFile('image')) 
                {
                    $imageName = \Str::random(6) . time().'.'.$requestData->image->extension();  
             
                    $requestData->image->move(public_path('images/testimonies'), $imageName);
        
                    $test->image()->create(['file_name' => $imageName]);
                    
                }
            }

            DB::commit();

            $cityData = new CityDetailsResource($city);

            return response()->json([
                'status' => true,
                'city' => $cityData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    public function update($id, Request $request)
    {
        $city = City::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'status' => 'required|in:1,0',
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',

            'fields' => 'array',
            'fields.*.translations' => 'array',
            'fields.*.translations.*.language_id' => 'required|exists:languages,id',

            'faqs' => 'array',
            'faqs.*.question' => 'required',
            'faqs.*.answer' => 'required',

            'featured_golf_courses' => 'array',
            'featured_golf_courses.*' => 'exists:golf_courses,id',

            'featured_hotels' => 'array',
            'featured_hotels.*' => 'exists:hotels,id',

            'featured_products' => 'array',
            'featured_products.*' => 'exists:products,id',

            'featured_hotel_products' => 'array',
            'featured_hotel_products.*' => 'exists:hotel_products,id',

            'featured_golf_holidays' => 'array',
            'featured_golf_holidays.*' => 'exists:golf_holidays,id',
        ]);

        try {
            DB::beginTransaction();

            $city->update([
                'name' => $request->name,
                'code' => $request->code,
                'status' => $request->status,
                'region_id' => $request->region_id,
                'country_id' => $request->country_id,
            ]);

            if(is_array($request->fields))
            {
                $city->fields()->forceDelete();
                foreach($request->fields as $fieldData)
                {  
                    $field = $city->fields()->create($fieldData);
        
                    if(isset($fieldData['translations']) && is_array($fieldData['translations']) && count($fieldData['translations']) > 0)
                    {
                        foreach($fieldData['translations'] as $translation)
                        {
                            $language = Language::findOrFail($translation['language_id']);
        
                            $translateDescription = (isset($translation['description'])) ? $translation['description'] : null;
        
                            $field->translations()->create([
                                'language_id' => $language->id,
                                'locale' => $language->code,
                                'description' => $translateDescription,
                            ]);
                        }
                    }
                }
            }

            if(is_array($request->faqs))
            {
                $city->faqs()->forceDelete();
                foreach($request->faqs as $faqData)
                {
                    $faq = $city->faqs()->create($faqData);
                }
            }

            if(is_array($request->featured_golf_courses))
            {
                $courses = GolfCourse::whereIn('id', $request->featured_golf_courses)->get();

                $city->featuredGolfCourses()->detach();
                foreach($courses as $course)
                {
                    $city->featuredGolfCourses()->save($course);
                }
            }

            if(is_array($request->featured_hotels))
            {
                $hotels = Hotel::whereIn('id', $request->featured_hotels)->get();

                $city->featuredHotels()->detach();
                foreach($hotels as $hotel)
                {
                    $city->featuredHotels()->save($hotel);
                }
            }

            if(is_array($request->featured_cities))
            {
                $cities = City::whereIn('id', $request->featured_cities)->get();

                $city->featuredCities()->detach();
                foreach($cities as $c)
                {
                    $city->featuredCities()->save($c);
                }
            }

            if(is_array($request->featured_products))
            {
                $products = Product::whereIn('id', $request->featured_products)->get();

                $city->featuredProducts()->detach();
                foreach($products as $p)
                {
                    $city->featuredProducts()->save($p);
                }
            }

            if(is_array($request->featured_hotel_products))
            {
                $products = HotelProduct::whereIn('id', $request->featured_hotel_products)->get();

                $city->featuredHotelProducts()->detach();
                foreach($products as $p)
                {
                    $city->featuredHotelProducts()->save($p);
                }
            }

            if(is_array($request->featured_golf_holidays))
            {
                $products = GolfHoliday::whereIn('id', $request->featured_golf_holidays)->get();

                $city->featuredGolfHolidays()->detach();
                foreach($products as $p)
                {
                    $city->featuredGolfHolidays()->save($p);
                }
            }

            DB::commit();

            $cityData = new CityDetailsResource($city);

            return response()->json([
                'status' => true,
                'city' => $cityData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function upload_images(Request $request, $id)
    {
        $validated = $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deleted_images' => 'array',
            'deleted_images.*' => 'exists:images,id',
        ]);
        
        $city = City::findOrFail($id);

        if(is_array($request->deleted_images))
        {   
            foreach($city->images()->whereIn('id', $request->deleted_images)->get() as $item)
            {
                $d_image_path = public_path('images/cities') . '/' . $item->file_name;
                if(File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $item->delete();
            }
            
        }

        if ($request->hasFile('main_image')) {

            $imageName = \Str::random(6) . time().'.'.$request->main_image->extension();  
     
            $request->main_image->move(public_path('images/cities'), $imageName);

            $city->images()->create(['file_name' => $imageName, 'is_main' => '1']);
            
        }

        if ($request->hasFile('images')) {
            
            foreach($request->file('images') as $image)
            {
                $imageName = \Str::random(6) . time().'.'.$image->extension();  
     
                $image->move(public_path('images/cities'), $imageName);
        
                $city->images()->create(['file_name' => $imageName]);
                 
            }
        }

        $cityData = new CityDetailsResource($city);

        return response()->json([
            'status' => true,
            'city' => $cityData
        ]);

    }

    public function delete_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);
        
        $city = City::findOrFail($id);
        
        $imageToDelete = $city->images()->where('id', $request->image_id)->first();
        
        $d_image_path = public_path('images/cities') . '/' . $imageToDelete->file_name;
        if(File::exists($d_image_path)) {
            File::delete($d_image_path);
        }

        $imageToDelete->delete();

        $cityData = new CityDetailsResource($city);

        return response()->json([
            'status' => true,
            'city' => $cityData
        ]);

    }

    public function change_main_image(Request $request, $id)
    {
        $city = City::findOrFail($id);
        
        $validated = $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);
        
        foreach($city->images as $img)
        {
            $img->update([
                'is_main' => '0'
            ]);
        }

        $image = Image::find($request->image_id)->update([
            'is_main' => '1'
        ]);


        $cityData = new cityDetailsResource($city);

        return response()->json([
            'status' => true,
            'city' => $cityData
        ]);

    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);

        try {
            DB::beginTransaction();

            $city->fields()->forceDelete();
            $city->faqs()->forceDelete();

            $city->delete();

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
