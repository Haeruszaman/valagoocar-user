<?php

namespace App\Http\Middleware;
use Illuminate\Http\Response;
use App\Users;
use Closure;
use Auth;

class CheckManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if (Auth::user()->roles != 'Manager') {

            return new Response(null, 204);
        }

        return $next($request);
    }
}