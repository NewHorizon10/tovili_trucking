<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
 
        'AuthAdmin' => [
            \App\Http\Middleware\AuthAdmin::class,
        ],
        'GuestFront' => [
            \App\Http\Middleware\GuestFront::class,
        ],
        'UsersFront' => [
            \App\Http\Middleware\UsersFront::class,
        ], 
         'AuthFront' => [
            \App\Http\Middleware\AuthFront::class,
        ], 
        'PrivateCustomer' => [
           \App\Http\Middleware\PrivateCustomer::class,
        ], 
        'BusinessCustomer' => [
           \App\Http\Middleware\BusinessCustomer::class,
        ], 

        'CompanyAuthApi' => [
            \App\Http\Middleware\CompanyAuthApi::class,
        ], 
        'DriverAuthApi' => [
            \App\Http\Middleware\DriverAuthApi::class,
        ], 
        'CompanyGuestApi' => [
            \App\Http\Middleware\CompanyGuestApi::class,
        ], 
        'DriverGuestApi' => [
            \App\Http\Middleware\DriverGuestApi::class,
        ], 


    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'GuestApi' => \App\Http\Middleware\GuestApi::class,
        'ResponseMiddleware' => \App\Http\Middleware\ResponseMiddleware::class,
        'AuthApi' => \App\Http\Middleware\AuthApi::class,
        'IfUserNotLogin' => \App\Http\Middleware\IfUserNotLogin::class,
    ];
}