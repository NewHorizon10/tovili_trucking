<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class UserCompanyInformation extends Model
{
    use HasFactory;
    protected $table = 'user_company_informations';

    protected $fillable = [
        'user_id',
        'company_id',
        'company_name',
        'company_mobile_number',
        'contact_person_name',
        'contact_person_email',
        'contact_person_phone_number',
        'contact_person_picture',
        'company_type',
        'company_logo',
        'company_market',
        'company_description',
        'company_location'

    ];

    public static function getCompanyLogoAttribute($value = ""){  
        // dd($value);
        if($value != "" && File::exists(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH').$value)){
        $value = Config('constants.COMPANY_LOGO_IMAGE_PATH').$value;
        }else{
            $value = Config('constants.NO_IMAGE_PATH');
        }
  
        return $value;
    }

    public static function getContactPersonPictureAttribute($value = ""){  
        if($value != "" && File::exists(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH').$value)){
        $value = Config('constants.CONTACT_PERSON_PROFILE_IMAGE_PATH').$value;
        }else{
            $value = Config('constants.NO_IMAGE_PATH');
        }
  
        return $value;
    }

    public function getCompanyRefuelingDescription($value = ""){
        return $this->hasOne(LookupDiscription::class, 'parent_id', 'company_refueling')->where('language_id', getAppLocaleId());
    }

    public function getCompanyTidalukCompanyDescription($value = ""){
        return $this->hasOne(LookupDiscription::class, 'parent_id', 'company_tidaluk')->where('language_id', getAppLocaleId());
    }


    // public function userDriverDetail($value='')
    // {
    //     return $this->hasOne(UserDriverDetail::class, 'user_id', 'id');
    // }
    // public static function getCompanyRefuelingDescription($value = "") {
    //     $user = new User();
    //     return $user->hasOne(LookupDescription::class, 'parent_id', 'company_refueling');
    // }
    

}
