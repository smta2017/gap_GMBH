<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyMinResource;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Company;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserFullDataResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserActivityResource;
use App\Models\PersonalAccessTokenModel;
use DB;
use File;

class UserController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $users = new User();
        
        $usersData = UserFullDataResource::collection($users->get_pagination($filter));

        return response()->json([
            'status' => true,
            'users' => $usersData->response()->getData()
        ]);
    }

    public function get_all_companies()
    {
        $search = request()->input('search');
        $companyTypeID = request()->input('company_type_id');
        $companyTypeIDs = request()->input('company_type_list');

        $companies = new Company();

        if($search)
        {
            $companies = $companies->where('name' , 'LIKE', '%' . $search . '%');
        }

        if($companyTypeID)
        {
            $companies = $companies->where('company_type_id', $companyTypeID);
        }
        if($companyTypeIDs && is_array($companyTypeIDs))
        {
            $companies = $companies->whereIn('company_type_id', $companyTypeIDs);
        }

        $companiesData = CompanyResource::collection($companies->get());

        return response()->json([
            'status' => true,
            'companies' => $companiesData
        ]);
    }

    public function get_all_companies_new()
    {
        $search = request()->input('search');
        $companyTypeID = request()->input('company_type_id');
        $companyTypeIDs = request()->input('company_type_list');

        $companies = new Company();

        if($search)
        {
            $companies = $companies->where('name' , 'LIKE', '%' . $search . '%');
        }

        if($companyTypeID)
        {
            $companies = $companies->where('company_type_id', $companyTypeID);
        }
        if($companyTypeIDs && is_array($companyTypeIDs))
        {
            $companies = $companies->whereIn('company_type_id', $companyTypeIDs);
        }

        $companiesData = CompanyMinResource::collection($companies->get());

        return response()->json([
            'status' => true,
            'companies' => $companiesData
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        $userData = new UserFullDataResource($user);

        return response()->json([
            'status' => true,
            'user' => $userData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'first_name' => 'required',
            'last_name' => 'required',
            // 'mobile_number' => 'required',
            // 'department' => 'required',
            // 'title' => 'required',
            'role_id' => 'required|exists:roles,id',
            'company_id' => 'required|exists:companies,id',

            'childs' => 'array',
            'childs.*.child_id' => 'required',
            'childs.*.child_type_id' => 'required|exists:company_types,id',
        ]);


        $username = $request->username;
        $email = $request->email;
        $password = $request->password;

        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $mobile_number = $request->mobile_number;
        $fax = $request->fax;
        $title = $request->title;
        $department = $request->department;

        $role_id = $request->role_id;
        $company_id = $request->company_id;

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => bcrypt($password),
            ]);

            $userDetails = UserDetails::create([
                'user_id' => $user->id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile_number' => $mobile_number,
                'fax' => $fax,
                'title' => $title,
                'department' => $department,
                'role_id' => $role_id,
                'company_id' => $company_id,
                'address_book_id' => $request->address_book_id,
            ]);

            if ($request->hasFile('image')) {

                $imageName = \Str::random(6) . time().'.'.$request->image->extension();  
         
                $request->image->move(public_path('images/users'), $imageName);
    
                $image = new Image;
                $image->file_name = $imageName;
    
                $user->image()->create(['file_name' => $imageName]);
                
            }

            if(is_array($request->childs))
            {
                foreach($request->childs as $child)
                {        
                    $user->childs()->create(['child_id' => $child['child_id'], 'child_type_id' => $child['child_type_id']]);
                }
            }

            DB::commit();

            $userData = new UserFullDataResource($user);

            return response()->json([
                'status' => true,
                'user' => $userData
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
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'unique:users,username,' . $user->id,
            'email' => 'email|unique:users,email,' . $user->id,
            'first_name' => 'required',
            'last_name' => 'required',
            // 'mobile_number' => 'required',
            // 'department' => 'required',
            // 'title' => 'required',
            // 'role_id' => 'required|exists:roles,id',
            'company_id' => 'required|exists:companies,id',

            'childs' => 'array',
            'childs.*.child_id' => 'required',
            'childs.*.child_type_id' => 'required|exists:company_types,id',
        ]);

        $username = $request->username;
        $email = $request->email;
        $password = $request->password;

        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $mobile_number = $request->mobile_number;
        $fax = $request->fax;
        $title = $request->title;
        $department = $request->department;

        $role_id = $request->role_id;
        $company_id = $request->company_id;

        try {
            DB::beginTransaction();

            if($username)
            {
                $user->update([
                    'username' => $username,
                ]);
            }

            if($email)
            {
                $user->update([
                    'email' => $email
                ]);
            }

            if($user->role_id != $role_id)
            {
                $user->tokens()->delete();
            }

            $user->details->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile_number' => $mobile_number,
                'fax' => $fax,
                'title' => $title,
                'department' => $department,
                'role_id' => $role_id,
                'company_id' => $company_id,
                'address_book_id' => $request->address_book_id
            ]);
            
            if(is_array($request->childs))
            {
                $user->childs()->forceDelete();
                foreach($request->childs as $child)
                {        
                    $user->childs()->create(['child_id' => $child['child_id'], 'child_type_id' => $child['child_type_id']]);
                }
            }

            DB::commit();

            $userData = new UserFullDataResource($user);

            return response()->json([
                'status' => true,
                'user' => $userData
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
        $user = User::findOrFail($id);

        try {
            DB::beginTransaction();

            $user->details->delete();
            $user->delete();

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


    public function update_image(Request $request, $id)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $user = User::findOrFail($id);

        if ($request->hasFile('image')) {

            $imageName = \Str::random(6) . time().'.'.$request->image->extension();  
     
            $request->image->move(public_path('images/users'), $imageName);

            $image = new Image;
            $image->file_name = $imageName;

            if($user->image) {

                $d_image_path = public_path('images/users') . '/' . $user->image->file_name;
                if(File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $user->image()->update(['file_name' => $imageName]);
            }else{
                $user->image()->create(['file_name' => $imageName]);
            }
        }

        $userData = new UserFullDataResource($user);

        return response()->json([
            'status' => true,
            'user' => $userData
        ]);
    }

    public function remove_image(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if($user->image)
        {
            $d_image_path = public_path('images/users') . '/' . $user->image->file_name;

            if(File::exists($d_image_path)) {
                File::delete($d_image_path);
            }

            $user->image->delete();
        }

        return response()->json([
            'status' => true,
        ]);
    }


    public function prepare_filter($request)
    {
        $filter = [];


        // if($request->country_id)
        // {
        //     array_push($filter, array('country_id', $request->country_id));
        // }

        // if($request->city_id)
        // {
        //     array_push($filter, array('city_id', $request->city_id));
        // }

        // if($request->company_type_id)
        // {
        //     array_push($filter, array('company_type_id', $request->company_type_id));
        // }

        return $filter;
    }

    public function update_lang($id,$lang)
    {
        $user_details  = UserDetails::whereUserId($id)->first();

        $user_details->update(['lang'=>$lang]);

        $user= User::find($id);
        
        $userData = new UserFullDataResource($user);

        return response()->json([
            'status' => true,
            'user' => $userData
        ]);
    }
    
    public function activity()
    {
        $filter = $this->prepare_filter(request());

        $personalAccessToken= new PersonalAccessTokenModel();

        $tokinable = $personalAccessToken->get_pagination($filter);
        
       return UserActivityResource::collection($tokinable);

    }

}
