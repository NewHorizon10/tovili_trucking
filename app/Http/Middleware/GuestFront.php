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

class GuestFront 
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */ 
    public function handle($request, Closure $next) {
        if (Session::has('admin_applocale') &&  Session::get('admin_applocale') != "") {
            App::setLocale(Session::get('admin_applocale'));
        }else {
            App::setLocale(Config::get('app.fallback_locale'));
        }
        return $next($request);
    }

}
