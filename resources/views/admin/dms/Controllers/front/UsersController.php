<?php
/**
 * user Controller
 */
namespace App\Http\Controllers\front;
use App\Http\Controllers\BaseController;
use App\Model\User;
use App\Model\EmailAction;
use App\Model\EmailTemplate;
use App,Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator;

class UsersController extends BaseController {
/** 
* Function to display website home page
*
* @param null
* 
* @return view page
*/
	public function index(){
		return View::make('front.user.index');
	}

}// end usersController class
