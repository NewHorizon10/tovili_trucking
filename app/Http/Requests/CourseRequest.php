<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth, Str,File;

class CourseRequest extends FormRequest
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
        // dd(parent::validationData());
        return [
            'title'                => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'sub_title'            => 'required',
            'topic'                => 'required',
            'image'                => 'required|mimes:jpeg,jpg,png',
            'point1'               => 'required',
            'point2'               => 'required',
            'point3'               => 'required',
            'overview'             => 'required',
            'tags'                 => 'required',
            'course_free_video'    => 'required|mimes:mp4,wmv,mkv,hevc',
            'lesson_name.*'        => 'required',
            'lesson_overview.*'    => 'required',
            // 'video'              =>  'required',
            'video.*'              =>  'required',
            // 'resource[*].*'           =>  'required'
        ];
    }

    public function validatedAttributes()
    {
        $attributes = parent::validationData();

        if(isset($attributes['topic'])){
            unset($attributes['topic']);
        }
        
        if(isset($attributes['lesson_name'])){
            unset($attributes['lesson_name']);
        }

        if(isset($attributes['lesson_overview'])){
            unset($attributes['lesson_overview']);
        }

        if (isset($attributes['resource'])) {
            unset($attributes['resource']);
        }

        if (isset($attributes['tags'])) {
            unset($attributes['tags']);
        }



        if (isset($attributes['video'])) {
            unset($attributes['video']);
        }

        $title = $attributes['title'];
        $attributes['slug'] = Str::slug($title, '-');

      

        if (isset($this->image)) {
            $dir = Config('constants.COURSE_IMAGE_ROOT_PATH');
            // $folder = ucwords(date('M')) . date('Y') . '/';
            $old_image = auth()->user()->image;
            if (isset($old_image)) {
                if (File::exists($dir . $old_image)) {
                    File::delete($dir . $old_image);
                }
            }
            $image = time() . '.' . $this->image->getClientOriginalExtension();
            $this->file('image')->move($dir , $image);
            $attributes['image'] =  $image;
        }

        if (isset($this->course_free_video)) {
            $dir = Config('constants.COURSE_IMAGE_ROOT_PATH');
            // $folder = ucwords(date('M')) . date('Y') . '/';
            $old_image = auth()->user()->course_free_video;
            if (isset($old_image)) {
                if (File::exists($dir . $old_image)) {
                    File::delete($dir . $old_image);
                }
            }
            $image = time() . '.' . $this->course_free_video->getClientOriginalExtension();
            $this->file('course_free_video')->move($dir , $image);
            $attributes['course_free_video'] =  $image;
        }

        $providerId =  Auth::id();
        $attributes['service_provider_id'] = $providerId;
        $attributes['approval_status'] = 'submitted';

        // dd($attributes);
        return $attributes;
    }
}
