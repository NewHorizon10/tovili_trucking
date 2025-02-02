<?php
namespace App\Http\Middleware;

use Closure;
Use Auth;
Use Redirect;
use Response;
use DB;
use Config;
use Input;
use Illuminate\Http\Request;
use App;
use App\Model\MobileApiLog;

class DriverGuestApi
{
    /**
    * Run the request filter.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next){
      if(!empty($request->header('Accept-Language'))){
        App::setLocale($request->header('Accept-Language'));
      }else{
        App::setLocale("he");
      }
	    return $next($request);
	}
}
