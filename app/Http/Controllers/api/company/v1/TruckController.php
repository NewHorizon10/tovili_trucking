<?php
 
namespace App\Http\Controllers\api\company\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserCompanyInformation;
use App\Models\UserDriverDetail;
use App\Models\TruckType;
use App\Models\Truck;
use App\Models\TruckCompanySubscription;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TruckController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
    }

    public function addTruckDriver(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $this->request->replace($this->arrayStripTags($request->all()));

            $validator = Validator::make(
                $request->all(),
                array(
                    'name'                          => "required",
                    'email'                         => "nullable|email",
                    'phone_number'                  => 'required|unique:users,phone_number|regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'licence_number'                => 'nullable',
                    'licence_exp_date'              => 'nullable',
                    'licence_picture'               => 'nullable',
                    'driver_picture'                => 'nullable',
                ),
                array(
                    "name.required"                         => trans("messages.This field is required"),
                    "password.required"                     => trans("messages.This field is required"),
					"password.between"          			=> trans("messages.password_should_be_in_between_4_to_8_characters"),
                    "password.regex"          				=> trans("messages.".Config('constants.PASSWORD_VALIDATION_MESSAGE_STRING')),
                    "password.digits"                       => trans("messages.Password should be 4 digits"),
                    "confirm_password.required"             => trans("messages.This field is required"),
                    "confirm_password.same"                 => trans("messages.The confirm password not matched with password"),
                    "phone_number.required"                 => trans("messages.This field is required"),
                    "phone_number.unique"                   => trans("messages.Mobile number already in use"),
                    'phone_number.regex'                    => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "email.required"                        => trans("messages.This field is required"),
                    "email.email"                           => trans("messages.The email must be a valid email address"),
                    "email.unique"                          => trans("messages.The email must be unique"),
                    "licence_number.required"               => trans("messages.This field is required"),
                    "licence_exp_date.required"             => trans("messages.This field is required"),
                    "licence_picture.required"              => trans("messages.This field is required"),
                    "driver_picture.required"               => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            } else {
                if (Auth::guard('api')->user()) {
                    $companyUser = Auth::guard('api')->user();
                }

                try {

                    DB::beginTransaction();

                    $user                               =   new User;
                    $user->user_role_id                 =   Config('constants.ROLE_ID.TRUCK_COMPANY_DRIVER_ID');
                    $user->name                         =   $request->input('name');
                    $user->truck_company_id             =   $companyUser->id;
                    $user->email                        =   $request->email ?? '';
                    $user->phone_number                 =   $request->phone_number;
                    $user->customer_type                 =   'business';
                    $user->password                     =   NUll;
                    $user->save();
                    $user->system_id = 0;
                    $user->save();

                    $system_id  =   1000 + $user->id;
                    User::where("id", $user->id)->update(array("system_id" => $system_id));

                    $driverDetails                               =   new UserDriverDetail();
                    $driverDetails->user_id                      =   $user->id;
                    $driverDetails->licence_number               =   $request->input('licence_number') ?? '';
                    $driverDetails->licence_exp_date             =   $request->input('licence_exp_date') ? Carbon::createFromFormat('d/m/Y', ($request->input('licence_exp_date')))->format('Y-m-d') : NULL;
                    $driverDetails->driver_picture               =   '';
                    $driverDetails->licence_picture              =   '';

                    if ($request->hasFile('driver_picture')) {
                        $file = rand() . '.' . $request->driver_picture->getClientOriginalExtension();
                        $request->file('driver_picture')->move(Config('constants.DRIVER_PICTURE_ROOT_PATH'), $file);
                        $driverDetails->driver_picture =  $file;
                    }
                    if ($request->hasFile('licence_picture')) {
                        $file = rand() . '.' . $request->licence_picture->getClientOriginalExtension();
                        $request->file('licence_picture')->move(Config('constants.LICENCE_PICTURE_ROOT_PATH'), $file);
                        $driverDetails->licence_picture = $file;;
                    }
                    $driverDetails->save();

                    DB::commit();

                    $response["status"]        =    "success";
                    $response["msg"]        =     trans("messages.Truck Driver has been added successfully");
                    $response["data"]        =    (object)array();
                    return response()->json($response);
                } catch (\Throwable $th) {

                    DB::rollback();

                    $response["status"]        =    "error";
                    $response["msg"]        =    $th->getMessage();
                    $response["data"]        =    (object)array();
                    return response()->json($response);
                }
            }
        }
    }

    public function truckDrivers()
    {
        if (Auth::guard('api')->user()) {
            $companyUser = Auth::guard('api')->user();
        }
        $truckDrivers    =    User::leftjoin('user_driver_details', 'users.id', 'user_driver_details.user_id')
            ->whereIn('users.user_role_id', [3,4])
            ->where('users.truck_company_id', $companyUser->id)
            ->where('users.is_deleted', 0)
            ->select(
                'users.id',
                'users.name',
                'users.name',
                'users.email',
                'users.email',
                'users.phone_number',
                'users.truck_company_id',
                'users.is_active',
                'users.user_role_id',
                'users.last_activity_date_time',
                'user_driver_details.licence_number',
                'user_driver_details.licence_exp_date',
                'user_driver_details.driver_picture',
                'user_driver_details.licence_picture'
            )->get();

           
            $truckDrivers->map(function ($truckDriver) {
                $truckDriver->driver_picture =  Config('constants.DRIVER_PICTURE_PATH') . $truckDriver->driver_picture;
                $truckDriver->licence_picture =  Config('constants.LICENCE_PICTURE_PATH'). $truckDriver->licence_picture;
            });

        $response["status"]        =    "success";
        $response["msg"]        =     trans("messages.Truck Drivers");
        $response["data"]        =    $truckDrivers;
        return response()->json($response);
    }

    public function updateTruckDriver(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $this->request->replace($this->arrayStripTags($request->all()));

            $validator = Validator::make(
                $request->all(),
                array(
                    'name'                          => "required",
                    'email'                         => "nullable|email",
                    'phone_number'                  => 'required|unique:users,phone_number,' . $formData['driver_user_id'] . 'regex:'.Config('constants.MOBILE_VALIDATION_STRING'),
                    'licence_number'                => 'nullable',
                    'licence_exp_date'              => 'nullable',
                ),
                array(
                    "name.required"                         => trans("messages.This field is required"),
                    "password.required"                     => trans("messages.This field is required"),
                    "password.min"                          => trans("messages.The Password must be atleast 4 characters"),
                    "confirm_password.required"             => trans("messages.This field is required"),
                    "confirm_password.same"                 => trans("messages.The confirm password not matched with password"),
                    "phone_number.required"                 => trans("messages.This field is required"),
                    "phone_number.unique"                   => trans("messages.Mobile number already in use"),
                    'phone_number.regex'                    => trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0'),
                    "email.required"                        => trans("messages.This field is required"),
                    "email.email"                           => trans("messages.The email must be a valid email address"),
                    "email.unique"                          => trans("messages.The email must be unique"),
                    "licence_number.required"               => trans("messages.This field is required"),
                    "licence_exp_date.required"             => trans("messages.This field is required"),
                    "licence_picture"                       => trans("messages.This field is required"),
                    "driver_picture"                        => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            } else {
                $user                               =   User::where(["id" => $formData['driver_user_id'], 'truck_company_id' => Auth::guard('api')->user()->id ])->first();
                if(!$user){
                    $response["status"]        =    "error";
                    $response["msg"]        =     trans("messages.User not found");
                    $response["data"]        =    (object)array();
                    return response()->json($response);
                }

                $user->name                         =   $request->name;
                $user->email                        =   $request->email;
                $user->phone_number                 =   $request->phone_number;
                $user->save();

                $driverDetails                               =   UserDriverDetail::where('user_id', $formData['driver_user_id'])->first();
                $driverDetails->user_id                      =   $user->id;
                $driverDetails->licence_number               =   $request->input('licence_number');
                $driverDetails->licence_exp_date             =   $request->input('licence_exp_date') ? Carbon::createFromFormat('d/m/Y', ($request->input('licence_exp_date')))->format('Y-m-d') : NULL;

                if ($request->hasFile('driver_picture')) {
                    $file = rand() . '.' . $request->driver_picture->getClientOriginalExtension();
                    $request->file('driver_picture')->move(Config('constants.DRIVER_PICTURE_ROOT_PATH'), $file);
                    $driverDetails->driver_picture =  $file;
                }
                if ($request->hasFile('licence_picture')) {
                    $file = rand() . '.' . $request->licence_picture->getClientOriginalExtension();
                    $request->file('licence_picture')->move(Config('constants.LICENCE_PICTURE_ROOT_PATH'), $file);
                    $driverDetails->licence_picture = $file;;
                }
                $driverDetails->save();

                $response["status"]        =    "success";
                $response["msg"]        =     trans("messages.Truck Driver has been updated successfully");
                $response["data"]        =    (object)array();
                return response()->json($response);
            }
        }
    }

    public function truckDriverDelete(Request $request)
    {
        $response    =    array();
        $user_id =  $request->driver_user_id;

        $userDetails   =   User::where(["id" => $user_id, 'truck_company_id' => Auth::guard('api')->user()->id ])->first();
        if (empty($userDetails)) {
            $response["status"]        =    "error";
            $response["msg"]        =     trans("messages.User not found");
            $response["data"]        =    (object)array();
            return response()->json($response);
        }
        if(Auth::guard('api')->user()->id == $user_id){
            $response["status"]        =    "error";
            $response["msg"]        =     trans("messages.Can not delete this truck driver");
            $response["data"]        =    (object)array();
        }else if ($user_id) {
            $email              =   'delete_' . $user_id . '_' .!empty($userDetails->email);
            $phone_number       =   'delete_' . $user_id . '_' .!empty($userDetails->phone_number);

            User::where('id', $user_id)->update(array(
                'is_deleted'    => 1, 
                'email'         => $email, 
                'phone_number'  => null,
            ));
            $response["status"]        =    "success";
            $response["msg"]        =     trans("messages.Truck Driver has been removed successfully");
            $response["data"]        =    (object)array();
            return response()->json($response);
        }
        return response()->json($response);
    }

    public function addTruck(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $this->request->replace($this->arrayStripTags($request->all()));
            $truck_type_descriptions = DB::table('truck_type_questions')->where([
                "truck_type_id"       => $request->type_of_truck,
                "is_active"           => 1,
                "is_deleted"          => 0,
            ])->get(); 

            $validator = Validator::make(
                $request->all(),
                array(
                    'truck_system_number'                           => "required",
                    'type_of_truck'                                 => "required",
                    'truck_insurance_expiration_date'               => 'required|date_format:d/m/Y',
                    'truck_licence_expiration_date'                 => 'required|date_format:d/m/Y',
                    'truck_insurance_picture'                       => 'required|file|mimes:jpeg,png,jpg,pdf',
                    'truck_licence_number'                          => 'required|file|mimes:jpeg,png,jpg,pdf',
                    'questionnaire.*'                               => ($truck_type_descriptions->count() > 0 ? 'required' : ''),
                    'questionnaire.*.*'                             => ($truck_type_descriptions->count() > 0 ? 'required' : ''),
                ),
                array(
                    "truck_system_number.required"                          => trans("messages.This field is required"),
                    "type_of_truck.required"                                => trans("messages.This field is required"),
                    "truck_insurance_expiration_date.required"              => trans("messages.This field is required"),
                    "truck_insurance_expiration_date.date_format"           => trans('messages.invalid_date_selected'),
                    "truck_licence_expiration_date.required"                => trans("messages.This field is required"),
                    "truck_licence_expiration_date.date_format"             => trans('messages.invalid_date_selected'),
                    "truck_insurance_picture.required"                      => trans("messages.This field is required"),
                    "truck_insurance_picture.mimes"                         => trans("messages.the_fields_must_be_a_file_of_type_jpeg_png_jpg_gif_pdf"),
                    "truck_licence_number.required"                         => trans("messages.This field is required"),
                    "truck_licence_number.mimes"                            => trans("messages.the_fields_must_be_a_file_of_type_jpeg_png_jpg_gif_pdf"),
                    "questionnaire.*.required"                              => trans("messages.This field is required"),
                    "questionnaire.*.*.required"                            => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            } else {
                $user = Auth::guard('api')->user();
                
                $truckRegistrationData = UserCompanyInformation::where("user_id",$user->id)->first();

            
                $file1 = $request->file('truck_insurance_picture');
                $filename1 = null;
                if ($file1) {
                    $filename1 = rand() . '.' . $file1->getClientOriginalExtension();
                    $file1->move(Config('constants.TRUCK_INSURANCE_IMAGE_ROOT_PATH'), $filename1);
                }
            
                $file2 = $request->file('truck_licence_number');
                $filename2 = null;
                if ($file2) {
                    $filename2 = rand() . '.' . $file2->getClientOriginalExtension();
                    $file2->move(Config('constants.TRUCK_LICENCE_NUMBER_IMAGE_ROOT_PATH'), $filename2);
                }

                $getActivePlan = TruckCompanySubscription::where('truck_company_id', $user->id)->where('status', 'activate')->first();
        
                $getTotalTrucksCount = Truck::where('truck_company_id', $user->id)->count();
                
        
                if($getActivePlan == null && $getTotalTrucksCount == 5){
                 
                    $response["status"]         =    "error";
                    $response["msg"]            =     trans("messages.you_donot_have_active_plan_you_cannot_add_more_then_five_trucks");
                    $response["data"]           =    (object)array();
                    return response()->json($response);

                }

                $column_type = $getActivePlan->column_type ?? 0;
        
                $getTruckCounts = Truck::where('truck_company_id', $user->id)->count();
                
                if($column_type == 0 && $getTruckCounts == 5){
        
                    $response["status"]         =    "error";
                    $response["msg"]            =     trans("messages.you_canot_add_more_then_five_trucks_as_per_your_plan_type");
                    $response["data"]           =    (object)array();
                    return response()->json($response);
                    
                }else{

                    DB::table('trucks')->insert(
                        array(
                            'truck_company_id' 					=> $user->id,
                            'truck_system_number' 				=> $request->truck_system_number,
                            'company_refueling' 				=> $truckRegistrationData->company_refueling,
                            'company_tidaluk' 					=> $truckRegistrationData->company_tidaluk,
                            'type_of_truck' 					=> $request->type_of_truck,
                            'basketman' 						=> $request->basketman,
                            'truck_licence_number' 				=> $filename2,
                            'truck_licence_expiration_date' 	=> Carbon::createFromFormat('d/m/Y', ($request->truck_licence_expiration_date))->format('Y-m-d'),
                            'truck_insurance_picture' 			=> $filename1,
                            'truck_insurance_expiration_date' 	=> Carbon::createFromFormat('d/m/Y', ($request->truck_insurance_expiration_date))->format('Y-m-d'),
                            'is_active' 						=> 1,
                            'is_deleted' 						=> 0,
                            'driver_id' 						=> $request->truck_driver_id,
                            'questionnaire'						=> json_encode($request->questionnaire) ,
                        )
                    );
                
                }

                $response["status"]         =    "success";
                $response["msg"]            =     trans("messages.truck_has_been_added_successfully");
                $response["data"]           =    (object)array();
                return response()->json($response);
            }
        }
    }


    public function truckTypes(Request $request) {
        $language_id    =   getAppLocaleId();
        $truckTypeQuestionnaire = TruckType::where(
            [
                'map_truck_type_id'=> 0,
                'is_active'=> 1,
                'is_deleted'=> 0
            ]
        )
        ->select(
            DB::raw("(select name from truck_type_descriptions where parent_id=truck_types.id and language_id=$language_id) as name"),
            "id"
        )
        ->with([
            'TruckTypeQuestionsList'=>function ($query) {
                $query->where('is_active', 1)->where('is_deleted', 0);
            },
            'TruckTypeQuestionsList.TruckTypeQuestionDiscription'=>function ($query) {
                $query->where('language_id', getAppLocaleId());
            }
        ])
        ->get();

        $driver = array();
        $all_driver  =   User::where("truck_company_id",Auth::guard('api')->user()->id)
                            ->whereIn('users.user_role_id', [3,4])
                            ->where("is_active",1)
                            ->where("is_deleted",0)
                            ->get();
        $driver['all_driver'] = array();
        foreach($all_driver as $driverRow){
            $driver['all_driver'][] = array('id'=>$driverRow["id"],'name'=>$driverRow["name"]);
        }




        $free_driver = User::where("users.truck_company_id",Auth::guard('api')->user()->id)
        ->select("users.id","users.name","users.user_role_id")
        ->leftjoin('trucks','trucks.driver_id','users.id');
        if($request->truck_id){
            $trucks = DB::table('trucks')->where("id",$request->truck_id)->first();
            $free_driver = $free_driver->whereRaw("trucks.driver_id IS NULL or trucks.driver_id = ".$trucks->driver_id);
        }else{
            $free_driver = $free_driver->whereRaw("trucks.driver_id IS NULL");
        }

        $free_driver = $free_driver
            ->whereIn('users.user_role_id', [3,4])
            ->where("users.is_active",1)
            ->where("users.is_deleted",0)
            ->get()->toArray();
        $driver['free_driver'] = array();
        
        foreach($free_driver as $driverRow){
            $driver['free_driver'][] = array('id'=>$driverRow["id"],'name'=>$driverRow["name"]);
        }

        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $truckTypeQuestionnaire;
        $response["driver_data"]            =   $driver;
        return response()->json($response);
	}

    public function trucksList(Request $request) {
        $results = array();
        $language_id    =   getAppLocaleId();

        $results["truck"]               =   Truck::
            where("trucks.is_active",1)
            ->where("trucks.is_deleted",0)
            ->where("trucks.truck_company_id",Auth::guard('api')->user()->id)
            ->with([
                'typeOfTruck.truckTypeDiscription'=>function ($query) {
                    $query->where('language_id', getAppLocaleId());
                },
                'typeOfTruck.TruckTypeQuestionsList.TruckTypeQuestionDiscription'=>function ($query) {
                    $query->where('language_id', getAppLocaleId());
                },
                'truckDriver'
            ]);

        $results["truck"]               =   $results["truck"]->get()->toArray();

        foreach($results["truck"] as &$truck){
            if($truck['truck_driver']){
                $truck['truck_driver'] = array( "id"=>$truck['truck_driver']["id"],"name"=>$truck['truck_driver']["name"] ) ;
            }
            foreach($truck['type_of_truck']['truck_type_questions_list'] as &$questionOptions){
                if(!empty($questionOptions['input_description'])){
                    $option = explode(",", $questionOptions['input_description']);
                    if($option){
                        $questionOptions['input_description'] = trans('messages.yes') . "," . trans('messages.no');
                    }else{
                        $questionOptions['input_description'] = "";
                    }
                }
            }
        }
        $response                           =   array();
        $response["status"]                 =   "success";
        $response["msg"]                    =   "";
        $response["data"]                   =   $results;
        return response()->json($response);
	}

    public function truckDelete(Request $request)
    {        
        $response    =    array();
        $truck_id =  $request->truck_id;

        $truckDetails   =   Truck::where(["id" => $truck_id, 'truck_company_id' => Auth::guard('api')->user()->id ])->first();
        if (empty($truckDetails)) {
            $response["status"]        =    "error";
            $response["msg"]        =     trans("messages.User not found");
            $response["data"]        =    (object)array();
            return response()->json($response);
        }
        if ($truck_id) {
            Truck::where('id', $truck_id)->update(array(
                    'driver_id'     => 0,
                    'is_deleted'    => 1 
            ));
            $response["status"]        =    "success";
            $response["msg"]        =     trans("messages.admin_Truck_has_been_removed_successfully");
            $response["data"]        =    (object)array();
            return response()->json($response);
        }
        return response()->json($response);
    }

    public function updateTruck(Request $request)
    {
        $formData    =    $request->all();
        $response    =    array();
        if (!empty($formData)) {
            $this->request->replace($this->arrayStripTags($request->all()));
            $truck_type_descriptions = DB::table('truck_type_questions')->where([
                "truck_type_id"       => $request->type_of_truck,
                "is_active"           => 1,
                "is_deleted"          => 0,
            ])->get(); 
            $validator = Validator::make(
                $request->all(),
                array(
                    'truck_id'                                              => "required",
                    'truck_system_number'                                   => "required",
                    'type_of_truck'                                         => "required",
                    'truck_insurance_expiration_date'                       => 'required|date_format:d/m/Y',
                    'truck_licence_expiration_date'                         => 'required|date_format:d/m/Y',
                    'truck_insurance_picture'                               => 'nullable|file|mimes:jpeg,png,jpg,pdf',
                    'truck_licence_number'                                  => 'nullable|file|mimes:jpeg,png,jpg,pdf',
                    'image'                                                 => 'nullable|file|mimes:jpeg,png,jpg',
                    'questionnaire.*'                                       => ($truck_type_descriptions->count() > 0 ? 'required' : ''),
                    'questionnaire.*.*'                                     => ($truck_type_descriptions->count() > 0 ? 'required' : ''),
                ),
                array(
                    "truck_id.required"                                     => trans("messages.This field is required"),
                    "truck_system_number.required"                          => trans("messages.This field is required"),
                    "type_of_truck.required"                                => trans("messages.This field is required"),
                    "truck_insurance_expiration_date.required"              => trans("messages.This field is required"),
                    "truck_insurance_expiration_date.date_format"           => trans('messages.invalid_date_selected'),
                    "truck_licence_expiration_date.required"                => trans("messages.This field is required"),
                    "truck_licence_expiration_date.date_format"             => trans('messages.invalid_date_selected'),
                    "truck_insurance_picture.required"                      => trans("messages.This field is required"),
                    "truck_insurance_picture.mimes"                         => trans("messages.the_fields_must_be_a_file_of_type_jpeg_png_jpg_gif_pdf"),
                    "truck_licence_number.required"                         => trans("messages.This field is required"),
                    "truck_licence_number.mimes"                            => trans("messages.the_fields_must_be_a_file_of_type_jpeg_png_jpg_gif_pdf"),
                    "image.mimes"                                           => trans("messages.the_fields_must_be_an_image_of_type_jpeg_png_jpg_gif"),
                    "questionnaire.*.required"                              => trans("messages.This field is required"),
                    "questionnaire.*.*.required"                            => trans("messages.This field is required"),
                )
            );
            if ($validator->fails()) {
                return $this->change_error_msg_layout($validator->errors()->getMessages());
            } else {
                
                $user = Auth::guard('api')->user();
                $truckDetails = DB::table('trucks')->where(['id'=>$request->truck_id,'truck_company_id'=>$user->id])->first();
                if(!$truckDetails){
                    $response["status"]         =    "error";
                    $response["msg"]            =     trans("messages.truck_not_found");
                    $response["data"]           =    (object)array();
                    return response()->json($response);

                }


                $truckRegistrationData = UserCompanyInformation::where("user_id",$user->id)->first();

                $image = $request->file('image');
                $file1 = $request->file('truck_licence_number');
                $file2 = $request->file('truck_insurance_picture');
                $imagename = null;
                $filename1 = null;
                $filename2 = null;
            
                if ($image) {
                    $imagename = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(Config('constants.TRUCK_IMAGE_ROOT_PATH'), $imagename);
                }else{
                    $imagename = $truckDetails->image;
                }

                if ($file1) {
                    $filename1 = rand() . '.' . $file1->getClientOriginalExtension();
                    $file1->move(Config('constants.TRUCK_LICENCE_NUMBER_IMAGE_ROOT_PATH'), $filename1);
                }else{
                    $filename1 = $truckDetails->truck_licence_number;
                }
            
                if ($file2) {
                    $filename2 = rand() . '.' . $file2->getClientOriginalExtension();
                    $file2->move(Config('constants.TRUCK_INSURANCE_IMAGE_ROOT_PATH'), $filename2);
                }else{
                    $filename2 = $truckDetails->truck_insurance_picture;
                }

                DB::table('trucks')->where("id",$request->truck_id)->update(
                    array(
                        'truck_system_number' 				            => $request->truck_system_number,
                        'company_refueling' 				            => $truckRegistrationData->company_refueling,
                        'company_tidaluk' 					            => $truckRegistrationData->company_tidaluk,
                        'type_of_truck' 					            => $request->type_of_truck,
                        'truck_licence_number' 				            => $filename1,
                        'image' 				                        => $imagename,
                        'truck_licence_expiration_date' 	            => Carbon::createFromFormat('d/m/Y', ($request->truck_licence_expiration_date))->format('Y-m-d'),
                        'truck_insurance_picture' 			            => $filename2,
                        'truck_insurance_expiration_date' 	            => Carbon::createFromFormat('d/m/Y', ($request->truck_insurance_expiration_date))->format('Y-m-d'),
                        'questionnaire'						            => json_encode($request->questionnaire) ,
                        'driver_id'						                => $request->truck_driver_id ,
                        'send_insurance_notification_before_30_days'    => 0,
                        'send_licence_notification_before_30_days'      => 0,
                    )
                );
                

                $response["status"]         =    "success";
                $response["msg"]            =     trans("messages.truck_has_been_update_successfully");
                $response["data"]           =    (object)array();
                return response()->json($response);
            }
        }
    }


}
