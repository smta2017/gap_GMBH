<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourOperator;
use App\Models\TravelAgency;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Note;
use App\Models\Image;
use App\Models\Language;
use App\Http\Resources\TourOperatorResource;
use App\Http\Resources\TourOperatorDetailsResource;
use App\Http\Resources\CompanyTypeResource;
use App\Models\Request as ModelsRequest;
use App\Models\RequestProduct;
use DB;
use File;

class TourOperatorController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $operators = new TourOperator();
        $operators = $operators->where($filter);
        
        $user = request()->user();

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '5')->pluck('child_id')->toArray();
            $operators = $operators->whereIn('id', $childs);
        }

        $operatorsData = TourOperatorResource::collection($operators->get());

        return response()->json([
            'status' => true,
            'touroperators' => $operatorsData
        ]);
    }

    public function get_all()
    {
        $search = request()->input('search');

        $active = request()->input('active');
 
        $operators = new TourOperator();

        if($search)
        {
            $operators = $operators->where('name' , 'LIKE', '%' . $search . '%');
        }

        if($active)
        {
            $operators = $operators->where('active', $active );
        }

        $user = request()->user();

        if($user->details->company->company_type_id != '1')
        {
            $childs = $user->childs->where('child_type_id', '5')->pluck('child_id')->toArray();
            $operators = $operators->whereIn('id', $childs);
        }
        
        return response()->json([
            'status' => true,
            'touroperators' => $operators->select(['id', 'name'])->get(),
        ]);
    }

    public function show($id)
    {
        $operator = TourOperator::findOrFail($id);

        $operatorData = new TourOperatorDetailsResource($operator);

        return response()->json([
            'status' => true,
            'touroperator' => $operatorData,
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

            // 'travelagencies' => 'array',
            'travelagencies.*' => 'exists:travel_agencies,id',

            // 'cities' => 'array',
            'cities.*' => 'exists:cities,id',

            // 'notes' => 'array'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $operator = $this->create_new_item($data);

            if(is_array($request->travelagencies) && count($request->travelagencies) > 0)
            {
                $ags = TravelAgency::whereIn('id', $request->travelagencies)->get();
                foreach($ags as $ag)
                {
                    $operator->travelagencies()->save($ag);
                }
            }

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $operator->cities()->save($city);
                }
            }

            if(is_array($request->notes) && count($request->notes) > 0)
            {
                foreach($request->notes as $r_note)
                {
                    $note = new Note;
                    $note->title = $r_note;
        
                    $operator->notes()->create(['title' => $r_note]);
                }
            }

            // if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            // {
            //     foreach($request->translations as $translation)
            //     {
            //         $language = Language::findOrFail($translation['language_id']);

            //         $translateName = (isset($translation['name'])) ? $translation['name'] : null;
            //         $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
            //         $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

            //         $operator->translations()->create([
            //             'language_id' => $language->id,
            //             'locale' => $language->code,
            //             'name' => $translateName, 
            //             'website_description' => $translateWebsiteDescription, 
            //             'internal_description' => $translateInternalDescription, 
            //         ]);
            //     }
            // }

            DB::commit();

            $tourData = new TourOperatorDetailsResource($operator);

            return response()->json([
                'status' => true,
                'touroperator' => $tourData,
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
        $operator = TourOperator::findOrFail($id);

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

            // 'travelagencies' => 'array',
            'travelagencies.*' => 'exists:travel_agencies,id',

            // 'cities' => 'array',
            'cities.*' => 'exists:cities,id',

            // 'notes' => 'array'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $operator->update($data);

            if(is_array($request->travelagencies) && count($request->travelagencies) > 0)
            {
                $operator->travelagencies()->detach();
                $ags = TravelAgency::whereIn('id', $request->travelagencies)->get();
                foreach($ags as $ag)
                {
                    $operator->travelagencies()->save($ag);
                }
            }

            if(is_array($request->cities) && count($request->cities) > 0)
            {
                $operator->cities()->detach();
                $cities = City::whereIn('id', $request->cities)->get();
                foreach($cities as $city)
                {
                    $operator->cities()->save($city);
                }
            }

            if(is_array($request->notes))
            {
                $operator->notes()->forceDelete();
                foreach($request->notes as $r_note)
                {
                    $note = new Note;
                    $note->title = $r_note;
        
                    $operator->notes()->create(['title' => $r_note]);
                }
            }

            // if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            // {
            //     $operator->translations()->forceDelete();
            //     foreach($request->translations as $translation)
            //     {
            //         $language = Language::findOrFail($translation['language_id']);

            //         $translateName = (isset($translation['name'])) ? $translation['name'] : null;
            //         $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
            //         $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

            //         $operator->translations()->create([
            //             'language_id' => $language->id,
            //             'locale' => $language->code,
            //             'name' => $translateName, 
            //             'website_description' => $translateWebsiteDescription, 
            //             'internal_description' => $translateInternalDescription, 
            //         ]);
            //     }
            // }

            DB::commit();

            $operatorData = new TourOperatorDetailsResource(TourOperator::find($operator->id));

            return response()->json([
                'status' => true,
                'operator' => $operatorData
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
        $operator = TourOperator::findOrFail($id);

        $has_requests = ModelsRequest::whereTourOperatorId($id)->first();

        if ($has_requests && $force==0) {
            return response()->json([
                'status' => true,
                'has_requests' => true,
            ]);
        }

        try {
            DB::beginTransaction();

            $operator->notes()->delete();

            $operator->delete();

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

        $active = $request->active;

        if(isset($active))
        {
            array_push($filter, array('active', $active));
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
        return TourOperator::create($data);
    }
}
