<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth,Str;

class ArticleRequest extends FormRequest
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
            'article_name'          => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'topic'         => 'required',
            'featured_image'       => 'required|mimes:jpeg,jpg,png',
            'content'       => 'required',
            'tags'       => 'required',
            'read_time'       => 'required',

        ];
    }


    public function validatedAttributes()
    {
        $attributes = parent::validationData();
        if(isset($attributes['topic'])){
            unset($attributes['topic']);
        }


        if(isset($this->featured_image)){
            $dir = Config('constants.ARTICLES_IMAGE_ROOT_PATH');
            $folder = ucwords(date('M')).date('Y').'/';
            $old_image = auth()->user()->featured_image;
            if(isset($old_image)){
                if(File::exists($dir. $old_image)){
                    File::delete($dir.$old_image);
                }
            }
            $image = time() . '.' . $this->featured_image->getClientOriginalExtension();
            $this->file('featured_image')->move($dir.$folder, $image);
            $attributes['featured_image'] = $folder.$image;
        }

        if(isset($attributes['tags'])){
            unset($attributes['tags']);
        }
      $title = $attributes['article_name'];
        $attributes['slug'] = Str::slug($title,'-');

      $providerId =  Auth::id();
         $attributes['service_provider_id'] =$providerId;
         $attributes['approval_status'] = 'submitted';
        return $attributes;
    }

}
