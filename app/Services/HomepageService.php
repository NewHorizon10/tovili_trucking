<?php

namespace App\Services;

use App\Models\HomeIntroduction;
use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;

class HomepageService
{
    // Home Data Functions
    public function section_1()
    {
        return HomeIntroduction::where('page','home')->where('type','home_interaction')->first();
    }

    public function mobile_section_1()
    {
        return HomeIntroduction::where('page','home')->where('type','mobile_interaction')->get();
    }

    public function section_2()
    {
        return HomeIntroduction::where('page','home')->where('type','secure_image')->limit(3)->get();
    }

    public function section_3()
    {
        return HomeIntroduction::where('page','home')->where('type','support')->first();
    }

    public function section_4()
    {
        return HomeIntroduction::where('page','home')->where('type','how_it_works')->get();
    }

    public function section_5()
    {
        return HomeIntroduction::where('page','home')->where('type','why_use_joymee')->first();
    }

    public function section_6()
    {
        return HomeIntroduction::where('page','home')->where('type','our_vision_one')->first();
    }

    public function section_7()
    {
        return HomeIntroduction::where('page','home')->where('type','our_vision')->get();
    }

    public function section_8()
    {
        return Testimonial::isActive()->get();
    }

    public function section_9()
    {
        return HomeIntroduction::where('page','home')->where('type','making_sexuality')->first();
    }


    public function aboutSection1()
    {
        return HomeIntroduction::where('page','about')->where('type','section_1')->first();
    }

    public function aboutSectionAll()
    {
        return HomeIntroduction::where('page','about')->where('type','section_all')->get();
    }

}
