<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
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
use App\Models\Tag;
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
use App\Http\Resources\GolfCourseMiniResource;
use App\Imports\GolfCourseImport;
use App\Models\BasicTranslation;
use App\Models\Playable;
use App\Models\RequestProductTeeTime;
use Carbon\Carbon;
use DB;
use File;

use Maatwebsite\Excel\Facades\Excel;

class GolfCourseController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $golfcourses = new GolfCourse();
        
        $golfcourses = $golfcourses->where($filter);
        
        if(request()->input('booking_code'))
        {
            $golfcourses = $golfcourses->where('booking_code',  request()->input('booking_code'));
        }
        
        if(request()->input('area_id'))
        {
            $golfcourses = $golfcourses->where('area_id',  request()->input('area_id'));
        }
        
        $user = request()->user();

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '3')->pluck('child_id')->toArray();
            $golfcourses = $golfcourses->whereIn('id', $childs);
        }

        $golfcourseData = GolfCourseResource::collection($golfcourses->get());

        return response()->json([
            'status' => true,
            'golfcourses' => $golfcourseData
        ]);
    }

    public function index_paginate()
    {
        $filter = $this->prepare_filter(request());
        
        $golfcourse  = new GolfCourse();
         
        return GolfCourseResource::collection($golfcourse->get_pagination($filter));
    }

    

    public function get_all()
    {
        $search = request()->input('search');
        $cityId = request()->input('city_id');
        $areaId = request()->input('area_id');
        $countryId = request()->input('country_id');
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

        if($areaId)
        {
            $golfcourses = $golfcourses->where('area_id',  $areaId);
        }

        if($countryId)
        {
            $golfcourses = $golfcourses->where('country_id',  $countryId);
        }

        if(request()->input('booking_code'))
        {
            $golfcourses = $golfcourses->where('booking_code',  request()->input('booking_code'));
        }

        if(isset($showWebsite))
        {
            $golfcourses = $golfcourses->where('show_website',  $showWebsite);
        }

        $user = request()->user();

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '3')->pluck('child_id')->toArray();
            $golfcourses = $golfcourses->whereIn('id', $childs);
        }
        
        return response()->json([
            'status' => true,
            'golfcourses' => GolfCourseMiniResource::collection($golfcourses->get())
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
        $types = BasicResource::collection(FieldType::active()->golfCourseFields()->get());

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

        $playables = BasicResource::collection(Playable::where('status', '1')->get());

        $dresses = BasicResource::collection(DressCode::where('status', '1')->get());

        $facilities = BasicResource::collection(Facility::where('type', 'Golf Course')->where('status', '1')->get());

        $fieldTypes = BasicResource::collection(FieldType::active()->golfCourseFields()->get());

        $styles =  BasicResource::collection(GolfCourseStyle::select(['id', 'name'])->where('status', '1')->get());
        
        return response()->json([
            'status' => true,
            'difficulties' => $difficulties,
            'terrains' => $terrains,
            'playables' => $playables,
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

            'tags' => 'array',
            'tags.*' => 'exists:tags,id',

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

          

            $data['booking_code'] = $this->getAutoBookingCode();
            
            
            $golfcourse = $this->create_new_item($data);
 
            if(is_array($request->difficulties) && count($request->difficulties) > 0)
            {
                $difficulties = Difficulty::whereIn('id', $request->difficulties)->get();
                foreach($difficulties as $difficulty)
                {
                    $golfcourse->difficulties()->save($difficulty);
                }
            }
            if(is_array($request->tags) && count($request->tags) > 0)
            {
                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach($tags as $tag)
                {
                    $golfcourse->tags()->save($tag);
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
            if(is_array($request->playables) && count($request->playables) > 0)
            {
                $playables = Playable::whereIn('id', $request->playables)->get();
                foreach($playables as $playable)
                {
                    $golfcourse->playables()->save($playable);
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

            if(is_array($request->related_golf_courses) && count($request->related_golf_courses) > 0)
            {
                $golfcourse->relatedGolfCourses()->detach();
                $golf_courses = GolfCourse::whereIn('id', $request->related_golf_courses)->get();
                foreach($golf_courses as $golf_course)
                {
                    $golfcourse->relatedGolfCourses()->attach($golf_course['id']);
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
            
                foreach($request->file('images') as $key => $image)
                {
    
                    $imageName = \Str::random(6) . time().'.'.$image->extension();  
         
                    $image->move(public_path('images/eggheads'), $imageName);
        
                    $image = new Image;
                    $image->file_name = $imageName;
        
                    $golfcourse->images()->create(['file_name' => $imageName]);
                }
            }

            if (is_array($request->links_hd_images) && count($request->links_hd_images) > 0) {
            
                foreach($request->links_hd_images as $singleLink)
                { 
                    $golfcourse->links()->create(['link' => $singleLink, 'type' => 'hd_images']);
                }
            }

            if (is_array($request->links_logo_images) && count($request->links_logo_images) > 0) {
            
                foreach($request->links_logo_images as $singleLink)
                { 
                    $golfcourse->links()->create(['link' => $singleLink, 'type' => 'logo_images']);
                }
            }


            if(is_array($request->davinici_codes) )
            {
                $golfcourse->DaviniciCodes()->forceDelete();
                foreach($request->davinici_codes as $davinici_code)
                {        
                    $golfcourse->DaviniciCodes()->create(['davinici_code' => $davinici_code]);
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


    public function getAutoBookingCode()
    {
        if (GolfCourse::count()) {
            $nextId= GolfCourse::latest()->first()->id + 1;
        }else
        {
            $nextId=0;
        }

        return 'GC-'. $nextId;
        
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

            if(is_array($request->services))
            {
                ObjectService::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($request) {
                    $q->where('type', $request->type);
                })->forceDelete();
                ObjectServiceAddon::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($request) {
                    $q->where('type', $request->type);
                })->forceDelete();
                ObjectServiceProperty::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($request) {
                    $q->where('type', $request->type);
                })->forceDelete();
                ObjectServiceFeeDetails::where('child_id', $golfcourse->id)->whereHas('service', function ($q) use ($request) {
                    $q->where('type', $request->type);
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
                        'type' => $request->type,
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

            $golfcourse->updateUpdatedAt();

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

            $golfcourse->updateUpdatedAt();

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

    public function updateFacilities($id,Request $request)
    {
        $validated = $request->validate([
            'facilities' => 'array'
        ]);

        $golfcourse = GolfCourse::findOrFail($id);


        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;
            $data['updated_at'] = Carbon::now();

            $golfcourse->update($data);

            if(is_array($request->facilities))
            {
                $golfcourse->facilities()->detach();

                foreach($request->facilities as $facility)
                {
                    if(isset($facility['id']))
                    {
                        $golfcourse->facilities()->attach([$facility['id'] => ['number' => $facility['number']]]);
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


    public function updateCms($id,Request $request)
    {

        $validated = $request->validate([
            // "golf_course_style_id" => 'required|exists:golf_course_styles,id', 
            "holes" => 'in:9,18,27,36,45',
            // "course_rating" => "required",
            // "club_rating" => "required",
            // "length_men" => "required",
            // "length_women" => "required",
            // "par_men" => "required",
            // "par_women" => "required",
            // "slope_from" => "required|numeric",
            // "slope_to" => "required|numeric",
        ]);
        
        $request =  $request->except(
            'company_id', 'company_name', 'region_id'
            , 'country_id', 'city_id'
            , 'area_id', 'services', 'trainings'
        );
 
        $request = new Request($request);
        // return $request->all();
        return $this->updateGolfCourse($id,$request);
    }
   

    public function update($id,Request $request)
    {
        
        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',
            // "golf_course_style_id" => 'required|exists:golf_course_styles,id',
            "active" => "required|in:0,1",
            "direct_contract" => "required|in:0,1",
            // "via_dmc" => "required|in:0,1",

            // "handler_type_id" => 'required|exists:company_types,id',
            // "handler_id" => 'required|exists:companies,id',
            
            // "length_men" => "",
            // "length_women" => "",
            // "par_men" => "",
            // "par_women" => "",
            // "holes" => "in:9,18,27,36",
            // "course_rating" => "",
            // "club_rating" => "",
            "academy" => "in:0,1",
            "pros" => "in:0,1",

            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // "location_link" => 'required',
            // "latitude" => "required",
            // "longitude" => "required",
            "email" => "required|email",
            // "length_men" => "required",
            // "length_women" => "required",
            // "par_men" => "required",
            // "par_women" => "required",
            // "slope_from" => "required|numeric",
            // "slope_to" => "required|numeric",
            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",

            "payee" => "in:0,1",
            "is_payee_only" => "in:0,1",
            "payee_key_created" => "in:0,1",

            // 'facilities' => 'array',
            'facilities.*.id' => 'exists:facilities,id',

            'difficulties' => 'array',
            'difficulties.*' => 'exists:difficulties,id',

            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
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

        return $this->updateGolfCourse($id,$request);

    }


    public function updateGolfCourse($id, Request $request)
    {
        $golfcourse = GolfCourse::findOrFail($id);


        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;
            $data['updated_at'] = Carbon::now();

            if($golfcourse->country_id != $request->country_id){
                $golfcourse->countryfeaturedGolfCourses()->detach();
            }
           
            if($golfcourse->city_id != $request->city_id){
                $golfcourse->cityfeaturedGolfCourses()->detach();
            }

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

            if(is_array($request->tags))
            {
                $golfcourse->tags()->detach();

                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach($tags as $tag)
                {
                    $golfcourse->tags()->save($tag);
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

            if(is_array($request->playables))
            {
                $golfcourse->playables()->detach();

                $playables = Terrain::whereIn('id', $request->playables)->get();
                foreach($playables as $playable)
                {
                    $golfcourse->playables()->save($playable);
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

            if(is_array($request->related_golf_courses))
            {
                $golfcourse->relatedGolfCourses()->detach();
                $golf_courses = GolfCourse::whereIn('id', $request->related_golf_courses)->get();
                foreach($golf_courses as $golf_course)
                {
                    $golfcourse->relatedGolfCourses()->attach($golf_course['id']);
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

            if (is_array($request->links_hd_images)) {
            
                $golfcourse->linksHDImages()->forceDelete();
                foreach($request->links_hd_images as $singleLink)
                {
                    $singleLink = (is_null($singleLink))  ? "" : $singleLink ;
                    $golfcourse->links()->create(['link' => $singleLink, 'type' => 'hd_images']);
                }
            }

            if (is_array($request->links_logo_images)) {
            
                $golfcourse->linksLogoImages()->forceDelete();
                foreach($request->links_logo_images as $singleLink)
                {
                    $singleLink = (is_null($singleLink))  ? "" : $singleLink ;
                    $golfcourse->links()->create(['link' => $singleLink, 'type' => 'logo_images']);
                }
            }

            if (is_array($request->images) && count($request->images) > 0) {
                foreach($request->images as $image)
                {
                    if (Image::find($image['id'])) {
                        
                    
                    // if (!is_null($image['alt'])) {
                        Image::find($image['id'])->update([ 
                        'alt' =>  (isset($image['alt'])) ? $image['alt'] : '',
                        'original_file_name' => (isset($image['original_file_name'])) ? $image['original_file_name'] : '', 
                        'size' => (isset($image['size'])) ? $image['size'] : '', 
                        'rank' => (isset($image['rank'])) ? $image['rank'] : ''
                        ]); 
                    // }
}
                    
                }
            }


            if(is_array($request->davinici_codes) )
            {
                $golfcourse->DaviniciCodes()->forceDelete();
                foreach($request->davinici_codes as $davinici_code)
                {        
                    $golfcourse->DaviniciCodes()->create(['davinici_code' => $davinici_code]);
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


    
    public function update_publish($id, Request $request)
    {
        $course = GolfCourse::findOrFail($id);

        try {
            DB::beginTransaction();

            $data = [];
            
            $user = request()->user();

            $data['updated_by'] = $user->id;
            $data['published_at'] = Carbon::now();

            $course->update($data);

            DB::commit();

            $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($course->id));

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
            // 'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        $golfcourse = Helpers::uploadItemImages(GolfCourse::class,$id,$request);

        $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($golfcourse->id));

        return response()->json([
            'status' => true,
            'golfcourse' => $golfcourseData
        ]);

    }

    public function change_main_image(Request $request, $id)
    {
        $golfcourse = GolfCourse::findOrFail($id);

        $validated = $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);
        
        foreach($golfcourse->images as $img)
        {
            $img->update([
                'is_main' => '0'
            ]);
        }

        $image = Image::find($request->image_id)->update([
            'is_main' => '1'
        ]);

        $golfcourse->updateUpdatedAt();

        $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($golfcourse->id));

        return response()->json([
            'status' => true,
            'golfcourse' => $golfcourseData
        ]);

    }

    public function delete_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image_id' => 'required',
            // 'image_id.*' => 'required|exists:images,id',
        ]);
        
        $golfcourse = Helpers::deleteItemImages(GolfCourse::class,$id,$request->image_id);

        $golfcourseData = new GolfCourseDetailsResource(GolfCourse::find($golfcourse->id));

        return response()->json([
            'status' => true,
            'golfcourse' => $golfcourseData
        ]);

    }

    public function destroy($id, $force=0)
    {

        $golfcourse = GolfCourse::findOrFail($id);

        $has_requests = RequestProductTeeTime::whereGolfCourseId($id)->first();
        
        if ($has_requests && $force==0) {
            return response()->json([
                'status' => true,
                'has_requests' => true,
            ]);
        }

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
        
        if($request->area_id)
        {
            array_push($filter, array('area_id', $request->area_id));
        }

        if($request->region_id)
        {
            array_push($filter, array('region_id', $request->region_id));
        }
        
        if($request->city_id)
        {
            array_push($filter, array('city_id', $request->city_id));
        }

        if($request->country_id)
        {
            array_push($filter, array('country_id', $request->country_id));
        }
        
        if(isset($request->letter_code))
        {
            array_push($filter, array('letter_code', $request->letter_code));
        }

        // if(isset($request->published))
        // {
        //     if($request->published =='Unpublished'){
        //         array_push($filter, array('show_website', 0));
        //     }
        //     if($request->published =='Pending Publish'){
        //         array_push($filter, array('show_website',1));
        //         array_push($filter, array('published_at', 'updated_at'));
        //     }
        //     if($request->published =='Published'){
        //         array_push($filter, array('show_website','=',1));
        //         array_push($filter, array('published_at','<', 'updated_at'));
        //     }
        // }

        if(isset($request->top))
        {
            array_push($filter, array('top', $request->top));
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

    public function import(Request $request)
    {

        $GolfCourses = Excel::toCollection(new GolfCourseImport, public_path('final_GC.xlsx'));
        
        $newGC= [];
        $foundGC= [];
        // return $GolfCourses;
        foreach ($GolfCourses[0] as $key=> $row) {
            if ($key) {
                 

                $region_id = $this->getTranslabelByDeName('Region',$row[3],'de');
                $country_id = $this->getTranslabelByDeName('Country',$row[4],'de');
                $city_id = $this->getTranslabelByDeName('City',$row[5],'de');
                $area_id = $this->getTranslabelByDeName('Area',$row[6],'de');

                $companydata = [
                    'name' => $row[0],
                    'email' => $row[2],
                    'region_id' => $region_id,
                    'country_id' => $country_id,
                    'city_id' => $city_id,
                    'area_id' => $area_id,
                    'company_type_id' => 3,
                    'active' => 1,
                ];

                try {
                    // DB::beginTransaction();
                    
                    $company=Company::whereName($row[0])->first();
                    
                    if(!$company){
                        if (Company::count()) {
                            $nextId= Company::latest()->first()->id + 1;
                        }else
                        {
                            $nextId=0;
                        }
            
                        $companydata['booking_code'] = 'CO-' . 'GC' . '-'. $nextId;
                        \DB::select('ALTER TABLE companies AUTO_INCREMENT = 1');
                        
                        $company = Company::create($companydata);
                    }

                    $GCdata = [
                        'company_id' => $company->id,
                        'name' => $row[1],
                        'email' => $row[2],
                        'booking_code' => $this->getAutoBookingCode(),
                        'region_id' => $region_id,
                        'country_id' => $country_id,
                        'city_id' => $city_id,
                        'area_id' => $area_id,
                        'company_type_id' => 3,
                        'active' => 1,
                        'ref_id'=>  $row[7],
                        'letter_code'=> $row[8],
                        'golf_course_style_id' => $this->getTranslabelByDeName('GolfCourseStyle',$row[11],'en'),
                        'designer'=> $row[12],
                        'length_men'=> $row[13],
                        'length_women'=> $row[14],
                        'par_men'=> $row[15],
                        'par_women'=> $row[16],
                        'holes'=> $row[17],
                        'hcp_men'=> $row[25],
                        'hcp_women'=> $row[26],
                        'slope_from' =>$row[37],
                        'slope_to' =>$row[38],
                        'location_link' =>$row[46],
                        'latitude' => $row[47],
                        'longitude' => $row[48],
                    ];


                   $golfcourse =  GolfCourse::whereName($row[1])->whereCompanyId($company->id)->first();

                    if ($golfcourse) {
                        \array_push($foundGC,$golfcourse->id);
                    }
                    else{
                        \DB::select('ALTER TABLE golf_courses AUTO_INCREMENT = 1');

                        $golfcourse = GolfCourse::create($GCdata);
                     

                    \array_push($newGC,$golfcourse->id);


                    $davinici_codes = \explode(',',$row[9]);
                    $golfcourse->DaviniciCodes()->forceDelete();
                    foreach($davinici_codes as $davinici_code)
                    {
                        if ($davinici_code) {
                            $golfcourse->DaviniciCodes()->create(['davinici_code' => $davinici_code]);
                        }
                    }


                    $facilitys = \explode(',',$row[34]);
                    $golfcourse->facilities()->forceDelete();
                    foreach($facilitys as $facility)
                    {
                        if ($facility) {
                            $facility_id  = $this->getTranslabelByDeName('Facility',$facility,'de');
                            if ($facility_id) {
                                $golfcourse->facilities()->attach([$facility_id => ['number' => 1]]);
                            }
                        }
                    }
 
                    $service_id =   $this->getServiceTranslabelByDeName('Golf Course','Service',$row[35],'de');
                    // $golfcourse->services()->forceDelete();
                    
                    if ($service_id) {
                        ObjectService::create([
                            'child_id' => $golfcourse->id,
                            'service_id' => $service_id,
                            'type'=> 'Golf Course',
                            'qty' => 1,
                            'fees' => 1,
                            'selected_option' => 1,
                            'notes' => null,
                            'active' => 1,
                        ]);
                    } 



                    $trainings = \explode(',',$row[36]);

                    // $golfcourse->trainings()->forceDelete();
                    
                    foreach($trainings as $training)
                    {
                        if ($training) {
                            $training_id = $this->getServiceTranslabelByDeName('Training','Service',$training,'de');
                            if ($training_id) {
                                ObjectService::create([
                                    'child_id' => $golfcourse->id,
                                    'service_id' => $training_id,
                                    'type'=> 'Training',
                                    'qty' => 1,
                                    'fees' => 1,
                                    'selected_option' => 1,
                                    'notes' => null,
                                    'active' => 1,
                                ]);
                            }
                       }
                    }

                    $golfcourse->fields()->forceDelete();
                    if($row[32])
                    { 
                        $type_id = 3; 
                        $field = $golfcourse->fields()->create(['type_id'=>$type_id,'is_html'=>"1",'description'=>$row[32]]); 
                            $translateDescription = (isset($row[32])) ? $row[32] : null; 
                            $field->translations()->create([
                                'language_id' => 1,
                                'locale' => 'en',
                                'description' => $translateDescription,
                            ]);
                            $field->translations()->create([
                                'language_id' => 2,
                                'locale' => 'de',
                                'description' => $translateDescription,
                            ]);                               
                    } 

                    if($row[33])
                    { 
                        $type_id = 5; 
                        $field = $golfcourse->fields()->create(['type_id'=>$type_id,'is_html'=>"1",'description'=>$row[33]]); 
                            $translateDescription = (isset($row[33])) ? $row[33] : null; 
                            $field->translations()->create([
                                'language_id' => 1,
                                'locale' => 'en',
                                'description' => $translateDescription,
                            ]);
                            $field->translations()->create([
                                'language_id' => 2,
                                'locale' => 'de',
                                'description' => $translateDescription,
                            ]);                               
                    } 

                }

                   

                } catch (\PDOException $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage(),
                    ], 422);
                }


            }
            
            
        // DB::commit();
            
            
        }
        return ['new_count' =>count($newGC),'exe' =>$foundGC,'new' => $newGC];

    }

    public function getTranslabelByDeName($transTable,$name,$locale='en')
    {
        $trans = BasicTranslation::whereName($name)->whereLocale($locale)->whereBasicableType('App\\Models\\' . $transTable)->orderBy('id', 'desc')->first();
        if ($trans) {   
            $intent_id = $trans['basicable_id'];
        }else{
            return null;
        }
        return $intent_id;
    }



    public function getServiceTranslabelByDeName($type ='', $transTable,$name,$locale='en')
    {
        $trans = Service::whereType($type)->whereHas('translations',function($q) use($transTable,$name,$locale){
            $q->whereName($name)->whereLocale($locale)->whereBasicableType('App\\Models\\' . $transTable);
        })->first();
         
        if ($trans) {   
            return $trans->id;
        }else{
            return null;
        }
    }

}
