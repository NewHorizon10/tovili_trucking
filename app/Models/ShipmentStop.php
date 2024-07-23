<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ShipmentStop extends Model
{
    use HasFactory;
    protected $table = 'shipment_stops';
    public function ShipmentStopAttchements($value='')
    {
        return $this->hasMany(ShipmentStopAttchement::class, 'shipment_stops_id', 'id');
    }



    public static function getRequestCertificateAttribute($value = ""){  
        // dd($value);
        if($value != "" && File::exists(Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH').$value)){
            $value = Config('constants.GALLERY_MEDIA_IMAGE').$value;
        }else{
            $value = null;
            // $value = Config('constants.NO_IMAGE_PATH');
        }
  
        return $value;
    }
    public static function getRequestDigitalSignatureAttribute($value = ""){
        // dd($value);
        if($value != "" && File::exists(Config('constants.SIGNATURE_IMAGES_ROOT_PATH').$value)){
            $value = Config('constants.SIGNATURE_IMAGE_PATH').$value;
        }else{
            $value = null;
            // $value = Config('constants.NO_IMAGE_PATH');
        }
  
        return $value;
    }

    public function shipmentDetails(){
        return $this->hasOne(Shipment::class, 'id', 'shipment_id');
    }


}
