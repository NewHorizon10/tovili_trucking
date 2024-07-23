<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminpnlx\OurServicesController;

Route::prefix('adminpnlx')->middleware(['GuestFront'])->group(function () {
    Route::match(['get', 'post'], '', [App\Http\Controllers\adminpnlx\LoginController::class, 'login'])->name('adminpnlx');
    Route::match(['get', 'post'], 'forget_password', [App\Http\Controllers\adminpnlx\LoginController::class, 'forgetPassword'])->name('forgetPassword');
    Route::match(['get', 'post'], 'reset_password/{validstring}', [App\Http\Controllers\adminpnlx\LoginController::class, 'resetPassword'])->name('reset_password/{validstring}');
    Route::match(['get', 'post'], 'save_password', [App\Http\Controllers\adminpnlx\LoginController::class, 'save_password'])->name('save_password');

    Route::middleware(['AuthAdmin'])->group(function () {
        /*dashboard Route */
        Route::get('get-chat-count', [App\Http\Controllers\adminpnlx\AdminDashboardController::class, 'getChatCount'])->name('get_chat_count');

        Route::get('dashboard', [App\Http\Controllers\adminpnlx\AdminDashboardController::class, 'showdashboard'])->name('dashboard');
        Route::get('logout', [App\Http\Controllers\adminpnlx\LoginController::class, 'logout'])->name('logout');
        Route::match(['get', 'post'], 'myaccount', [App\Http\Controllers\adminpnlx\AdminDashboardController::class, 'myaccount'])->name('myaccount');
        Route::match(['get', 'post'], 'changedPassword', [App\Http\Controllers\adminpnlx\AdminDashboardController::class, 'changedPassword'])->name('changedPassword');

        /* customers routes */
        Route::match(['get', 'post'], '/customers', [App\Http\Controllers\adminpnlx\UsersController::class, 'index'])->name('users.index');
        Route::match(['get', 'post'], '/customers/create', [App\Http\Controllers\adminpnlx\UsersController::class, 'create'])->name('users.create');
        Route::post("/customers/save", [App\Http\Controllers\adminpnlx\UsersController::class, 'save'])->name('users.save');
        Route::match(['get', 'post'], '/customers/edit/{enuserid}', [App\Http\Controllers\adminpnlx\UsersController::class, 'edit'])->name('users.edit');
        Route::post("/customers/update/{enuserid}", [App\Http\Controllers\adminpnlx\UsersController::class, 'update'])->name('users.update');
        Route::get('customers/show/{enuserid}', [App\Http\Controllers\adminpnlx\UsersController::class, 'view'])->name('users.show');
        Route::get('customers/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\UsersController::class, 'destroy'])->name('users.delete');
        Route::get('customers/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\UsersController::class, 'changeStatus'])->name('users.status');
        Route::get('customers/approve-status/{id}/{status}', [App\Http\Controllers\adminpnlx\UsersController::class, 'approvestatus'])->name('users.Approvestatus');
        Route::match(['get', 'post'], 'customers/changed-password/{enuserid?}', [App\Http\Controllers\adminpnlx\UsersController::class, 'changedPassword'])->name('users.changedPassword');
        Route::get('customers/send-credentials/{id}', [App\Http\Controllers\adminpnlx\UsersController::class, 'sendCredentials'])->name('users.sendCredentials');
        Route::get('customers/deleterow/{id?}', [App\Http\Controllers\adminpnlx\UsersController::class, 'deleterow'])->name('users.deleterow');
        /* customers routes */


        /* customers routes */
        Route::match(['get', 'post'], '/private-customers', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'index'])->name('private-customers.index');
        Route::get('/private-customers/create', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'create'])->name('private-customers.create');
        Route::post("/private-customers/save", [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'save'])->name('private-customers.save');
        Route::get('private-customers/show/{enuserid}', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'view'])->name('private-customers.show');
        Route::get('/private-customers/edit/{enuserid}', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'edit'])->name('private-customers.edit');
        Route::post("/private-customers/update/{enuserid}", [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'update'])->name('private-customers.update');
        Route::get('private-customers/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'destroy'])->name('private-customers.delete');
        Route::get('private-customers/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'changeStatus'])->name('private-customers.status');
        Route::get('private-customers/send-credentials/{id}', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'sendCredentials'])->name('private-customers.sendCredentials');
        Route::get('private-customers/approve-status/{id}/{status}', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'approvestatus'])->name('private-customers.Approvestatus');
        Route::match(['get', 'post'], 'private-customers/changed-password/{enuserid?}', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'changedPassword'])->name('private-customers.changedPassword');
        Route::get('private-customers-export', [App\Http\Controllers\adminpnlx\PrivateCustomerController::class, 'export'])->name('private-customers.export');
        /* customers routes */

        /* Business customers routes */
        Route::match(['get', 'post'], '/business-customers', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'index'])->name('business-customers.index');
        Route::get('/business-customers/create', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'create'])->name('business-customers.create');
        Route::post("/business-customers/save", [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'save'])->name('business-customers.save');
        Route::get('business-customers/show/{enuserid}', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'view'])->name('business-customers.show');
        Route::get('/business-customers/edit/{enuserid}', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'edit'])->name('business-customers.edit');
        Route::post("/business-customers/update/{enuserid}", [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'update'])->name('business-customers.update');
        Route::get('business-customers/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'destroy'])->name('business-customers.delete');
        Route::get('business-customers/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'changeStatus'])->name('business-customers.status');
        Route::get('business-customers/send-credentials/{id}', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'sendCredentials'])->name('business-customers.sendCredentials');
        Route::get('business-customers/approve-status/{id}/{status}', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'approvestatus'])->name('business-customers.Approvestatus');
        Route::match(['get', 'post'], 'business-customers/changed-password/{enuserid?}', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'changedPassword'])->name('business-customers.changedPassword');
        Route::get('business-customers-export', [App\Http\Controllers\adminpnlx\BusinessCustomerController::class, 'export'])->name('business-customers.export');

        /*Business customers routes */

        /* Truck company routes */
        Route::match(['get', 'post'], '/truck-company-fueling-methods', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'truckCompanyFuelingMethods'])->name('truck-company.fueling-methods');
        Route::get('truck-company-fueling-methods-export', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'fuelingMethodsExport'])->name('truck-company.fueling-methods-export');

        Route::match(['get', 'post'], '/truck-company-tidaluk-company', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'truckCompanyTidalukCompany'])->name('truck-company.tidaluk-company');
        Route::get('truck-company-tidaluk-company-export', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'tidalukCompanyExport'])->name('truck-company.tidaluk-company-export');
        
        Route::match(['get', 'post'], '/truck-company', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'index'])->name('truck-company.index');
        Route::get('/truck-company/create', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'create'])->name('truck-company.create');
        Route::post("/truck-company/save", [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'save'])->name('truck-company.save');
        Route::get('truck-company/show/{enuserid}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'view'])->name('truck-company.show');
        Route::get('/truck-company/edit/{enuserid}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'edit'])->name('truck-company.edit');
        Route::post("/truck-company/update/{enuserid}", [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'update'])->name('truck-company.update');
        Route::get('truck-company/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'destroy'])->name('truck-company.delete');
        Route::get('truck-company/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'changeStatus'])->name('truck-company.status');
        Route::get('truck-company/send-credentials/{id}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'sendCredentials'])->name('truck-company.sendCredentials');
        Route::get('truck-company/approve-status/{id}/{status}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'approveStatus'])->name('truck-company.Approvestatus');
        Route::match(['get', 'post'], 'truck-company/changed-password/{enuserid?}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'changedPassword'])->name('truck-company.changedPassword');
        Route::match(['get', 'post'], '/truck-company-export-sample/{id}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'export'])->name('truck-company.export-sample');
        Route::match(['get', 'post'], '/truck-company/import', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'import'])->name('truck-company.import');
        Route::match(['get', 'post'], '/truck-company/import-list', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'importList'])->name('truck-company.importList');
        Route::match(['get', 'post'], '/truck-company/import-data', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'importListdata'])->name('truck-company.importListdata');

        Route::get('/truck-company/subscription-plan/{id}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'subscriptionPlan'])->name('truck-company.companySubscriptionPlans');
        Route::get('/truck-company/get-plan-duration/{paymentMethod?}/{type?}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'getPlanDuration'])->name('truck-company.getPlanDuration');

        /*Truck company routes */
        Route::get("/truck-company/truck-list/{truckId?}", [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'truckDetails'])->name('truck-company.index_truck');
        Route::match(['get', 'post'], '/truck-company/truck-details/create/{truckId?}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'truckDetailsCreate'])->name('truck-company.truck_create');
        Route::match(['get', 'post'], "/truck-company/truck-details/save/{truckId?}", [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'truckDetailsSave'])->name('truck-company.truck_save');

        Route::get('truck-company/truck-details/show/{enuserid}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'view_truck'])->name('truck-company.show_truck');
        Route::get('/truck-company/truck-details/edit/{enuserid}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'edit_truck'])->name('truck-company.edit_truck');
        Route::put("/truck-company/truck-details/update/{enuserid}", [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'update_truck'])->name('truck-company.update_truck');
        Route::get('truck-company/truck-details/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'destroy_truck'])->name('truck-company.delete_truck');
        Route::get('truck-company/truck-details/update-status/{enuserid}/{status}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'changeStatus_truck'])->name('truck-company.status_truck');
        Route::get("/truck-list", [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'allTruckList'])->name('truck-company.all-truck-list');
        Route::get('truck-company-export', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'tcexport'])->name('truck-company.export');

        Route::match(['get', 'post'], 'truck-company-plan-expiry-extend', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'extendPlanExpiry'])->name('truck-company-plan-expiry-extend');
        Route::match(['get', 'post'], 'fetch-truck-drivers', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'fetch_truck_drivers'])->name('fetch-truck-drivers');


        /* Truck company driver */
        Route::match(['get', 'post'], '/truck-company-driver', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'index'])->name('truck-company-driver.index');
        Route::get('/truck-company-driver/create', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'create'])->name('truck-company-driver.create');
        Route::post("/truck-company-driver/save", [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'save'])->name('truck-company-driver.save');
        Route::get('truck-company-driver/show/{enuserid}', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'view'])->name('truck-company-driver.show');
        Route::get('/truck-company-driver/edit/{enuserid}', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'edit'])->name('truck-company-driver.edit');
        Route::post("/truck-company-driver/update/{enuserid}", [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'update'])->name('truck-company-driver.update');
        Route::get('truck-company-driver/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'destroy'])->name('truck-company-driver.delete');
        Route::get('truck-company-driver/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'changeStatus'])->name('truck-company-driver.status');
        Route::get('truck-company-driver/send-credentials/{id}', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'sendCredentials'])->name('truck-company-driver.sendCredentials');
        Route::get('truck-company-driver/approve-status/{id}/{status}', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'approvestatus'])->name('truck-company-driver.Approvestatus');
        Route::match(['get', 'post'], 'truck-company-driver/changed-password/{enuserid?}', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'changedPassword'])->name('truck-company-driver.changedPassword');
        Route::get('truck-company-driver-export', [App\Http\Controllers\adminpnlx\TruckCompanyDriverController::class, 'export'])->name('truck-company-driver.export');
        
        //Admin Action Log Route
        Route::get('action-logs', [App\Http\Controllers\adminpnlx\AdminActionLogController::class, 'index'])->name('action-log.index');

        //End Admin Action Log Route 

        //Shipment Requst Route
        Route::get('shipment-request', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'index'])->name('shipment-request.index');
        
        Route::match(['get', 'post'],'shipment-request/apply-offer/{systemid}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'applyOffer'])->name('shipment-request.apply_offer');
        Route::match(['get', 'post'],'shipment-request/edit-apply-offer/{offerId}/{shipmentId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'editApplyOffer'])->name('shipment-request.edit_apply_offer');
        Route::match(['get', 'post'],'shipment-request/reject-shipment-offer/{offerId}/{shipmentId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'rejectShipmentOffer'])->name('shipment-request.reject_shipment_offer');
        Route::match(['get', 'post'],'shipment-request/approve-shipment-offer/{offerId}/{shipmentId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'approveShipmentOffer'])->name('shipment-request.approve_shipment_offer');
        Route::match(['get', 'post'],'shipment-request/shipment-schedule/{offerId}/{shipmentId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'shipmentSchedule'])->name('shipment-request.shipment-schedule');
        Route::match(['get', 'post'],'shipment-request/delete-shipment-schedule/{scheduleId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'deleteShipmentSchedule'])->name('shipment-request.delete-shipment-schedule');
        Route::match(['get', 'post'],'shipment-request/shipment-schedule-start/{scheduleId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'shipmentScheduleStart'])->name('shipment-request.shipment-schedule-start');
        Route::match(['get', 'post'],'shipment-request/shipment-schedule-end/{scheduleId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'shipmentScheduleEnd'])->name('shipment-request.shipment-schedule-end');
        Route::match(['get', 'post'],'shipment-request/send-shipment-invoice/{shipmentId}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'sendShipmentInvoice'])->name('shipment-request.send-shipment-invoice');
        Route::match(['get', 'post'],'shipment-request/make-payment-status/{shipmentId}/{status}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'makePaymentStatus'])->name('shipment-request.make-payment-status');
        Route::post('shipment-request-upload-files', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'uploadShipmentStopDocuments'])->name('shipment-request.upload-files');

        Route::get('shipment-request/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'changeStatus'])->name('shipment-request.status');
        Route::get('shipment-request/show/{systemid?}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'view'])->name('shipment-request.show');
        Route::get('shipment-request/offer-details/show/{systemid?}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'ViewOfferDetails'])->name('offer-details.show');
        Route::get('shipment-request-offers/details/{systemid?}/{type?}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'ViewOfferDetails'])->name('list-offer-details.show');
        Route::get('shipment-request-offers', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'offersList'])->name('shipment-request-offers-list');
        Route::get('shipment-request-export', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'export'])->name('shipment-request.export');
        Route::get('shipment-request-offers-export', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'offerExport'])->name('shipment-request-offers.export');

        Route::get('shipment-request-certificate-skip/{stop_id}', [App\Http\Controllers\adminpnlx\ShipmentRequestController::class, 'shipmentRequestCertificateSkip'])->name('shipment-request-certificate-skip');

        /* cms manager routes */
        Route::resource('cms-manager', App\Http\Controllers\adminpnlx\CmspagesController::class);
        Route::get('cms-manager/destroy/{encmsid?}', [App\Http\Controllers\adminpnlx\CmspagesController::class, 'destroy'])->name('cms-manager.delete');
        //  cms manager routes 

        /* faq routes */
        Route::resource('faqs', App\Http\Controllers\adminpnlx\FaqController::class);
        Route::get('faqs/destroy/{enfaqid?}', [App\Http\Controllers\adminpnlx\FaqController::class, 'destroy'])->name('faqs.delete');
        /* faq routes */

        /* Language setting start */
        Route::resource('language-settings', App\Http\Controllers\adminpnlx\LanguageSettingsController::class);
        Route::match(['get', 'post'], 'language-settings/update1/{id?}', [App\Http\Controllers\adminpnlx\LanguageSettingsController::class, 'update1'])->name('language-settings.update1');
        /* Language setting start */

        /** email templates routing**/
        Route::resource('email-templates', App\Http\Controllers\adminpnlx\EmailtemplateController::class);
        Route::match(['get', 'post'], 'email-templates/get-constant', [App\Http\Controllers\adminpnlx\EmailtemplateController::class, 'getConstant'])->name('email-templates.getConstant');
        
        // Email  status change
        Route::get('admin-email-template-mail-enable', [App\Http\Controllers\adminpnlx\EmailtemplateController::class, 'emailEnable'])->name('email-templates.admin-email-template-mail-enable');
        /** email templates routing**/

        /** email logs routing**/
        Route::match(['get', 'post'], 'email-logs', [App\Http\Controllers\adminpnlx\EmailLogsController::class, 'index'])->name('emaillogs.listEmail');
        Route::match(['get', 'post'], 'email-logs/email_details/{enmailid?}', [App\Http\Controllers\adminpnlx\EmailLogsController::class, 'emailDetail'])->name('emaillogs.emailDetail');
        /** email logs routing**/

        /** notification templates routing**/
        Route::resource('notification-templates', App\Http\Controllers\adminpnlx\NotificationTemplateController::class);
        Route::match(['get', 'post'], 'notification-templates/get-constant', [App\Http\Controllers\adminpnlx\NotificationTemplateController::class, 'getConstant'])->name('notification-templates.getConstant');

        Route::get('admin-notification-template-notification-enable', [App\Http\Controllers\adminpnlx\NotificationTemplateController::class, 'notificationEnable'])->name('notification-templates.admin-notification-template-notification-enable');
        /** notification templates routing**/

        /** settings routing**/
        Route::resource('settings', App\Http\Controllers\adminpnlx\SettingsController::class);
        Route::match(['get', 'post'], '/settings/prefix/{enslug?}', [App\Http\Controllers\adminpnlx\SettingsController::class, 'prefix'])->name('settings.prefix');
        Route::get('settings/destroy/{ensetid?}', [App\Http\Controllers\adminpnlx\SettingsController::class, 'destroy'])->name('settings.delete');
        /** settings routing**/

        /** Access Control Routes Starts **/
        Route::resource('acl', App\Http\Controllers\adminpnlx\AclController::class);
        Route::get('acl/destroy/{enaclid?}', [App\Http\Controllers\adminpnlx\AclController::class, 'destroy'])->name('acl.delete');
        Route::get('acl/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\AclController::class, 'changeStatus'])->name('acl.status');
        Route::post('acl/add-more/add-more', [App\Http\Controllers\adminpnlx\AclController::class, 'addMoreRow'])->name('acl.addMoreRow');
        Route::get('acl/delete-function/{id}', [App\Http\Controllers\adminpnlx\AclController::class, 'delete_function'])->name('acl.delete_function');

        Route::match(['get', 'post'], 'menu-setting', [App\Http\Controllers\adminpnlx\AclController::class, 'menu_setting'])->name('menu_setting.index');
        Route::get('menu-edit/{id}', [App\Http\Controllers\adminpnlx\AclController::class, 'menu_edit'])->name('menu_setting.edit');
        Route::post('menu-update/{id}', [App\Http\Controllers\adminpnlx\AclController::class, 'menu_update'])->name('menu_setting.update');
        /** Access Control Routes Ends **/

        /** Department routes **/
        Route::resource('roles', App\Http\Controllers\adminpnlx\RolesController::class);
        Route::get('roles/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\RolesController::class, 'changeStatus'])->name('roles.status');
        Route::get('roles/destroy/{endepid?}', [App\Http\Controllers\adminpnlx\RolesController::class, 'destroy'])->name('roles.delete');
        // /* Department routes */


        /* staff routes */
        Route::resource('staff', App\Http\Controllers\adminpnlx\StaffController::class);
        Route::get('staff/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\StaffController::class, 'changeStatus'])->name('staff.status');
        Route::get('staff/destroy/{enstfid?}', [App\Http\Controllers\adminpnlx\StaffController::class, 'destroy'])->name('staff.delete');
        Route::match(['get', 'post'], 'staff/changed-password/{enstfid?}', [App\Http\Controllers\adminpnlx\StaffController::class, 'changedPassword'])->name('staff.changerpassword');
        Route::match(['get', 'post'], 'staff/get-designations', [App\Http\Controllers\adminpnlx\StaffController::class, 'getDesignations'])->name('staff.getDesignations');
        Route::match(['get', 'post'], 'staff/get-staff-permission', [App\Http\Controllers\adminpnlx\StaffController::class, 'getStaffPermission'])->name('staff.getStaffPermission');

        /* Our services routes */
        Route::resource('our-services', App\Http\Controllers\adminpnlx\OurServicesController::class);
        Route::get('our-services/destroy/{id}', [OurServicesController::class, 'destroy'])->name('our-services.delete');
        Route::get('our-services/{id}/{status}', [OurServicesController::class, 'changeStatus'])->name('our-services.status');
        /* Our services routes */

        Route::resource('contact-enquiry', App\Http\Controllers\adminpnlx\ContactEnquiryController::class);
        Route::post('contact-enquiry/reply/{id}', [App\Http\Controllers\adminpnlx\ContactEnquiryController::class, 'reply'])->name('contact-enquiry.reply');
        Route::get('contact-enquiry/destroy/{id}', [ContactEnquiryController::class, 'destroy'])->name('contact-enquiry.delete');
        Route::get('contact-enquiry/{id}/{status}', [ContactEnquiryController::class, 'changeStatus'])->name('contact-enquiry.status');

        /* Plans routes */
        Route::match(['get', 'post'], '/plans', [App\Http\Controllers\adminpnlx\PlanController::class, 'index'])->name('plan.index');
        Route::match(['get', 'post'], '/plans/create', [App\Http\Controllers\adminpnlx\PlanController::class, 'create'])->name('plan.create');
        Route::post("/plans/save", [App\Http\Controllers\adminpnlx\PlanController::class, 'save'])->name('plan.save');
        Route::match(['get', 'post'], '/plans/edit/{enuserid}', [App\Http\Controllers\adminpnlx\PlanController::class, 'edit'])->name('plan.edit');
        Route::post("/plans/update/{enuserid}", [App\Http\Controllers\adminpnlx\PlanController::class, 'update'])->name('plan.update');
        Route::get('plans/show/{enuserid}', [App\Http\Controllers\adminpnlx\PlanController::class, 'view'])->name('plan.show');
        Route::get('plans/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\PlanController::class, 'destroy'])->name('plan.delete');
        Route::get('plans/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\PlanController::class, 'changeStatus'])->name('plan.status');
        /* Plans routes */
        Route::get("/plans/features/{planid}", [App\Http\Controllers\adminpnlx\PlanController::class, 'planFeatureIndex'])->name('plan.feature.index');
        Route::match(['get', 'post'], '/plans/features/create/{planid}', [App\Http\Controllers\adminpnlx\PlanController::class, 'planFeatureCreate'])->name('plan.feature.create');
        Route::match(['get', 'post'], "/plans/features/save/{planid}", [App\Http\Controllers\adminpnlx\PlanController::class, 'planFeatureSave'])->name('plan.feature.save');
        Route::match(['get', 'post'], '/plans/features/edit/{planid}/{id}', [App\Http\Controllers\adminpnlx\PlanController::class, 'planFeatureEdit'])->name('plan.feature.edit');
        Route::match(['get', 'post', 'put'], "/plans/features/update/{planid}/{id}", [App\Http\Controllers\adminpnlx\PlanController::class, 'planFeatureUpdate'])->name('plan.feature.update');
        Route::get('plans/features/destroy/{id}', [App\Http\Controllers\adminpnlx\PlanController::class, 'planFeatureDestroy'])->name('plan.feature.delete');


        /* Truck Types routes */
        Route::match(['get', 'post'], '/truck-types', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'index'])->name('truck-types.index');
        Route::match(['get', 'post'], '/truck-types/create', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'create'])->name('truck-types.create');
        Route::post("/truck-types/save", [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'save'])->name('truck-types.save');
        Route::match(['get', 'post'], '/truck-types/edit/{enuserid}', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'edit'])->name('truck-types.edit');
        Route::post("/truck-types/update/{enuserid}", [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'update'])->name('truck-types.update');
        Route::get('truck-types/show/{enuserid}', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'view'])->name('truck-types.show');
        Route::get('truck-types/destroy/{enuserid?}', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'destroy'])->name('truck-types.delete');
        Route::get('truck-types/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'changeStatus'])->name('truck-types.status');
        Route::get("/truck-types/questionnaire/{trucktypesid}", [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'truckTtypesQuestionnaireIndex'])->name('truck-types.questionnaire.index');
        Route::match(['get', 'post'], '/truck-types/questionnaire/create/{trucktypesid}', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'truckTypesQuestionnaireCreate'])->name('truck-types.questionnaire.create');
        Route::match(['get', 'post'], "/truck-types/questionnaire/save/{trucktypesid}", [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'truckTypesQuestionnaireSave'])->name('truck-types.questionnaire.save');
        Route::match(['get', 'post'], '/truck-types/questionnaire/edit/{trucktypesid}/{id}', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'truckTypesQuestionnaireEdit'])->name('truck-types.questionnaire.edit');
        Route::match(['get', 'post', 'put'], "/truck-types/questionnaire/update/{trucktypesid}/{id}", [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'truckTypesQuestionnaireUpdate'])->name('truck-types.questionnaire.update');
        Route::get('truck-types/questionnaire/destroy/{id}', [App\Http\Controllers\adminpnlx\TruckTypesController::class, 'truckTypesQuestionnaireDestroy'])->name('truck-types.questionnaire.delete');
        /* Truck Types routes */

        /* homepageslider */
        Route::resource('homepage-slider', App\Http\Controllers\adminpnlx\HomepageSliderController::class);
        Route::get('homepage-slider/destroy/{encatid?}', [App\Http\Controllers\adminpnlx\HomepageSliderController::class, 'destroy'])->name('homepage-slider.delete');
        Route::get('homepage-slider/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\HomepageSliderController::class, 'changeStatus'])->name('homepage-slider.status');
        /* homepageslider */

        Route::match(['get', 'post'], 'seo-page-manager', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'index'])->name('SeoPage.index');
        Route::get('seo-page-manager/add-doc', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'addDoc'])->name('SeoPage.create');
        Route::post('seo-page-manager/add-doc', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'saveDoc'])->name('SeoPage.save');
        Route::get('seo-page-manager/edit-doc/{id}', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'editDoc'])->name('SeoPage.edit');
        Route::post('seo-page-manager/edit-doc/{id}', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'updateDoc'])->name('SeoPage.update');
        Route::any('seo-page-manager/delete-page/{id}', [App\Http\Controllers\adminpnlx\SeoPageController::class, 'deletePage'])->name('SeoPage.delete');

        /* Lookups manager  module  routing start here */
        Route::match(['get', 'post'], '/lookups-manager/{type}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'index'])->name('lookups-manager.index');
        Route::match(['get', 'post'], '/lookups-manager/add/{type}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'add'])->name('lookups-manager.add');
        Route::get('lookups-manager/destroy/{enlokid?}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'destroy'])->name('lookups-manager.delete');
        Route::get('lookups-manager/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'changeStatus'])->name('lookups-manager.status');
        Route::match(['get', 'post'], 'lookups-manager/{type?}/edit/{enlokid?}', [App\Http\Controllers\adminpnlx\LookupsController::class, 'update'])->name('lookups-manager.edit');
        /* Lookups manager  module  routing start here */

        /* Lookups manager  module  routing start here */

        /**  Designations routes **/
        Route::match(['get', 'post'], '/designations', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'index'])->name('designations.index');
        Route::match(['get', 'post'], 'designations/add', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'add'])->name('designations.add');
        Route::match(['get', 'post'], 'designations/edit/{endesid?}', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'update'])->name('designations.edit');
        Route::get('designations/update-status/{id}/{status}', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'changeStatus'])->name('designations.status');
        Route::get('designations/delete/{endesid}', [App\Http\Controllers\adminpnlx\DesignationsController::class, 'delete'])->name('designations.delete');
        /* Designations routes */

        Route::match(['get', 'post'], '/aboutus', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'aboutUs'])->name('aboutus.index');

        Route::match(['get', 'post'], '/clients', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'clients'])->name('clients.index');
        Route::match(['get', 'post'], '/client-add', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'clientAdd'])->name('client.add');
        Route::match(['get', 'post'], '/client-delete/{endesid?}', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'clientDelete'])->name('client.delete');

        Route::match(['get', 'post'], '/team', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'team'])->name('team.index');
        Route::match(['get', 'post'], '/team-add', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'teamAdd'])->name('team.add');
        Route::match(['get', 'post'], '/team-edit/{endesid?}', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'teamEdit'])->name('team.edit');
        Route::match(['get', 'post'], '/team-delete/{endesid?}', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'teamDelete'])->name('team.delete');

        Route::match(['get', 'post'], '/achievment', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'achievment'])->name('achievment.index');
        Route::match(['get', 'post'], '/achievment-add', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'achievmentAdd'])->name('achievment.add');
        Route::match(['get', 'post'], '/achievment-edit/{endesid?}', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'achievmentEdit'])->name('achievment.edit');
        Route::match(['get', 'post'], '/achievment-delete/{endesid?}', [App\Http\Controllers\adminpnlx\FrontPagesController::class, 'achievmentDelete'])->name('achievment.delete');

        Route::match(['get', 'post'], '/admin-customer-support', [App\Http\Controllers\adminpnlx\MessageController::class, 'customersupport'])->name('customersupport');
        Route::match(['get', 'post'], 'send-sms', [App\Http\Controllers\adminpnlx\MessageController::class, 'sendSmsUser'])->name('admin.sendSms');
        Route::match(['post'], 'attachment-image', [App\Http\Controllers\adminpnlx\MessageController::class, 'attachment_image'])->name('admin.attachment_image');
        Route::match(['post'], 'portfolio-image-add-delete', [App\Http\Controllers\adminpnlx\MessageController::class, 'portfolio_image_add_delete'])->name('admin.portfolio_image_add_delete');
        Route::match(['get', 'post'], 'toggle-chat-admin', [App\Http\Controllers\adminpnlx\MessageController::class, 'toggleChat'])->name('admin.toggleChat');
        Route::match(['get', 'post'], 'toggle_chat_html', [App\Http\Controllers\adminpnlx\MessageController::class, 'toggle_chat_html'])->name('admin.toggle_chat_html');
        Route::match(['get', 'post'], 'toggle_chat_media', [App\Http\Controllers\adminpnlx\MessageController::class, 'toggle_chat_media'])->name('admin.toggle_chat_media');

        Route::get('rating-review', [App\Http\Controllers\adminpnlx\ReviewRatingController::class, 'index'])->name('ReviewRating.index');
        Route::get('rating-review/view/{id}', [App\Http\Controllers\adminpnlx\ReviewRatingController::class, 'show'])->name('ReviewRating.show');
        Route::get('rating-review/delete/{id}', [App\Http\Controllers\adminpnlx\ReviewRatingController::class, 'delete'])->name('ReviewRating.delete');
        Route::get('rating-review-export', [App\Http\Controllers\adminpnlx\ReviewRatingController::class, 'reviewExport'])->name('ReviewRating.export');
        Route::match(['get', 'post'], '/rating-review/edit/{enuserid}', [App\Http\Controllers\adminpnlx\ReviewRatingController::class, 'edit'])->name('ReviewRating.edit');

        Route::post('subscribe-plan-save/{id}', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'subscribePlanSave'])->name('subscribe_plan.save');

        /* Reports routes */
        Route::match(['get', 'post'], 'truck-insurance', [App\Http\Controllers\adminpnlx\TruckController::class, 'index_insurance'])->name('truck.insurance.index');
        Route::match(['get', 'post'], '/truck/insurance/export', [App\Http\Controllers\adminpnlx\TruckController::class, 'export_index_insurance'])->name('truck.insurance.export');
        Route::match(['get', 'post'], 'truck-license', [App\Http\Controllers\adminpnlx\TruckController::class, 'index_license'])->name('truck.license.index');
        Route::match(['get', 'post'], '/truck/license/export', [App\Http\Controllers\adminpnlx\TruckController::class, 'export_index_license'])->name('truck.license.export');


        /* Reports routes */

        /* Subscription plans route */

           Route::get('company-subscription-plans', [App\Http\Controllers\adminpnlx\CompanySubscriptionPlansController::class, 'index'])->name('company-subscription-plans.index');
           Route::match(['get', 'post'], '/company-subscription-plans/export', [App\Http\Controllers\adminpnlx\CompanySubscriptionPlansController::class, 'export'])->name('company-subscription-plans.export');

        /* Subscription plans route */


        /* Transactions routes */
        Route::match(['get', 'post'], 'transactions', [App\Http\Controllers\adminpnlx\TransactionController::class, 'index'])->name('transaction.index');
        Route::match(['get', 'post'], '/transactions/export', [App\Http\Controllers\adminpnlx\TransactionController::class, 'export'])->name('transaction.export');

        Route::match(['get', 'post'], 'shipments', [App\Http\Controllers\adminpnlx\ShipmentController::class, 'index'])->name('shipments.index');
        Route::match(['get', 'post'], 'shipments-export', [App\Http\Controllers\adminpnlx\ShipmentController::class, 'export'])->name('shipments.export');

         /** Access Control Routes Starts **/
         Route::match(['get','post'],'menu-setting', [App\Http\Controllers\adminpnlx\AclController::class,'menu_setting'])->name('menu_setting.index');
         Route::get('menu-edit/{id}', [App\Http\Controllers\adminpnlx\AclController::class,'menu_edit'])->name('menu_setting.edit');
         Route::post('menu-update/{id}', [App\Http\Controllers\adminpnlx\AclController::class,'menu_update'])->name('menu_setting.update');



        // Multiple Notification By The Select Boxes...
         // Insurance expiration notification to truck
        Route::any( 'truck-insurance/truck-insurance-notification', [App\Http\Controllers\adminpnlx\TruckController::class, 'truckInsuranceExpiryNotification'])->name('truck.insurance.notification');
        Route::match(['get', 'post'], 'truck-insurance-notification-set', [App\Http\Controllers\adminpnlx\TruckController::class, 'NotificationSet'])->name('truck.insurance.notification-set');

        Route::match(['get', 'post'], 'send-insurance-expire-notification', [App\Http\Controllers\adminpnlx\TruckController::class, 'sendInsuranceExpireNotification'])->name('send-insurance-expire-notification');

        // licence expiration notification to truck
        Route::match(['get', 'post'], 'truck-license/truck-licence-notification', [App\Http\Controllers\adminpnlx\TruckController::class, 'truckLicenceExpiryNotification'])->name('truck.licence.notification');

        Route::match(['get', 'post'], 'send-licence-expire-notification', [App\Http\Controllers\adminpnlx\TruckController::class, 'sendlicenceExpireNotification'])->name('send-licence-expire-notification');

        // Fueling method notification to company
        Route::match(['get', 'post'], 'truck-company-fueling-methods/truck-company-fueling-methods-notification', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'truckCompanyFuelingMethodNotification'])->name('truck.company.fueling.methods.notification');

        Route::match(['get', 'post'], 'send-truck-company-fueling-methods-notification', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'sendCompanyFuelingMethodNotification'])->name('send-fueling-methods-notification');

       // Tidaluk company notification to company
        Route::match(['get', 'post'], 'truck-company-tidaluk-company/truck-company-tidaluk-company-notification', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'truckCompanyTidalukMethodNotification'])->name('truck.company.tidaluk.company.notification');

        Route::match(['get', 'post'], 'send-truck-company-tidaluk-company-notification', [App\Http\Controllers\adminpnlx\TruckCompanyController::class, 'sendCompanyTidalukCompanyNotification'])->name('send.tidaluk.company.notification');

         // Company subscription plans notification to company
        Route::match(['get', 'post'], 'company-subscription-plans/truck-company-subscription-plans-notification', [App\Http\Controllers\adminpnlx\CompanySubscriptionPlansController::class, 'truckCompanySubscriptionPlanNotification'])->name('truck.company.subscription.plans.notification');

        Route::match(['get', 'post'], 'send-truck-company-subscription-plans-notification', [App\Http\Controllers\adminpnlx\CompanySubscriptionPlansController::class, 'sendCompanySubscriptionPlanNotification'])->name('send.tidaluk.companysubscription.plans.notification');

        

    });
});


Route::middleware(['GuestFront'])->group(function () {
    Route::get('greenapimessage', [App\Http\Controllers\Controller::class, 'greenapimessage']);
    
    Route::match(['get', 'post'], '/', [App\Http\Controllers\frontend\CustomerController::class, 'index'])->name('index');
    Route::match(['get', 'post'], 'about', [App\Http\Controllers\frontend\CustomerController::class, 'about'])->name('about');
    Route::match(['get', 'post'], 'service', [App\Http\Controllers\frontend\CustomerController::class, 'service'])->name('service');
    Route::match(['get', 'post'], 'plan', [App\Http\Controllers\frontend\CustomerController::class, 'plan'])->name('plan');
    Route::match(['get', 'post'], 'subscribe-plan/{validate_string}/{plan?}', [App\Http\Controllers\frontend\CustomerController::class, 'subscribePlan'])->name('subscribe-plan');
    Route::match(['get', 'post'], 'plan-subscription/{name}', [App\Http\Controllers\frontend\CustomerController::class, 'planSubscription'])->name('plan-subscription');

    // Route::get('send-scheduled-messages', [App\Http\Controllers\frontend\TruckCompanyController::class, 'willSendScheduledMessages'])->name('manage-subscription-plans');
    // Payment plan-subscribe
    Route::match(['get', 'post'], 'subscribe-now/{name}', [App\Http\Controllers\frontend\TransactionController::class, 'subscribeNow'])->name('payment');
    Route::match(['get', 'post'], 'success', [App\Http\Controllers\frontend\TransactionController::class, 'successPayment'])->name('success');
    Route::get('failure', [App\Http\Controllers\frontend\TransactionController::class, 'failPayment'])->name('failure');
    Route::get('thank-you', [App\Http\Controllers\frontend\TransactionController::class, 'thankYou'])->name('thank-you');
  
    Route::get('link-is-expired', [App\Http\Controllers\frontend\TransactionController::class, 'linkExpired'])->name('link-is-expired');

    // truck company registration  
    Route::match(['get', 'post'], 'truck-company-registration/{id?}', [App\Http\Controllers\frontend\TruckCompanyController::class, 'index'])->name('truckCcompanyRegistration');
    Route::match(['post'], 'truck-registration/{id?}', [App\Http\Controllers\frontend\TruckCompanyController::class, 'truckRegistrationstep2'])->name('truckRegistrationstep2');
    Route::match(['post'], 'truck-registration', [App\Http\Controllers\frontend\TruckCompanyController::class, 'truckRegistrationstep2checkMobile'])->name('truckRegistrationstep2checkMobile');
    Route::match(['get', 'post'], 'verify-otp-truck-company', [App\Http\Controllers\frontend\TruckCompanyController::class, 'verifyOtptruck'])->name('verifyOtptruck');
    Route::match(['get', 'post'], 'verify-mobile-truck-company', [App\Http\Controllers\frontend\TruckCompanyController::class, 'verifyMobiletruck'])->name('verify-mobile-truck-company');
    Route::match(['get', 'post'], 'check-otp-truck-company', [App\Http\Controllers\frontend\TruckCompanyController::class, 'checkOtp'])->name('check-otp-truck-company');
    Route::match(['get', 'post'], 'truck-company-registration-step-4', [App\Http\Controllers\frontend\TruckCompanyController::class, 'truckcompanyregistrationstep4'])->name('truck-company-registration-step-4');
    Route::match(['get', 'post'], 'number-of-truck-registration/', [App\Http\Controllers\frontend\TruckCompanyController::class, 'truckCompanyRegistration'])->name('truck-company-registration');
    Route::match(['get', 'post'], 'truck-company-registration-step-5', [App\Http\Controllers\frontend\TruckCompanyController::class, 'truckcompanyregistrationstep5'])->name('truck-company-registration-step-5');
    Route::match(['post'], 'submittruckCompany', [App\Http\Controllers\frontend\TruckCompanyController::class, 'submittruckCompany'])->name('submittruckCompany');
    Route::match(['get'], 'thankyou', [App\Http\Controllers\frontend\TruckCompanyController::class, 'thankyou'])->name('thankyou');
    // truck company registration  

    Route::match(['get', 'post'], 'contact', [App\Http\Controllers\frontend\CustomerController::class, 'contact'])->name('contact');
    Route::match(['get', 'post'], '/privacy-policy', [App\Http\Controllers\frontend\HomeController::class, 'privacy_policy'])->name('home.privacy');
    Route::get('/term-condition', [App\Http\Controllers\frontend\HomeController::class, 'term_condition'])->name('home.term-condition');
    Route::match(['get', 'post'], '/contact-enquiry', [App\Http\Controllers\frontend\HomeController::class, 'contactEnquiry'])->name('home.contactEnquiry');


    //private user generate shipment request
    Route::match(['get', 'post'], 'private/shipment-request', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentRequest'])->name('private-shipment-request');
    Route::match(['get', 'post'], 'private/shipment-request/verify-mobile', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentRequestMobileVerify'])->name('private.shipment.verify.mobile');

    Route::match(['get', 'post'], 'private/shipment-otp-request', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentOtpRequest'])->name('private.shipment.otp.request');
    Route::match(['get', 'post'], 'private/shipment-check-otp', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'checkShipmentOtp'])->name('private.check.shipment.otp');
    Route::match(['get', 'post'], 'private/shipment-create-password', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'shipmentCreatePassword'])->name('private.shipment-create-password');

    // Route::match(['get', 'post'], 'business-customer-profile', [App\Http\Controllers\frontend\CustomersController::class, 'customerProfile'])->name('businessCustomerProfile');

    // Route::match(['get', 'post'], 'login', [App\Http\Controllers\frontend\CustomerController::class, 'userLogin'])->name('login');
    // Route::match(['get', 'post'], 'forgot-password', [App\Http\Controllers\frontend\CustomerController::class, 'userForgotPassword'])->name('forgot-password');
    // Route::match(['get', 'post'], 'otp/{string?}', [App\Http\Controllers\frontend\CustomerController::class, 'Otp'])->name('otp');
    // Route::match(['get', 'post'], 'otp-verify/{string?}', [App\Http\Controllers\frontend\CustomerController::class, 'verifyOtp'])->name('otp-verify');
    // Route::match(['get', 'post'], 'create-new-password/{id?}', [App\Http\Controllers\frontend\CustomerController::class, 'newPassword'])->name('create-new-password');


    // Route::match(['get', 'post'],'customers-login-screen', [App\Http\Controllers\frontend\CustomersController::class, 'customersLoginScreen'])->name('customersLoginScreen');

    // Route::match(['get', 'post'], 'sign-up', [App\Http\Controllers\frontend\CustomerController::class, 'signUp'])->name('sign-up');
    // Route::match(['get', 'post'], 'customers-login', [App\Http\Controllers\frontend\CustomersController::class, 'customersLogin'])->name('customersLogin');
    // Route::match(['get', 'post'], 'customers-otp/{string?}', [App\Http\Controllers\frontend\CustomersController::class, 'customersOtp'])->name('customersOtp');
    // Route::match(['get', 'post'], 'customers-otp-verify/{string?}', [App\Http\Controllers\frontend\CustomersController::class, 'customersOtpVerify'])->name('customersOtpVerify');
    // Route::match(['get', 'post'], 'customers-create-new-password/{id?}', [App\Http\Controllers\frontend\CustomersController::class, 'customersCreateNewPassword'])->name('customersCreateNewPassword');
    // Route::match(['get', 'post'], 'customers-sign-up', [App\Http\Controllers\frontend\CustomersController::class, 'customerSignUp'])->name('customerSignUp');
    // Route::match(['get', 'post'], 'customers-bisness-sign-up', [App\Http\Controllers\frontend\CustomersController::class, 'customerBusinessSignUp'])->name('customerBusinessSignUp');
    Route::middleware(['IfUserNotLogin'])->group(function () {
        Route::match(['get', 'post'], 'login', [App\Http\Controllers\frontend\CustomerController::class, 'userLogin'])->name('login');
        Route::match(['get', 'post'], 'forgot-password', [App\Http\Controllers\frontend\CustomerController::class, 'forgotPassword'])->name('forgot-password');
        Route::match(['get', 'post'], 'forgot-password-verify-otp/{string?}', [App\Http\Controllers\frontend\CustomerController::class, 'forgotPasswordVerifyOtp'])->name('forgot-password-verify-otp');
        // Route::match(['get', 'post'], 'otp-verify/{string?}', [App\Http\Controllers\frontend\CustomerController::class, 'verifyOtp'])->name('otp-verify');
        Route::match(['get', 'post'], 'create-new-password/{string?}', [App\Http\Controllers\frontend\CustomerController::class, 'createNewPasswordn'])->name('create-new-password');

        Route::match(['get', 'post'], 'chooce-customer', [App\Http\Controllers\frontend\CustomerController::class, 'chooceCustomer'])->name('sign-up');
        Route::match(['get', 'post'], 'seleted-customer', [App\Http\Controllers\frontend\CustomerController::class, 'seletedCustomer'])->name('seleted-customer');

        Route::match(['get', 'post'], 'verify-mobile', [App\Http\Controllers\frontend\CustomerController::class, 'verifyMobile'])->name('verify-mobile');
        Route::match(['get', 'post'], 'verify-otp', [App\Http\Controllers\frontend\CustomerController::class, 'verifyOtp'])->name('verify-otp');
        Route::match(['get', 'post'], 'check-otp', [App\Http\Controllers\frontend\CustomerController::class, 'checkOtp'])->name('check-otp');
        Route::match(['get', 'post'], 'create-password', [App\Http\Controllers\frontend\CustomerController::class, 'createPassword'])->name('create-password');
        Route::match(['get', 'post'], 'customers-sign-up', [App\Http\Controllers\frontend\CustomerController::class, 'customerSignUp'])->name('customerSignUp');
        Route::match(['get', 'post'], 'sign-up-business-costomer', [App\Http\Controllers\frontend\CustomerController::class, 'signUpBusinessCostomer'])->name('sign-up-business-costomer');
    });
});

Route::middleware(['AuthFront'])->group(function () {

    // Route::match(['get', 'post'], 'customer-profile', [App\Http\Controllers\frontend\CustomerController::class, 'customerProfile'])->name('customerProfile');
    Route::match(['get', 'post'], 'customers-profile', [App\Http\Controllers\frontend\CustomerController::class, 'customersProfile'])->name('customers-profile');
    Route::get('logout', [App\Http\Controllers\frontend\CustomerController::class, 'logout'])->name('user-logout');
    Route::post('gallery-images-uploads', [App\Http\Controllers\frontend\CustomerController::class, 'galleryImagesUploads'])->name('gallery.images.uploads');
    Route::post('soft-delete-files', [App\Http\Controllers\frontend\CustomerController::class, 'softDeleteFiles'])->name('soft.delete.files');
    Route::match(['get', 'post'], 'change-password', [App\Http\Controllers\frontend\CustomerController::class, 'viewchangePassword'])->name('change-password');
    Route::match(['get', 'post'], 'customer-change-password', [App\Http\Controllers\frontend\CustomerController::class, 'changePassword'])->name('customerchangepassword');

    Route::match(['get', 'post'], 'send-sms', [App\Http\Controllers\frontend\MessageController::class, 'sendSmsUser'])->name('host.sendSms');

    Route::match(['post'], 'attachment-image', [App\Http\Controllers\frontend\MessageController::class, 'attachment_image'])->name('attachment_image');
    Route::match(['post'], 'portfolio-image-add-delete', [App\Http\Controllers\frontend\MessageController::class, 'portfolio_image_add_delete'])->name('portfolio_image_add_delete');
    Route::match(['get', 'post'], 'toggle-chat', [App\Http\Controllers\frontend\MessageController::class, 'toggleChat'])->name('user.toggleChat');
    Route::match(['get', 'post'], 'toggle_chat_html', [App\Http\Controllers\frontend\MessageController::class, 'toggle_chat_html'])->name('toggle_chat_html');
    Route::match(['get', 'post'], 'toggle_chat_media', [App\Http\Controllers\frontend\MessageController::class, 'toggle_chat_media'])->name('toggle_chat_media');

    //review and reting
    Route::match(['get', 'post'], 'submit-review', [App\Http\Controllers\frontend\RatingController::class, 'submitReview'])->name('host.submit.review');


    //Private Customer here
    Route::prefix('private')->middleware(['PrivateCustomer'])->group(function () {
        Route::get('customer-service', [App\Http\Controllers\frontend\MessageController::class, 'customerservice'])->name('private.customerservice');
        Route::match(['get', 'post'], 'chat', [App\Http\Controllers\frontend\MessageController::class, 'index'])->name('private.chat');
        Route::get('customer-dashboard', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'customerDashboard'])->name('private.customer-dashboard');
        Route::post('private-customers-profile-update', [App\Http\Controllers\frontend\CustomerController::class, 'ProfileUpdate'])->name('private-customers-profile-update');
        Route::post('profile-update', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ProfileUpdate'])->name('private-customer-profile-update');

        Route::get('shipment-request-details/{endesid?}', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentRequestDetails'])->name('private-shipment-request-details');
        Route::get('shipment-request-details-delete/{endesid?}', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentRequestDetailsDelete'])->name('private-shipment-request-details-delete');

        Route::get('shipment-details/{endesid?}', [App\Http\Controllers\frontend\PrivateShipmentController::class, 'ShipmentDetails'])->name('private-shipment-details');
        Route::get('shipment-list', [App\Http\Controllers\frontend\PrivateShipmentController::class, 'shipmentViewAll'])->name('private-shipment.view-all');
        Route::get('my-invoices', [App\Http\Controllers\frontend\PrivateShipmentController::class, 'PrivateAllinvoice'])->name('private.shipment.all-invoice');


        Route::get('shipment-request/destroy/{systemid?}', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentRequestDestroy'])->name('private-shipment-request-destroy');
        Route::get('shipment-offer-details/{systemid?}', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentOfferDetails'])->name('private-shipment-offer-details');
        Route::get('shipment-offer-approved/{systemid?}', [App\Http\Controllers\frontend\PrivateCustomerControllers::class, 'ShipmentOfferApproved'])->name('private-shipment-offer-approved');
        //Notifications
        Route::match(['get', 'post'], 'notifications', [App\Http\Controllers\frontend\Private\NotificationController::class, 'notificationsList'])->name('private.notification-list');
        Route::get('notifications/destroy/{mapid?}', [App\Http\Controllers\frontend\Private\NotificationController::class, 'notificationsDelete'])->name('private.notification-destroy');
        
    });

    //Business Customer here
    Route::prefix('business')->middleware(['BusinessCustomer'])->group(function () {
        Route::get('customer-service', [App\Http\Controllers\frontend\MessageController::class, 'customerservice'])->name('business.customerservice');
        Route::match(['get', 'post'], 'chat', [App\Http\Controllers\frontend\MessageController::class, 'index'])->name('business.chat');
        Route::get('customer-dashboard', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'customerDashboard'])->name('business.customer-dashboard');
        Route::get('shipment-requests', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'shipmentRequestsViewAll'])->name('business.shipment-requests.view-all');
        Route::post('profile-update', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ProfileUpdate'])->name('business-customers-profile-update');
        Route::match(['get', 'post'], 'shipment-request', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentRequest'])->name('business-shipment-request');
        Route::match(['get', 'post'], 'shipment-otp-request', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentOtpRequest'])->name('business.shipment.otp.request');
        Route::match(['get', 'post'], 'shipment-check-otp-request', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'checkShipmentOtpRequest'])->name('business.check.shipment.otp');
        Route::get('shipment-request-details/{systemid?}', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentRequestDetails'])->name('business-shipment-request-details');
        Route::get('shipment-request-details-delete/{endesid?}', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentRequestDetailsDelete'])->name('business-shipment-request-details-delete');
        Route::get('shipment-request-cancel/{endesid?}', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentRequestCancel'])->name('business-shipment-request-cancel');
        Route::get('shipment-request/destroy/{systemid?}', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentRequestDestroy'])->name('business-shipment-request-destroy');
        Route::get('shipment-offer-details/{systemid?}', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentOfferDetails'])->name('business-shipment-offer-details');
        Route::match(['get', 'post'], 'business-shipment-offer-approved/{systemid?}', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'ShipmentOfferApproved'])->name('business-shipment-offer-approved');

        Route::match(['get', 'post'], 'current-transportation', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'transportationAll'])->name('business.transportation.view-all');
        Route::match(['get', 'post'], 'send-proposal', [App\Http\Controllers\frontend\BusinessCustomerController::class, 'sendProposal'])->name('business-send-proposal');

        Route::match(['get', 'post'], 'shipment-details/{systemid?}', [App\Http\Controllers\frontend\BusinessShipmentController::class, 'ShipmentDetails'])->name('business-shipment-details');
        Route::get('shipment-list', [App\Http\Controllers\frontend\BusinessShipmentController::class, 'shipmentViewAll'])->name('business.shipment.view-all');

        Route::get('my-invoices', [App\Http\Controllers\frontend\BusinessShipmentController::class, 'BusinessAllinvoice'])->name('business.shipment.all-invoice');

        //Notifications
        Route::match(['get', 'post'], 'notifications', [App\Http\Controllers\frontend\Business\NotificationController::class, 'notificationsList'])->name('business.notification-list');
        //Route::get('notifications', [App\Http\Controllers\frontend\NotificationController::class, 'notificationsList'])->name('business.notification-list');
        //Route::get('notification-details/{mapid?}', [App\Http\Controllers\frontend\Business\BusinessCustomerController::class, 'ShipmentRequestDetails'])->name('business.notification-details');
        Route::get('notifications/destroy/{mapid?}', [App\Http\Controllers\frontend\Business\NotificationController::class, 'notificationsDelete'])->name('business.notification-destroy');
    });
});

Route::group(array('middleware' => 'App\Http\Middleware\Language'), function () {
    Route::get('change-language-settings/{lang}', [App\Http\Controllers\LanguageController::class, 'switchLang'])->name('lang.switch');
});

Route::get('calculation-ats', [App\Http\Controllers\LanguageController::class, 'calculationAts'])->name('calculation.ats');

// Route::get('shipmentsReviewAfterScheduleEnd', [App\Http\Controllers\frontend\TruckCompanyController::class, 'shipmentsReviewAfterScheduleEnd']);