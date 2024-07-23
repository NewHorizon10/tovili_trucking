<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UsersController;
use App\Http\Controllers\api\driver\v1\DriverApiController;
use App\Http\Controllers\api\driver\v1\ShippingRequestsController;
use App\Http\Controllers\frontend\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['DriverGuestApi', 'ResponseMiddleware'])->group(function () {
    Route::post('login', [DriverApiController::class, 'login']);
    Route::post('verify-login-otp', [DriverApiController::class, 'verifyLoginOtp']);
    Route::post('verify-otp',[DriverApiController::class, 'verifyOtp']);
    Route::post('reset-password', [DriverApiController::class, 'resetPassword']);
    Route::post('create-new-password', [DriverApiController::class, 'createNewPassword']);
});

Route::middleware(['DriverAuthApi', 'ResponseMiddleware'])->group(function () {
    /*  User profile update Route */
    Route::post('change-password', [DriverApiController::class, 'changePassword']);
    Route::post('update-profile', [DriverApiController::class, 'updateProfile']);
    Route::post('share-location', [DriverApiController::class, 'updateDriverLocation']);


    /*  User dashboard Route */
    Route::get('dashboard', [DriverApiController::class, 'dashboard']);
    Route::get('notifications', [DriverApiController::class, 'notifications']);
    Route::post('clear-selected-notifications', [DriverApiController::class, 'clearSelectedNotifications']);
    Route::post('clear-all-notifications', [DriverApiController::class, 'clearSelectedNotifications']);
    
    Route::post('view-notifications', [DriverApiController::class, 'viewNotifications']);
    /*  User shipment Route */
    Route::get('current-shipment', [ShippingRequestsController::class, 'currentShipment']);
    Route::post('shipment-schedule-start', [ShippingRequestsController::class, 'shipmentScheduleStart']);
    Route::get('view-shipment-request/{shipmentid?}', [ShippingRequestsController::class, 'viewShipmentRequests']);
    Route::post('upload-shipment-stop-document', [ShippingRequestsController::class, 'uploadShipmentStopDocument']);
    Route::post('upload-shipment-stop-signature', [ShippingRequestsController::class, 'uploadShipmentStopSignature']);
    Route::post('shipment-schedule-end', [ShippingRequestsController::class, 'shipmentScheduleEnd']);

    Route::match(['get', 'post'],'chat', [MessageController::class, 'index']);
    Route::match(['get', 'post'], 'toggle-chat', [MessageController::class, 'toggleChat']);
    Route::match(['post'], 'attachment-image', [MessageController::class, 'attachment_image']);        
    Route::match(['post'], 'portfolio-image-add-delete', [MessageController::class, 'portfolio_image_add_delete']); 
    Route::match(['get', 'post'],'send-sms', [MessageController::class, 'sendSmsUser']);
    
    /*  User logout Route */
    Route::post('logout',  [DriverApiController::class, 'logout']);
    Route::post('change-language', [DriverApiController::class, 'changeLanguage']);

    // Skip certificate upload
    Route::match(['get', 'post'], 'skip-certificate-upload', [ShippingRequestsController::class, 'skipCerificateUpload']);
});