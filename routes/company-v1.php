<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UsersController;
use App\Http\Controllers\api\company\v1\CompanyApiController;
use App\Http\Controllers\api\company\v1\ShippingRequestsController;
use App\Http\Controllers\api\company\v1\TruckController;
use App\Http\Controllers\api\company\v1\TransactionsController;
use App\Http\Controllers\frontend\MessageController;

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

Route::middleware(['CompanyGuestApi', 'ResponseMiddleware'])->group(function () {
    Route::post('login', [CompanyApiController::class, 'login']);
    Route::post('reset-password', [CompanyApiController::class, 'resetPassword']);
    Route::post('verify-otp',[CompanyApiController::class, 'verifyOtp']);
    Route::post('create-new-password', [CompanyApiController::class, 'createNewPassword']);
    Route::get('company-type', [CompanyApiController::class, 'companyType']);
});

Route::middleware(['CompanyAuthApi', 'ResponseMiddleware'])->group(function () {
    /*  User profile update Route */
    Route::get('truck-company-details', [CompanyApiController::class, 'truckCompanyDetails']);
    Route::post('update-company-details', [CompanyApiController::class, 'updateCompanyDetails']);
    Route::post('update-person-details', [CompanyApiController::class, 'updatePersonDetails']);
    Route::post('change-password', [CompanyApiController::class, 'changePassword']);

    // driver  routes
    Route::post('add-truck-driver', [TruckController::class, 'addTruckDriver']);
    Route::post('update-truck-driver', [TruckController::class, 'updateTruckDriver']);
    Route::post('truck-drivers', [TruckController::class, 'truckDrivers']);
    Route::post('delete-truck-driver', [TruckController::class, 'truckDriverDelete']);

    // Truck routes
    Route::post('add-truck', [TruckController::class, 'addTruck']);
    Route::get('trucks-list', [TruckController::class, 'trucksList']);
    Route::get('truck-types', [TruckController::class, 'truckTypes']);
    Route::post('delete-truck', [TruckController::class, 'truckDelete']);
    Route::post('update-truck', [TruckController::class, 'updateTruck']);

    
    // driver  routes
    //Route::get('new-requests', [CompanyApiController::class, 'newRequests']);

    /*  User  logout Route */
    Route::post('logout',  [CompanyApiController::class, 'logout']);

    // dashboard  routes
    Route::get('dashboard', [CompanyApiController::class, 'dashboard']);
    Route::get('notifications', [CompanyApiController::class, 'notifications']);
    Route::post('clear-selected-notifications', [CompanyApiController::class, 'clearSelectedNotifications']);
    Route::post('clear-all-notifications', [CompanyApiController::class, 'clearSelectedNotifications']);

    Route::get('transactions', [TransactionsController::class, 'transactions']);

    Route::post('view-notifications', [CompanyApiController::class, 'viewNotifications']);

    // Shipment routes
    Route::get('new-requests', [ShippingRequestsController::class, 'newRequests']);
    Route::get('waiting-requests', [ShippingRequestsController::class, 'waitingRequests']);
    Route::get('previous-requests', [ShippingRequestsController::class, 'previousRequests']);
    Route::delete('delete-request', [ShippingRequestsController::class, 'deleteRequests']);
    Route::post('apply-offer', [ShippingRequestsController::class, 'applyOffer']);
    Route::post('edit-apply-offer', [ShippingRequestsController::class, 'editApplyOffer']);
    Route::post('reject-shipment-requests', [ShippingRequestsController::class, 'rejectShipmentRequests']);
    Route::post('approved-shipment-requests', [ShippingRequestsController::class, 'approvedShipmentRequests']);
    Route::get('view-request/{shipmentid?}', [ShippingRequestsController::class, 'viewRequests']);

    Route::get('shipment-requests', [ShippingRequestsController::class, 'shipmentRequests']);
    Route::get('view-shipment-request/{shipmentid?}', [ShippingRequestsController::class, 'viewShipmentRequests']);

    Route::get('current-shipment', [ShippingRequestsController::class, 'currentShipment']);
    Route::post('cancel-shipment', [ShippingRequestsController::class, 'cancelShipment']);
    Route::post('shipment-schedule', [ShippingRequestsController::class, 'shipmentSchedule']);
    Route::post('delete-shipment-schedule', [ShippingRequestsController::class, 'deleteShipmentSchedule']);


    // Truck routes
    Route::get('shipment-trucks-list', [ShippingRequestsController::class, 'shipmentTrucksList']);
    Route::match(['get', 'post'],'chat', [MessageController::class, 'index']);
    Route::match(['get', 'post'], 'toggle-chat', [MessageController::class, 'toggleChat']);
    Route::match(['post'], 'attachment-image', [MessageController::class, 'attachment_image']);        
    Route::match(['post'], 'portfolio-image-add-delete', [MessageController::class, 'portfolio_image_add_delete']); 
    Route::match(['get', 'post'],'send-sms', [MessageController::class, 'sendSmsUser']);

    Route::match(['get', 'post'],'send-shipment-invoice', [ShippingRequestsController::class, 'sendShipmentInvoice']);
    Route::match(['get', 'post'],'resend-shipment-invoice', [ShippingRequestsController::class, 'resendShipmentInvoice']);
    Route::match(['get', 'post'],'make-payment-status', [ShippingRequestsController::class, 'makePaymentStatus']);

    // activated driver routes
    Route::post('activate-driver', [CompanyApiController::class, 'activateDriver']);
    Route::post('change-language', [CompanyApiController::class, 'changeLanguage']);
    Route::post('/send-proposal', [CompanyApiController::class, 'sendProposal']);

    // My subscription plan 
    Route::get('my-subscription', [CompanyApiController::class, 'mySubscription']);

     // Skip certificate upload
     Route::match(['get', 'post'], 'skip-certificate-upload', [ShippingRequestsController::class, 'skipCerificateUpload']);

    
});