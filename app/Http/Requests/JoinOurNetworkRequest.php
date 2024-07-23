<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Config, Hash;

class JoinOurNetworkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'              =>  'required|regex:/^[a-zA-Z0-9\s]+$/',
            'surname'           =>  'nullable|regex:/^[a-zA-Z0-9\s]+$/',
            'title'             =>  'required|regex:/^[a-zA-Z0-9\s]+$/',
            'country'           =>  'required',
            'email'             =>  'required|email|unique:users|regex:/(.+)@(.+)\.(.+)/i',
            'phone_number'      =>  'required|numeric|digits:10',
            'password'          =>  'required|min:8',
            'confirm_password'  =>  'required|same:password',
            "education.*"       =>  'required|min:1',
            "experience.*"      =>  'required|min:1',
            "specialization.*"  =>  'required|min:1',
        ];
    }
    public function validatedAttributes()
    {
        $attributes = parent::validationData();
        if(isset($attributes['confirm_password'])){
            unset($attributes['confirm_password']);
        }
        if(isset($attributes['education'])){
            unset($attributes['education']);
        }
        if(isset($attributes['experience'])){
            unset($attributes['experience']);
        }
        if(isset($attributes['specialization'])){
            unset($attributes['specialization']);
        }
        if(isset($attributes['title'])){
            unset($attributes['title']);
        }
        $encryptPassword = Hash::make($attributes['password']);
        if(isset($attributes['password'])){
            unset($attributes['password']);
        }        
        
        $attributes['password'] = $encryptPassword;
       
         $attributes['user_role_id'] = Config('constants.ROLE_ID.PROVIDER_ROLE_ID');
         $attributes['is_approved'] = 0;
        return $attributes;
    }
}
