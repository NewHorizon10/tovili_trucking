<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Config, Hash;

class RegisterUserRequest extends FormRequest
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
            'name'                     => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'email'                    => 'required|email|unique:users|regex:/(.+)@(.+)\.(.+)/i',
            'country'                  => 'required',
            'account_type'             => 'required',
            'password'                 => 'required',
            'confirm_password'         => 'same:password',
        ];
    }

    public function validatedAttributes()
    {
        $attributes = parent::validationData();
        if(isset($attributes['confirm_password'])){
            unset($attributes['confirm_password']);
        }
        $encryptPassword = Hash::make($attributes['password']);
        if(isset($attributes['password'])){
            unset($attributes['password']);
        }        
        $attributes['user_role_id'] = Config('constants.ROLE_ID.CUSTOMER_ROLE_ID');
        $attributes['password'] = $encryptPassword;

        $attributes['is_approved'] = 1;
        return $attributes;
    }
}
