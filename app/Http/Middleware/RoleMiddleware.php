<?php
namespace App\Http\Middleware;

use Closure;
use RA\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $roles = explode('|', strtolower($roles));

        //check user type
        if ( !in_array(\Auth::user()->team->role, $roles) ) {
            return Response::error('Not allowed.');
        }

        return $next($request);
    }
}
