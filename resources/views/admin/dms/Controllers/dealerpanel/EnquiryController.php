<?php
namespace App\Http\Controllers\dealerpanel;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\Enquiry;
use App\Model\DropDown;
use App\Model\EnquiryFollowUp;
use App\Model\DealerLocation;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

use App\PHPExcel\PHPExcel\IOFactory1;
use App\PHPExcel\PHPExcel\PHPExcel_Cell;

/**
* EnquiryController Controller
*
* Add your methods in the class below
*
* This file will render views\EnquiryController\dashboard
*/
	class EnquiryController extends BaseController {
		
		public $model	=	'enquiry';

	public function __construct() {
		View::share('modelName',$this->model);
	}
		
	public function listEnquiry(){
		$DB 					= 	Enquiry::query();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of username and email */ 
		if ((Input::get())) {
			$searchData			=	Input::get();
			unset($searchData['display']);
			unset($searchData['_token']);
			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == 'follow_up_end_date' || $fieldName == 'follow_up_start_date' || $fieldName == 'enquiry_start_date' || $fieldName == 'enquiry_end_date'  ){
						if($fieldName == 'follow_up_end_date'){  
							$DB->where('enquiries.next_follow_up_date','<=',$fieldValue);
						}
						if($fieldName == 'follow_up_start_date'){  
							$DB->where('enquiries.next_follow_up_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_start_date'){  
							$DB->where('enquiries.enquiry_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_end_date'){  
							$DB->where('enquiries.enquiry_date','<=',$fieldValue);
						}
					}elseif($fieldValue != "closed"){
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		if(Input::get('status') != ''){
			$DB->where('enquiries.status', Input::get('status'));
		}else{
			$DB->where('enquiries.status', "!=",ENQUIRY_CLOSE_STATUS);
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'updated_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->where('enquiries.is_deleted',0)
									->where('enquiries.dealer_id',$dealer_id)
									->select("enquiries.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_type) as enquiry_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.status) as enquirystatus"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = enquiries.location_name) as location_name"),
									DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"),
									DB::raw("(SELECT name FROM cities WHERE id = enquiries.city) as city"))
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("enquiry_search_data",$inputGet);
		 
		$leakage_analysis =  $this->getDropDownListBySlug('cancelReason');
		$enquirymode 		=  $this->getDropDownListBySlug('enquirymode');
		$status_type 		=  $this->getDropDownListBySlug('enquirystatus');
		$buying_competition 		=  $this->getDropDownListBySlug('buying-competition');

		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where("dealer_id",$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
	
		return  View::make('dealerpanel.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string','sales_consultant','enquirymode','status_type','leakage_analysis','buying_competition'));
	}
		  
	/**
	* Function for add enquiry page
	*
	* @param null
	*
	* @return view page. 
	*/ 
	public function addEnquiry(){
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',COUNTRY_ID)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray();

		$vehiclecolor 	=  $this->getDropDownListBySlug('vehiclecolor');
		$vehiclemodel 	=  $this->getDropDownListBySlug('vehiclemodel');
		$sourceOfInformation =  $this->getDropDownListBySlug('sourceOfInformation');
		$customerOccupation =  $this->getDropDownListBySlug('customerOccupation');
		$enquirymode =  $this->getDropDownListBySlug('enquirymode');
		$customer_category =  $this->getDropDownListBySlug('customer-category');
		$status_type =  $this->getDropDownListBySlug('enquirystatus');
		$finenceroptions =  $this->getDropDownListBySlug('finenceroptions');
		$vehiclebrand =  $this->getDropDownListBySlug('vehiclebrand');
		$enquirylevel 	=  $this->getDropDownListBySlug('enquirylevel');
		$outletList 	=  $this->getDropDownListBySlug('outlet');
		$dealer_id				=	$this->get_dealer_id();
		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where("dealer_id",$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
							
		$dealerLocationName 	=  DB::table('dealer_location')
									->where('dealer_id',$dealer_id)
									->where('is_active',1)
									->orderBy('location_name','ASC')
									->pluck('location_name','id')
									->toArray();	
									

		return View::make('dealerpanel.'.$this->model.'.add', compact('result' ,'outletList','enquirylevel','stateList','vehiclecolor','vehiclemodel','sourceOfInformation','dealerLocationName','sales_consultant','enquirymode','customer_category','status_type','finenceroptions','vehiclebrand','customerOccupation'));
	} 


	/**
	* Function for import enquiry page
	*
	* @param null
	*
	* @return view page. 
	*/ 
	public function importEnquiry(){
		return View::make('dealerpanel.'.$this->model.'.import_enquiry');
	} 

	
	/**
	* Function for save enquiry
	*
	* @param null
	*
	* @return view page. 
	*/
	public function saveEnquiry(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					//'enquiry_mode' 					=>	 'required',
					'mobile_number' 				=>	 'required|integer|digits:10',
					'customer_name'					=>	 'required',
					// 'email'							=>	 'email',
					// 'gender'						=>	 'required',
					// 'source_of_info'				=>	 'required',
					// 'address_1'						=>	 'required',
					// 'state'							=>	 'required',
					// 'city'							=>	 'required',
					// 'location_name'					=>	 'required',
					// 'customer_category'				=>	 'required',
					'vehicle_modal'					=>	 'required',
					//'current_vehicle'				=>	 (empty(Input::get('customer_category')) && Input::get('customer_category') != COMPETITOR_CISTOMER) ? 'required' : '',
				//	'current_vehicle_brand'			=>	 'required_if:customer_category,==,'.COMPETITOR_CISTOMER,
					//'vehicle_color'					=>	 'required',
				//	'current_vehicle_modal'			=>	 'required_if:customer_category,==,'.COMPETITOR_CISTOMER,
					// 'test_drive_date'				=>	 'required',
					// 'finenceroptions'				=>	 'required',
					// 'financer'						=>	 'required_if:finenceroptions,==,'.FINENCE_OPTION,
					//'test_drive_remark'				=>	 'required',
					//'sales_consultant'				=>	 'required',
					// 'enquiry_date'					=>	 'required',
					// 'remarks'	 					=>	 'required',
					// 'next_follow_up_date'			=>	 'required',
					// 'delivery_date'					=>	 'required',
					// 'source_of_info_remakrs'		=>	 'required_if:source_of_info,==,'.SOURCE_OF_INFO_OTHERS,
					// 'customer_occupation_remarks'	=>	 'required_if:customer_occupation,==,'.Occupation_Other_Details,
					// 'referred_by_name'				=>	 'required_if:source_of_info,==,'.REFERRED_BY_ID,
					//'referred_by_number'			=>	 'integer|digits:10|required_if:source_of_info,==,'.REFERRED_BY_ID,
				),
				array(
					//'current_vehicle.required'	=> 	 'The current vehicle info is required.',
					//'financer.required_if'	=> 	 'The financer is required.',
					//'current_vehicle_brand.required_if'	=> 	 'The current vehicle brand is required.',
					//'current_vehicle_modal.required_if'	=> 	 'The current vehicle model is required.',
				//'email.email'					=> 	 'The email address is invalid.',
				//	'source_of_info.required' 		=>	 'The source of information field is required.',
					'vehicle_modal.required' 		=>	 'The model enquired field is required.',
					//'vehicle_color.required' 		=>	 'The vehicle color field is required.',
				//	'dob.required' 					=>	 'The date of birth is required.',
					//'zip.required' 					=>	 'The zipcode field is required.',
					"mobile_number.integer"			=>	  trans("Phone number must have a numeric value."),
					"mobile_number.digits"			=>	  trans("Phone number must have 10 digits."),
				//	'enquiry_type.required_if' 		=>	 'The enquiry type field is required.',
				//	'remarks.required_if' 			=>	 'The remarks field is required.',
					//'test_drive_remark.required' 			=>	 'The test ride remark field is required.',
					//'test_drive_date.required' 			=>	 'The test ride date field is required.',
				//	'finenceroptions.required'   	=>	"The payment options field is required.",
				//	"source_of_info_remakrs.required_if"=>"The remarks field is required.",
				//	"customer_occupation_remarks.required_if"=>"The remarks field is required.",
				//	"referred_by_name.required_if"=>"The person name field is required.",
				//	"referred_by_number.required_if"=>"The mobile number field is required.",
					//"referred_by_number.integer"	=>	  trans("Phone number must have a numeric value."),
					//"referred_by_number.digits"		=>	  trans("Phone number must have 10 digits."),
				)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{ 
				if(Input::get('is_draft') != '' && Input::get('is_draft')=="Save As Draft"){
					$is_drafted		=	1;
				}else{
					$is_drafted		=	0;
				}
				$dealer_id				=	$this->get_dealer_id();
				$enquiry 						= 	new Enquiry; 
				$enquiry->dealer_id				=	$dealer_id;
				$enquiry->enquiry_date			=	!empty(Input::get('enquiry_date')) ? date('Y-m-d',strtotime(Input::get('enquiry_date'))) : '0000-00-00';
				$enquiry->vehicle_modal			=	Input::get('vehicle_modal');
				$enquiry->vehicle_color			=	!empty(Input::get('vehicle_color')) ? Input::get('vehicle_color') : '';
				$enquiry->customer_name			=	Input::get('customer_name');
				$enquiry->gender				=	Input::get('gender');
				$enquiry->email					=	Input::get('email');
				$enquiry->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$enquiry->address_1				=	Input::get('address_1');
				$enquiry->address_2				=	!empty(Input::get('address_2')) ? Input::get('address_2') : '';
				$enquiry->city					=	Input::get('city');
				$enquiry->state					=	Input::get('state');
				$enquiry->zip					=	!empty(Input::get('zip')) ? Input::get('zip') : '';
				$enquiry->location_name			=	Input::get('location_name');
				$enquiry->mobile_number			=	Input::get('mobile_number');
				$enquiry->next_follow_up_date	=	!empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$enquiry->customer_occupation	=	!empty(Input::get('customer_occupation')) ? Input::get('customer_occupation') : '';
				$enquiry->status					=	ENQUIRY_IN_PROCESS_STATUS;
				$enquiry->remarks				=	Input::get('remarks');
				$enquiry->current_vehicle		=	!empty(Input::get('current_vehicle')) ? Input::get('current_vehicle') : '';
				$enquiry->current_vehicle_brand		=	!empty(Input::get('current_vehicle_brand')) ? Input::get('current_vehicle_brand') : '';
				$enquiry->current_vehicle_model		=	!empty(Input::get('current_vehicle_modal')) ? Input::get('current_vehicle_modal') : '';
				$enquiry->source_of_info			=	Input::get('source_of_info');
				$enquiry->sales_consultant		=	Input::get('sales_consultant');
				$enquiry->enquiry_type			=	Input::get('enquiry_mode');
				// $enquiry->enquiry_type			=	Input::get('enquiry_type');
				$enquiry->test_drive_date		=	!empty(Input::get('test_drive_date')) ? date('Y-m-d',strtotime(Input::get('test_drive_date'))) : '0000-00-00';
				$enquiry->test_drive_remark		=	!empty(Input::get('test_drive_remark')) ? Input::get('test_drive_remark') : '';
				$enquiry->is_drafted			=	$is_drafted;
				$enquiry->created_at			=  	date("Y-m-d H:i:s");
				$enquiry->delivery_address_2	=	Input::get('delivery_address_2');
				$enquiry->delivery_address_1	=	Input::get('delivery_address_1');
				$enquiry->delivery_date			=	Input::get('delivery_date');
				$enquiry->finencer_option		=	Input::get('finenceroptions');
				$enquiry->financer				=	!empty(Input::get('financer')) ? Input::get('financer') : '';
				$enquiry->customer_category		=	Input::get('customer_category');
				$enquiry->contact_number		=	Input::get('contact_number');
				$enquiry->age					=	Input::get('age');
				$enquiry->outlet				=	Input::get('outlet');
				$enquiry->enquiry_level			=	Input::get('enquiry_level');
				$enquiry->source_of_info_remakrs=	Input::get('source_of_info_remakrs');
				$enquiry->customer_occupation_remarks=	Input::get('customer_occupation_remarks');
				$source_of_info_id 				= 	Input::get('source_of_info');
				$referred_by_name 				= 	Input::get('referred_by_name');
				$referred_by_number 			= 	Input::get('referred_by_number');
				$enquiry->referred_by_name		=	!empty($referred_by_name) && ($source_of_info_id == REFERRED_BY_ID) ? $referred_by_name : '';
				$enquiry->referred_by_number	=	!empty($referred_by_number) && ($source_of_info_id == REFERRED_BY_ID) ? $referred_by_number : '';
				$enquiry->save();
				$id  = $enquiry->id;
				if(!empty($id)){
					$enquiry_number		=	'#E000'.$id;
					Enquiry::where('id',$id)->update(array('enquiry_number'=>$enquiry_number));
					$followUpObj 	= new EnquiryFollowUp;
					$followUpObj->user_id 				= $dealer_id;
					$followUpObj->enquiry_id 				= $id;
					$followUpObj->next_follow_up_date   = !empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
					$followUpObj->detail 				= Input::get('remarks');
					$followUpObj->save();
					
				}
				Session::flash("success",trans("Enquiry added successfully."));
				return Redirect::to('/dealerpanel/enquiry-management');
				//return Redirect::back();
			}
		}
	}

	public function importsaveEnquiry(){
		$file			=	Input::file('file')->getRealPath();
		$objPHPExcel 	= 	IOFactory1::load($file);
		$allDataInSheet = 	$objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		unset($allDataInSheet[1]);
		if(!empty($allDataInSheet)){
			foreach($allDataInSheet as $DataInSheet){
				$enquiry_mode				=	44;
				$mobile_number				=	$DataInSheet["B"];
				$customer_name				=	$DataInSheet["C"];
				$gender						=	($DataInSheet["D"]== 'MALE') ? 1 : 2;
				$address_1					=	$DataInSheet["F"];

				if($DataInSheet["E"] == "ACIVITY"){
					$DataInSheet["E"]	=	"ACTIVITY";
				}
				$source_of_Information		=	DB::table('dropdown_managers')
												->where("name",'like','%'.$DataInSheet["E"].'%')
												->value('id');
				$state						=	DB::table('states')
												->where("name",'like','%'.$DataInSheet["G"].'%')
												->value('id');
				$city						=	DB::table('cities')
												->where("name",'like','%'.$DataInSheet["H"].'%')
												->value('id');


				if($DataInSheet["I"] == "GURGAON"){
					$DataInSheet["I"]	=	"Gurugram";
				}
				$location_name				=	DB::table('dealer_location')
												->where("location_name",'like','%'.$DataInSheet["I"].'%')
												->value('id');
				if($location_name == ""){
					$network 						= 	new DealerLocation;
					$network->dealer_id				=	243;
					$network->location_name			=	$DataInSheet["I"];
					$network->created_at			=  	'2020-01-01 00:00:00';
					$network->save();
					$locationId						=	$network->id;
					if($locationId != ''){
						$location_words				=	explode(' ',$DataInSheet["I"]);
						$location_code				=	'';
						foreach($location_words as &$word){
							$location_code			.=	ucfirst($word[0]);
						}
						$location_code				.=	$locationId;
						if($location_code != ''){
							DealerLocation::where('id', $locationId)->update(['location_code'=>$location_code]);
						}
					}

					$location_name	=	$locationId;
				}
				

				$customer_category			=	DB::table('dropdown_managers')
												->where("name",'like','%'.$DataInSheet["J"].'%')
												->value('id');
												
				$modeldata					=	explode("/",$DataInSheet["K"]);						
				$vehicle_modal				=	DB::table('dropdown_managers')
												->where("name",'like','%'.$modeldata[0].'%')
												->value('id');
				$finencer_option			=	DB::table('dropdown_managers')
												->where("dropdown_type","finenceroptions")
												->where("name",'like','%'.$DataInSheet["L"].'%')
												->value('id');

				$enquiry_date				=	'2020-01-01';
				$remarks					=	$DataInSheet["N"];
				$next_follow_up_date		=	'2020-01-23';
				$delivery_date				=	'2020-03-31';
				$current_brand				=	$DataInSheet["Q"];
				$current_Model				=	$DataInSheet["R"];
				$current_vehicle_info		=	$DataInSheet["S"];
				$financer					=	$DataInSheet['T']; 
				$other_details				=	$DataInSheet['U']; 
				$referred_person_name		=	$DataInSheet['V'];
				$referred_person_mobile_number	=	$DataInSheet['W'];
				$email						=	$DataInSheet['X'];
				$date_of_birth				=	!empty($DataInSheet["Y"]) ? date('Y-m-d',strtotime($DataInSheet["Y"])) : '0000-00-00';
				$customer_occupation		=	DB::table('dropdown_managers')
												->where("name",'like','%'.$DataInSheet["Z"].'%')
												->value('id');
				$contact_number				=	$DataInSheet['AA'];
				$owner_age					=	$DataInSheet['AB']; 
				$address_2					=	$DataInSheet['AC']; 
				$zipcode					=	$DataInSheet['AD'];
				$vehicle_color				=	DB::table('dropdown_managers')
												->where("name",'like','%'.$DataInSheet["AE"].'%')
												->value('id');
				$test_drive_date				=	!empty($DataInSheet["AF"]) ? date('Y-m-d',strtotime($DataInSheet["AF"])) : '0000-00-00';
				$test_drive_remarks			=	$DataInSheet['AG'];
				$delivery_address_1			=	$DataInSheet['AH'];
				$delivery_address_2			=	$DataInSheet['AI'];

				$sales_consultant			=	249;
				$outlet						=	DB::table('dropdown_managers')
												->where("name",'like','%'.$DataInSheet["AK"].'%')
												->value('id');
				$enquiry_level				=	DB::table('dropdown_managers')
												->where("name",'like','%'.$DataInSheet["AL"].'%')
												->value('id');
				$occupations_other_details	=	$DataInSheet['AM'];




				//dealerId ka kam krnaa hai
				$enquiry 						= 	new Enquiry; 
				$enquiry->dealer_id				=	243;
				$enquiry->enquiry_date			=	$enquiry_date;
				$enquiry->vehicle_modal			=	!empty($vehicle_modal) ? $vehicle_modal : 0;
				$enquiry->customer_name			=	!empty($customer_name) ? $customer_name : '';
				$enquiry->gender				=	$gender;
				$enquiry->email					=	$email;
				$enquiry->dob					=	$date_of_birth;
				$enquiry->address_1				=	!empty($address_1) ? $address_1 : '';
				$enquiry->address_2				=	!empty($address_2) ? $address_2 : '';
				$enquiry->city					=	$city;
				$enquiry->state					=	$state ;
				$enquiry->zip					=	!empty($zipcode) ? $zipcode : '';
				$enquiry->location_name			=	!empty($location_name) ? $location_name : 0;
				$enquiry->mobile_number			=	!empty($mobile_number) ? $mobile_number : '';
				$enquiry->next_follow_up_date	=	$next_follow_up_date;
				$enquiry->customer_occupation	=	!empty($customer_occupation) ? $customer_occupation : '';
				$enquiry->status					=	ENQUIRY_IN_PROCESS_STATUS;
				$enquiry->remarks				=	$remarks;
				$enquiry->current_vehicle		=	!empty($current_vehicle_info) ? $current_vehicle_info : '';
				$enquiry->current_vehicle_brand	=	!empty($current_brand) ? $current_brand : '';
				$enquiry->current_vehicle_model	=	!empty($current_Model) ? $current_Model : '';

				$enquiry->source_of_info		=	$source_of_Information;
				$enquiry->sales_consultant		=	$sales_consultant;
				$enquiry->enquiry_type			=	$enquiry_mode;
				// $enquiry->enquiry_type			=	Input::get('enquiry_type');
				$enquiry->test_drive_date		=	$test_drive_date;
				$enquiry->test_drive_remark		=	$test_drive_remarks;
				$enquiry->is_drafted			=	0; 
				$enquiry->created_at			=  	date("Y-m-d H:i:s");
				$enquiry->delivery_address_2	=	!empty($delivery_address_2) ? $delivery_address_2 : '';
				$enquiry->delivery_address_1	=	!empty($delivery_address_1) ? $delivery_address_1 : '';
				$enquiry->delivery_date			=	$delivery_date;
				$enquiry->finencer_option		=	!empty($finencer_option) ? $finencer_option : 0;
				$enquiry->financer				=	!empty($financer) ? $financer : '';
				$enquiry->customer_category		=	$customer_category;
				$enquiry->contact_number		=	!empty($contact_number) ? $contact_number : '';
				$enquiry->age					=	!empty($owner_age) ? $owner_age : '';
				$enquiry->outlet				=	!empty($outlet) ? $outlet : '';
				$enquiry->enquiry_level			=	!empty($enquiry_level) ? $enquiry_level : '';
				$enquiry->source_of_info_remakrs	=	'';
				$enquiry->customer_occupation_remarks=	!empty($occupations_other_details) ? $occupations_other_details : '';
				$source_of_info_id 				= '';
				$enquiry->referred_by_name 				= 	!empty($referred_person_name) ? $referred_person_name : '';
				$enquiry->referred_by_number 			= 	!empty($referred_person_mobile_number) ? $referred_person_mobile_number : '';
				$enquiry->save();


				$id  = $enquiry->id;
				if(!empty($id)){
					$enquiry_number		=	'#E000'.$id;
					Enquiry::where('id',$id)->update(array('enquiry_number'=>$enquiry_number));
					$followUpObj 	= new EnquiryFollowUp;
					$followUpObj->user_id 				= 10;
					$followUpObj->enquiry_id 				= $id;
					$followUpObj->next_follow_up_date   = $next_follow_up_date;
					$followUpObj->detail 				= $remarks;
					$followUpObj->save();
				}
			}
		}
		die("success");
	}
	
	public function editEnquiry($enquiry_id = ""){
		$dealer_id				=	$this->get_dealer_id();
		$enquiryDetails	    =	DB::table('enquiries')
								->where('id',$enquiry_id)
								->where("dealer_id",$dealer_id)
								->first();

		if(empty($enquiryDetails)) {
			return Redirect::back();
		}			
		$stateList		=	DB::table('states')
							->where('status',1)
							->where('country_id',COUNTRY_ID)
							->orderBy('name','ASC')
							->pluck('name','id')
							->toArray(); 
		$vehiclecolor 	=  $this->getDropDownListBySlug('vehiclecolor');
		$vehiclemodel 	=  $this->getDropDownListBySlug('vehiclemodel');
		$sourceOfInformation =  $this->getDropDownListBySlug('sourceOfInformation');
		$customerOccupation =  $this->getDropDownListBySlug('customerOccupation');
		$enquirymode =  $this->getDropDownListBySlug('enquirymode');
		$customer_category =  $this->getDropDownListBySlug('customer-category');
		$status_type =  $this->getDropDownListBySlug('enquirystatus');
		$finenceroptions =  $this->getDropDownListBySlug('finenceroptions');
		$vehiclebrand =  $this->getDropDownListBySlug('vehiclebrand');
		$enquirylevel 	=  $this->getDropDownListBySlug('enquirylevel');
		$outletList 	=  $this->getDropDownListBySlug('outlet');
		$sales_consultant	= User::where('user_role_id',STAFF_USER_ROLE_ID)
							->where("dealer_id",$dealer_id)
							->where("is_active",1)
							->where("is_deleted",0)
							->orderBy('full_name', 'ASC')
							->pluck('full_name','id')
							->toArray();
							
		$dealerLocationName 	=  DB::table('dealer_location')
									->where('dealer_id',$dealer_id)
									->where('is_active',1)
									->orderBy('location_name','ASC')
									->pluck('location_name','id')
									->toArray();	
					 
		$cityList		=	DB::table('cities')
							->where('state_id',$enquiryDetails->state)
							->distinct('name')
							->pluck('name','id')
							->toArray(); 
		return View::make('dealerpanel.'.$this->model.'.edit', compact("enquiryDetails",'outletList','enquirylevel','cityList','stateList','vehiclecolor','vehiclemodel','dealerLocationName','sourceOfInformation','sales_consultant','enquirymode','customer_category','status_type','finenceroptions','vehiclebrand','customerOccupation'));
	
		
	} // end editUser()
	
	public function updateEnquiry($enquiry_id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
				//	'enquiry_mode' 					=>	 'required',
					'mobile_number' 				=>	 'required|integer|digits:10',
					'customer_name'					=>	 'required',
					// 'email'							=>	 'email',
					// 'gender'						=>	 'required',
					// 'source_of_info'				=>	 'required',
					// 'address_1'						=>	 'required',
					// 'state'							=>	 'required',
					// 'city'							=>	 'required',
					// 'location_name'					=>	 'required',
					// 'customer_category'				=>	 'required',
					'vehicle_modal'					=>	 'required',
					//'current_vehicle'				=>	 (empty(Input::get('customer_category')) && Input::get('customer_category') != COMPETITOR_CISTOMER) ? 'required' : '',
					//'current_vehicle_brand'			=>	 'required_if:customer_category,==,'.COMPETITOR_CISTOMER,
					//'vehicle_color'					=>	 'required',
					// 'current_vehicle_modal'			=>	 'required_if:customer_category,==,'.COMPETITOR_CISTOMER,
					// 'test_drive_date'				=>	 'required',
					// 'finenceroptions'				=>	 'required',
					// 'financer'						=>	 'required_if:finenceroptions,==,'.FINENCE_OPTION,
					//'test_drive_remark'				=>	 'required',
					//'status'						=>	 'required',
					//'sales_consultant'				=>	 'required',
					// 'enquiry_date'					=>	 'required',
					// 'remarks'	 					=>	 'required',
					// 'next_follow_up_date'			=>	 'required',
					// 'delivery_date'					=>	 'required',
					// 'source_of_info_remakrs'		=>	 'required_if:source_of_info,==,'.SOURCE_OF_INFO_OTHERS,
					// 'customer_occupation_remarks'	=>	 'required_if:customer_occupation,==,'.Occupation_Other_Details,
					// 'referred_by_name'				=>	 'required_if:source_of_info,==,'.REFERRED_BY_ID,
					// 'referred_by_number'			=>	 'integer|digits:10|required_if:source_of_info,==,'.REFERRED_BY_ID,
				),
				array(
					// 'current_vehicle.required'	=> 	 'The current vehicle info is required.',
					// 'financer.required_if'	=> 	 'The financer is required.',
					// 'current_vehicle_brand.required_if'	=> 	 'The current vehicle brand is required.',
					// 'current_vehicle_modal.required_if'	=> 	 'The current vehicle model is required.',
					// 'email.email'					=> 	 'The email address is invalid.',
					// 'source_of_info.required' 		=>	 'The source of information field is required.',
					'vehicle_modal.required' 		=>	 'The model enquired field is required.',
					// 'vehicle_color.required' 		=>	 'The vehicle color field is required.',
					// 'dob.required' 					=>	 'The date of birth is required.',
					// 'zip.required' 					=>	 'The zipcode field is required.',
					"mobile_number.integer"			=>	  trans("Phone number must have a numeric value."),
					"mobile_number.digits"			=>	  trans("Phone number must have 10 digits."),
					//'status.required' 				=>	 'The enquiry status field is required.',
					// 'enquiry_type.required_if' 		=>	 'The enquiry type field is required.',
					// 'remarks.required_if' 			=>	 'The remarks field is required.',
					//'test_drive_remark.required' 			=>	 'The test ride remark field is required.',
					// 'test_drive_date.required' 			=>	 'The test ride date field is required.',
					// 'finenceroptions.required'   	=>	"The payment options field is required.",
					// "source_of_info_remakrs.required_if"=>"The remarks field is required.",
					// "customer_occupation_remarks.required_if"=>"The remarks field is required.",
					// "referred_by_name.required_if"=>"The person name field is required.",
					// "referred_by_number.required_if"=>"The mobile number field is required.",
					// "referred_by_number.integer"	=>	  trans("Phone number must have a numeric value."),
					// "referred_by_number.digits"		=>	  trans("Phone number must have 10 digits."),
				)
			); 
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				if(Input::get('is_draft') != '' && Input::get('is_draft')=="Save As Draft"){
					$is_drafted		=	1;
				}else{
					$is_drafted		=	0;
				}
				$dealer_id				=	$this->get_dealer_id();
				$enquiry						= 	Enquiry::find($enquiry_id);
				$enquiry->dealer_id				=	$dealer_id;
				$enquiry->enquiry_date			=	!empty(Input::get('enquiry_date')) ? date('Y-m-d',strtotime(Input::get('enquiry_date'))) : '0000-00-00';
				$enquiry->vehicle_modal			=	Input::get('vehicle_modal');
				$enquiry->vehicle_color			=	!empty(Input::get('vehicle_color')) ? Input::get('vehicle_color') : '';
				$enquiry->customer_name			=	Input::get('customer_name');
				$enquiry->gender					=	Input::get('gender');
				$enquiry->email					=	Input::get('email');
				$enquiry->dob					=	!empty(Input::get('dob')) ? date('Y-m-d',strtotime(Input::get('dob'))) : '0000-00-00';
				$enquiry->address_1				=	Input::get('address_1');
				$enquiry->address_2				=	!empty(Input::get('address_2')) ? Input::get('address_2') : '';
				$enquiry->city					=	Input::get('city');
				$enquiry->state					=	Input::get('state');
				$enquiry->zip					=	!empty(Input::get('zip')) ? Input::get('zip') : '';
				$enquiry->location_name			=	Input::get('location_name');
				$enquiry->mobile_number			=	Input::get('mobile_number');
				$enquiry->next_follow_up_date	=	!empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$enquiry->customer_occupation	=	!empty(Input::get('customer_occupation')) ? Input::get('customer_occupation') : '';
				//$enquiry->status				=   ENQUIRY_IN_PROCESS_STATUS;
				$enquiry->remarks				=	Input::get('remarks');
				$enquiry->current_vehicle		=	!empty(Input::get('current_vehicle')) ? Input::get('current_vehicle') : '';
				$enquiry->current_vehicle_brand		=	!empty(Input::get('current_vehicle_brand')) ? Input::get('current_vehicle_brand') : '';
				$enquiry->current_vehicle_model		=	!empty(Input::get('current_vehicle_modal')) ? Input::get('current_vehicle_modal') : '';
				$enquiry->source_of_info			=	Input::get('source_of_info');
				$enquiry->sales_consultant		=	Input::get('sales_consultant');
				$enquiry->enquiry_type			=	Input::get('enquiry_mode');
				// $enquiry->enquiry_type			=	Input::get('enquiry_type');
				$enquiry->test_drive_date		=	!empty(Input::get('test_drive_date')) ? date('Y-m-d',strtotime(Input::get('test_drive_date'))) : '0000-00-00';
				$enquiry->test_drive_remark		=	!empty(Input::get('test_drive_remark')) ? Input::get('test_drive_remark') : '';
				$enquiry->is_drafted			=	$is_drafted;
				$enquiry->updated_at			=   date("Y-m-d H:i:s");
				$enquiry->delivery_address_2	=	Input::get('delivery_address_2');
				$enquiry->delivery_address_1	=	Input::get('delivery_address_1');
				$enquiry->delivery_date			=	Input::get('delivery_date');
				$enquiry->finencer_option		=	Input::get('finenceroptions');
				$enquiry->financer				=	!empty(Input::get('financer')) ? Input::get('financer') : '';
				$enquiry->customer_category		=	Input::get('customer_category');
				$enquiry->contact_number		=	Input::get('contact_number');
				$enquiry->outlet				=	Input::get('outlet');
				$enquiry->enquiry_level			=	Input::get('enquiry_level');
				$enquiry->age					=	Input::get('age');
				$enquiry->source_of_info_remakrs=	Input::get('source_of_info_remakrs');
				$enquiry->customer_occupation_remarks=	Input::get('customer_occupation_remarks');
				$source_of_info_id 				= 	Input::get('source_of_info');
				$referred_by_name 				= 	Input::get('referred_by_name');
				$referred_by_number 			= 	Input::get('referred_by_number');
				$enquiry->referred_by_name		=	!empty($referred_by_name) && ($source_of_info_id == REFERRED_BY_ID) ? $referred_by_name : '';
				$enquiry->referred_by_number	=	!empty($referred_by_number) && ($source_of_info_id == REFERRED_BY_ID) ? $referred_by_number : '';
				$enquiry->save();
				Session::flash('flash_notice', trans("Enquiry has been updated successfully.")); 
				return Redirect::to('/dealerpanel/enquiry-management');
			}
		}
	}


	public function viewEnquiry($enquiry_id=""){ 
		$dealer_id				=	$this->get_dealer_id();
		$enquiryDetails			=	DB::table('enquiries')
									->where("id",$enquiry_id)
									->where("dealer_id",$dealer_id)
									->select("enquiries.*",
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.status) as enquiry_stats"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.current_vehicle_brand) as current_vehicle_brand_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.finencer_option) as finencer_option_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_category) as customer_category_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_type) as enquiry_mode"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.close_reason) as close_reason"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.buying_competition) as buying_competition"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_color) as vehicle_color"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_occupation) as customer_occupation"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.outlet) as outlet"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_level) as enquiry_level"),
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = enquiries.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.source_of_info) as source_of_info"),
									DB::raw("(SELECT name FROM states WHERE id = enquiries.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = enquiries.city) as city"),
									DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"))
									->first();
	
		if(empty($enquiryDetails)) {
			return Redirect::back();
		}else{ 
			$status_type =  $this->getDropDownListBySlug('enquirystatus');
			$bookingCloseReason =  $this->getDropDownListBySlug('cancelReason'); 
			$buying_competition 		=  $this->getDropDownListBySlug('buying-competition');

			$followUpDetails = DB::table('enquiry_follow_up')
								 ->leftJoin('users','users.id','=','enquiry_follow_up.user_id')
								 ->select('enquiry_follow_up.*','users.full_name as fullname')
								->where('enquiry_follow_up.enquiry_id',$enquiry_id)->get();
			
			return View::make('dealerpanel.'.$this->model.'.view', compact("enquiryDetails","followUpDetails",'status_type','bookingCloseReason',"buying_competition"));
		}
	}

	
	public function deleteEnquiry($enquiry_id = ''){
		$enquiryDetails			=	Enquiry::find($enquiry_id); 
		if(empty($enquiryDetails)) {
			return Redirect::back();
		}
		if($enquiry_id){	
			$Model					=	Enquiry::where('id',$enquiry_id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Enquiry has been deleted successfully.")); 
		}
		return Redirect::back();
	}
	
	public function addFollowUp(){
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'enquiry_id' 					=>	 'required',
					'next_follow_up_date' 				=>	 'required',
					'detail' 				=>	 'required',
					),
				array(
					"detail.required"					=>	trans("The remarks field is required."),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'success' 			=> 	2,
					'errors' 			=> 	$validator->errors()
				);
				return Response::json($response); 
				die;;
			}else{
				$dealer_id				=	$this->get_dealer_id();
				$followUpObj 						= new EnquiryFollowUp;
				$followUpObj->user_id 				= $dealer_id;
				$followUpObj->enquiry_id 			= Input::get('enquiry_id');
				$followUpObj->next_follow_up_date   = !empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$followUpObj->detail 				= Input::get('detail');
				$followUpObj->save();
				Enquiry::where('id',Input::get('enquiry_id'))->update(array('next_follow_up_date'=>Input::get('next_follow_up_date')));
				
				$response				=	array(
					'success' 			=> 	1,
					'errors' 			=> 	'',
				);
				Session::flash('flash_notice',trans("Follow up added successfully.")); 
				return Response::json($response); 
				die;
			}
		}
	}
	
	public function exportEnquiryToExcel(){
		$genderArr 			= 	Config::get("gender_type_array");
		$sourceOfInformation =  $this->getDropDownListBySlug('sourceOfInformation');
		// echo "<pre>";print_r($sourceOfInformation[21]);die;
		$searchData			=	Session::get('enquiry_search_data');
		$DB 					= 	Enquiry::query();
		$searchVariable			=	array(); 
		if ($searchData) {
			unset($searchData['display']);
			unset($searchData['_token']);
			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
			foreach($searchData as $fieldName => $fieldValue){
				if($fieldValue != ""){
					if($fieldName == 'follow_up_end_date' || $fieldName == 'follow_up_start_date' || $fieldName == 'enquiry_start_date' || $fieldName == 'enquiry_end_date'  ){
						if($fieldName == 'follow_up_end_date'){  
							$DB->where('enquiries.next_follow_up_date','<=',$fieldValue);
						}
						if($fieldName == 'follow_up_start_date'){  
							$DB->where('enquiries.next_follow_up_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_start_date'){  
							$DB->where('enquiries.enquiry_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_end_date'){  
							$DB->where('enquiries.enquiry_date','<=',$fieldValue);
						}
					}else{
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$dealer_id				=	$this->get_dealer_id();
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';						
		$result 				= 	$DB
									->where('enquiries.is_deleted',0)
									->where('enquiries.dealer_id',$dealer_id)
									->select("enquiries.*",
									DB::raw("(SELECT location_name FROM dealer_location WHERE id = enquiries.location_name) as location_name"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.outlet) as outlet"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_level) as enquiry_level"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.close_reason) as close_reason"),
									DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.buying_competition) as buying_competition"),
									DB::raw("(SELECT full_name FROM users WHERE id = enquiries.sales_consultant) as sales_consultant"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_color) as vehicle_color"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.source_of_info) as source_of_info"),
									DB::raw("(SELECT name FROM states WHERE id = enquiries.state) as state"),
									DB::raw("(SELECT name FROM cities WHERE id = enquiries.city) as city"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_category) as customer_category"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.current_vehicle_brand) as current_vehicle_brand"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.current_vehicle_model) as current_vehicle_model"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.status) as enquiry_stats"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.finencer_option) as finencer_option"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.enquiry_type) as enquiry_type"),DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.customer_occupation) as customer_occupation"))
									->orderBy($sortBy, $order)
									->get()->toArray();	
									// echo "<pre>";print_r($result);die;						
									
												
		$thead = array();
		$thead[]		= array("Enquiry Date","Enquiry No.","Model enquired","Vehicle Color","Enquiry Mode","Customer Name","Mobile No.","Email Address","dob","Owner Age","gender","Address","City","State","ZipCode","Location Name","Customer Category","Sales Consultant","Enquiry Status","Payment Options","Financer","Next Follow-up Date","Remarks","Customer Occupation","Contact Number","Source of Information","Source of Info Remarks","Current Vehicle Info","Test Ride Date","Test Ride Remarks","Current Brand","Current Model","Outlet","Enquiry Level","Close Reason","Close Remarks","Reason for buying competition", "Other Details");
		if(!empty($result)) {
			foreach($result as $record) {
				$enquiry_date					=	!empty($record['enquiry_date'])?date(Config::get("Reading.date_format") , strtotime($record['enquiry_date'])):'';
				$enquiry_number					=	!empty($record['enquiry_number'])?$record['enquiry_number']:'';
				$vehicle_modal					=	!empty($record['vehicle_modal'])?$record['vehicle_modal']:'';
				$vehicle_color					=	!empty($record['vehicle_color'])?$record['vehicle_color']:'';
				$enquiry_type					=	!empty($record['enquiry_type'])?$record['enquiry_type']:'';
				$customer_name					=	!empty($record['customer_name'])?$record['customer_name']:'';
				$mobile_number					=	!empty($record['mobile_number'])?$record['mobile_number']:'';
				$email							=	!empty($record['email'])?$record['email']:'';
				$dob							=	!empty($record['dob'])? date(Config::get("Reading.date_format") , strtotime($record['dob'])):'';
				$age							=	!empty($record['age'])?$record['age']:'';
				$gender							=	!empty($record['gender'])? $genderArr[$record['gender']]:'';
				$Address						=	$record['address_1'].", ".$record['address_2'];
				$city							=	!empty($record['city'])?$record['city']:'';
				$state							=	$record['state'];
				$zipcode						=	$record['zip'];
				$location_name					=	!empty($record['location_name'])?$record['location_name']:'';
				$customer_category				=	!empty($record['customer_category'])?$record['customer_category']:'';
				$sales_consultant				=	!empty($record['sales_consultant'])?$record['sales_consultant']:'';
				$status							=	!empty($record['enquiry_stats'])?$record['enquiry_stats']:'';
				$finencer_option				=	!empty($record['finencer_option'])?$record['finencer_option']:'';
				$financer						=	!empty($record['financer'])?$record['financer']:'';
				$next_follow_up_date			=	!empty($record['next_follow_up_date'])?date(Config::get("Reading.date_format") , strtotime($record['next_follow_up_date'])):'';
				$remarks						=	!empty($record['remarks'])?$record['remarks']:'';
				$customer_occupation			=	!empty($record['customer_occupation'])?$record['customer_occupation']:'';
				$contact_number					=	!empty($record['contact_number'])?$record['contact_number']:'';
				
				$sourceOfInformation			=	!empty($record['source_of_info'])? $record['source_of_info']:'';
				$source_of_info_remarks			=	!empty($record['source_of_info_remakrs'])? $record['source_of_info_remakrs']:'';
				$current_vehicle				=	!empty($record['current_vehicle'])?$record['current_vehicle']:'';
				$test_drive_date				=	!empty($record['test_drive_date'])?date(Config::get("Reading.date_format") , strtotime($record['test_drive_date'])):'';
				$test_drive_remark				=	!empty($record['test_drive_remark'])?$record['test_drive_remark']:'';
				$delivery_date				=	!empty($record['delivery_date'])?date(Config::get("Reading.date_format") , strtotime($record['delivery_date'])):'';
				$delivery_address_1				=	!empty($record['delivery_address_1'])?$record['delivery_address_1']:'';
				$delivery_address_2				=	!empty($record['delivery_address_2'])?$record['delivery_address_2']:'';
				$current_vehicle_brand			=	!empty($record['current_vehicle_brand'])?$record['current_vehicle_brand']:'';
				$current_vehicle_model			=	!empty($record['current_vehicle_model'])?$record['current_vehicle_model']:'';
				$outlet							=	!empty($record['outlet'])?$record['outlet']:'';
				$enquiry_level					=	!empty($record['enquiry_level'])?$record['enquiry_level']:'';
				$close_reason					=	!empty($record['close_reason'])?$record['close_reason']:'';
				$close_remarks					=	!empty($record['cancel_remarks'])?$record['cancel_remarks']:'';
				$buying_competition				=	!empty($record['buying_competition'])?$record['buying_competition']:'';
				$other_details					=	!empty($record['other_details'])?$record['other_details']:'';
				$thead[]						= 	array($enquiry_date,$enquiry_number,$vehicle_modal,$vehicle_color,$enquiry_type,$customer_name,$mobile_number,$email,$dob,$age,$gender,$Address,$city,$state,$zipcode,$location_name,$customer_category,$sales_consultant,$status,$finencer_option,$financer,$next_follow_up_date,$remarks,$customer_occupation,$contact_number, $sourceOfInformation,$source_of_info_remarks,$current_vehicle,$test_drive_date,$test_drive_remark,$current_vehicle_brand,$current_vehicle_model,$outlet,$enquiry_level,$close_reason,$close_remarks,$buying_competition,$other_details);
			}
		}								
		// echo '<pre>'; print_r($thead); die;					
		return  View::make('dealerpanel.'.$this->model.'.export_excel', compact('thead'));
		
	}

	/**
	* Function for get the details of user by mobile number
	*
	* @param null
	*
	* @return view page. 
	*/
	public function findUserByMobileNumber(){
		$mobile_number	=	Input::get('number');
		$response		=	array();
		if($mobile_number != ''){
			// get data from 'enquiries'
			$EnquiryUserData		=	DB::table('enquiries')->where('mobile_number', $mobile_number)->where('is_deleted', 0)->orderBy('updated_at', 'desc')->first();
			if(empty($EnquiryUserData)){
				// get data from 'users'
				$userData			=	DB::table('users')->where('phone_number', $mobile_number)->where('is_deleted', 0)->orderBy('updated_at', 'desc')->first();
				if(empty($userData)){
					// get data from 'advance_booking'
					$AdvanceBookingUserData	=	DB::table('advance_booking')->where('mobile_number', $mobile_number)->where('is_deleted', 0)->orderBy('updated_at', 'desc')->first();
					if(empty($AdvanceBookingUserData)){
						// get data from 'booking'
						$BookingUserData	=	DB::table('booking')->where('mobile_number', $mobile_number)->where('is_deleted', 0)->orderBy('updated_at', 'desc')->first();
						if(empty($BookingUserData)){
							$response['success']	=	0;
							$response['data']		=	'';
						}else{
							$response['success']	=	1;
							$response['data']		=	$BookingUserData;
						}
					}else{
						$response['success']	=	1;
						$response['data']		=	$AdvanceBookingUserData;
					}
				}else{
					$response['success']	=	1;
					$response['data']		=	$userData;
				}
			}else{
				$response['success']	=	1;
				$response['data']		=	$EnquiryUserData;
			}
		}else{
			$response		=	array(
				'success'=>0,
				'data'=>''
			);
		}
		// find, current user has any advance bookings.... 
		if(isset($_POST['get_advance_booking'])){
			$advance_booking_count 			=	DB::table('advance_booking')->where('is_deleted', 0)->where('mobile_number', $mobile_number)->get()->count();
			if($advance_booking_count > 0 && $advance_booking_count != ''){
				$response['is_advance_booking']	=	1;
			}else{
				$response['is_advance_booking']	=	0;
			}
			//return View::make('dealerpanel.'.$this->model.'.show_advance_booking', compact());
		}else{
			$response['is_advance_booking']	=	0;
		}
		return $response;die;
	}

	/**
	* Function for cancel inquiry reason
	*
	* @param null
	*
	* @return index page. 
	*/
	public function cancelEnquiry(){
		$thisData			=	Input::all();
		$enquiry_id    		=   Input::get('enquiry_id');
	
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					//'cancel_reason' 		=>	 'required',
					'cancel_remarks' 		=>	 'required',
					'close_reason' 			=>	 'required',
					'buying_competition' 	=>	 'required_if:close_reason,==,'.BOUGHT_COMPETITION_REASON,
					'other_details' 		=>	 'required_if:buying_competition,==,'.BUYING_COMPETITION_OTHER_REASON,

					),
				array(
					"cancel_remarks.required"		=>	trans("The remarks field is required."),
					"other_details.required_if"		=>	trans("The other details field is required."),
					"buying_competition.required_if"=>	trans("The reason for buying competition field is required."),
					//"cancel_reason.required"				=>	trans("Please select close reason."),
				)
			);
			if ($validator->fails()) {	
				$response				=	array(
					'success' 			=> 	2,
					'errors' 			=> 	$validator->errors()
				);
				return Response::json($response); 
				die;
			}else{
				$buying_competition	=	!empty(Input::get('buying_competition')) ? Input::get('buying_competition') : '';
				$other_details		=	!empty(Input::get('other_details')) ? Input::get('other_details') : '';
				Enquiry::where('id',$enquiry_id)->update(array('status'=>ENQUIRY_CLOSE_STATUS,'cancel_remarks'=>Input::get('cancel_remarks'),'is_cancelled'=>1,'close_reason'=>Input::get('close_reason'),'buying_competition'=>$buying_competition,'other_details'=>$other_details));
				
				$response				=	array(
					'success' 			=> 	1,
					'errors' 			=> 	'',
				);
				Session::flash('flash_notice',trans("Enquiry has been closed successfully.")); 
				return Response::json($response); 
				die;
			}
		}
	}

	/**
	* Function for Change enquiry status
	*
	* @param null
	*
	* @return index page. 
	*/
	public function changeEnquiryStatus(){
		$enquiry_id		=	Input::get('enquiry_id');
		$change_status	=	Input::get('change_status');
		$close_reason	=	!empty(Input::get('close_reason')) ? Input::get('close_reason') : '';
		$remark			=	!empty(Input::get('remark')) ? Input::get('remark') : '';
		$buying_competition	=	!empty(Input::get('buying_competition')) ? Input::get('buying_competition') : '';
		$other_details		=	!empty(Input::get('other_details')) ? Input::get('other_details') : '';
		$response		=	array();
		if($enquiry_id != '' && $change_status != ''){
			$findEnquiry	=	Enquiry::find($enquiry_id);
			if($findEnquiry){
				$findEnquiry->close_reason			=	$close_reason;
				$findEnquiry->cancel_remarks		=	$remark;
				$findEnquiry->status				=	$change_status;
				$findEnquiry->buying_competition	=	$buying_competition;
				$findEnquiry->other_details			=	$other_details;
				$findEnquiry->save();
				$response['success']	=	1;
				$response['data']		=	'Enquiry Status has been changed successfully.';
			}
		}else{
			$response['success']	=	0;
			$response['data']	=	'';
		}
		Session::flash('flash_notice',trans("Enquiry status has been changed successfully.")); 
		return Response::json($response);die;
	}

	/**
	* Function for Change enquiry status
	*
	* @param null
	*
	* @return index page. 
	*/
	public function AddQuotaion($enquiry_id=''){
		$dealer_id				=	$this->get_dealer_id();
		$enquiryDetails	    =	DB::table('enquiries')
								->where('id',$enquiry_id)
								->where("dealer_id",$dealer_id)
								->first();
		$enquiry_id			=	$enquiryDetails->id;
		$enquiry_number		=	$enquiryDetails->enquiry_number;
		if(empty($enquiryDetails)) {
			return Redirect::back();
		}
		return View::make('dealerpanel.'.$this->model.'.add_quotation', compact('enquiry_id', 'enquiry_number'));
	}

	/**
	* Function for Change enquiry status
	*
	* @param null
	*
	* @return index page. 
	*/
	public function generateQuotaionPdf(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData			=	Input::all();
		
		$response	=	array();
		$validator = Validator::make(
			Input::all(),
			array(
				'ex_showroom_price'			=>	'required|numeric',
				'registration_certificate'	=>	'required|numeric',
				'insurance'					=>	'required|numeric',
				'number_plate'					=>	'required|numeric',
				'helmet'					=>	'required|numeric',
			),
			array(
				'ex_showroom_price.required'		=>	'The ex showroom price field is required.',
				'ex_showroom_price.numeric'			=>	'The ex showroom price must be numeric.',
				'registration_certificate.required'	=>	'The registration certificate field is required.',
				'registration_certificate.numeric'	=>	'The registration certificate must be numeric.',
				'insurance.required'				=>	'The insurance field is required.',
				'insurance.numeric'					=>	'The insurance must be numeric.',
				'number_plate.required'				=>	'The number plate field is required.',
				'number_plate.numeric'				=>	'The number plate field must be numeric.',
				'helmet.required'					=>	'The helmet field is required.',
				'helmet.numeric'					=>	'The helmet field must be numeric.',
			)
		);
		if($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			try {

					$ex_showroom_price					=	!empty(Input::get('ex_showroom_price'))?Input::get('ex_showroom_price'):0;
				$registration_certificate			=	!empty(Input::get('registration_certificate'))?Input::get('registration_certificate'):0;
				$insurance							=	!empty(Input::get('insurance'))?Input::get('insurance'):0;
				$number_plate						=	!empty(Input::get('number_plate'))?Input::get('number_plate'):0;
				$helmet								=	!empty(Input::get('helmet'))?Input::get('helmet'):0;
				$other_charges						=	!empty(Input::get('other_charges'))?Input::get('other_charges'):0;
				
				$enquiry_id	=	Input::get('enquiry_id');
				$dealer_id				=	$this->get_dealer_id();
				$enquiryDetails			=	DB::table('enquiries')
													->where('enquiries.id',$enquiry_id)
													->where("enquiries.dealer_id",$dealer_id)
													->leftjoin('dropdown_managers as vehiclemodal', 'vehiclemodal.id','=','enquiries.vehicle_modal')
													->leftjoin('dropdown_managers as vehiclecolor', 'vehiclecolor.id','=','enquiries.vehicle_color')
													->leftjoin("states as s","s.id",'=','enquiries.state')
													->leftjoin("cities","cities.id",'=','enquiries.city')
													->select('enquiries.*', 'vehiclemodal.name as vehicle_modal_name','vehiclecolor.name as vehicle_color_name','s.name as state_name','cities.name as city_name',DB::raw("(SELECT name FROM dropdown_managers WHERE id = enquiries.vehicle_modal) as vehicle_modal_name"))
													->first();
													//echo "<pre>";print_r($enquiryDetails);exit; 
				if(empty($enquiryDetails)) {
					return Redirect::back();
				}
				
				$total_amount		=	$ex_showroom_price+$registration_certificate+$insurance+$number_plate+$helmet+$other_charges;
				$model_name					=	ucfirst($enquiryDetails->vehicle_modal_name);
				$total_amount  				= 	number_format($total_amount, 2);
				$ex_showroom_price  		= 	number_format($ex_showroom_price, 2);
				$registration_certificate  	= 	number_format($registration_certificate, 2);
				$insurance  				= 	number_format($insurance, 2);
				$number_plate  				= 	number_format($number_plate, 2);
				$helmet  					= 	number_format($helmet, 2);
				$other_charges  			= 	number_format($other_charges, 2);
				
				
				
				$dealerDetails = DB::table('users')
										->where('users.id',$enquiryDetails->dealer_id)
										->leftjoin('states', 'users.state_id', '=', 'states.id')
										->leftjoin("states as s","s.id",'=','users.state_id')
										->leftjoin("cities","cities.id",'=','users.city')
										->select('users.*', 'states.name as state_name','s.name as state_name','cities.name as city_name')
										->first();
				// get total amount in words
				//echo '<pre>'; print_r($dealerDetails); echo '</pre>'; die;
				$total_amount_in_words			=	$this->convert_number_into_words($total_amount);
				return View::make('dealerpanel.'.$this->model.'.view_quotation_pdf', compact('enquiryDetails','dealerDetails', 'total_amount_in_words', 'ex_showroom_price', 'registration_certificate', 'insurance', 'number_plate', 'other_charges','total_amount','helmet','model_name'));
				


				
			} catch (\Throwable $th) {
				return Redirect::back()->withErrors($th->getMessage());

				//throw $th;
			}
			
			
		}
	}
} //end EnquiryController()
