<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Image;
use App\Models\Company;
use App\Models\TravelAgency;
use App\Models\GolfCourse;
use App\Models\Hotel;
use App\Models\TourOperator;
use App\Models\DMC;
use App\Models\Language;
use App\Models\User;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserFilterResource;
use App\Http\Resources\CompanyChildResource;
use App\Http\Resources\BookingCodeResource;
use DB;
use File;

class CompanyController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());

        $companies = new Company();

        $companiesData = CompanyResource::collection($companies->get_pagination($filter));

        return response()->json([
            'status' => true,
            'companies' => $companiesData->response()->getData()
        ]);
    }

    public function show($id)
    {
        $company = Company::findOrFail($id);

        $companyData = new CompanyResource($company);

        return response()->json([
            'status' => true,
            'company' => $companyData,
        ]);
    }

    public function get_all_users()
    {
        $search = request()->input('search');
        $company_id = request()->input('company_id');

        // $users = User::whereHas('details', function($query) use ($search, $company_id){
        //     if($company_id)
        //     {
        //         $query->where('company_id', $company_id);
        //     }
        //     $query->where('first_name', 'LIKE', '%'. $search .'%' )
        //         ->orWhere('last_name', 'LIKE', '%'. $search .'%' );
        // })->with('details')->select('users.id')->get();

        $users = User::join('user_details', 'users.id', 'user_details.user_id');

        if($company_id)
        {
            $users = $users->where('user_details.company_id', $company_id);
        }

        if($search)
        {
            $users = $users->where('user_details.first_name', 'LIKE', '%' . $search . '%')->orWhere('user_details.last_name', 'LIKE', '%' . $search . '%');
        }
        $users = $users->select('users.id')->get();
 
        $usersData = UserFilterResource::collection($users);

        return response()->json([
            'status' => true,
            'users' => $usersData,
        ]);
    }

    public function get_company_childs($company_id)
    {
        $company = Company::findOrFail($company_id);

        $company_type_id = $company->company_type_id;

        if(!in_array($company_type_id, [2,3,4,5,6]))
        {
            return [];
        }
        
        $hotels = Hotel::where('company_id', $company_id)->get();
        $courses = GolfCourse::where('company_id', $company_id)->get();
        $dmcs = DMC::where('company_id', $company_id)->get();
        $agencies = TravelAgency::where('company_id', $company_id)->get();
        $operators = TourOperator::where('company_id', $company_id)->get();

        $allItems = new \Illuminate\Database\Eloquent\Collection; //Create empty collection which we know has the merge() method
        $allItems = $allItems->concat($hotels);
        $allItems = $allItems->concat($courses);
        $allItems = $allItems->concat($dmcs);
        $allItems = $allItems->concat($agencies);
        $allItems = $allItems->concat($operators);

        return $allItems;
    }

    public function get_childs()
    {
        $company_id = request()->input('company_id');

        $company = Company::findOrFail($company_id);

        $company_type_id = $company->company_type_id;

        if(!in_array($company_type_id, [2,3,4,5,6]))
        {
            return response()->json([
                'status' => true,
                'childs' => []
            ]);
        }
        
        switch ($company_type_id) {
            case "2":
                $provider = 'App\Models\TravelAgency';
                break;
            case "3":
                $provider = 'App\Models\GolfCourse';
                break;
            case "4":
                $provider = 'App\Models\Hotel';
                break;
            case "5":
                $provider = 'App\Models\TourOperator';
                break;
            case "6":
                $provider = 'App\Models\DMC';
                break;
            default:
        }
        
        // $childs = $provider::where('company_id', $company_id)->get();

        $hotels = Hotel::where('company_id', $company_id)->get();
        $courses = GolfCourse::where('company_id', $company_id)->get();
        $dmcs = DMC::where('company_id', $company_id)->get();
        $agencies = TravelAgency::where('company_id', $company_id)->get();
        $operators = TourOperator::where('company_id', $company_id)->get();

        $allItems = new \Illuminate\Database\Eloquent\Collection; //Create empty collection which we know has the merge() method
        $allItems = $allItems->concat($hotels);
        $allItems = $allItems->concat($courses);
        $allItems = $allItems->concat($dmcs);
        $allItems = $allItems->concat($agencies);
        $allItems = $allItems->concat($operators);
        // $allItems = $this->get($allItems);

        return response()->json([
            'status' => true,
            'childs' => CompanyChildResource::collection($allItems)
        ]);
    }

    public function get_booking_codes()
    {
        $booking_code = request()->input('booking_code');
        $company_id = request()->input('company_id');
        
        $models = [Hotel::class, GolfCourse::class, DMC::class, Company::class];

        $allItems = new \Illuminate\Database\Eloquent\Collection; //Create empty collection which we know has the merge() method

        foreach ($models as $model) {
            $result = $model::select([
                "name", 
                "booking_code",
                DB::raw("'hotel' as type")
            ])->where(function($q)  use($booking_code){
                $q->where('booking_code', 'LIKE', '%' . $booking_code . '%');
                $q->orWhere('name', 'LIKE', '%' . $booking_code . '%');
            });

            if (isset($company_id)) {
                if ($model=='App\Models\Company') {
                    $result = $result->where('id', $company_id );
                }else{
                    $result = $result->where('company_id', $company_id );
                }
            }

            $result = $result->get();
            $allItems = $allItems->concat($result);
        }

        return response()->json([
            'status' => true,
            'childs' => BookingCodeResource::collection($allItems)
        ]);
    }

    public function get_providers()
    {
        $booking_code = request()->input('booking_code');
        
        $hotels = Hotel::where('booking_code', 'LIKE', '%' . $booking_code . '%')->get();
        $courses = GolfCourse::where('booking_code', 'LIKE', '%' . $booking_code . '%')->get();
        $dmcs = DMC::where('booking_code', 'LIKE', '%' . $booking_code . '%')->get();

        $allItems = new \Illuminate\Database\Eloquent\Collection; //Create empty collection which we know has the merge() method
        $allItems = $allItems->concat($hotels);
        $allItems = $allItems->concat($courses);
        $allItems = $allItems->concat($dmcs);

        // $allItems = $this->get($allItems);

        return response()->json([
            'status' => true,
            'childs' => CompanyChildResource::collection($allItems)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            // 'phone' => 'required',
    
            // 'delegate_name' => 'string',
            // 'delegate_email' => 'email',
            // 'delegate_mobile_number' => 'string',
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",

            'company_type_id' => 'required|exists:company_types,id',

            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // 'postal_code' => 'required',
            // 'street' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $user = request()->user();
            $data['created_by'] = $user->id;


            if ($request->company_type_id==2) {
                $coType = "TA";
            }
            elseif($request->company_type_id==3){
                $coType = "GC";
            }
            elseif($request->company_type_id==4){
                $coType = "HO";
            }
            elseif($request->company_type_id==5){
                $coType = "TO";
            }
            elseif($request->company_type_id==6){
                $coType = "DMC";
            }


            if (Company::count()) {
                $nextId= Company::latest()->first()->id + 1;
            }else
            {
                $nextId=0;
            }

            $data['booking_code'] = 'CO-' . $coType . '-'. $nextId;
            
            $company = $this->create_new_item($data);

            
            if($request->create_child)
            {
                $companyTypeID = $request->company_type_id;
                $data['is_company_address'] = 1;
                $data['company_id'] = $company->id;
                        
                switch ($companyTypeID) {
                    case "2":
                        (new TravelAgencyController())->create_new_item($data);
                        break;
                    case "3":
                        $data['booking_code'] = (new GolfCourseController())->getAutoBookingCode();
                        (new GolfCourseController())->create_new_item($data);
                        break;
                    case "4":
                        $data['booking_code'] = (new HotelController())->getAutoBookingCode();
                        (new HotelController())->create_new_item($data);
                        break;
                    case "5":
                        (new TourOperatorController())->create_new_item($data);
                        break;
                    case "6":
                        $data['booking_code'] = (new DmcController())->getAutoBookingCode();
                        (new DmcController())->create_new_item($data);
                        break;
                    default:   
                }
            }

            if ($request->hasFile('image')) {

                $imageName = \Str::random(6) . time().'.'.$request->image->extension();  
         
                $request->image->move(public_path('images/companies'), $imageName);
    
                $image = new Image;
                $image->file_name = $imageName;
    
                $company->logo()->create(['file_name' => $imageName]);
                
            }

            // if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            // {
            //     foreach($request->translations as $translation)
            //     {
            //         $language = Language::findOrFail($translation['language_id']);

            //         $translateName = (isset($translation['name'])) ? $translation['name'] : null;
            //         $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
            //         $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

            //         $company->translations()->create([
            //             'language_id' => $language->id,
            //             'locale' => $language->code,
            //             'name' => $translateName, 
            //             'website_description' => $translateWebsiteDescription, 
            //             'internal_description' => $translateInternalDescription, 
            //         ]);
            //     }
            // }

            DB::commit();

            $companyData = new CompanyResource($company);

            return response()->json([
                'status' => true,
                'company' => $companyData
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
        $validated = $request->validate([
            'name' => 'required',
            // 'phone' => 'required',
    
            // 'delegate_name' => 'required',
            // 'delegate_email' => 'required|email',
            // 'delegate_mobile_number' => 'required',
            // "delegate_user_id" => "exists:users,id",
            // "assigned_user_id" => "exists:users,id",

            'company_type_id' => 'required|exists:company_types,id',

            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            // 'postal_code' => 'required',
            // 'street' => 'required',
        ]);

        $company = Company::findOrFail($id);

        try {
            DB::beginTransaction();

            $company->update($request->all());

            $has_childs=$company->check_has_childs();
            if($has_childs){   
                $childs = $this->get_company_childs($company->id);
                 foreach ($childs as $child) { 
                    if($child->is_company_address){// if use company info
                        $child->update([
                            "region_id"=>$request['region_id'],
                            "country_id"=> $request['country_id'],
                            "city_id"=>$request['city_id'],
                            "area_id"=>(isset($request['area_id']))? $request['area_id'] : '' ,
                            "street"=> $request['street'],
                            "postal_code"=>$request['postal_code'],
                            "phone"=> $request['phone'],
                            "fax"=> $request['fax'],
                            "website"=> $request['website'],
                            "website_link"=> $request['website'],
                            "email"=> $request['email'],
                        ]);
                    }
                } 
            }

            // if($request->translations && is_array($request->translations) && count($request->translations) > 0)
            // {
            //     $company->translations()->forceDelete();
            //     foreach($request->translations as $translation)
            //     {
            //         $language = Language::findOrFail($translation['language_id']);

            //         $translateName = (isset($translation['name'])) ? $translation['name'] : null;
            //         $translateWebsiteDescription = (isset($translation['website_description'])) ? $translation['website_description'] : null;
            //         $translateInternalDescription = (isset($translation['internal_description'])) ? $translation['internal_description'] : null;

            //         $company->translations()->create([
            //             'language_id' => $language->id,
            //             'locale' => $language->code,
            //             'name' => $translateName, 
            //             'website_description' => $translateWebsiteDescription, 
            //             'internal_description' => $translateInternalDescription, 
            //         ]);
            //     }
            // }

            DB::commit();

            $companyData = new CompanyResource($company);

            return response()->json([
                'status' => true,
                'company' => $companyData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        try {
            DB::beginTransaction();

            if($company->check_has_childs() || $company->company_type_id == '1')
            {
                return response()->json([
                    'status' => false
                ], 422);
            }
            $company->delete();;

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

    public function bulk_destroy(Request $request)
    {
        $validated = $request->validate([
            'companies' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $companies = Company::whereIn('id', $request->companies)->get();

            foreach($companies as $company)
            {
                if($company->check_has_childs() || $company->company_type_id == '1')
                {
                    return response()->json([
                        'status' => false
                    ], 422);
                }
            }
            Company::whereIn('id', $request->companies)->delete();;

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

    public function update_company_logo(Request $request, $id)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $company = Company::findOrFail($id);

        if ($request->hasFile('image')) {

            $imageName = \Str::random(6) . time().'.'.$request->image->extension();  
     
            $request->image->move(public_path('images/companies'), $imageName);

            $image = new Image;
            $image->file_name = $imageName;

            if($company->logo) {

                $d_image_path = public_path('images/companies') . '/' . $company->logo->file_name;
                if(File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $company->logo()->update(['file_name' => $imageName]);
            }else{
                $company->logo()->create(['file_name' => $imageName]);
            }
        }

        $companyData = new CompanyResource(Company::find($company->id));

        return response()->json([
            'status' => true,
            'company' => $companyData
        ]);
    }

    public function remove_company_logo(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        
        if($company->logo)
        {
            $d_image_path = public_path('images/companies') . '/' . $company->logo->file_name;

            if(File::exists($d_image_path)) {
                File::delete($d_image_path);
            }

            $company->logo->delete();
        }

        return response()->json([
            'status' => true,
        ]);
    }


    public function prepare_filter($request)
    {
        $filter = [];

        if($request->region_id)
        {
            array_push($filter, array('region_id', $request->region_id));
        }

        if($request->country_id)
        {
            array_push($filter, array('country_id', $request->country_id));
        }

        if($request->city_id)
        {
            array_push($filter, array('city_id', $request->city_id));
        }

        if($request->company_type_id)
        {
            array_push($filter, array('company_type_id', $request->company_type_id));
        }

        $user = $request->user();

        if($user->details && $user->details->company && $user->details->company->company_type_id != 1)
        {
            array_push($filter, array('id', $user->details->company_id));
        }
        // if($request->search)
        // {
        //     $columns = Schema::getColumnListing('companies');
        //     foreach($columns as $column)
        //     {
        //         array_push($filter, array($column, 'LIKE', '%' . $request->search . '%'));
        //     }
        // }

        return $filter;
    }

    public function create_new_item($data)
    {
        return Company::create($data);
    }
}
