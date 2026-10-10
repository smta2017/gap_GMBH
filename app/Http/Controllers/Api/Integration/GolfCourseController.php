<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GolfCourse;
use App\Models\GolfCourseStyle;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Facility;
use App\Models\Service;
use App\Models\ServiceProperty;
use App\Models\ServiceAddon;
use App\Models\ServiceDetails;

use App\Models\ObjectService;
use App\Models\ObjectServiceAddon;
use App\Models\ObjectServiceFeeDetails;
use App\Models\ObjectServiceProperty;

use App\Models\Difficulty;
use App\Models\Terrain;
use App\Models\DressCode;
use App\Models\Note;
use App\Models\Field;
use App\Models\FieldType;
use App\Models\Image;
use App\Models\ClubBrand;
use App\Models\Language;
use App\Http\Resources\GolfCourseResource;
use App\Http\Resources\GolfCourseDetailsResource;
use App\Http\Resources\CompanyTypeResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\BasicResource;
use DB;
use File;

class GolfCourseController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $golfcourses = new GolfCourse();
        
        $golfcourseData = GolfCourseResource::collection($golfcourses->where($filter)->get());

        return response()->json([
            'status' => true,
            'golfcourses' => $golfcourseData
        ]);
    }

    public function get_all()
    {
        $search = request()->input('search');
        $cityId = request()->input('city_id');
        $showWebsite = request()->input('show_website');
 
        $golfcourses = new Golfcourse();

        if($search)
        {
            $golfcourses = $golfcourses->where('name' , 'LIKE', '%' . $search . '%');
        }

        if($cityId)
        {
            $golfcourses = $golfcourses->where('city_id',  $cityId);
        }

        if(isset($showWebsite))
        {
            $golfcourses = $golfcourses->where('show_website',  $showWebsite);
        }

        return response()->json([
            'status' => true,
            'golfcourses' => $golfcourses->select(['id', 'name'])->get(),
        ]);
    }

    public function show($id)
    {
        $golfcourse = GolfCourse::findOrFail($id);
        $golfcourseData = new GolfCourseDetailsResource($golfcourse);

        return response()->json([
            'status' => true,
            'golfcourse' => $golfcourseData,
        ]);
    }
    
    public function get_styles()
    {
        $golfcoursestyles = GolfCourseStyle::select(['id', 'name'])->get();

        return response()->json([
            'status' => true,
            'styles' => $golfcoursestyles
        ]);
    }

    public function get_facilities()
    {
        $facilities = BasicResource::collection(Facility::where('type', 'Golf Course')->get());

        return response()->json([
            'status' => true,
            'facilities' => $facilities
        ]);
    }

    public function get_field_types()
    {
        $types = BasicResource::collection(FieldType::where('status', '1')->get());

        return response()->json([
            'status' => true,
            'field_types' => $types
        ]);
    }

    public function get_services()
    {
        $type = request()->type;

        $typeList = request()->type_list;

        $serviceObj = Service::where('active', '1');

        if($type)
        {
            $serviceObj = $serviceObj->where('type', $type);
        }

        if($typeList)
        {
            $serviceObj = $serviceObj->whereIn('type', $typeList);
        }

        $services = ServiceResource::collection($serviceObj->get());

        return response()->json([
            'status' => true,
            'services' => $services
        ]);
    }

    public function get_activities($id)
    {
        $golfcourse = GolfCourse::findOrFail($id);

        $activities = ActivityResource::collection($golfcourse->activities);

        return response()->json([
            'status' => true,
            'activities' => $activities
        ]);
    }

    public function get_basics()
    {

        $difficulties = BasicResource::collection(Difficulty::where('status', '1')->get());
        
        $terrains = BasicResource::collection(Terrain::where('status', '1')->get());

        $dresses = BasicResource::collection(DressCode::where('status', '1')->get());

        $facilities = BasicResource::collection(Facility::where('type', 'Golf Course')->where('status', '1')->get());

        $fieldTypes = BasicResource::collection(FieldType::where('status', '1')->get());

        $styles = GolfCourseStyle::select(['id', 'name'])->get();
        
        return response()->json([
            'status' => true,
            'difficulties' => $difficulties,
            'terrains' => $terrains,
            'dressed' => $dresses,
            'facilities' => $facilities,
            'styles' => $styles,
            'field_types' => $fieldTypes
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',
            // "golf_course_style_id" => 'required|exists:golf_course_styles,id',

            "active" => "required|in:0,1",
            "direct_contract" => "required|in:0,1",
            // "via_dmc" => "required|in:0,1",

            // "handler_type_id" => 'exists:company_types,id',
            // "handler_id" => 'exists:companies,id',
            
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // "location_link" => 'required',
            "email" => "required|email",

            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",

            // "length_men" => "required",
            // "length_women" => "required",
            // "par_men" => "required",
            // "par_women" => "required",
            // "holes" => 'required|in:9,18,27,36',
            // "course_rating" => "required",
            // "club_rating" => "required",
            // "academy" => "required|in:0,1",
            // "pros" => "required|in:0,1",

            // "payee" => "required|in:0,1",
            // "is_payee_only" => "required|in:0,1",
            // "payee_key_created" => "required|in:0,1",

            'facilities' => 'array',
            'facilities.*.id' => 'exists:facilities,id',

            'difficulties' => 'array',
            'difficulties.*' => 'exists:difficulties,id',
            'terrains' => 'array',
            'terrains.*' => 'exists:terrains,id',
            'dressed' => 'array',
            'dresses.*' => 'exists:dress_codes,id',

            'notes' => 'array',

            'fields' => 'array',
            'fields.*.translations' => 'array',
            'fields.*.translations.*.language_id' => 'required|exists:languages,id',

            'translations' => 'array',
            'translations.*.language_id' => 'required|exists:languages,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $golfcourse = $this->create_new_item($data);
 
            if(is_array($request->difficulties) && count($request->difficulties) > 0)
            {
                $difficulties = Difficulty::whereIn('id', $request->difficulties)->get();
                foreach($difficulties as $difficulty)
                {
                    $golfcourse->difficulties()->save($difficulty);
                }
            }
            if(is_array($request->terrains) && count($request->terrains) > 0)
            {
                $terrains = Terrain::whereIn('id', $request->terrains)->get();
                foreach($terrains as $terrain)
                {
                    $golfcourse->terrains()->save($terrain);
                }
            }
            if(is_array($request->dresses) && count($request->dresses) > 0)
            {
                $dresses = DressCode::whereIn('id', $request->dresses)->get();
                foreach($dresses as $dresse)
                {
                    $golfcourse->dresses()->save($dresse);
                }
            }

            if(is_array($request->facilities) && count($request->facilities) > 0)
            {
                foreach($request->facilities as $facility)
                {
                    if(isset($facility['id']) && isset($facility['number']))
                    {
                        $golfcourse->facilities()->attach([$facility['id'] => ['number' => $facility['number']]]);
                    }
                }
            }

            // if(is_array($request->notes) && count($request->notes) > 0)
            // {
            //     foreach($request->notes as $r_note)
            //     {
            //         $note = new Note;
            //         $note->title = $r_note;
        
            //         $golfcourse->notes()->create(['title' => $r_note]);
            //     }
            // }

            if(is_array($request->fields) && count($request->fields) > 0)
            {
                foreach($request->fields as $fieldData)
                {
                    $field = $golfcourse->fields()->create($fieldData);

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

            if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            {
                foreach($request->translations as $translation)
                {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;
                    $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
                    $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

                    $golfcourse->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName, 
                        'website_description' => $translateWebsiteDescription, 
                        'internal_description' => $translateInternalDescription, 
                    ]);
                }
            }

            if ($request->hasFile('images')) {
            
                foreach($request->file('images') as $image)
                {
    
                    $imageName = \Str::random(6) . time().'.'.$image->extension();  
         
                    $image->move(public_path('images/eggheads'), $imageName);
        
                    $image = new Image;
                    $image->file_name = $imageName;
        
                    $golfcourse->images()->create(['file_name' => $imageName]);
                }
            }

            DB::commit();

            $golfcourseData = new GolfCourseDetailsResource($golfcourse);

            return response()->json([
                'status' => true,
                'golfcourse' => $golfcourseData,
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_services($id, Request $request)
    {
        $golfcourse = GolfCourse::findOrFail($id);

        $validated = $request->validate([
            // 'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.selected_option' => 'required',
            'services.*.properties' => 'array',
            'services.*.properties.*.property_id' => 'exists:service_properties,id',
            'services.*.addons' => 'array',
            'services.*.addons.*.addon_id' => 'exists:service_addons,id',
            'services.*.addond.fee_details' => 'array',
            'services.*.addond.fee_details.*.fee_id' => 'exists:sertice_fee_details,id',
            'services.*.fee_details' => 'array',
            'services.*.fee_details.*.fee_id' => 'exists:service_fee_details,id',
        ]);

        try {
            DB::beginTransaction();

            if(is_array($request->services) && count($request->services) > 0)
            {

                $servicesData = $request->services;

                $serviceDataID = $servicesData[0]['service_id'];

                $serviceObj = Service::findOrFail($serviceDataID);


                ObjectService::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($serviceObj) {
                    $q->where('type', $serviceObj->type);
                })->forceDelete();
                ObjectServiceAddon::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($serviceObj) {
                    $q->where('type', $serviceObj->type);
                })->forceDelete();
                ObjectServiceProperty::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($serviceObj) {
                    $q->where('type', $serviceObj->type);
                })->forceDelete();
                ObjectServiceFeeDetails::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($serviceObj) {
                    $q->where('type', $serviceObj->type);
                })->forceDelete();


                foreach($request->services as $service)
                {
                    if(isset($service['active']))
                    {
                        $isServiceActive = $service['active'];
                    }else{
                        $isServiceActive = 0;
                    }
                    ObjectService::create([
                        'child_id' => $golfcourse->id,
                        'service_id' => $service['service_id'],
                        'type' => $serviceObj->type,
                        'qty' => $service['qty'],
                        'fees' => $service['fees'],
                        'selected_option' => $service['selected_option'],
                        'notes' => $service['notes'],
                        'active' => $isServiceActive,
                    ]);

                    if(isset($service['properties']) && is_array($service['properties']))
                    {
                        foreach($service['properties'] as $property)
                        {
                            if(isset($property['notes']))
                            {
                                $propertyNote = $property['notes'];
                            }else{
                                $propertyNote = null;
                            }
                            
                            ObjectServiceProperty::create([
                                'child_id' => $golfcourse->id,
                                'service_id' => $service['service_id'],
                                'service_property_id' => $property['property_id'],
                                'selected_option' => $property['selected_option'],
                                'notes' => $propertyNote,
                            ]);
                        }
                    }

                    if(isset($service['fee_details']) && is_array($service['fee_details']))
                    {
                        foreach($service['fee_details'] as $fee)
                        {
                            ObjectServiceFeeDetails::create([
                                    'child_id' => $golfcourse->id,
                                    'service_id' => $service['service_id'],
                                    'service_fees_details_id' => $fee['fee_id'],
                                    'qty' => $fee['qty'],
                                    'fees' => $fee['fees'],
                                    'unit' => $fee['unit'],                            
                                    'notes' => $fee['notes']
                            ]);
                        }
                    }

                    if(isset($service['addons']) && is_array($service['addons']))
                    {
                        foreach($service['addons'] as $addon)
                        {
                            ObjectServiceAddon::create([
                                'child_id' => $golfcourse->id,
                                'service_id' => $service['service_id'],
                                'service_addon_id' => $addon['addon_id'],
                        
                                'qty' => $addon['qty'],
                                'fees' => $addon['fees'],
                        
                                'selected_option' => $addon['selected_option'],
                                'notes' => $addon['notes'],
                            ]);
                        }

                        if(isset($addon['fee_details']) && is_array($addon['fee_details']))
                        {
                            foreach($addon['fee_details'] as $fee)
                            {
                                ObjectServiceFeeDetails::create([
                                        'child_id' => $golfcourse->id,
                                        'service_id' => $service['service_id'],
                                        'service_addon_id' => $addon['addon_id'],
                                        'service_fees_details_id' => $fee['fee_id'],
                                        'qty' => $fee['qty'],
                                        'fees' => $fee['fees'],
                                        'unit' => $fee['unit'],                            
                                        'notes' => $fee['notes']
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($golfcourse->id));

            return response()->json([
                'status' => true,
                'golfcourse' => $golfcourseData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store_activity($id, Request $request)
    {
        $golfcourse = GolfCourse::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required',
            // 'start_time' => 'string',
            // 'end_time' => 'string',
            
            // 'start_recur' => 'string',
            // 'end_recur' => 'string',
            
            // 'duration' => 'string',
            // 'days_of_week' => 'array',
            // 'is_recurring' => 'string|in:0,1',

            // 'color' => 'string',
            'type_id' => 'required|exists:activity_types,id'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            if(is_array($request->days_of_week) && count($request->days_of_week) > 0)
                $data['days_of_week'] = implode(',', $request->days_of_week);

            $golfcourse->activities()->create($data);

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

    public function update($id, Request $request)
    {
        $golfcourse = GolfCourse::findOrFail($id);

        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',
            // "golf_course_style_id" => 'required|exists:golf_course_styles,id',
            "active" => "required|in:0,1",
            "direct_contract" => "required|in:0,1",
            // "via_dmc" => "required|in:0,1",

            // "handler_type_id" => 'required|exists:company_types,id',
            // "handler_id" => 'required|exists:companies,id',
            
            "length_men" => "required",
            "length_women" => "required",
            "par_men" => "required",
            "par_women" => "required",
            "holes" => 'in:9,18,27,36',
            "course_rating" => "required",
            "club_rating" => "required",
            "academy" => "required|in:0,1",
            "pros" => "required|in:0,1",

            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // "location_link" => 'required',
            // "latitude" => "required",
            // "longitude" => "required",
            "email" => "required|email",
 
            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",

            "payee" => "required|in:0,1",
            "is_payee_only" => "required|in:0,1",
            "payee_key_created" => "required|in:0,1",

            'facilities' => 'array',
            'facilities.*.id' => 'exists:facilities,id',

            'difficulties' => 'array',
            'difficulties.*' => 'exists:difficulties,id',
            'terrains' => 'array',
            'terrains.*' => 'exists:terrains,id',
            'dresses' => 'array',
            'dresses.*' => 'exists:dress_codes,id',

            'notes' => 'array',
            
            'fields' => 'array',
            'fields.*.translations' => 'array',
            'fields.*.translations.*.language_id' => 'required|exists:languages,id',
            
            'translations' => 'array',
            'translations.*.language_id' => 'required|exists:languages,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $golfcourse->update($data);

            if(is_array($request->facilities))
            {
                $golfcourse->facilities()->detach();

                foreach($request->facilities as $facility)
                {
                    if(isset($facility['id']) && isset($facility['number']))
                    {
                        $golfcourse->facilities()->attach([$facility['id'] => ['number' => $facility['number']]]);
                    }
                }
            }

            if(is_array($request->difficulties))
            {
                $golfcourse->difficulties()->detach();

                $difficulties = Difficulty::whereIn('id', $request->difficulties)->get();
                foreach($difficulties as $difficulty)
                {
                    $golfcourse->difficulties()->save($difficulty);
                }
            }

            if(is_array($request->terrains))
            {
                $golfcourse->terrains()->detach();

                $terrains = Terrain::whereIn('id', $request->terrains)->get();
                foreach($terrains as $terrain)
                {
                    $golfcourse->terrains()->save($terrain);
                }
            }

            if(is_array($request->dresses))
            {
                $golfcourse->dresses()->detach();

                $dresses = DressCode::whereIn('id', $request->dresses)->get();
                foreach($dresses as $dress)
                {
                    $golfcourse->dresses()->save($dress);
                }
            }

            // if(is_array($request->notes))
            // {

            //     $golfcourse->notes()->forceDelete();
            //     foreach($request->notes as $r_note)
            //     {
            //         $note = new Note;
            //         $note->title = $r_note;
        
            //         $golfcourse->notes()->create(['title' => $r_note]);
            //     }
            // }

            if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            {
                $golfcourse->translations()->forceDelete();
                foreach($request->translations as $translation)
                {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;
                    $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
                    $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

                    $golfcourse->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName, 
                        'website_description' => $translateWebsiteDescription, 
                        'internal_description' => $translateInternalDescription, 
                    ]);
                }
            }

            if(is_array($request->fields))
            {
                $golfcourse->fields()->forceDelete();
                foreach($request->fields as $fieldData)
                {  
                    $field = $golfcourse->fields()->create($fieldData);
        
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

            DB::commit();

            $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($golfcourse->id));

            return response()->json([
                'status' => true,
                'golfcourse' => $golfcourseData
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
        
        $golfcourse = GolfCourse::findOrFail($id);

        if(is_array($request->deleted_images))
        {   
            foreach($golfcourse->images()->whereIn('id', $request->deleted_images)->get() as $item)
            {
                $d_image_path = public_path('images/eggheads') . '/' . $item->file_name;
                if(File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $item->delete();
            }
            
        }
        if ($request->hasFile('images')) {
            
            foreach($request->file('images') as $image)
            {

                $imageName = \Str::random(6) . time().'.'.$image->extension();  
     
                $image->move(public_path('images/eggheads'), $imageName);
    
                $image = new Image;
                $image->file_name = $imageName;
    
                $golfcourse->images()->create(['file_name' => $imageName]);
                 
            }
        }

        $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($golfcourse->id));

        return response()->json([
            'status' => true,
            'golfcourse' => $golfcourseData
        ]);

    }

    public function delete_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);
        
        $golfcourse = GolfCourse::findOrFail($id);
        
        $imageToDelete = $golfcourse->images()->where('id', $request->image_id)->first();
        
        $d_image_path = public_path('images/eggheads') . '/' . $imageToDelete->file_name;
        if(File::exists($d_image_path)) {
            File::delete($d_image_path);
        }

        $imageToDelete->delete();

        $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($golfcourse->id));

        return response()->json([
            'status' => true,
            'golfcourse' => $golfcourseData
        ]);

    }

    public function destroy($id)
    {
        $golfcourse = GolfCourse::findOrFail($id);

        try {
            DB::beginTransaction();

            $golfcourse->notes()->delete();

            $golfcourse->delete();

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

    public function prepare_filter($request)
    {
        $filter = [];

        if($request->city_id)
        {
            array_push($filter, array('city_id', $request->city_id));
        }
        if(isset($request->show_website))
        {
            array_push($filter, array('show_website', $request->show_website));
        }
        return $filter;
    }

    public function getFalseResponse()
    {
        return response()->json([
            'status' => false
        ], 422);
    }

    public function create_new_item($data)
    {
        return GolfCourse::create($data);
    }
}
