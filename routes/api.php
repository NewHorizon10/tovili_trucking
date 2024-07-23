<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['GuestApi','ResponseMiddleware'])->group(function () {
    // Language Update Route:-
    // Route::get('update-language/{lang_code}', [App\Http\Controllers\frontend\AllPageController::class,'updateLanguage']);

    // Users route
    Route::match(['get','post'],'login', [App\Http\Controllers\frontend\UsersController::class,'login']);
    Route::match(['get','post'],'reset-password', [App\Http\Controllers\frontend\UsersController::class,'resetPassword']);
    Route::match(['get','post'],'verify-otp/{token}', [App\Http\Controllers\frontend\UsersController::class,'verifyOtp']);
    Route::match(['get','post'],'create-new-password/{token}', [App\Http\Controllers\frontend\UsersController::class,'createNewPassword']);

    Route::match(['get','post'],'verify-phone-number', [App\Http\Controllers\frontend\UsersController::class,'verifyPhoneNumber']);
    Route::match(['get','post'],'sign-up', [App\Http\Controllers\frontend\UsersController::class,'signUp']);
    Route::match(['get','post'],'verify-account', [App\Http\Controllers\frontend\UsersController::class,'verify_account']);
    Route::get('forgot', [App\Http\Controllers\frontend\UsersController::class,'forgot']);
    Route::post('forgot-save', [App\Http\Controllers\frontend\UsersController::class,'forgotSave']);
    Route::match(['get','post'],'reset-password/{token}', [App\Http\Controllers\frontend\UsersController::class,'resetPassword']);
    Route::match(['get','post'],'reset-password-save/{token}', [App\Http\Controllers\frontend\UsersController::class,'resetPasswordSave']);
    /* socialLogin Route */
    /* End socialLogin Route */
   
    Route::middleware(['AuthApi','ResponseMiddleware'])->group(function () {
        /*  User profile update Route */
        Route::get('manage-profile', [App\Http\Controllers\frontend\UsersController::class,'manageProfile'])->name('manage-profile');
        Route::match(['post'],'update-company-details',[App\Http\Controllers\frontend\UsersController::class,'updateCompanyDetails']);
        Route::match(['post'],'update-person-details',[App\Http\Controllers\frontend\UsersController::class,'updatePersonDetails']);
        Route::match(['post'],'change-password',[App\Http\Controllers\frontend\UsersController::class,'changePassword']);
        Route::match(['post'],'update-profile',[App\Http\Controllers\frontend\UsersController::class,'updateProfile']);
        /*  User  logout Route */
        Route::match(['get','post'],'logout',  [App\Http\Controllers\frontend\UsersController::class,'logout']);
    
    });

});