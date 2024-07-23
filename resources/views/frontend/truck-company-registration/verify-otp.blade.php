@extends('frontend.layouts.truckCompanyLayout')
@section('content')
<section class="form_section">
    <div class="container">
        <div class="outer_companyform_box middleHeight">
            <div class="track_company_box track_company_page">
                <div class="white_form_theme">
                    <h1 class="form_page_title">
                        <span class=""> {{trans('messages.Account Registration')}} </span>
                    </h1>
                    <div class="stepsProgressBar">
                      <ul class="list-unstyled multi-steps">
                        <li id="step-1" class="is-active">
                         <!--    <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div> -->
                        </li>
                        <li id="step-2" class="is-active">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        <li id="step-3" class="is-active">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        <li id="step-4" class="">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        <li id="step-5">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        {{-- <li id="step-6">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li> --}}
                      </ul>
                    </div>
                    <p class="company_page_subtitle"> {{trans('messages.verify')}} {{trans('messages.Phone Number')}}</p>
                    <div class="companyFormBox">
                        <div class="otp_verification client_form_theme">
                            <div class="otp_info">
                            {{trans('messages.please_enter_a_4_digit_code_sent_to_your_phone_number')}}<a href="tel:{{  Session::get('userTypePhoneData') }}" class="otp_info_number"> {{  Session::get('userTypePhoneData') }}</a> 
                            </div>
                        
                            <form action="{{ route('check-otp-truck-company') }}" method="post" class="otpRow " id="otp-form">
                            @csrf
                                <input class="otp form-control dark-form-control tabchange" type="text" name="otp1" oninput='digitValidate(this)' onkeyup='tabChange(0)' maxlength=1 >
                                <input class="otp form-control dark-form-control tabchange" type="text" name="otp2" oninput='digitValidate(this)' onkeyup='tabChange(1)' maxlength=1 >
                                <input class="otp form-control dark-form-control tabchange" type="text" name="otp3" oninput='digitValidate(this)' onkeyup='tabChange(2)' maxlength=1 >
                                <input class="otp form-control dark-form-control tabchange" type="text" name="otp4" oninput='digitValidate(this)' onkeyup='tabChange(3)' maxlength=1 > 
                        </form>
                        @if ($errors->has('fullOtp'))
                            <input class="is-invalid" type="hidden">
                            <span class="text-danger text-center"> {{ $errors->first('fullOtp') }}  </span>
                        @endif
                        <button type="submit" class="btn secondary-btn w-100 submit"  onclick="document.getElementById('otp-form').submit();">{{trans('messages.verify')}}</button>
                        <div class="dont_have_ac">
                            <form id="resendOtpForm" action="{{route('verify-mobile-truck-company')}}" method="post">
                                @csrf
                                <input type="hidden" value="{{  Session::get('userTypePhoneData') }}" name="phone_number">
                                <a href="javascript:void(0)" onclick="myFunction()">  {{trans('messages.resend_otp')}}</a>
                            </form>
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

    <script>

        let digitValidate = function(ele){
            ele.value = ele.value.replace(/[^0-9]/g,'');
            // ele.select();
            }

        let tabChange = function(val){
            let ele = $('.tabchange');
            if(ele[val].value != ''){
            ele[val+1].focus();
            }else if(ele[val].value == ''){
            ele[val-1].focus();
            ele[val-1].select();
            }   
        }


        function showMe(evt) {
            console.log("evt.value ", evt.value);
        }

        function makeDd() {
            'use strict';
            let json = new Function(`return ${document.getElementById('json_data').innerHTML}`)();
            MsDropdown.make("#json_dropdown", {
                byJson: {
                    data: json,
                    selectedIndex: 0
                }
            });
        }

    </script>
@stop