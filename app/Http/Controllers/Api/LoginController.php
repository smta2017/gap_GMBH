<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Company;
use App\Models\DeviceKey;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserFullDataResource;
use App\Http\Resources\UserDetailsResource;
use App\Http\Resources\CompanyResource;
use App\Models\PersonalAccessTokenModel;
use DB;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->username;
        $password = $request->password;

        if (! \Auth::attempt(array('username' => $username, 'password' => $password))){
            return response()->json([
                'status' => false
            ], 401);
        }

        $user = User::where('username', $username)->first();

        if(!$user)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        if($request->device_key)
        {
            $user->deviceKeys()->create(['device_key' => $request->device_key]);
        }
        
        $userData = new UserFullDataResource($user);
        $token = $user->createToken((request()->device_name) ? request()->device_name : 'auth-token')->plainTextToken;

        $pt_id= (int) \explode('|',$token)[0];

        $user_login_data = [
            'ip'=>$request->ip,
            'geoip_city_name'=>$request->geoip_city_name,
            'browser_name'=>$request->browser_name
        ];
        
        // update user login info
        PersonalAccessTokenModel::find($pt_id)->update($user_login_data);

        return response()->json([
            'status' => true,
            'user' => $userData,
            'token' => $token
        ]);
    }

    public function direct_login(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        $user = User::find($userId);

        if(!$user)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        if($request->device_key)
        {
            $user->deviceKeys()->create(['device_key' => $request->device_key]);
        }
        
        $userData = new UserFullDataResource($user);

        $token= $user->createToken((request()->device_name) ? request()->device_name : 'auth-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'user' => $userData,
            'token' => $token
        ]);
    }

    public function token_login(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|exists:password_resets,token',
        ]);

        $token = $request->token;

        $tokenData = DB::table('password_resets')->where('token', $token)->first();

        if(!$tokenData)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        if(filter_var($tokenData->email, FILTER_VALIDATE_EMAIL)) {
            // valid address
            $user = User::where('email', $tokenData->email)->first();
        }
        else {
            // invalid address
            $user = User::where('id', $tokenData->email)->first();
        }
        
        if(!$user)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        // DB::table('password_resets')->where('token', $tokenData->token)->delete();

        if($request->device_key)
        {
            $user->deviceKeys()->create(['device_key' => $request->device_key]);
        }
        
        $userData = new UserFullDataResource($user);

        $access_token = $user->createToken((request()->device_name) ? request()->device_name : 'auth-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'user' => $userData,
            'token' => $access_token
        ]);
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        if($request->device_key)
        {
            DeviceKey::where('device_key', $request->device_key)->where('user_id', $user->id)->forceDelete();
        }

        return response()->json([
            'status' => true
        ]);
    }

    /**
     * The user has refresh his expired token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
    */

    protected function refresh_token(Request $request)
    {
        $token = $request->bearerToken();

        if(!$token)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        $model = 'Laravel\\Sanctum\\PersonalAccessToken';

        $accessToken = $model::findToken($token);

        $user_id = $accessToken->tokenable_id;

        $user = User::findOrFail($user_id);

        $refresh_token = $user->createToken((request()->device_name) ? request()->device_name : 'auth-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'token' => $refresh_token
        ]);
    }
}
