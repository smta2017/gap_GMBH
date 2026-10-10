<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Integration;
use Carbon\Carbon;

class ApiKey
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
        $token = null;
        $header = $request->header('Authorization', '');
        $position = strrpos($header, 'Bearer ');

        if ($position !== false) {
            $header = substr($header, $position + 7);

            $token = strpos($header, ',') !== false ? strstr($header, ',', true) : $header;
        }

        if (!$token) {
            return $this->unauthenticatedRespose();
        }

        $integration = Integration::where('api_key', $token)
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('expiry_date', '>=', Carbon::now()->format('Y-m-d'))
                  ->orWhere('no_expire', 1);
            })->first();

        if (!$integration) {
            return $this->unauthenticatedRespose();
        }

        return $next($request);
    }

    public function unauthenticatedRespose()
    {
        return response()->json([
            "message" => "Unauthenticated.updated"
        ], 401);
    }
}
