<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class SanctumValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = request()->bearerToken();

        if($token)
        {
            $model = 'Laravel\\Sanctum\\PersonalAccessToken';

            $accessToken = $model::findToken($token);
            
            if($accessToken)
            {
                $accessToken->created_at = Carbon::now();
                $accessToken->updated_at = Carbon::now();
                $accessToken->save();
            }
        }

        return $next($request);
    }
}
