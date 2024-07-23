<?php

namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect;
Use Session;
Use Response;
Use App;
Use Config;
use Illuminate\Http\Request;

class IfUserNotLogin 
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if(Auth::user()){
            if(Auth::user()->user_role_id == 2){
                return redirect::route(Auth::user()->customer_type.'.customer-dashboard');
            }
        }
        return $next($request);
    }

}
