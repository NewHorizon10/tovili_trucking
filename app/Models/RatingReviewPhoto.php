<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class RatingReviewPhoto extends Model
{
    use HasFactory;

    public static function getPhotoAttribute($value = ""){  
        if($value != "" && File::exists(Config('constants.GALLERY_MEDIA_IMAGE_ROOT_PATH').$value)){
        $value = Config('constants.GALLERY_MEDIA_IMAGE').$value;
        }else{
            $value = Config('constants.NO_IMAGE_PATH');
        }
  
        return $value;
    }
}
