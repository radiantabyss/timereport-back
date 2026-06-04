<?php
namespace App\Http\Middleware;

use Closure;
use RA\Response;

class TypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $types)
    {
        $types = explode('|', strtolower($types));
        $types[] = 'super_admin';

        //check user type
        if ( !in_array(\Auth::user()->type, $types) ) {
            return Response::error('Not allowed.');
        }

        return $next($request);
    }
}
