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

class PrivateCustomer 
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if(Auth::user()->user_role_id == 2 && Auth::user()->customer_type == "private"){
            return $next($request);
        }
        return Redirect::to('/business/customer-dashboard');
    }

}
