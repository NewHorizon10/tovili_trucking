<?php
namespace App\Http\Controllers;
use App\Model\EmailAction;
use App\Model\EmailTemplate;
use App\Model\User;
use App\Model\DropDown;
use App\Model\Leads;
use App\Model\DealerLocation;
use App\Model\LeadLogs;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator,Str,App,Route;

/**
* Base Controller
*
* Add your methods in the class below
*
* This is the base controller called everytime on every request
*/
class BaseController extends Controller {
	
	protected $user;
	
	public function __construct() {
		Session::put('exchange_login',0);  
		$this->middleware(function ($request, $next){
			
			if(!empty(Auth::user()) && (Auth::user()->is_active ==0 || Auth::user()->is_deleted ==1)){
				$user_role_id	=	Auth::user()->user_role_id;
				Auth::logout();
				if($user_role_id ==1){
					return Redirect::to('/adminpnlx');
				}else {
					return Redirect::to('/dealerpanel');
				}
				
			}
			return $next($request);
		});
	}// end function __construct()
	
/**
* Setup the layout used by the controller.
*
* @return layout
*/
	protected function setupLayout(){
		if(Request::segment(1) != 'admin'){
			
		}
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}//end setupLayout()
	
/** 
* Function to make slug according model from any certain field
*
* @param title     as value of field
* @param modelName as section model name
* @param limit 	as limit of characters
* 
* @return string
*/	
	public function getSlug($title, $fieldName,$modelName,$limit = 30){
		$slug 		= 	 substr(Str::slug($title),0 ,$limit);
		$Model		=	 "\App\Model\\$modelName";
		$slugCount 	=    count($Model::where($fieldName, 'regexp', "/^{$slug}(-[0-9]*)?$/i")->get());
		return ($slugCount > 0) ? $slug."-".$slugCount : $slug;
	}//end getSlug()
/** 
* Function to make slug without model name from any certain field
*
* @param title     as value of field
* @param tableName as table name
* @param limit 	as limit of characters
* 
* @return string
*/	
	public function getSlugWithoutModel($title, $fieldName='' ,$tableName,$limit = 30){ 	
		$slug 		=	substr(Str::slug($title),0 ,$limit);
		$slug 		=	Str::slug($title);
		$DB 		= 	DB::table($tableName);
		$slugCount 	= 	count( $DB->whereRaw("$fieldName REGEXP '^{$slug}(-[0-9]*)?$'")->get() );
		return ($slugCount > 0) ? $slug."-".$slugCount: $slug;
	}//end getSlugWithoutModel()

/** 
* Function to search result in database
*
* @param data  as form data array
*
* @return query string
*/		
	public function search($data){
		unset($data['display']);
		unset($data['_token']);
		$ret	=	'';
		if(!empty($data )){
			foreach($data as $fieldName => $fieldValue){
				$ret	.=	"where('$fieldName', 'LIKE',  '%' . $fieldValue . '%')";
			}
			return $ret;
		}
	}//end search()
/** 
* Function to send email form website
*
* @param string $to            as to address
* @param string $fullName      as full name of receiver
* @param string $subject       as subject
* @param string $messageBody   as message body
*
* @return void
*/
	public function sendMail($to,$fullName,$subject,$messageBody, $from = '',$files = false,$path='',$attachmentName='') {
		
		/* $url 			= 	'https://api.sendgrid.com/v3/mail/send';
		$access_token	=	"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";
		
		$view = View::make('emails.template',compact("messageBody"));
		$messageBody = $view->render();
		
		$json_string 		=	array(
			'personalizations' => array(
			
				array("to"	=>	array(
					array("email"=>$to)
				))
				),
				"from"	=>	array(
					"email"=>(!empty($from) ? $from : Config::get("Site.email"))
				),
				"content"	=>	array(
					array("type"=>"text/html",
					"value"=>$messageBody)
				),
				"subject"=> $subject
		);
		$s 		= 	curl_init($url); 
		curl_setopt($s, CURLOPT_POST, 1);
		curl_setopt($s, CURLOPT_POSTFIELDS, json_encode($json_string));
		curl_setopt($s,CURLOPT_HTTPHEADER,array('Content-Type:application/json','Authorization: Bearer '.$access_token));
		curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
		# Get the response
		$result = curl_exec($s); 
		
		
		$data				=	array();
		$data['to']			=	$to;
		$data['from']		=	(!empty($from) ? $from : Config::get("Site.email"));
		$data['fullName']	=	$fullName;
		$data['subject']	=	$subject;
		$data['parents_email']	=	$parents_email; */
		/* 
		if(!empty($data['parents_email'])){
			Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
				$message->to($data['to'], $data['fullName'])->from($data['from'],Config::get("Site.title"))->subject($data['subject'])->bcc($data['parents_email']);
			});
		}else{
			Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
				$message->to($data['to'], $data['fullName'])->from($data['from'],Config::get("Site.title"))->subject($data['subject']);
			});
		}
		 */
		$data				=	array();
		$data['to']			=	$to;
		$data['from']		=	(!empty($from) ? $from : Config::get("Site.email"));
		$data['fullName']	=	$fullName;
		$data['subject']	=	$subject;
		$data['filepath']	=	$path;
		$data['attachmentName']	=	$attachmentName;
		if($files===false){
			Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
				$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);

			});
		}else{
			if($attachmentName!=''){
				Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
					$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'],array('as'=>$data['attachmentName']));
				});
			}else{
				Mail::send('emails.template', array('messageBody'=> $messageBody), function($message) use ($data){
					$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
				});
			}
		}
		DB::table('email_logs')->insert(
			array(
				'email_to'	 => $data['to'],
				'email_from' => $data['from'],
				'subject'	 => $data['subject'],
				'message'	 =>	$messageBody,
				'created_at' => DB::raw('NOW()')
			)
		); 
	}
	
	public  function arrayStripTags($array){
		$result			=	array();
		foreach ($array as $key => $value) {
			// Don't allow tags on key either, maybe useful for dynamic forms.
			$key = strip_tags($key,ALLOWED_TAGS_XSS);
	 
			// If the value is an array, we will just recurse back into the
			// function to keep stripping the tags out of the array,
			// otherwise we will set the stripped value.
			if (is_array($value)) {
				$result[$key] = $this->arrayStripTags($value);
			} else {
				// I am using strip_tags(), you may use htmlentities(),
				// also I am doing trim() here, you may remove it, if you wish.
				$result[$key] = trim(strip_tags($value,ALLOWED_TAGS_XSS));
			}
		}
		
		return $result;
		
	}
	
	
	public function buildTree($parentId = 0){
		$user_id	    =	Auth::User()->id;
		$user_role_id	=	Auth::User()->user_role_id;
		$branch         =   array();
		$elements       =   array();
		//$elements = DB::table("admin_modules")->where("parent_id",$parentId)->get(); 
		
		if($user_role_id == 1 || $user_role_id == 6){
			$type = 1;
		}else{
			$type = 0;
		}
		if($user_id == 1){
			$elements = DB::table("admin_modules")
							->where("parent_id",$parentId)
							->where("type",1)
							->orderBy('admin_modules.module_order','ASC')
							->get(); 
		}else {
			if($parentId == 0){
				$elements = DB::table("admin_modules")
							->where("parent_id",$parentId)
							->where("type",$type)
							->where("admin_modules.id",DB::raw("(select admin_module_id from user_permissions where user_permissions.admin_module_id = admin_modules.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
							->orderBy('admin_modules.module_order','ASC')
							->get(); 
			}else{ 
				$elements = 	DB::table("admin_modules")
								->where("parent_id",$parentId)
								->where("type",$type)
								->where("admin_modules.id",DB::raw("(select admin_sub_module_id from user_permission_actions where user_permission_actions.admin_sub_module_id = admin_modules.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
								->orderBy('admin_modules.module_order','ASC')
								->get();  
			}
		}
		
		foreach($elements as $element){
			if ($element->parent_id == $parentId){
				$children = $this->buildTree($element->id);
				if ($children){
					$element->children = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}
	
	
	public function checkPermission(){
		if(Auth::check()){
			$user_id				=	Auth::user()->id;
			$user_role_id			=	Auth::user()->user_role_id;
			$path					=	Request::path();
			$action					=	Route::current()->getAction();
			$function				=	"";
			$controller				=	"";
			
			if($user_role_id ==1){
				$userPanel 			=	EVOLET_PANEL;
			}else {
				$userPanel			=	DEALER_PANEL;
			}
			
			$controllersPath 		=	'App\\Http\\Controllers'.'\\'.$userPanel;
			
				if (array_key_exists('controller', $action))
				{ 
					$array = explode($controllersPath, $action['controller']);
					$value = isset($array[1])?$array[1]:0;	
					if(!empty($value)){
						$array = explode('@', $value); 
						$controller = isset($array[0])?trim($array[0],'"\"'):0; 
						$function = isset($array[1])?$array[1]:0;  					
					}
				}
				$path_array 	 		=	explode('/',$path);
				$moduleMainPath			=	"";
				if(!empty($path_array)){
					/* echo "<pre>";
					print_r($path_array);die; */
					$moduleMainPath			=	(isset($path_array[0])?$path_array[0]:'').'/'.(isset($path_array[1])?$path_array[1]:'');
				}
				
				$admin_module			=	DB::table("admin_modules")
											->where('path',$moduleMainPath)
											->first();							
				if($admin_module){
					$admin_module_id		=	$admin_module->id;
					$module_type_data		=	DB::table("admin_module_actions")
												->where('admin_module_id',$admin_module_id)
												//->where('controller',$controller)
												//->where('action',$function)
												->first();
					//if($module_type_data){
						$action_type			=	$module_type_data->type;
						$permissionData			=	DB::table("user_permission_actions")
													->select("user_permission_actions.is_active","admin_module_actions.type")
													->leftJoin("admin_module_actions","admin_module_actions.id","=","user_permission_actions.admin_module_action_id")
													->where('user_permission_actions.user_id',$user_id)
													->where('user_permission_actions.admin_sub_module_id',$admin_module_id)
													//->where('admin_module_actions.type',$action_type)
													->where('user_permission_actions.is_active',1)
													->first();							
						if($permissionData){
							$permissionActive 	=	$permissionData->is_active;
							if($permissionActive == 0 && $permissionActive == ""){
								return 0;
							}else{
								return 1;
							}
						}else{
							return 0;
						}
					//}
				}else{
					return 1;
				}
		}
	}
	
	public function saveCkeditorImages() {
		if(isset($_GET['CKEditorFuncNum'])){
			$image_url				=	"";
			$msg					=	"";
			// Will be returned empty if no problems
			$callback = ($_GET['CKEditorFuncNum']);        // Tells CKeditor which function you are executing
			$image_details 				= 	getimagesize($_FILES['upload']["tmp_name"]);
			$image_mime_type			=	(isset($image_details["mime"]) && !empty($image_details["mime"])) ? $image_details["mime"] : "";
			if($image_mime_type	==	'image/jpeg' || $image_mime_type == 'image/jpg' || $image_mime_type == 'image/gif' || $image_mime_type == 'image/png'){
				$ext					=	$this->getExtension($_FILES['upload']['name']);
				$fileName				=	"ck_editor_".time().".".$ext;
				$upload_path			=	CK_EDITOR_ROOT_PATH;
				if(move_uploaded_file($_FILES['upload']['tmp_name'],$upload_path.$fileName)){
					$image_url 			= 	CK_EDITOR_URL. $fileName;    
				}
			}else{
				$msg =  'error : Please select a valid image. valid extension are jpeg, jpg, gif, png';
			}
			$output = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$callback.', "'.$image_url .'","'.$msg.'");</script>';
			echo $output;
			exit;
		}
	}
	
	function getExtension($str) {
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		$ext = strtolower($ext);
		return $ext;
	}
	
/** 
 * Function to _update_all_status
 *
 * param source tableName,id,status,fieldName
 */	
	public function _update_all_status($tableName = null,$id = 0,$status= 0,$fieldName = 'is_active'){
		DB::beginTransaction();
		$response			=	DB::statement("CALL UpdateAllTableStatus('$tableName',$id,$status)");
		if(!$response) {
			DB::rollback();
			Session::flash('error', trans("messages.msg.error.something_went_wrong")); 
			return Redirect::back();
		}
		DB::commit();
	}
		
/** 
 * Function to _delete_table_entry
 *
 * param source tableName,id,fieldName
 */
	public function _delete_table_entry($tableName = null,$id = 0,$fieldName = null){
		DB::beginTransaction();
		$response			=	DB::statement("CALL DeleteAllTableDataById('$tableName',$id,'$fieldName')");
		if(!$response) {
			DB::rollback();
			Session::flash('error', trans("messages.msg.error.something_went_wrong")); 
			return Redirect::back();
		}
		DB::commit();
	}// end _delete_table_entry()
	
	public function encrypt($data = ""){
		$password	=	CBC_ENCRYPT_KEY;
		$method		=	'aes-256-cbc';
		$iv			=	CBC_ENCRYPT_IV;

		// Must be exact 32 chars (256 bit)
		$password = substr(hash('sha256', $password, true), 0, 32);
		// IV must be exact 16 chars (128 bit)
		
		$encrypted = base64_encode(openssl_encrypt(json_encode($data), $method, $password, OPENSSL_RAW_DATA, $iv));
		return $encrypted;	
	}
		
	public function decrypt($data = ""){
		$password	=	CBC_ENCRYPT_KEY;
		$method		=	'aes-256-cbc';
		$iv			=	CBC_ENCRYPT_IV;
		// Must be exact 32 chars (256 bit)
		$password = substr(hash('sha256', $password, true), 0, 32);
		// IV must be exact 16 chars (128 bit)
		//print_r($data);die;
		$decrypted = openssl_decrypt(base64_decode($data), $method, $password, OPENSSL_RAW_DATA, $iv);
		//print_r($decrypted);die;
		return json_decode($decrypted,true);
	}
	/**
	 * Function for get list or drop down by slug
	 * 
	 * @param $slug
	 * 
	 * @retun List of dropdown 
	 *
	 * */
	
	public function getDropDownListBySlug($slug = ''){
		$list = DropDown::where('dropdown_type',$slug)
							->where("is_active",1)
							->orderBy('name', 'ASC')
							->pluck('name','id');
							
		return  $list;	 
	}

	/**
	 * Function for convert number into words
	 * 
	 * @param $number
	 * 
	 * @retun 
	 *
	 * */
	
	function convert_number_into_words($number) {
    
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);
	
		if (!is_numeric($number)) {
			return false;
		}
	
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}
	
		if ($number < 0) {
			return $negative . $this->convert_number_into_words(abs($number));
		}
	
		$string = $fraction = null;
	
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
	
		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . $this->convert_number_into_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = $this->convert_number_into_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= $this->convert_number_into_words($remainder);
				}
				break;
		}
	
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
	
		return ucwords($string);
		}
		
	public function get_dealer_list(){
		$DB 					= 	User::query();
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if($assignedDealer){
				$DB->whereIn('id', $assignedDealer);
			}
		}
		$dealer_list  			=	$DB->where('user_role_id',DEALER_ROLE_ID)
											->where('is_active',1)
											->where('is_deleted',0)
											->orderBy('full_name','ASC')
											->pluck('full_name','id')->toArray(); 
		
		return $dealer_list;
	}
	public function get_dealer_location_code_list(){
		$DB 					= 	DealerLocation::query();
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if($assignedDealer){
				$DB->whereIn('dealer_id', $assignedDealer);
			}
		}
		$location_code_list  			=	$DB->where('is_active',1)
										->where('is_deleted',0)
										->orderBy('location_code','ASC')
										->pluck('location_code','id')->toArray();
		
		return $location_code_list;
	}
	
	public function get_dealer_location_name_list(){
		$DB 					= 	DealerLocation::query();
		if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
			$assignedDealer	=	DB::table('assign_dealer_staff')->where('staff_id', Auth::user()->id)->pluck('dealer_id')->toArray();
			if($assignedDealer){
				$DB->whereIn('dealer_id', $assignedDealer);
			}
		}
		$location_name_list  			=	$DB->where('is_active',1)
										->where('is_deleted',0)
										->orderBy('location_name','ASC')
										->pluck('location_name','id')->toArray();
		
		return $location_name_list;
	}

	public function get_dealer_id(){
		if(Auth::user()->user_role_id == STAFF_USER_ROLE_ID){
			$dealer_id	=	Auth::user()->dealer_id;
		}elseif(Auth::user()->user_role_id == DEALER_ROLE_ID){
			$dealer_id	=	Auth::user()->id;
		}else{
			$dealer_id	=	0;
		}
		return $dealer_id;
	}
	
	public function getSerialNumber($num){

	    $number = $num;
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if (($number %100) >= 11 && ($number%100) <= 13)
		   $abbreviation = $number. 'th';
		else
		   $abbreviation = $number. $ends[$number % 10];
		
		return $abbreviation.' Service';
											
	}// End getSerialNumber
	
	public function send_push_notification($deviceToken = "",$device_type = "",$message = "",$notification_type = "",$data = array(),$notification_title = ""){
		$registrationIds  = "";
		if($device_type == "android"){
			$server_key		=	Config::get("Site.android_sever_api_key");
			$registrationIds 	= array($deviceToken); 
			$msg = array (
				'title'				=> $notification_title, 
				'body'			=> $message,
				"data"=>array('priority'=> "high")
				//'vibrate'			=> 1,
				//'sound'				=> 1, 
				//'response_data'		=> base64_encode(json_encode($data)), 
				//'notification_type'	=> $notification_type, 
			); 
			$fields = array (
				'to'	=>	 $deviceToken,
				'notification'	=>	 $msg,
				'priority'	=>	 "high"
			);
			/* echo  "<pre>";
			print_r(json_encode($fields));die; */
			$headers = array (
				'Authorization: key=' . $server_key,
				'Content-Type: application/json'
			);
			$ch = curl_init(); 
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields) );
			$result = curl_exec($ch);  
			curl_close( $ch );
		}else{
			$server_key	=	Config::get("Site.android_sever_api_key");
			$ch = curl_init("https://fcm.googleapis.com/fcm/send");
			$title = Config::get('Site.title');
			$notification = array('title' =>$notification_title,'text' => $message);
			//This array contains, the token and the notification. The 'to' attribute stores the token.
			$arrayToSend = array('to' => $deviceToken, 'notification' => $notification,'priority'=>'high');
			//Generating JSON encoded string form the above array.
			$json = json_encode($arrayToSend);
			//Setup headers:
			$headers = array();
			$headers[] = 'Content-Type: application/json';
			$headers[] = "Authorization: key= $server_key"; // key here
			//Setup curl, add headers and post parameters.
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
			//Send the request
			$response = curl_exec($ch);     
			//prd($response);
			//Close request
			curl_close($ch);
			return json_encode($response);
		}
	}

   public function AddleadLogs($lead_id=0,$user_id=0,$data_string,$action=''){
   	     $lead              = new LeadLogs;
		 if($user_id != ADMIN_ID){
			$lead->dealer_id   = $this->get_dealer_id();
		 }else{
			$lead->dealer_id   = 0; 
		 }
         $lead->user_id     = $user_id;
         $lead->lead_id     = $lead_id;
         $lead->action_name = $action;
         $lead->data_string = $data_string;
         $lead->save();
   }
  
   public function imageUpload($fileName,$path,$image_name){
        $newFolder     	  =	strtoupper(date('M'). date('Y'));
		$folderPath	      =	$path.$newFolder; 
	    if (!File::exists($folderPath)) {
		  File::makeDirectory($folderPath, $mode = 0777, true);
	    }
        $file             = $fileName;
		$time 		      = rand(000,999).time();
		$destinationPath  = $folderPath; 
		$extension        = $file->getClientOriginalExtension();
		$fileName         = $time.$image_name.'.'.$extension;
		$file->move($destinationPath, $fileName);
		return $newFolder."/".$fileName;
   }

   public function fileSizeReadable($bytes){
      $i = floor(log($bytes) / log(1024));
      $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
      return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
   }

	public function getLeadList(){
		if(Auth::user()->user_role_id==STAFF_USER_ROLE_ID){
			$leadList=Leads::where(['is_deleted'=>0])->where('leads.sales_person_assigned',Auth::user()->id)->pluck('lead_num','id');
		}else{
			$dealer_id				=	$this->get_dealer_id();
			$leadList=Leads::where(['is_deleted'=>0])->where('leads.dealer_id',$dealer_id)->pluck('lead_num','id');
		}
		return $leadList;
	}
	
	public function change_error_msg_layout($errors = array()){
		$response				=	array();
		$response["status"]		=	"error";
		if(!empty($errors)){
			$error_msg				=	"";
			foreach($errors as $errormsg){
				$error_msg1			=	(!empty($errormsg[0])) ? $errormsg[0] : "";
				$error_msg			.=	$error_msg1.", ";
			}
			$response["msg"]	=	trim($error_msg,", ");			
		}else {
			$response["msg"]	=	"";			
		}
		$response["data"]			=	(object)array();
		$response["errors"]			=	$errors;
		return $response;
	}
	
	public function check_section_permission($data = array()){
		$response = '';
		
		if(isset($data['section']) && !empty($data['section'])){
			$section = $data['section'];
			if($section == "item"){
				if(Auth::user()->user_role_id == ADMIN_ID){
					$response = 1;
				}else if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->department == PURCHASE_DEPARTMENT){
					$response = 1;
				}
			}
			if($section == "purchase_reqisition"){
				if(Auth::user()->user_role_id == ADMIN_ID){
					$response = 1;
				}else if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->department == PURCHASE_DEPARTMENT){
					$response = 1;
				}
			}
			if($section == "commodity_category"){
				if(Auth::user()->user_role_id == ADMIN_ID){
					$response = 1;
				}else if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->department == PURCHASE_DEPARTMENT){
					$response = 1;
				}
			}
			if($section == "item_category"){
				if(Auth::user()->user_role_id == ADMIN_ID){
					$response = 1;
				}else if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->department == PURCHASE_DEPARTMENT){
					$response = 1;
				}
			}
			if($section == "purchase_order"){
				if(Auth::user()->user_role_id == ADMIN_ID){
					$response = 1;
				}else if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && (Auth::user()->department == PURCHASE_DEPARTMENT || Auth::user()->department == ACCOUNTS_DEPARTMENT)){
					$response = 1;
				}
			}
			
			
		}
		return $response;
	}
	
	public function check_entry_allow_view($data = array()){
		$response = '';
		if(isset($data['section']) && !empty($data['section'])){
			$section = $data['section'];
			if($section == "item"){
				if(Auth::user()->user_role_id == ADMIN_ID){
					$response = 1;
				}else if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID && Auth::user()->department == PURCHASE_DEPARTMENT){
					$id = $data['id'];
					$created_by = DB::table('items')->where('id',$id)->select("created_by")->first();
					if(Auth::user()->designation == PURCHASE_HOD || $created_by->created_by == Auth::user()->id){
						$response = 1;
					}
				}
			}
			if($section == "purchase_reqisition"){
				
				if(Auth::user()->user_role_id == ADMIN_ID){
					$response = 1;
				}else if(Auth::user()->user_role_id == ADMIN_STAFF_ROLE_ID){
					$id = $data['id'];
					
					$created_by = DB::table('purchase_requisitions')->where('id',$id)->select("created_by")->first();
					if(Auth::user()->designation == PURCHASE_HOD || $created_by->created_by == Auth::user()->id){
						$response = 1;
					}
				}
			}
		}
		return $response;
	}

	
}// end BaseController class
