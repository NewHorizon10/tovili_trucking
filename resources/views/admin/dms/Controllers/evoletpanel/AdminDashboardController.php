<?php
namespace App\Http\Controllers\adminpnlx;
use App\Http\Controllers\BaseController;
use App\Model\AdminUser;
use App\Model\User;
use Carbon\Carbon;
use mjanssen\BreadcrumbsBundle\Breadcrumbs as Breadcrumb;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;
/**
* AdminDashBoard Controller
*
* Add your methods in the class below
*
* This file will render views\admin\dashboard
*/
	class AdminDashboardController extends BaseController {
/**
* Function for display adminpnlx dashboard
*
* @param null
*
* @return view page. 
*/
	public function showdashboard(){
		//$this->checkPath();	
		$customerCount 						=DB::table('users')
											->where('user_role_id','=',CUSTOMER_ROLE_ID)
											->where('dealer_id','!=','')
											->where('is_deleted',0)
											->count();

		$bookingCount 						=DB::table('advance_booking')
											->where('is_deleted',0)
											->count();

		$enquiriesCount 					=DB::table('enquiries')
											->where('is_deleted',0)
											->count();

		//User Graph Data
		$month							=	date('m');
		$year							=	date('Y');
		for ($i = 0; $i < 6; $i++) {
			$months[] 					=	date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
		}
		$months							=	array_reverse($months);
		$num							=	0;
		$allUsers						=	array();
		//Active Users
		$thisMothUsers					=	0;
		foreach($months as $month){
			$month_start_date			=	date('Y-m-01 00:00:00', strtotime($month));
			$month_end_date				=	date('Y-m-t 23:59:59', strtotime($month));
			$allUsers[$num]['month']	=	$month;
			$allUsers[$num]['users']	=	DB::table('users')->where('created_at','>=',$month_start_date)->where('created_at','<=',$month_end_date)->where('is_deleted','!=',1)->where('user_role_id','=',DEALER_ROLE_ID)->count();
			if($month_start_date == date( 'Y-m-01 00:00:00', strtotime( 'first day of ' . date( 'F Y')))){
				$thisMothUsers	=	$allUsers[$num]['users'];
			}
			$num ++;
		}
		
		
		
		$months = array();
		$month							=	date('m');
		$year							=	date('Y');
		for ($i = 0; $i < 6; $i++) {
			$months[] 					=	date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
		}
		$months							=	array_reverse($months);
		$num1							=	0;
		$Enquiry						=	array();
		//Active Users
		$thisMothUsers					=	0;
		foreach($months as $month){
			$month_start_date			=	date('Y-m-01 00:00:00', strtotime($month));
			$month_end_date				=	date('Y-m-t 23:59:59', strtotime($month));
			$Enquiry[$num1]['months']	=	$month;
			$Enquiry[$num1]['enquires']	=	DB::table('dealer_enquiry')->where('created_at','>=',$month_start_date)->where('created_at','<=',$month_end_date)->where('is_deleted','!=',1)->count();
			if($month_start_date == date( 'Y-m-01 00:00:00', strtotime( 'first day of ' . date( 'F Y')))){
				$thisMothUsers	=	$Enquiry[$num1]['enquires'];
			}
			$num1 ++;
		}
		//print_r($Enquiry);die;						
		return  View::make('admin.dashboard.dashboard',compact('customerCount','bookingCount','enquiriesCount','allUsers','Enquiry'));
	}
/**
* Function for display admin account detail
*
* @param null
*
* @return view page. 
*/
	public function myaccount(){
		return  View::make('admin.dashboard.myaccount');
	}// end myaccount()
/**
* Function for change_password
*
* @param null
*
* @return view page. 
*/	
	public function change_password(){
		return  View::make('admin.dashboard.change_password');
	}// end myaccount()
/**
* Function for update admin account update
*
* @param null
*
* @return redirect page. 
*/
	public function myaccountUpdate(){
		$thisData				=	Input::all(); 
		Input::replace($this->arrayStripTags($thisData));
		$ValidationRule = array(
            'full_name' 		=> 'required',
            'email' 			=> 'required|email',
        );
        $validator 				= 	Validator::make(Input::all(), $ValidationRule);
		if ($validator->fails()){	
			return Redirect::to('adminpnlx/myaccount')
				->withErrors($validator)->withInput();
		}else{
			$user 				= 	User::find(Auth::user()->id);
			$user->full_name 	= 	Input::get('full_name'); 
			$user->email	 	= 	Input::get('email');
			if($user->save()) {
				return Redirect::intended('adminpnlx/myaccount')
					->with('success', 'Information updated successfully.');
			}
		}
	}// end myaccountUpdate()
/**
* Function for changedPassword
*
* @param null
*
* @return redirect page. 
*/	
	public function changedPassword(){
		$thisData				=	Input::all(); 
		Input::replace($this->arrayStripTags($thisData));
		$old_password    		= 	Input::get('old_password');
        $password         		= 	Input::get('new_password');
        $confirm_password 		= 	Input::get('confirm_password');
		Validator::extend('custom_password', function($attribute, $value, $parameters) {
			if (preg_match('#[0-9]#', $value) && preg_match('#[a-zA-Z]#', $value) && preg_match('#[\W]#', $value)) {
				return true;
			} else {
				return false;
			}
		});
		$rules        		  	= 	array(
			'old_password' 		=>	'required',
			'new_password'		=>	'required|min:8|custom_password',
			'confirm_password'  =>	'required|same:new_password'
		);
		$validator 				= 	Validator::make(Input::all(), $rules,
		array(
			"new_password.custom_password"	=>	"Password must have combination of numeric, alphabet and special characters.",
		));
		if ($validator->fails()){
			return Redirect::to('adminpnlx/change-password')
				->withErrors($validator)->withInput();
		}else{
			$user 				= User::find(Auth::user()->id);
			$old_password 		= Input::get('old_password'); 
			$password 			= Input::get('new_password');
			$confirm_password 	= Input::get('confirm_password');
			if($old_password !=''){
				if(!Hash::check($old_password, $user->getAuthPassword())){
					/* return Redirect::intended('change-password')
						->with('error', 'Your old password is incorrect.');
						 */
					Session::flash('error',trans("Your old password is incorrect."));
					return Redirect::to('adminpnlx/change-password');
				}
			}
			if(!empty($old_password) && !empty($password ) && !empty($confirm_password )){
				if(Hash::check($old_password, $user->getAuthPassword())){
					$user->password = Hash::make($password);
				// save the new password
					if($user->save()) {
						Session::flash('success',trans("Password changed successfully."));
						return Redirect::to('adminpnlx/change-password');
					}
				} else {
					/* return Redirect::intended('change-password')
						->with('error', 'Your old password is incorrect.'); */
					Session::flash('error',trans("Your old password is incorrect."));
					return Redirect::to('adminpnlx/change-password');
				}
			}else{
				$user->username = $username;
				if($user->save()) {
					Session::flash('success',trans("Password changed successfully."));
					return Redirect::to('adminpnlx/change-password');
					/* return Redirect::intended('change-password')
						->with('success', 'Password changed successfully.'); */
				}
			}
		}
	}// end myaccountUpdate()
/* 
* For User Listing Demo 
*/
	public function usersListing(){
		return View::make('admin.user.user');
	}
} //end AdminDashBoardController()
