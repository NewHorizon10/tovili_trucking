<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class Language
{
    public function handle($request, Closure $next) {
        if (Session::has('applocale')) { 
            App::setLocale(Session::get('applocale'));	
            
        }else {
            App::setLocale(Session::get('applocale'));	
        }
        return $next($request);
    }
}