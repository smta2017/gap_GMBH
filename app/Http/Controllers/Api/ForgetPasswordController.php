<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ForgetPasswordMail;
use Carbon\Carbon;
use App\Models\User;
use DB;

class ForgetPasswordController extends Controller
{
    public function forget_password(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            'route_url' => 'required'
        ]);

        $email = $request->email;
        $routeUrl = $request->route_url;

        $user = User::where('email', $email)->first();

        if(!$user)
        {
            return response()->json([
                'status' => false
            ], 404);
        }
        
        $token = \Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $link = $request->route_url . '?token=' . urlencode($token);

        $data = [
            'title' => 'Forgot your password?',
            'body' => 'You have requested to reset your password. To reset your password, click the following link and follow the instructions.',
            'link' => $link
        ];
       
        \Mail::to($user->email)->send(new ForgetPasswordMail($data));
        
        return response()->json([
            'status' => true
        ]);
    }

    public function forget_password_store(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $password = $request->password;
        $token = $request->token;


        $tokenData = DB::table('password_resets')->where('token', $token)->first();

        if(!$tokenData)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        $user = User::where('email', $tokenData->email)->first();

        if(!$user)
        {
            return response()->json([
                'status' => false
            ], 404);
        }

        try {

            DB::beginTransaction();
            
            $user->update([
                'password' => bcrypt($request->password)
            ]);

            DB::table('password_resets')->where('token', $tokenData->token)->delete();
            
            DB::commit();

            return response()->json([
                'status' => true
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
