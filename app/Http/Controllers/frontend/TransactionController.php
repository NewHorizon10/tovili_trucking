<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\TruckCompanySubscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Plan;
use App\Models\TruckCompanyRequestSubscription;

use Carbon\Carbon;

class TransactionController extends Controller
{

	public function __construct(Request $request)
	{
		parent::__construct();
		$this->request              =   $request;
	}

    public function subscribeNow(Request $request, $name)
    {
		
        $Details = TruckCompanyRequestSubscription::where('validate_string', $name)->first();
		if($Details == null){
			return redirect()->route('link-is-expired');
		}
		$truckCompanyId = $Details->truck_company_id;
		$planid         = $Details->plan_id;

		
		$totalPrice     = $Details->total_price;
		if($totalPrice == 0){
			$totalPrice     = $Details->price;
		}
		
		$planName       = $Details->type;
		$startDate      = $Details->start_time;
		$endDate        = $Details->end_time;
		
		if($planName =='0'){
         $planName =  trans('messages.monthly'); 
		}
		elseif($planName =='1') {
		 $planName = trans('messages.quarterly');
		}
		elseif($planName =='2'){
		 $planName =  trans('messages.Half Yearly'); 
		}
		else{
		 $planName =  trans('messages.Yearly'); 
		}

		$checkId = Transaction::where('company_subscription_plan_id', $planid)->where('status', 'pending')->first();
		$planDetails = Plan::find($planid);
		
		$userDetails       = User::find($truckCompanyId);
		$userEmail         = $userDetails->email;
		$userName          = $userDetails->name;
		$userMobileNumber  = $userDetails->phone_number;

		$characters = '12345678';
        $transactionId = '';

        for ($i = 0; $i < strlen($characters); $i++) {
            $transactionId .= $characters[rand(0, strlen($characters) - 1)];
        }

		$transactionId = 'TXN_' . $transactionId;

		if($checkId){
               
			$checkId->status   = 'failed';
			$response = $checkId->save();
			if($response == true){
				$obj                                  = new Transaction;
				$obj->company_subscription_plan_id	  = $planid;
				$obj->truck_company_id                = $truckCompanyId;
				$obj->transaction_id                  = $transactionId . $checkId->id;
				$obj->amount                          = $totalPrice;
				$obj->plan_name                       = $planDetails->plan_name;
				$obj->plan_type                       = $Details->type;
				$obj->status                          = 'pending';
				$responsereturn = $obj->save();
			}

		}else{

			$obj                                  = new Transaction;
			$obj->company_subscription_plan_id	  = $planid;
			$obj->truck_company_id                = $truckCompanyId;
			$obj->amount                          = $totalPrice;
			$obj->plan_name                       = $planDetails->plan_name;
			$obj->plan_type                       = $Details->type;
			$obj->status                          = 'pending';
			$obj->save();

			$obj->transaction_id                  = $transactionId . $obj->id;
			$obj->save();

		}

		$arrayData = [
			'userEmail'        => $userEmail,
			'userName'         => $userName,
			'transactionId'    => $obj->transaction_id,
			'startDate'        => $startDate,
			'endDate'          => $endDate,
			'planId'           => $planid,
			'validateString'   => $name, 
			'is_free'          => $Details->is_free,
		];

		$errorArray = [
			'validateString'   => $name,
		];
		
			$langCode = App::getLocale();

		if($Details->is_free == 1){

                 return redirect()->route('success', $arrayData);             

		}else{


		if(Config('Cardcom.production_mode') == 1){
			$TerminalNumber = Config('Cardcom.live_company_terminal'); # Company terminal 
			$UserName       = Config('Cardcom.live_api_mame');   # API User
		}else{
			$TerminalNumber = Config('Cardcom.test_company_terminal'); # Company terminal 
			$UserName       = Config('Cardcom.test_api_mame');   # API User
		}
		
		$CreateInvoice = true;  # to Create Invoice (Need permissions to create invoice )
		$IsIframe = true;   # Iframe or Redirect 
		$Operation = 1;  # = 1 - Bill Only , 2- Bill And Create Token , 3 - Token Only , 4 - Suspended Deal (Order).
		


		#Create Post Information
		// Account vars
		$vars = array();
		$vars['TerminalNumber'] = $TerminalNumber;
		$vars['UserName'] = $UserName;
		$vars["APILevel"] = "10"; // req
		$vars['codepage'] = '65001'; // unicodenotification
		$vars["Operation"] = $Operation;
		

		$vars["Language"] = $langCode;   // page languge he- hebrew , en - english , ru , ar
		$vars["CoinID"] = '1'; // billing coin , 1- NIS , 2- USD other , article

		$vars["SumToBill"] = $totalPrice; // Sum To Bill 
		$vars['ProductName'] = $planName; // Product Name , will how if no invoice will be created.



		
		$vars['SuccessRedirectUrl'] = route('success', $arrayData); // Success Page
		$vars['ErrorRedirectUrl'] = route('failure', $errorArray); // Error Page

		$vars['IndicatorUrl'] = "http://www.yoursite.com/NotifyURL";

		$vars["ReturnValue"] = "1234"; // Optional , ,recommended , value that will be return and save in CardCom system
		$vars["MaxNumOfPayments"] = "5"; // max num of payments to show  to the user

		$vars["ShowInvoiceHead"] = "true"; //  if show & edit Invoice Details on the page.
		$vars["InvoiceHeadOperation"] = "1"; //  0 = no create & show Invoice.  1 =(default)create Invoice.  2 = show Details Invoice but not create Invoice !	 

		if ($CreateInvoice) {

			$vars['IsCreateInvoice'] = "true";
			// customer info :
			$vars["InvoiceHead.CustName"] = $userName; // customer name
			$vars["InvoiceHead.SendByEmail"] = "true"; // will the invoice be send by email to the customer
			$vars["InvoiceHead.Language"] = $langCode; // he or en only
			$vars["InvoiceHead.Email"] = $userEmail;
			$vars["InvoiceHead.Phone"] = $userMobileNumber;
			

			// products info 

			// Line 1
			$vars["InvoiceLines1.Description"] = $planName;
			$vars["InvoiceLines1.Price"] = $totalPrice;
			$vars["InvoiceLines1.Quantity"] = "1";

			// ********   Sum of all Lines Price*Quantity  must be equals to SumToBill ***** //
		}

		// Send Data To Bill Gold Server
		$r = $this->PostVars($vars, 'https://secure.cardcom.solutions/Interface/LowProfile.aspx');
		parse_str($r, $responseArray);


		# Is Deal OK 
		if ($responseArray['ResponseCode'] == "0") {
			return redirect($responseArray['url']);
		} else {
			return redirect()->route('failure', $errorArray);
		}
	 }

    }

	function PostVars($vars, $PostVarsURL)
	{
		$urlencoded = http_build_query($vars);
		#init curl connection
		if (function_exists("curl_init")) {
			$CR = curl_init();
			curl_setopt($CR, CURLOPT_URL, $PostVarsURL);
			curl_setopt($CR, CURLOPT_POST, 1);
			curl_setopt($CR, CURLOPT_FAILONERROR, true);
			curl_setopt($CR, CURLOPT_POSTFIELDS, $urlencoded);
			curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($CR, CURLOPT_FAILONERROR, true);
			#actual curl execution perfom
			$r = curl_exec($CR);
			$error = curl_error($CR);
			# some error , send email to developer
			if (!empty($error)) {
				echo $error;
				die();
			}
			curl_close($CR);
			return $r;
		} else {
			echo "No curl_init";
			die();
		}

	}


	public function successPayment(Request $request){

		$planDetails = Plan::find($request->planId);

		if($request->is_free == 1){

           $subscribePlan = TruckCompanyRequestSubscription::where('plan_id', $request->planId)->where('validate_string', $request->validateString)->first();
			if($subscribePlan == null){
				
				Session()->flash('error', trans('messages.something_went_wrong'));
				return redirect()->route('link-is-expired');
			}

			$planName = $subscribePlan->type;
			
			$currentDate = Carbon::now();
			$transactionDetails = Transaction::where('transaction_id', $request->transactionId)
			->where('status', 'pending')
			->first();
			if(!empty($transactionDetails)){
				$transactionDetails->status    = 'process';
				$transactionDetails->save();

				$startDate = $currentDate->copy();
				if($planName =='0'){
					$endDate = $currentDate->copy()->addMonth(); 
				}elseif($planName =='1') {
					$endDate = $currentDate->copy()->addMonth(3); 
				}elseif($planName =='2'){
					$endDate = $currentDate->copy()->addMonth(6); 
				}else{
					$endDate = $currentDate->copy()->addYear(); 
				}
				
				$getActivatedPlan = TruckCompanySubscription::where('truck_company_id', $subscribePlan->truck_company_id)->first();
				if(!$getActivatedPlan){
					$getActivatedPlan = new TruckCompanySubscription;
				}
				$getActivatedPlan->start_time       			= $startDate;
				$getActivatedPlan->end_time         			= $endDate;
				$getActivatedPlan->status  		    			= 'activate';
				$getActivatedPlan->truck_company_id       		= $subscribePlan->truck_company_id;
				$getActivatedPlan->plan_id       				= $subscribePlan->plan_id;
				$getActivatedPlan->price       					= $subscribePlan->price;
				$getActivatedPlan->discount       				= $subscribePlan->discount;
				$getActivatedPlan->total_price       			= $subscribePlan->total_price;
				$getActivatedPlan->type       					= $planDetails->type;
				$getActivatedPlan->column_type       			= $subscribePlan->column_type;
				$getActivatedPlan->total_truck       			= $subscribePlan->total_truck;
				$getActivatedPlan->is_free                      = $subscribePlan->is_free;
				$getActivatedPlan->two_days_before_mail_send    = 0;
				$getActivatedPlan->same_day_mail_send 			= 0;
				
				if($getActivatedPlan->save()){
					$jsonData = json_encode($request->all());
					$transactionDetails->status        = 'success';
					$transactionDetails->plan_type     = $planDetails->type;
					$transactionDetails->responce_json = $jsonData;
					$transactionDetails->save();	
					$subscribePlan->delete();

					Session()->flash('success', trans('messages.payment_has_been_received_successfully'));
					return redirect()->route('thank-you');
				}
			}else{
				Session()->flash('error', trans('messages.something_went_wrong'));
				return redirect()->route('plan-subscription', $request->validateString);
			}

		}else{


			// Check payment status
			$lowProfileCode = $request->lowprofilecode;
			
			$checkResponseStatus = paymentResult($lowProfileCode);
			$responseArray = $checkResponseStatus;
			
			if($responseArray['ResponseCode'] == 0){
				
				$subscribePlan = TruckCompanyRequestSubscription::where('plan_id', $request->planId)->where('validate_string', $request->validateString)->first();
				

				if($subscribePlan == null){
					
					Session()->flash('error', trans('messages.something_went_wrong'));
					return redirect()->route('link-is-expired');
				}

				$planName = $subscribePlan->type;
				
				$currentDate = Carbon::now();
				$transactionDetails = Transaction::where('transaction_id', $request->transactionId)
				->where('status', 'pending')
				->first();
				if(!empty($transactionDetails)){
					$transactionDetails->status    = 'process';
					$transactionDetails->save();

					$startDate = $currentDate->copy();
					if($planName =='0'){
						$endDate = $currentDate->copy()->addMonth(); 
					}elseif($planName =='1') {
						$endDate = $currentDate->copy()->addMonth(3); 
					}elseif($planName =='2'){
						$endDate = $currentDate->copy()->addMonth(6); 
					}else{
						$endDate = $currentDate->copy()->addYear(); 
					}
					
					$getActivatedPlan = TruckCompanySubscription::where('truck_company_id', $subscribePlan->truck_company_id)->first();

					if(!$getActivatedPlan){
						$getActivatedPlan = new TruckCompanySubscription;
					}
					$getActivatedPlan->start_time       			= $startDate;
					$getActivatedPlan->end_time         			= $endDate;
					$getActivatedPlan->status  		    			= 'activate';
					$getActivatedPlan->truck_company_id       		= $subscribePlan->truck_company_id;
					$getActivatedPlan->plan_id       				= $subscribePlan->plan_id;
					$getActivatedPlan->price       					= $subscribePlan->price;
					$getActivatedPlan->discount       				= $subscribePlan->discount;
					$getActivatedPlan->total_price       			= $subscribePlan->total_price;
					$getActivatedPlan->type       					= $planDetails->type;
					$getActivatedPlan->column_type       			= $subscribePlan->column_type;
					$getActivatedPlan->total_truck       			= $subscribePlan->total_truck;
					$getActivatedPlan->is_free                      = $subscribePlan->is_free;
					$getActivatedPlan->two_days_before_mail_send    = 0;
					$getActivatedPlan->same_day_mail_send 			= 0;
					
					if($getActivatedPlan->save()){
						$jsonData = json_encode($request->all());
						$transactionDetails->status        = 'success';
						$transactionDetails->plan_type     = $planDetails->type;
						$transactionDetails->responce_json = $jsonData;
						$transactionDetails->save();	
						$subscribePlan->delete();

						Session()->flash('success', trans('messages.payment_has_been_received_successfully'));
						return redirect()->route('thank-you');
					}
				}else{
					Session()->flash('error', trans('messages.something_went_wrong'));
					return redirect()->route('plan-subscription', $request->validateString);
				}
			}else{
						Session()->flash('error', trans('messages.something_went_wrong'));
						return redirect()->route('plan-subscription', $request->validateString);
			}

		}

	}

	public function failPayment(Request $request){
		Session()->flash('error', trans('messages.something_went_wrong'));
		return view('frontend.payment-fail');
	}

	public function thankYou(){
		return view('frontend.thank-you');
	}

	public function linkExpired(){
		return view('frontend.link-expired');
	}


}
