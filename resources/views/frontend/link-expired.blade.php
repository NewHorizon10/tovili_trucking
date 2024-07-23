@extends('frontend.layouts.defaultpages')
@section('backgroundImage')
<body class="homePage ">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="img/logo.png" alt="">
        </div>
    </div>
@stop
@section('content')

<section class="form_section track_company">
        <div class="container">
            <div class="outer_companyform_box pt-3">
                <div class="track_company_box track_company_page pt-0">
                    <div class="white_form_theme">
                        <div class="companyFormBox">
                            <div class="thankYou_page text-center">
                               <div class="thankYouIcon">
                                    <img src="{{asset('public/frontend/img/wrong_symbol.png')}}" alt="">
                               </div>
                               <div class="thankText">
                                    <h3 class="thankyouTitle text-danger">{{trans('messages.expired_link')}}</h3>
                                    <!-- <p class="thankyouSubTitle">have received your payment</p> -->
                               </div>
                               <div class="downloadApp">
                                <p class="appTitle">{{trans('messages.download_app_now')}}</p>
                                    <div class="downloadLink">
                                        <a href="javascript:void(0)"><img src="{{asset('public/frontend/img/appStore.png')}}" alt=""></a>
                                        <a href="javascript:void(0)"><img src="{{asset('public/frontend/img/GooglePlay.png')}}" alt=""></a>
                                    </div>
                               </div>
                                
                               
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop