<?php

namespace App\Services;

use App\Models\HomeIntroduction;
use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;

class TherapistHomePageService
{
    // Home Data Functions
    public function section_1()
    {
        return HomeIntroduction::where('page','therapist')->where('type','therapist_home_interaction')->first();
    }

 
   
   

    public function section_2()
    {
        return HomeIntroduction::where('page','therapist')->where('type','therapist_our_vision_one')->first();
    }

    public function section_3()
    {
        return HomeIntroduction::where('page','therapist')->where('type','therapist_our_vision')->get();
    }

    public function section_4()
    {
        return Testimonial::isActive()->get();
    }

   

    public function section_5()
    {
        return HomeIntroduction::where('page','therapist')->where('type','therapist_about_us')->first();
    }
    

    public function aboutSectionAll()
    {
        return HomeIntroduction::where('page','therapist')->where('type','section_all')->get();
    }

}
