<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\DMC;
use App\Models\City;
use App\Models\GolfCourse;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\TravelType;
use App\Models\Note;
use App\Models\Image;
use App\Http\Resources\DmcResource;
use App\Http\Resources\DmcDetailsResource;
use App\Http\Resources\CompanyTypeResource;
use App\Http\Resources\ActivityResource;
use App\Models\RequestProduct;
use DB;
use File;

class DmcController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $dmcs = new DMC();
        $dmcs = $dmcs->where($filter);
        
        if(request()->input('booking_code'))
        {
            $dmcs = $dmcs->where('booking_code',  request()->input('booking_code'));
        }

        $user = request()->user();

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '6')->pluck('child_id')->toArray();
            $dmcs = $dmcs->whereIn('id', $childs);
        }

        $dmcssData = DMCResource::collection($dmcs->get());

        return response()->json([
            'status' => true,
            'dmcs' => $dmcssData
        ]);
    }

    public function get_all()
    {
        $search = request()->input('search');
 
        $dmcs = new DMC();

        if($search)
        {
            $dmcs = $dmcs->where('name' , 'LIKE', '%' . $search . '%');
        }

        if(request()->input('booking_code'))
        {
            $dmcs = $dmcs->where('booking_code',  request()->input('booking_code'));
        }
        
        $user = request()->user();

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '6')->pluck('child_id')->toArray();
            $dmcs = $dmcs->whereIn('id', $childs);
        }
        
        return response()->json([
            'status' => true,
            'dmcs' => $dmcs->select(['id', 'name'])->get(),
        ]);
    }

    public function show($id)
    {
        $dmc = DMC::findOrFail($id);

        $dmcData = new DmcDetailsResource($dmc);

        return response()->json([
            'status' => true,
            'dmc' => $dmcData,
        ]);
    }
    
    public function get_travel_types()
    {
        $types = TravelType::select(['id', 'name'])->get();

        return response()->json([
            'status' => true,
            'travel_types' => $types
        ]);
    }

    public function get_basics()
    {
        $types = TravelType::select(['id', 'name'])->get();
  
        return response()->json([
            'status' => true,
            'travel_types' => $types,
        ]);
    }


    public function getAutoBookingCode()
    {
        if (DMC::count()) {
            $nextId= DMC::latest()->first()->id + 1;
        }else
        {
            $nextId=0;
        }

        return 'DM-'. $nextId;
        
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',

            "active" => "required|in:0,1",
            
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            "email" => "required|email",

            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",


            'travel_types' => 'array',
            'travel_types.*' => 'exists:travel_types,id',

            'golfcourses' => 'array',
            'golfcourses.*' => 'exists:golf_courses,id',

            // 'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            // 'cities' => 'array',
            'cities.*' => 'exists:cities,id',

            // 'notes' => 'array'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;


            $data['booking_code'] = $this->getAutoBookingCode();
            
            $dmc = $this->create_new_item($data);
 
            if(is_array($request->travel_types) && count($request->travel_types) > 0)
            {
                $types = TravelType::whereIn('id', $request->travel_types)->get();
                foreach($types as $type)
                {
                    $dmc->traveltypes()->save($type);
                }
            }

            if(is_array($request->golfcourses) && count($request->golfcourses) > 0)
            {
                $courses = Golfcourse::whereIn('id', $request->golfcourses)->get();
                foreach($courses as $course)
                {
                    $dmc->golfcourses()->save($course);
                }
            }

            if(is_array($request->hotels) && count($request->hotels) > 0)
            {
                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotels as $hotel)
                {
                    $dmc->hotels()->save($hotel);
                }
            }

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $dmc->cities()->save($city);
                }
            }


            if(is_array($request->notes) && count($request->notes) > 0)
            {
                foreach($request->notes as $r_note)
                {
                    $note = new Note;
                    $note->title = $r_note;
        
                    $dmc->notes()->create(['title' => $r_note]);
                }
            }

            if ($request->hasFile('images')) {
            
                foreach($request->file('images') as $image)
                {
    
                    $imageName = \Str::random(6) . time().'.'.$image->extension();  
         
                    $image->move(public_path('images/dmcs'), $imageName);
        
                    $image = new Image;
                    $image->file_name = $imageName;
        
                    $dmc->images()->create(['file_name' => $imageName]);
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

                    $dmc->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName, 
                        'website_description' => $translateWebsiteDescription, 
                        'internal_description' => $translateInternalDescription, 
                    ]);
                }
            }

            DB::commit();

            $dmcData = new DmcDetailsResource($dmc);

            return response()->json([
                'status' => true,
                'dmc' => $dmcData,
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
        $dmc = DMC::findOrFail($id);

        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',

            "active" => "required|in:0,1",
            
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            "email" => "required|email",

            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",


            'travel_types' => 'array',
            'travel_types.*' => 'exists:travel_types,id',

            'golfcourses' => 'array',
            'golfcourses.*' => 'exists:golf_courses,id',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            'cities' => 'array',
            'cities.*' => 'exists:cities,id',

            'notes' => 'array'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $dmc->update($data);


            if(is_array($request->travel_types) && count($request->travel_types) > 0)
            {
                $dmc->traveltypes()->detach();
                $types = TravelType::whereIn('id', $request->travel_types)->get();
                foreach($types as $type)
                {
                    $dmc->traveltypes()->save($type);
                }
            }


            if(is_array($request->golfcourses) && count($request->golfcourses) > 0)
            {
                $dmc->golfcourses()->detach();
                $courses = Golfcourse::whereIn('id', $request->golfcourses)->get();
                foreach($courses as $course)
                {
                    $dmc->golfcourses()->save($course);
                }
            }

            if(is_array($request->hotels) && count($request->hotels) > 0)
            {
                $dmc->hotels()->detach();
                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotels as $hotel)
                {
                    $dmc->hotels()->save($hotel);
                }
            }

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $dmc->cities()->detach();
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $dmc->cities()->save($city);
                }
            }

            if(is_array($request->notes))
            {
                $dmc->notes()->forceDelete();
                foreach($request->notes as $r_note)
                {
                    $note = new Note;
                    $note->title = $r_note;
        
                    $dmc->notes()->create(['title' => $r_note]);
                }
            }

            if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            {
                $ag->translations()->forceDelete();
                foreach($request->translations as $translation)
                {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;
                    $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
                    $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

                    $ag->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName, 
                        'website_description' => $translateWebsiteDescription, 
                        'internal_description' => $translateInternalDescription, 
                    ]);
                }
            }

            DB::commit();

            $dmcData = new DmcDetailsResource(DMC::find($dmc->id));

            return response()->json([
                'status' => true,
                'dmc' => $dmcData
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
        
        $dmc = DMC::findOrFail($id);

        if(is_array($request->deleted_images))
        {   
            foreach($dmc->images()->whereIn('id', $request->deleted_images)->get() as $item)
            {
                $d_image_path = public_path('images/dmcs') . '/' . $item->file_name;
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
     
                $image->move(public_path('images/dmcs'), $imageName);
    
                $image = new Image;
                $image->file_name = $imageName;
    
                $dmc->images()->create(['file_name' => $imageName]);
                 
            }
        }

        $dmcData = new DMCDetailsResource(DMC::find($dmc->id));

        return response()->json([
            'status' => true,
            'dmc' => $dmcData
        ]);

    }

    public function delete_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image_id' => 'required|exists:images,id',
        ]);
        
        $dmc = DMC::findOrFail($id);
        
        $imageToDelete = $dmc->images()->where('id', $request->image_id)->first();
        
        $d_image_path = public_path('images/dmcs') . '/' . $imageToDelete->file_name;
        if(File::exists($d_image_path)) {
            File::delete($d_image_path);
        }

        $imageToDelete->delete();

        $dmcData = new DMCDetailsResource(DMC::find($dmc->id));

        return response()->json([
            'status' => true,
            'dmc' => $dmcData
        ]);

    }

    public function destroy($id, $force=0)
    {
        $dmc = DMC::findOrFail($id);

        $has_requests = RequestProduct::whereServiceHandlerId($dmc->company->id)->whereServiceHandlerTypeId(6)->first();

        if ($has_requests && $force==0) {
            return response()->json([
                'status' => true,
                'has_requests' => true,
            ]);
        }

        try {
            DB::beginTransaction();

            $dmc->notes()->delete();

            $dmc->delete();

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
        return DMC::create($data);
    }
}
