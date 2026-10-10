<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourOperator;
use App\Models\TravelAgency;
use App\Models\TravelType;
use App\Models\Hotel;
use App\Models\GolfCourse;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Note;
use App\Models\Image;
use App\Http\Resources\TravelAgencyResource;
use App\Http\Resources\TravelAgencyDetailsResource;
use App\Http\Resources\CompanyTypeResource;
use App\Models\Request as ModelsRequest;
use App\Models\RequestProduct;
use DB;
use File;

class TravelAgencyController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $ag = new TravelAgency();
        

        $agData = TravelAgencyResource::collection($ag->get_pagination($filter));

        return response()->json([
            'status' => true,
            'travelagencies' => $agData->response()->getData()
        ]);
    }

    public function get_all()
    {
        $search = request()->input('search');
        $ref_id = request()->input('ref_id');
 
        $ag = new TravelAgency();

        if($search)
        {
            $ag = $ag->where('name' , 'LIKE', '%' . $search . '%')->orWhere('ref_id', 'LIKE', '%' . $search . '%');
        }

        if($ref_id)
        {
            $ag = $ag->where('ref_id', $ref_id);
        }

        $user = request()->user();

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '2')->pluck('child_id')->toArray();
            $ag = $ag->whereIn('id', $childs);
        }

        return response()->json([
            'status' => true,
            'agencies' => TravelAgencyResource::collection($ag->get())
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

    public function show($id)
    {
        $ag= TravelAgency::findOrFail($id);

        $agData = new TravelAgencyDetailsResource($ag);

        return response()->json([
            'status' => true,
            'travelagency' => $agData,
        ]);
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
            // "email" => "required|email",

            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",

            'touroperators' => 'array',
            'touroperators.*' => 'exists:tour_operators,id',

            'golfcourses' => 'array',
            'golfcourses.*' => 'exists:golf_courses,id',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            'notes' => 'array',

            'cities.*' => 'exists:cities,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            // if($request->is_parent)
            // {
            //     $companyData = $data;
            //     $companyData['user_id'] = $user->id;

            //     $company = (new CompanyController())->create_new_item($companyData);

            //     $data['company_id'] = $company->id;
            // }

            $ag = $this->create_new_item($data);

            if(is_array($request->golfcourses) && count($request->golfcourses) > 0)
            {
                $courses = Golfcourse::whereIn('id', $request->golfcourses)->get();
                foreach($courses as $course)
                {
                    $ag->golfcourses()->save($course);
                }
            }

            if(is_array($request->touroperators) && count($request->touroperators) > 0)
            {
                $opers = TourOperator::whereIn('id', $request->touroperators)->get();
                foreach($opers as $oper)
                {
                    $ag->touroperators()->save($oper);
                }
            }

            if(is_array($request->hotels) && count($request->hotels) > 0)
            {
                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotels as $hotel)
                {
                    $ag->cities()->save($hotel);
                }
            }

            if(is_array($request->travel_types) && count($request->travel_types) > 0)
            {
                $types = TravelType::whereIn('id', $request->travel_types)->get();
                foreach($types as $type)
                {
                    $ag->traveltypes()->save($type);
                }
            }

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $ag->cities()->save($city);
                }
            }

            if(is_array($request->notes) && count($request->notes) > 0)
            {
                foreach($request->notes as $r_note)
                {
                    $note = new Note;
                    $note->title = $r_note;
        
                    $ag->notes()->create(['title' => $r_note]);
                }
            }

            if($request->is_parent)
            {

            }

            if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            {
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

            $agData = new TravelAgencyDetailsResource($ag);

            return response()->json([
                'status' => true,
                'travelagency' => $agData,
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
        $ag = TravelAgency::findOrFail($id);

        $validated = $request->validate([
            "company_id" => 'required|exists:companies,id',
            "name" => 'required',

            "active" => "required|in:0,1",
            
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // "email" => "required|email",

            // "delegate_name" => "string",
            // "delegate_email" => "email",
            // "delegate_mobile_number" => "string",
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",

            'touroperators' => 'array',
            'touroperators.*' => 'exists:tour_operators,id',

            'golfcourses' => 'array',
            'golfcourses.*' => 'exists:golf_courses,id',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            'cities.*' => 'exists:cities,id',

            'notes' => 'array'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $ag->update($data);


            if(is_array($request->golfcourses) && count($request->golfcourses) > 0)
            {
                $ag->golfcourses()->detach();
                $courses = Golfcourse::whereIn('id', $request->golfcourses)->get();
                foreach($courses as $course)
                {
                    $ag->golfcourses()->save($course);
                }
            }

            if(is_array($request->touroperators) && count($request->touroperators) > 0)
            {
                $ag->touroperators()->detach();
                $opers = TourOperator::whereIn('id', $request->touroperators)->get();
                foreach($opers as $oper)
                {
                    $ag->touroperators()->save($oper);
                }
            }

            if(is_array($request->hotels) && count($request->hotels) > 0)
            {
                $ag->hotels()->detach();
                $hotels = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotels as $hotel)
                {
                    $ag->cities()->save($hotel);
                }
            }

            if(is_array($request->travel_types) && count($request->travel_types) > 0)
            {
                $ag->traveltypes()->detach();
                $types = TravelType::whereIn('id', $request->travel_types)->get();
                foreach($types as $type)
                {
                    $ag->traveltypes()->save($type);
                }
            }

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $ag->cities()->detach();
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $ag->cities()->save($city);
                }
            }

            if(is_array($request->notes))
            {
                $ag->notes()->forceDelete();
                foreach($request->notes as $r_note)
                {
                    $note = new Note;
                    $note->title = $r_note;
        
                    $ag->notes()->create(['title' => $r_note]);
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

            $agData = new TravelAgencyDetailsResource(TravelAgency::find($ag->id));

            return response()->json([
                'status' => true,
                'travelagency' => $agData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy($id, $force=0)
    {
        $ag = TravelAgency::findOrFail($id);

        $has_requests = ModelsRequest::whereTravelAgencyId($id)->first();

        if ($has_requests && $force==0) {
            return response()->json([
                'status' => true,
                'has_requests' => true,
            ]);
        }

        try {
            DB::beginTransaction();

            $ag->notes()->delete();

            $ag->delete();

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

        if($request->ref_id)
        {
            array_push($filter, array('ref_id', $ref_id));
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
        return TravelAgency::create($data);
    }
}
