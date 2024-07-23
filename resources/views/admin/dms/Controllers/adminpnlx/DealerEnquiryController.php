<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use App\Model\DealerEnquiry;
use App\Model\DropDown;
use App\Model\DealerEnquiryFollowUp;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* DealerEnquiryController Controller
*
* Add your methods in the class below
*
* This file will render views\DealerEnquiryController\dashboard
*/
	class DealerEnquiryController extends BaseController {
		
		public $model	=	'dealerEnquiry';

	public function __construct() {
		View::share('modelName',$this->model);
	}

/**
	* Function for add DealerEnquiry List
	*
	* @param null
	*
	* @return view page. 
	*/

		
	public function DealerEnquiryList(){
		$DB 					= 	DealerEnquiry::query();
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
					if($fieldName == 'follow_up_end_date' || $fieldName == 'follow_up_start_date' || $fieldName == 'enquiry_date_start' || $fieldName == 'enquiry_date_end'){
						if($fieldName == 'follow_up_end_date'){  
							$DB->where('dealer_enquiry.next_follow_up_date','<=',$fieldValue);
						}
						if($fieldName == 'follow_up_start_date'){  
							$DB->where('dealer_enquiry.next_follow_up_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_date_start'){  
							$DB->where('dealer_enquiry.enquiry_date','>=',$fieldValue);
						}
						if($fieldName == 'enquiry_date_end'){  
							$DB->where('dealer_enquiry.enquiry_date','<=',$fieldValue);
						}
					}else{
						$DB->where("$fieldName",'like','%'.$fieldValue.'%');
					}
					
					
				}
				$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
			}
		}
		$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'dealer_enquiry.created_at';
		$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';
		$result 				= 	$DB
									->leftjoin('cities','cities.id','=','dealer_enquiry.city')
									->where('is_deleted',0)
									->select('dealer_enquiry.*','cities.name as city_name' )
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
		//echo'<pre>'; print_r($result); echo'</pre>'; die;
								
		$complete_string		=	Input::query();
		unset($complete_string["sortBy"]);
		unset($complete_string["order"]);
		$query_string			=	http_build_query($complete_string);
		$result->appends(Input::all())->render();
		Session::put("DealerEnquiry_search_data",$inputGet);
		return  View::make('admin.'.$this->model.'.index',compact('result','searchVariable','sortBy','order','query_string'));
	}
		// end function DealerEnquiryList
	

	/**
	* Function for dealer Enquiry Add page
	*
	* @param null
	*
	* @return view page. 
	*/

	public function dealerEnquiryAdd(){
		$state=	DB::table('states')
					->where("status",1)
					->where("country_id",COUNTRY_ID)
					->pluck("name","id")->toArray();

		$sales_person=DB::table('users')
						->where('user_role_id',STAFF_USER_ROLE_ID)
						->where('department','sales')
						->where('designation','Sales Person')
						->pluck('full_name','id')
						->toArray();
		return View::make('admin.'.$this->model.'.add',compact('state','sales_person'));
	}// end function dealerEnquiryAdd


	
	/**
	* Function for save dealerEnquiry
	*
	* @param null
	*
	* @return view page. 
	*/
	
	public function dealerEnquirySave(){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		//print_r($thisData);die;
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
								$thisData,
								array(
									'name' 					=>	 'required',
									'mobile_no' 			=>	 'required|integer|digits:10',
									'phone_number' 			=>	 'integer|digits:10',
									'pincode' 				=>	 'required|numeric',
									'email' 				=>	 'required|email',
									//'address' 			=>	 'required',
									'city' 					=>	 'required',
									'state' 				=>	 'required',
									'next_follow_up_date' 	=>	 'required',
									'remarks' 				=>	 'required',
									'enquiry_date' 			=>	 'required',
									'contact_person' 		=>	 'required',

									//'sales_person' 			=>	 'required',	
								),
								array
								(
									"enquiry_date.required"				=>	trans("The enquiry date field is required."),
									"email.required"					=>	trans("The email address field is required."),
									"email.unique"						=>	trans("This email address is already exist."),
									"mobile_no.required"				=>	trans("The mobile number field is required."),
									"mobile_no.integer"					=>	trans("The mobile number must have a numeric value."),
									"mobile_no.numeric"					=>	trans("The mobile number must be a number."),
									"mobile_no.digits"					=>	trans("The mobile number must have 10 digits."),
									
								)	
							);
			if ($validator->fails()) {	
				return Redirect::to('adminpnlx/dealer-enquiry/add-enquiry')->withErrors($validator)->withInput();
			}else{
				DB::beginTransaction();
				$dealer 						= 	new DealerEnquiry;
				$dealer->full_name   			= 	Input::get('name');
				$dealer->phone_number   		= 	Input::get('mobile_no');
				$dealer->address   				= 	Input::get('address');
				$dealer->telephone   			= 	Input::get('phone_number');
				$dealer->pincode   				= 	Input::get('pincode');
				$dealer->contact_person   		= 	Input::get('contact_person');
				$dealer->city   				= 	Input::get('city');
				$dealer->state_id   			= 	Input::get('state');
				$dealer->sales_person_id 		= 	Input::get('sales_person');
				$dealer->email 					= 	Input::get('email');
				$dealer->next_follow_up_date	=	!empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$dealer->remarks				=	Input::get('remarks');
				$dealer->enquiry_date			=	!empty(Input::get('enquiry_date')) ? date('Y-m-d',strtotime(Input::get('enquiry_date'))) : '0000-00-00';
				$dealer->save();

				$id  = $dealer->id;
				if(!empty($id)){
					$enquiry_number		=	'#DE000'.$id;
					DealerEnquiry::where('id',$id)->update(array('enquiry_number'=>$enquiry_number));
					$followUpObj 	= new DealerEnquiryFollowUp;
					$followUpObj->user_id 				= Auth::user()->id;
					$followUpObj->dealer_enquiry_id 	= $id;
					$followUpObj->next_follow_up_date   = !empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
					$followUpObj->detail 				= Input::get('remarks');
					$followUpObj->save();	
				}
				
				DB::commit();
				
				 Session::flash('flash_notice', trans("Dealer Enquiry has been added successfully")); 
				return Redirect::to('adminpnlx/dealer-enquiry');
			}
		}
	}// end function dealerEnquirySave



	/**
	* Function for edit  dealerEnquiryEdit
	*
	* @param user_id
	*
	* @return view page. 
	*/
	
	public function dealerEnquiryEdit($userId = 0){
		$userDetails			=	DealerEnquiry::find($userId); 
		if(empty($userDetails)) {
			return Redirect::back();
		}
		$state = DB::table('states')
				->where("status",1)
				->where("country_id",COUNTRY_ID)
				->pluck("name","id")->toArray();

		$sales_person = DB::table('users')
						->where('user_role_id',STAFF_USER_ROLE_ID)
						->where('department','sales')
						->where('designation','Sales Person')
						->pluck('full_name','id')
						->toArray();
		$cityList		=	DB::table('cities')
							->where('state_id',$userDetails->state_id)
							->distinct('name')
							->pluck('name','id')
							->toArray();
							
		

	//print_r($state);die;			
		return View::make("admin.dealerEnquiry.edit", compact("userDetails",'cityList',"state","sales_person"));
		
	} // end dealerEnquiryEdit()


	/**
	* Function for update  dealer Enquiry 
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function dealerEnquiryUpdate($userId){	
	Input::replace($this->arrayStripTags(Input::all()));
	$thisData						=	Input::all(); 
	if(!empty($thisData)){
			$validator 					= 	Validator::make(
				Input::all(),
				array(
					'name' 					=>	 'required',
					'mobile_no' 			=>	 'required|integer|digits:10',
					'phone_number' 			=>	 'integer|digits:10',
					'pincode' 				=>	 'required|numeric',
					'email' 				=>	 'required|email',
					//'address' 				=>	 'required',
					'city' 					=>	 'required',
					'state' 				=>	 'required',
					'next_follow_up_date' 	=>	 'required',
					'remarks' 				=>	 'required',
					'enquiry_date' 			=>	 'required',	
					'contact_person' 		=>	 'required',	
					
									
				),
				array
				(

					"enquiry_date.required"				=>	trans("The enquiry date field is required."),
					"email.required"					=>	trans("The email address field is required."),
					"email.unique"						=>	trans("This email address is already exist."),
					"mobile_no.required"				=>	trans("The mobile number field is required."),
					"mobile_no.integer"					=>	trans("The mobile number must have a numeric value."),
					"mobile_no.numeric"					=>	trans("The mobile number must be a number."),
					"mobile_no.digits"					=>	trans("The mobile number must have 10 digits."),
					
				)
			);
			//print_r($thisData);die;
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				DB::beginTransaction();
				
			$dealer1 						= 	DealerEnquiry::find($userId);
			$dealer1->full_name   			= 	Input::get('name');
			$dealer1->phone_number   		= 	Input::get('mobile_no');
			$dealer1->address   			= 	Input::get('address');
			$dealer1->telephone   			= 	Input::get('phone_number');
			$dealer1->pincode   			= 	Input::get('pincode');
			$dealer1->contact_person   		= 	Input::get('contact_person');
			$dealer1->city   				= 	Input::get('city');
			$dealer1->state_id   			= 	Input::get('state');
			$dealer1->sales_person_id 		= 	Input::get('sales_person');
			$dealer1->email 				= 	Input::get('email');
			$dealer1->next_follow_up_date	=	!empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
			$dealer1->remarks				=	Input::get('remarks');
			$dealer1->enquiry_date			=	!empty(Input::get('enquiry_date')) ? date('Y-m-d',strtotime(Input::get('enquiry_date'))) : '0000-00-00';
			$dealer1->remarks				=	Input::get('remarks');
			$dealer1->created_at			=  	date("Y-m-d H:i:s");
			$dealer1->updated_at			=  	date("Y-m-d H:i:s");
			$dealer1->save();
			DB::commit();
			Session::flash('flash_notice', trans("Dealer Enquiry has been updated successfully")); 
			return Redirect::to('adminpnlx/dealer-enquiry');
			}
		}
	}// end dealerEnquiryUpdate()



	/**
	* Function for delete  dealer Enquiry 
	*
	* @param user_id
	*
	* @return view page. 
	*/
	public function deleteEnquiry($userId = 0){
		$userDetails	=	DealerEnquiry::find($userId); 
		if(empty($userDetails)) {
			return Redirect::back();
		}
		if($userId){	
			$email 						=	'delete_'.$userId .'_'.$userDetails->email;
			$userModel					=	DealerEnquiry::where('id',$userId)->update(array('is_deleted'=>1,'email'=>$email,'deleted_at'=>date("Y-m-d H:i:s")));
			Session::flash('flash_notice',trans("Dealer Enquiry deleted successfully")); 
		}
		return Redirect::back();
	} // end deleteEnquiry()
	

	/**
	* Function for view  dealer Enquiry 
	*
	* @param enquiry_id
	*
	* @return view page. 
	*/

	public function viewDealerEnquiry($enquiry_id=""){
		$enquiryDetails	 =	DealerEnquiry::where('id',$enquiry_id)->select('dealer_enquiry.*', DB::raw("(SELECT name FROM cities WHERE id = dealer_enquiry.city) as city"))->first(); 
		if(empty($enquiryDetails)) {
			return Redirect::back();
		}else{
			
			$followUpDetails = DB::table('dealer_enquiry_follow_up')
								 ->leftJoin('users','users.id','=','dealer_enquiry_follow_up.user_id')
								 ->select('dealer_enquiry_follow_up.*','users.full_name as fullname')
								->where('dealer_enquiry_follow_up.dealer_enquiry_id',$enquiry_id)->get();
			
			return View::make('admin.'.$this->model.'.view', compact("enquiryDetails","followUpDetails"));
		}
	}// end function viewDealerEnquiry()

	/**
	* Function for addFollowUp  dealer Enquiry 
	*
	* @param null
	*
	* @return view page. 
	*/


	public function addFollowUp(){
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator	 =	 Validator::make(
				$thisData,
				array(
					'enquiry_id' 				=>	 'required',
					'next_follow_up_date' 		=>	 'required',
					'detail' 					=>	 'required',
					),
				array(
					"detail.required"					=>	trans("The remarks field is required"),
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
				$followUpObj 						= new DealerEnquiryFollowUp;
				$followUpObj->user_id 				= Auth::user()->id;
				$followUpObj->dealer_enquiry_id 			= Input::get('enquiry_id');
				$followUpObj->next_follow_up_date   = !empty(Input::get('next_follow_up_date')) ? date('Y-m-d',strtotime(Input::get('next_follow_up_date'))) : '0000-00-00';
				$followUpObj->detail 				= Input::get('detail');
				$followUpObj->save();
				DealerEnquiry::where('id',Input::get('enquiry_id'))->update(array('next_follow_up_date'=>Input::get('next_follow_up_date')));
				
				$response				=	array(
					'success' 			=> 	1,
					'errors' 			=> 	'',
				);
				Session::flash('flash_notice',trans("Follow up added successfully.")); 
				return Response::json($response); 
				die;
			}
		}
	}// end function addFollowUp()

} //end DealerEnquiryController()
