<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Truck extends Model
{
    use HasFactory;



    public static function getImageAttribute($value = ""){  
        if( $value &&  $value != "" && File::exists(Config('constants.TRUCK_IMAGE_ROOT_PATH').$value)){
            $value = Config('constants.TRUCK_IMAGE_PATH').$value;
        }else{
            $value = Config('constants.NO_TRUCK_IMAGE_PATH');//noimage
        }
        return $value;
    }
    public static function getTruckLicenceNumberAttribute($value = ""){  
        if( $value &&  $value != "" && File::exists(Config('constants.TRUCK_LICENCE_NUMBER_IMAGE_ROOT_PATH').$value)){
            $value = Config('constants.TRUCK_LICENCE_NUMBER_IMAGE_PATH').$value;
        }else{
            $value = Config('constants.NO_TRUCK_IMAGE_PATH');
        }
        return $value;
    }
    public static function getTruckInsurancePictureAttribute($value = ""){  
        if( $value &&  $value != "" && File::exists(Config('constants.TRUCK_INSURANCE_IMAGE_ROOT_PATH').$value)){
            $value = Config('constants.TRUCK_INSURANCE_IMAGE_PATH').$value;
        }else{
            $value = Config('constants.NO_TRUCK_IMAGE_PATH');
        }
        return $value;
    }

    public static function getQuestionnaireAttribute($value = ""){  
        return json_decode($value);
    }

    public function typeOfTruck($value='')
    {
        return $this->hasOne(TruckType::class, 'id', 'type_of_truck');
    }

    public function truckDriver($value='')
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }

    public function truckCompanyDetails(){
        return $this->hasOne(User::class, 'id', 'truck_company_id');
    }

    public function truck_company_details(){
        return $this->hasOne(UserCompanyInformation::class, 'user_id', 'truck_company_id');
    }

    public function truckTypeDetails(){
        return $this->hasOne(TruckTypeDescription::class, 'parent_id', 'type_of_truck')->where('language_id', getCurrentLanguage());
    }

    public function companyTidulakDetails(){
        return $this->hasOne(LookupDiscription::class, 'parent_id', 'company_tidaluk')->where('language_id', getCurrentLanguage());
    }

    public function companyRefueling(){
        return $this->hasOne(LookupDiscription::class, 'parent_id', 'company_refueling')->where('language_id', getCurrentLanguage());
    }

      public function companyUser(){
        return $this->hasOne(User::class, 'id', 'truck_company_id');
    }

    
}