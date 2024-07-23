<?php

use App\Models\Acl;
use  App\Models\Department;
use  App\Models\Designation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Models\Language;
use App\Models\HomeUpdate;
use App\Models\HomeUpdateDescription;
use App\Models\AboutUpdate;
use App\Models\AboutUpdateDescription;
use App\Models\Lookup;
use App\Models\Setting;
use App\Models\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
/*Setting Name get Function*/

if(!function_exists('lookbycode'))
{
    function lookbycode($id=Null)
    {
      $lookVal='';
        if(!empty($id))
        {
            $lookVal='';
        $lookVal=Lookup::where('id','=',$id)->value('code');

        }
         return $lookVal; 
    } 
}
function get_other_data($pageid = null,$page_name = null){
  if($pageid == ""){
  $pageid = "/";
  }
  $blogs_seo = '';
  // if(DB::table('seo_pages')->where('page_id',$pageid)->where('is_deleted',0)->exists()){
    
  //   $blogs_seo = DB::table('seo_pages')->where('page_id',$pageid)->where('is_deleted',0)->first();
  // }
  return $blogs_seo;
}

if(!function_exists('AclParnentByName'))
{
    function AclparentByName($parentid=Null)
    {
      $parentidname='';
        if(!empty($parentid))
        {
        
        $parentidname=Acl::where('id',$parentid)->value('title');
        return $parentidname; 
        }
    } 
}

if(!function_exists('DepartmentbyName'))
{
    function DepartmentbyName($Departid=Null)
    {
      $Departmentname='';
        if(!empty($Departid))
        {
      
        $Departmentname=Department::where('id',$Departid)->value('name');
        return $Departmentname; 
        }
    } 
}

function getDefaultLangId()
{
  $lang_code =  session('default_language') ?? config('constants.DEFAULT_LANGUAGE.LANG_CODE') ?? config('app.fallback_locale') ?? app()->getLocale();
  return DB::table('languages')->where('lang_code', $lang_code)->value('id');
}

if(!function_exists('DesignationbyName'))
{
    function DesignationbyName($Desid=Null)
    {
        if(!empty($Desid))
        {
          $Desginationname='';
        $Desginationname=Designation::where('id',$Desid)->value('name');
        return $Desginationname; 
        }
    } 
}

function  addhttp($url = "") {
  if($url == ""){
    return "";
  }
  if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "http://" . $url;
  }
  return $url;
}

function getUserPermission(){
  $user_id				=	Auth::user()->id;
  $path					=	Request()->path(); 
  $admin_module_id		=	DB::table("acls")->where('path',$path)->pluck("id");
  
  $permissionData			=	DB::table("user_permission_actions")->leftJoin("acl_admin_actions","acl_admin_actions.id","=","user_permission_actions.admin_module_action_id")->where('user_permission_actions.user_id',$user_id)->where('user_permission_actions.admin_sub_module_id',$admin_module_id)->where('user_permission_actions.is_active',1)->where('acl_admin_actions.name','!=','List')->select("user_permission_actions.is_active","acl_admin_actions.type")->lists('acl_admin_actions.type','acl_admin_actions.type'); 
  
  return $permissionData;
}

  function sideBarNavigation($menus){
 
    $website_url  = Config('constants.WEBSITE_URL');
    $treeView	=	"";  
    $segment3	=	Request()->segment(2); 
    $segment4	=	Request()->segment(2); 
    $segment5	=	Request()->segment(3);  
    $segment6	=	Request()->segment(4);  
     
    if(!empty($menus)){

      $treeView	.=	"<ul class='menu-nav'>";
      foreach($menus as $record){
        $currentSection	=	"";
        $currentPlugin	=	"";
        $plugin			=	explode('/',$record->path); 
        $pluginSlug3	=	isset($plugin[1])?$plugin[1]:'';        
        $myArray		=	[];
        $myArray1		=	[];      
        if(!empty($record->children)){
          $plugin_array	=	"";
          $plugin_array1	=	"";
          foreach($record->children as $li_record){         
            $plugin			=	explode('/',$li_record->path); 
            $slug			=	isset($plugin[0])?$plugin[0]:''; 
            $slug1			=	isset($plugin[1])?$plugin[1]:'';
            $plugin_array 	.= 	"".$slug.",";
            $plugin_array1 	.= 	"".$slug1.",";
          }
          $myArray = explode(',', $plugin_array);
          $myArray1 = explode(',', $plugin_array1);
        }   
        $class = (in_array($segment3,$myArray1) && ($segment3 != '')) ? 'menu-item-open':''; #* 
      
        $classActive		=	($pluginSlug3 == $segment3)?"menu-item-active":'';
        $style = (in_array($segment3,$myArray1) && ($segment3 != '')) ? 'display:block;':'display:none;'; 
        $classActive1 = "";


        $path	=	((!empty($record->path) && ($record->path != 'javascript::void()') && ($record->path != 'javascript::void(0)') && ($record->path != 'javascript:void()') && ($record->path != 'javascript::void();') && ($record->path != 'javascript:void(0);'))?URL($record->path):'javascript:void(0)');

        $messageChatcount = "";
        if (strpos($path, 'admin-customer-support') !== false) {
          $messageChatcount = "<span class='badge badge-pill badge-primary chatClass'></span>";
        }
        $second_icon	=	((!empty($record->path) && ($record->path == 'javascript::void()') || ($record->path == 'javascript::void(0)') || ($record->path == 'javascript:void()') || ($record->path == 'javascript::void();') || ($record->path == 'javascript:void(0);'))?'menu-arrow':'');   


        if((!empty($record->path) && ($record->path != 'javascript::void()') && ($record->path != 'javascript::void(0)') )){     
          $pluginData			=	explode('/',$record->path);  
          $plugin				=	isset($pluginData[0])?$pluginData[0]:'';      
          $plugin1			=	isset($pluginData[1])?$pluginData[1]:'';
          $classActive1		=	((($plugin == $segment3 && ($plugin1 == "")) || ($plugin1 == $segment3) || ($plugin == $segment3 && ($plugin1 == "user-chat")))?'menu-item-active':'');
        }  
        $treeView .= "<li class='menu-item menu-item-submenu  ".(!empty($record->children)? 'menu-item-submenu '.$class:' ').' '.$classActive1."' aria-haspopup='true' data-menu-toggle='hover'><a href='".$path."' class='menu-link menu-toggle'>"."<span class='svg-icon menu-icon'>".$record->icon."</span><span class='menu-text'>$record->title</span><i class='".$second_icon."'>$messageChatcount</i></a>";

        if(!empty($record->children)){       
          $treeView	.= "<div class='menu-submenu'><i class='menu-arrow'></i><ul class='menu-subnav'><li class='menu-item menu-item-parent' aria-haspopup='true'>
          <span class='menu-link'>
            <span class='menu-text'>".$record->title."</span>
          </span>
          </li>";
        
          foreach($record->children as $li_record){
            
            $path	=	((!empty($li_record->path) && ($li_record->path != 'javascript::void()') && ($li_record->path != 'javascript::void(0)') && ($li_record->path != 'javascript:void()') && ($li_record->path != 'javascript:void(0);'))?URL($li_record->path):'javascript:void(0)');    
            $messageChatcount = "";
            if (strpos($path, 'admin-customer-support') !== false) {
              $messageChatcount = "<span class='badge badge-pill badge-primary chatClass'></span>";
            }        
            $second_icon	=	((!empty($li_record->path) && ($li_record->path == 'javascript::void()') || ($li_record->path == 'javascript::void(0)') || ($li_record->path == 'javascript:void()') || ($li_record->path == 'javascript::void();') || ($li_record->path == 'javascript:void(0);'))?'fa fa-angle-left pull-right':'');   
            $plugin			=	explode('/',$li_record->path); 
            $currentPlugin	=	isset($plugin[1])?$plugin[1]:'';
          
            $currentPlugin1	=	isset($plugin[2])?$plugin[2]:''; 
            
            $currentPlugin2	=	isset($plugin[3])?$plugin[3]:'';   
      
            $activeClass = "";
          
            if(  (!empty($segment5) && $segment5 == $currentPlugin1 && $segment5 =='Speaker' ) || (!empty($segment6) && $segment6 == $currentPlugin1 && $segment6=='Speaker' ) ){
              
              $activeClass =  "menu-item-active";
            }elseif( (!empty($segment5) && $segment5 == $currentPlugin1  && $segment5 =='Assistant' ) ||  (!empty($segment6) && $segment6 == $currentPlugin1 && $segment6=='Assistant' )){
              $activeClass =  "menu-item-active";
            }elseif( $segment4=='lookups-manager'){
              if(!empty($segment5) && $segment4=='lookups-manager' ){

                if (!empty($segment5) && $segment4 == 'lookups-manager' && $segment5 == $currentPlugin1) {
                    $activeClass = "menu-item-active";
                }

              }
            }elseif($segment4=='settings'){
             
                if( $currentPlugin2 == $segment6 ){
                  $activeClass =  "menu-item-active";

                }elseif( $currentPlugin2 == $segment6 ){
                  $activeClass =  "menu-item-active";
                  
                }elseif( $currentPlugin2 == $segment6 ){
                  $activeClass =  "menu-item-active";

                }
              
            }else{
              if( $currentPlugin == $segment4 && $segment4 !='settings' && $segment4!='lookups-manager' && $segment5 !='Speaker' && $segment6 !='Speaker' && $segment5 !='Assistant' && $segment6 !='Assistant'  )
              $activeClass =  "menu-item-active";
            }
              
                $treeView .= "<li class='menu-item ".$activeClass."'  aria-haspopup='true'>
                <a href='".$path."' class='menu-link'>
                  <i class='menu-bullet menu-bullet-line'>
                    <span>".$messageChatcount."</span>
                  </i>
                  <span class='menu-text'>".$li_record->title."</span>
                </a>";
            if(!empty($li_record->children)){ 
              $treeView  .= sideBarNavigation($li_record->children);
            } 
            $treeView  .= "</li>"; 
          }
          $treeView  .= "</ul></div>";
        } 
        $treeView  .= "</li>"; 
      }
      $treeView  .= "</ul>";
    } 
    
    return $treeView;
  }

  function functionCheckPermission($function_name = ""){
    if( Auth::guard('admin')->user()->id != 1){
     
    
    $user_id				  =	Auth::guard('admin')->user()->id;

    $permissionData			=	DB::table("user_permission_actions")
                              ->select("user_permission_actions.is_active")
                              ->leftJoin("acl_admin_actions","acl_admin_actions.id","=","user_permission_actions.admin_module_action_id")
                              ->where('user_permission_actions.user_id',$user_id)
                              ->where('user_permission_actions.is_active',1)
                              ->where('acl_admin_actions.function_name',$function_name)
                              ->first();
   
      if(!empty($permissionData)){
          return 1;
        }else{
          return 0;
        }
      }else {
        return 1;
      }
}


function setting($key='')
{
  $setting = Setting::where('key',$key)->first();
  return $setting;
}


function image_size_Get($value='')
{
  return number_format(File::size($value)/1024,2);
}


function end_date_time_get($value){
  $create_time = strtotime($value);
  
  $current_time = time();
  
  $dtCurrent = DateTime::createFromFormat('U', $current_time);
  $dtCreate = DateTime::createFromFormat('U', $create_time);
  $diff = $dtCurrent->diff($dtCreate);

  $year = $diff->format("%y y");
    $year = preg_replace('/(^0| 0) (y|m|d|h|m|s)/', '', $year);
  $months = $diff->format("%m m");
    $months = preg_replace('/(^0| 0) (y|m|d|h|m|s)/', '', $months);
  $day = $diff->format("%d d");
    $day = preg_replace('/(^0| 0) (y|m|d|h|m|s)/', '', $day);
  $hours = $diff->format("%h h");
    $hours = preg_replace('/(^0| 0) (y|m|d|h|m|s)/', '', $hours);
  $minutes = $diff->format("%i m");
    $minutes = preg_replace('/(^0| 0) (y|m|d|h|m|s)/', '', $minutes);
  $second = $diff->format("%s s");
    $second = preg_replace('/(^0| 0) (y|m|d|h|m|s)/', '', $second);
  if(!empty($year)){
    $interval = $year;
  }elseif(!empty($months)){
    $interval = $months;
  }elseif(!empty($day)){
    $interval = $day;
  }elseif(!empty($hours)){
    $interval = 'about '.$hours;
  }elseif(!empty($minutes)){
    $interval = $minutes;
  }elseif(!empty($second)){
    $interval = $second;
  }
  echo $interval;
}

function getcurrentRole()
{
  return $role_type = (Auth::user()->user_role_id==2)?'service-provider':'user';
}



function  getActiveLanguages() {

  $languages		=	DB::table("languages")->get()->toArray();
  return $languages;
}	

function getCurrentLanguage(){

  if(Session::has('currentLanguageId')){
    $language_id      = Session::get('currentLanguageId');
    $lang_code        = DB::table("languages")->where('id',$language_id)->value('lang_code');
    App::setLocale($lang_code);

  }else{
    $language_id      = 1;
    App::setLocale('he');
  }
  return $language_id; 
  
}

function getAppLocaleId(){
  $language_id  = DB::table("languages")->where('lang_code',App::getLocale())->first()->id;
  return $language_id; 
}

function saveNotification($forNotificationArr){
  return Notification::insert($forNotificationArr);
}

function generatePassword($length = 8) {
  $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $lowercase = 'abcdefghijklmnopqrstuvwxyz';
  $numbers = '0123456789';
  $specialChars = '!@#$%^&*';
  
  $password = '';
  
  $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
  $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
  $password .= $numbers[rand(0, strlen($numbers) - 1)];
  $password .= $specialChars[rand(0, strlen($specialChars) - 1)];
  
  $remainingLength = $length - 4;
  
  for ($i = 0; $i < $remainingLength; $i++) {
      $allCharacters = $uppercase . $lowercase . $numbers . $specialChars;
      $password .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
  }
  
  // Shuffle the password to randomize character positions
  $password = str_shuffle($password);
  
  return $password;
}

function paymentResult($lowProfileCode){
  if(Config('Cardcom.production_mode') == 1){
    $terminalNumber = Config('Cardcom.live_company_terminal'); # Company terminal 
    $apiName       = Config('Cardcom.live_api_mame');   # API User
  }else{
    $terminalNumber = Config('Cardcom.test_company_terminal'); # Company terminal 
    $apiName       = Config('Cardcom.test_api_mame');   # API User
  }
  
  $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://secure.cardcom.solutions//api/v11/LowProfile/GetLpResult',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode([
    "TerminalNumber" => $terminalNumber,
    "ApiName" => $apiName,
    "LowProfileId" => $lowProfileCode,
  ]),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Accept: application/json',
    'Cookie: ARRAffinity=4867dc03eb2ea26dfe8316a07a847ed2fabc85c9bb74c721d01159cbe3a652b6; ASP.NET_SessionId=l134bszni3jcunqurdbd1wmt'
  ),
  ));
  
  $response = curl_exec($curl);

  curl_close($curl);
  return json_decode($response, true);

}


function greenApiMessage($message=null, $user=array())
{     
   
      $message = strip_tags(html_entity_decode($message));
      
        // if ($user['phone_number'][0] == '0') {
              $phone_number = '972' . substr($user['phone_number'], 1);
        // } else {
        //       $phone_number = '972' . $user['phone_number'];
        // }

      $curl = curl_init();
      $payload = json_encode(array(
            "chatId" => $phone_number . "@c.us",
            "message" => $message 
      ));


      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://7103.api.greenapi.com/" . Config('Site.id_instance') . "/sendMessage/" . Config('Site.api_token_instance') . "",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);
   
   Log::info($phone_number);
   Log::info($response);
   Log::info("checkMobileNumber");


      curl_close($curl);
      Log::info('green API Response.');

   
  
}
function getUserProfileImage($userId){
  $img = DB::table("users")->where("id",$userId)->first()->image;
  if($img == null){
    $img = Config('constants.NO_IMAGE_PATH');
  }else{
    $img = Config('constants.CUSTOMER_IMAGE_PATH').$img;
  }
  return $img;
}

