<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\UserDetails;
use App\Models\Image;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailsResource;
use App\Http\Resources\UserFullDataResource;
use App\Http\Resources\CompanyResource;
use Carbon\Carbon;
use File;
use DB;
use Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = request()->user();

        $userData = new UserFullDataResource($user);

        return response()->json([
            'status' => true,
            'user' => $userData,
        ]);
    }

    public function update_profile(Request $request)
    {
        $user = request()->user();  

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            // 'mobile_number' => 'required',
            // 'department' => 'required',
            // 'title' => 'required',

            'childs' => 'array',
            'childs.*.child_id' => 'required',
            'childs.*.child_type_id' => 'required|exists:company_types,id',
        ]);

        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $mobile_number = $request->mobile_number;
        $fax = $request->fax;
        $title = $request->title;
        $department = $request->department;
        
        $role_id = $request->role_id;
        $company_id = $request->company_id;

        $userDetails = UserDetails::where('user_id', $user->id)->first();

        if($userDetails->role_id != $role_id)
        {
            $userDetails->user->tokens()->delete();
        }

        try {
            DB::beginTransaction();

            $userDetails->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile_number' => $mobile_number,
                'fax' => $fax,
                'title' => $title,
                'department' => $department,
                'role_id' => $role_id,
                'company_id' => $company_id,
            ]);

            $user->update([
                'email' => $email
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
                'user' => $userData,
                'token' => request()->bearerToken()
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_image(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = request()->user();
        
        $userDetails = UserDetails::where('user_id', $user->id)->first();

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
            'user' => $userData,
            'token' => request()->bearerToken()
        ]);
    }

    public function reset_password(Request $request)
    {
        $validated = $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = request()->user();
        
        $oldPassword = $request->old_password;
        $newPassword = $request->password;
        if (!Hash::check($oldPassword, $user->password)) {
            return response()->json([
                'status' => false
            ], 422);
        }

        $user->update([
            'password' => bcrypt($newPassword)
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function check_email(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = request()->user();
        
        $emailCheck = User::where('email', $request->email)->where('id', '!=', $user->id)->first();

        if($emailCheck)
        {
            $email_valid = false;
        }else{
            $email_valid = true;
        }

        return response()->json([
            'status' => $email_valid
        ]);
    }

    public function remove_image(Request $request)
    {
        $user = request()->user();
        
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

    public function get_company_profile()
    {
        $user = request()->user();

        $companyData = new CompanyResource($user->details->company);

        return response()->json([
            'status' => true,
            'company' => $companyData,
        ]);
    }

    public function update_company_profile(Request $request)
    {
        $user = request()->user();  

        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required',
    
            // 'delegate_name' => 'string',
            // 'delegate_email' => 'email',
            // 'delegate_mobile_number' => 'string',
    
            'region_id' => 'required|exists:regions,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'required',
            'street' => 'required',
        ]);

        $company = $user->details->company;

        try {
            DB::beginTransaction();

            $company->update($request->all());

            DB::commit();

            $companyData = new CompanyResource($user->details->company);

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

    public function update_company_logo(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = request()->user();
   
        if ($request->hasFile('image')) {

            $imageName = \Str::random(6) . time().'.'.$request->image->extension();  
     
            $request->image->move(public_path('images/companies'), $imageName);

            $image = new Image;
            $image->file_name = $imageName;

            if($user->details->company->logo) {

                $d_image_path = public_path('images/companies') . '/' . $user->details->company->logo->file_name;
                if(File::exists($d_image_path)) {
                    File::delete($d_image_path);
                }

                $user->details->company->logo()->update(['file_name' => $imageName]);
            }else{
                $user->details->company->logo()->create(['file_name' => $imageName]);
            }
        }

        $companyData = new CompanyResource(Company::find($user->details->company->id));

        return response()->json([
            'status' => true,
            'company' => $companyData
        ]);
    }

    public function remove_company_logo(Request $request)
    {
        $user = request()->user();
        
        if($user->details->company->logo)
        {
            $d_image_path = public_path('images/companies') . '/' . $user->details->company->logo->file_name;

            if(File::exists($d_image_path)) {
                File::delete($d_image_path);
            }

            $user->details->company->logo->delete();
        }

        return response()->json([
            'status' => true,
        ]);
    }
}
