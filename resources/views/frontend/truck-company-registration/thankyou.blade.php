@extends('frontend.layouts.truckCompanyLayout')
@section('extraCssLinks')
@stop
@section('content')


<section class="form_section">
    <div class="container">
        <div class="outer_companyform_box">
            <div class="track_company_box track_company_page pt-0">
                <div class="white_form_theme">
                    <div class="companyFormBox">
                        <div class="thankYou_page text-center">
                            <div class="thankYouIcon">
                                <img src="{{asset('public/img/Symbol.png')}}" alt="">
                            </div>
                            <div class="thankText">
                                <h3 class="thankyouTitle">{{trans('messages.thank_you')}} </h3>
                                <p class="thankyouSubTitle">{{trans('messages.have_received_your_payment')}}</p>
                            </div>
                            <div class="downloadApp">
                            <P class="appTitle"> {{trans('messages.download_app_now')}}</P>
                                <div class="downloadLink">
                                    <a href="https://www.apple.com/in/store"><img src="{{asset('public/img/appStore.png')}}" alt=""></a>
                                    <a href="https://play.google.com/store/games"><img src="{{asset('public/img/GooglePlay.png')}}" alt=""></a>
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



    @section('scriptCode')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    var body = document.querySelector('body');
    body.classList.add('track_company');
    });
   
</script>
<script>
    function myFunction() {
        var form = document.getElementById("resendOtpForm");
        form.submit();
    }
</script>
@stop