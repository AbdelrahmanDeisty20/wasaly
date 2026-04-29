<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class serviceProviderOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()->type !== 'service_provider'){
            return response()->json([
                'status' => false,
                'message' => __('messages.unauthorized_action'),
            ], 403);
        }
        return $next($request);
    }
}
