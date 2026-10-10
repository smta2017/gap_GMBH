<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CompanyType;
use App\Models\UserDetails;

class EnsureAdmin
{
    /**
     * Handle an incoming request for administrative routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $details = $user->details;
        $isAuthorizedAdmin = false;

        if ($details) {
            $companyType = $details->company ? $details->company->company_type_id : null;
            
            // Authorized Admin must belong to Golf Globe (GG) and not be a client role
            if ($companyType == CompanyType::GG && $details->role_id != UserDetails::CLIENT_ROLE) {
                $isAuthorizedAdmin = true;
            }
        }

        if (!$isAuthorizedAdmin) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Forbidden. Administrative access required.'], 403);
            }
            return response()->json(['message' => 'Forbidden. Administrative access required.'], 403);
        }

        return $next($request);
    }
}
