<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\VendorsContact;
use App\Model\Vendors;

use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* WarehouseController Controller
*
* Add your methods in the class below
*
* This file will render views\VendorsController\dashboard
*/
	class VendorsController extends BaseController {
		
		public $model	=	'Vendors';

	public function __construct() {
		View::share('modelName',$this->model);
    }

	/**
	* Function for list  page
	*
	* @param null
	*
	* @return view page. 
	*/
    public function list(){
		$DB 					= 	Vendors::query();
		$state	 	=	DB::table('states')
						->where("status",1)
						->where("country_id",COUNTRY_ID)
						->pluck("name","id")->toArray();
		$searchVariable			=	array(); 
		$inputGet				=	Input::get();
		/* seacrching on the basis of name and warehouse_id */ 
			if ((Input::get())) {
				$searchData			=	Input::get();
				//echo'<pre>'; print_r($searchData); echo'</pre>'; die;
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
					$DB->where("vendors.".$fieldName,'like','%'.$fieldValue.'%');
					$searchVariable	=	array_merge($searchVariable,array($fieldName => $fieldValue));
				}
			}
			$DB->where('is_deleted',0);
			$sortBy 				= 	(Input::get('sortBy')) ? Input::get('sortBy') : 'created_at';
			$order  				= 	(Input::get('order')) ? Input::get('order')   : 'DESC';

			$result 				= 	$DB
									->orderBy($sortBy, $order)
									->paginate(Config::get("Reading.records_per_page"));
			
			// echo'<pre>'; print_r($result); echo'</pre>'; die;
									
			$complete_string		=	Input::query();
			unset($complete_string["sortBy"]);
			unset($complete_string["order"]);
			$query_string			=	http_build_query($complete_string);
			$result->appends(Input::all())->render();
			Session::put("_search_data",$inputGet);

			return  View::make('admin.'.$this->model.'.index', compact('result' ,'searchVariable','sortBy','order','query_string'));
		}

    /**
	* Function for add  page
	*
	* @param null
	*
	* @return view page. 
	*/
    public function add(){	
        $state=	DB::table('states')
				->where("status",1)
				->where("country_id",COUNTRY_ID)
				->pluck("name","id")->toArray();
        return View::make('admin.'.$this->model.'.add',compact('state'));
    }
    
    /**
	* Function for save 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function save(){
		Input::replace($this->arrayStripTags(Input::all()));
		$formData						=	Input::all();
		//echo'<pre>'; print_r($formData); echo'</pre>'; die;
		if(!empty($formData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					
					'company_name' 			=>	 'required',
                    'phone_number' 			=>	 'required|numeric|digits:10',
                    'state' 				=>	 'required',
                    'email' 				=>	 'required|email|unique:vendors,email',
                    'city' 				    =>	 'required',
                    //'pan_number'            =>   'required',
                    //'pin_code' 				=>	 'required|numeric',
                    'tin_number' 			=>	 'required',
                    'gst_number' 			=>	 'required',
                    'tan_number' 			=>	 'required',
                    //'service_tax_number' 	=>	 'required',
					'address' 				=>	 'required',
					// 'first_name' 			=>	 'required',
					// 'last_name' 			=>	 'required',
					// 'designation' 			=>	 'required',
					// 'contact_email' 		=>	 'required|unique:vendor_contacts,contact_email',
					// 'phone' 				=>	 'required',
					// 'mobile_number' 		=>	 'required',
				),
				array(
                    "company_name.required"					=>	trans("The company name field is required."),
                    "phone_number.required"					=>	trans("The phone number field is required."),
                    "phone_number.integer"					=>	trans("The phone number must be integer."),
                    "phone_number.digits"					=>	trans("The phone number must be 10 digits."),
					"state.required"						=>	trans("The state field is required."),
					"email.required"						=>	trans("The email field is required."),
					"email.unique"							=>	trans("The email must be unique."),
					"email.email"							=>	trans("The email must be a valid email address"),									
                    "city.required"						    =>	trans("The city field is required."),
                    //"pan_number.required"					=>	trans("The city field is required."),
                   // "pin_code.required"						=>	trans("The pin code field is required."),
                    //"pin_code.numeric"						=>	trans("The pin code must be numeric."),
                    "tin_number.required"					=> 	trans("The tin number field is required."),
                    "gst_number.required"					=> 	trans("The gst number field is required."),
                    "tan_number.required"					=> 	trans("The tan number field is required."),
                   // "service_tax_number.required"			=> 	trans("The service tax number field is required."),
					"address.required"						=> 	trans("The address field is required."),
					// "first_name.required"					=> 	trans("The first name field is required."),
					// "last_name.required"					=> 	trans("The last name field is required."),
					// "designation.required"					=> 	trans("The designation field is required."),
					// "contact_email.required"				=> 	trans("The contact email field is required."),
					// "contact_email.unique"					=>	trans("The contact email must be unique."),
					// "contact_email.email"					=>	trans("The contact email must be a valid email address"),		
					// "phone.required"						=> 	trans("The phone field is required."),
					// "moblie_number.required"				=> 	trans("The moblie number field is required."),
					)
			);
			if ($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();
				
			}else{ 
				$day 						=  date("my");
				$rand 						=  mt_rand(1000,9999);
				$value 						=  "#V".$day."".$rand;
				$vendors 					= 	new Vendors;
				$vendors->vendor_id			=	$value;							
				$vendors->company_name		=	Input::get('company_name');
				$vendors->phone_number		=	Input::get('phone_number');
				$vendors->email				=	Input::get('email');
				$vendors->pan_number		=	Input::get('pan_number');
				$vendors->tin_number		=	Input::get('tin_number');
				$vendors->address			=	Input::get('address')	;
				$vendors->country			=	Input::get('country');
				$vendors->state				=	Input::get('state');
				$vendors->city				=	Input::get('city');
				$vendors->pin_code			=	Input::get('pin_code');
				$vendors->gst_number		=	Input::get('gst_number');
				$vendors->tan_number		=	Input::get('tan_number');
				$vendors->service_tax_number=	Input::get('service_tax_number');
				$vendors->is_active			= 	1;
				$vendors->created_at		=  date("Y-m-d H:i:s");
				$vendors->save();
				$id = $vendors->id;
				$values = $value."".$id;
				$data = Vendors::find($id);
				$data->vendor_id = $values;
				$data->save();

				// if(!empty($formData)){
				// 	$model = new VendorsContact;
				// 	foreach($formData['first_name'] as $fieldName=>$fieldValue){
				// 		echo $fieldValue; 
				// 	}
				// 	die;
				// 	$model->designation = $fieldValue['designation'];
				// 	$model->contact_email = $fieldValue['contact_email'];
				// 	$model->phone = $fieldValue['phone'];
				// 	$model->mobile_number = $fieldValue['mobile_number'];
				// 	$model->save();
				// 	// }
				// }

				Session::flash("success",trans("Vendor added successfully."));
				return Redirect::to('adminpnlx/vendors');
				//return Redirect::back();
			}
		}
	}

	/**
	* Function for edit 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function edit($id = ""){
		$state=	DB::table('states')
				->where("status",1)
				->where("country_id",COUNTRY_ID)
				->pluck("name","id")->toArray();
		$Details	    =	DB::table('vendors')
								->where('vendors.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}	
		return View::make('admin.'.$this->model.'.edit', compact("Details","state"));
	} // end editUser()

	/**
	* Function for view 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function view($id=""){
		$Details	    =	DB::table('vendors')
								->where('vendors.id',$id)
								->first();

		// echo'<pre>'; print_r($Details); echo'</pre>'; die;
		if(empty($Details)) {
			return Redirect::back();
		}else{	
			return View::make('admin.'.$this->model.'.view', compact("Details"));
		}
	}

	/**
	* Function for update 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function update($id=""){
		Input::replace($this->arrayStripTags(Input::all()));
		$thisData					=	Input::all();
		if(!empty($thisData)){
			$validator 					=	Validator::make(
				Input::all(),
				array(
					
					'company_name' 			=>	 'required',
                    'phone_number' 			=>	 'required|numeric|digits:10',
                    'state' 				=>	 'required',
                    'email' 				=>	 "required|email|unique:vendors,email,$id",
                    'city' 				    =>	 'required',
                    //'pan_number'            =>   'required',
                   // 'pin_code' 				=>	 'required|numeric',
                    'tin_number' 			=>	 'required',
                    'gst_number' 			=>	 'required',
                    'tan_number' 			=>	 'required',
                   // 'service_tax_number' 	=>	 'required',
					'address' 				=>	 'required',
					// 'first_name' 			=>	 'required',
					// 'last_name' 			=>	 'required',
					// 'designation' 			=>	 'required',
					// 'contact_email' 		=>	 'required|unique:vendor_contacts,contact_email',
					// 'phone' 				=>	 'required',
					// 'mobile_number' 		=>	 'required',
				),
				array(
                    "company_name.required"					=>	trans("The company name field is required."),
                    "phone_number.required"					=>	trans("The phone number field is required."),
                    "phone_number.integer"					=>	trans("The phone number must be integer."),
                    "phone_number.digits"					=>	trans("The phone number must be 10 digits."),
					"state.required"						=>	trans("The state field is required."),
					"email.required"						=>	trans("The email field is required."),
					"email.unique"							=>	trans("The email must be unique."),
					"email.email"							=>	trans("The email must be a valid email address"),									
                    "city.required"						    =>	trans("The city field is required."),
                    //"pan_number.required"					=>	trans("The city field is required."),
                   // "pin_code.required"						=>	trans("The pin code field is required."),
                   // "pin_code.numeric"						=>	trans("The pin code must be numeric."),
                    "tin_number.required"					=> 	trans("The tin number field is required."),
                    "gst_number.required"					=> 	trans("The gst number field is required."),
                    "tan_number.required"					=> 	trans("The tan number field is required."),
                   // "service_tax_number.required"			=> 	trans("The service tax number field is required."),
					"address.required"						=> 	trans("The address field is required."),
					// "first_name.required"					=> 	trans("The first name field is required."),
					// "last_name.required"					=> 	trans("The last name field is required."),
					// "designation.required"					=> 	trans("The designation field is required."),
					// "contact_email.required"				=> 	trans("The contact email field is required."),
					// "contact_email.unique"					=>	trans("The contact email must be unique."),
					// "contact_email.email"					=>	trans("The contact email must be a valid email address"),		
					// "phone.required"						=> 	trans("The phone field is required."),
					// "moblie_number.required"				=> 	trans("The moblie number field is required."),
					)
			);
			if ($validator->fails()) {	
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				
				$vendors					= 	Vendors::find($id);
				$vendors->company_name		=	Input::get('company_name');
				$vendors->phone_number		=	Input::get('phone_number');
				$vendors->email				=	Input::get('email');
				$vendors->pan_number		=	Input::get('pan_number');
				$vendors->tin_number		=	Input::get('tin_number');
				$vendors->address			=	Input::get('address')	;
				$vendors->country			=	Input::get('country');
				$vendors->state				=	Input::get('state');
				$vendors->city				=	Input::get('city');
				$vendors->pin_code			=	Input::get('pin_code');
				$vendors->gst_number		=	Input::get('gst_number');
				$vendors->tan_number		=	Input::get('tan_number');
				$vendors->service_tax_number=	Input::get('service_tax_number');
				$vendors->is_active			= 	1;
				$vendors->updated_at		=  date("Y-m-d H:i:s");
				$vendors->save();
				Session::flash('flash_notice', trans("Vendors has been updated successfully.")); 
				return Redirect::to('adminpnlx/vendors');
			}
		}
	}

	/**
	* Function for delete 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function delete($id = ''){
		$Details			=	Vendors::find($id); 
		if(empty($Details)) {
			return Redirect::back();
		}
		if($id){	
			$userModel					=	Vendors::where('id',$id)->update(array('is_deleted'=>1));
			Session::flash('flash_notice',trans("Vendors has been deleted successfully.")); 
		}
		return Redirect::back();
	}


	/**
	* Function for updateStatus 
	*
	* @param null
	*
	* @return view page. 
	*/
	public function updateStatus($id = 0, $Status = 0){
		if($Status == 0){
			$statusMessage	=	trans("Vendors has been deactivated.");
			$staffDetails		=	Vendors::find($id); 
		}else{
			$statusMessage	=	trans("Vendors has been activated.");
		}
		$this->_update_all_status("vendors",$id,$Status);	
		Session::flash("flash_notice", $statusMessage); 
		return Redirect::back();
	} // end updateVendorsstatus()
}
?>