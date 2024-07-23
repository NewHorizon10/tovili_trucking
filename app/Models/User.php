<?php
 
namespace App\Models;
 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\File;
 
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $timestamps = true;

    protected $guarded = [];
 
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
 

    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getImageAttribute($value = ""){  

        if($value != "" && File::exists(Config('constants.CUSTOMER_IMAGE_ROOT_PATH').$value)){
        $value = Config('constants.CUSTOMER_IMAGE_PATH').$value;
        }else{
            $value = Config('constants.NO_IMAGE_PATH');
        }   
            // dd($value);
        return $value;
    }


    public function getNameAttribute($value = ""){
        // dd($this->attributes);  
        if($this->attributes['user_role_id'] == 2){
            if($this->attributes['customer_type'] == "business" && $this->attributes['name'] == ""){
                $value = trans("messages.Business Customer");
            }else if($this->attributes['customer_type'] == "private" && $this->attributes['name'] == ""){
                $value = trans("messages.private_customer");
            }
        }
        return $value;

        // if($this->attributes['user_role_id'] == 2){
        //     if($this->attributes['customer_type'] == "business" && $this->attributes['name'] == ""){
        //         $value = trans("messages.Business Customer");
        //     }else if($this->attributes['customer_type'] == "private" && empty($this->attributes['name']) && empty($this->attributes['names'])){
        //         $value = trans("messages.private_customer");
        //     }
        // }
        // return $value;
    }

    public function scopeUsertype($query, $role_id)
    {
        return $query->where('user_role_id', $role_id);
    }

    public function scopeisActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopenotDeleted($query)
    {
        return $query->where('is_deleted', 0);
    }



    public function countrydata($value='')
    {
        return $this->hasOne(Countries::class, 'id', 'country');
    }

    public function provider($value='')
    {
        return $this->hasOne(ServiceProviderDetail::class, 'service_provider_id', 'id');
    }

    public function specialization($value='')
    {
        return $this->hasMany(ServiceProviderSpecialization::class, 'service_provider_id', 'id');
    }

    public function tags($value='')
    {
        return $this->hasMany(ServiceProviderTag::class, 'service_provider_id', 'id');
    }

    public function education($value='')
    {
        return $this->hasMany(ServiceProviderEducation::class, 'service_provider_id', 'id');
    }

    public function experience($value='')
    {
        return $this->hasMany(ServiceProviderExperience::class, 'service_provider_id', 'id');
    }


    public function avatar($value='')
    {
        return $this->hasOne(Avatars::class, 'id', 'avatar_id');
    }

    // public static function getUploadProofAttribute($value = ""){    
    //     if($value != "" && File::exists(Config('constants.UPLOAD_PROFF_ROOT_PATH').$value)){
    //     $value = Config('constants.UPLOAD_PROFF_ROOT_PATH').$value;
    //     }
    //     return $value;
    // }

    public function userCompanyInformation($value='')
    {
        return $this->hasOne(UserCompanyInformation::class, 'user_id', 'id');
    }

    public function userDriverDetail($value='')
    {
        return $this->hasOne(UserDriverDetail::class, 'user_id', 'id');
    }

    

    public function TruckUserInformation($value='')
    {
        return $this->hasOne(User::class, 'id', 'truck_company_id');
    }

    public function TruckCompanyInformation($value='')
    {
        return $this->hasOne(UserCompanyInformation::class, 'user_id', 'truck_company_id');
    }

    public function shipment_offer($value='')
    {
        return $this->hasOne(ShipmentOffer::class, 'truck_company_id', 'id');
    }


    public function planDetails(){
        return $this->hasOne(TruckCompanySubscription::class, 'truck_company_id', 'id');
    }

    
       
}