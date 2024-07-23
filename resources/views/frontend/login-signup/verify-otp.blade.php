@extends('frontend.layouts.default')

@section('extraCssLinks')
@stop
@section('backgroundImage')

    <body class="login-wrapper">
        <div class="form_section_bg" style="background-image: url({{ asset('public/frontend/img/sigup_bg.jpg') }});">
        </div>
    @stop

    @section('content')
       
        <section class="form_section">
            <div class="container">
                <div class="outer_form_box">
                    <div class="form_box otp_verification client_form_theme">
                            <h1 class="form_page_title"> {{trans('messages.verify')}} {{trans('messages.Phone Number')}} </h1>
                            <div class="otp_info">
                             {{trans('messages.please_enter_a_4_digit_code_sent_to_your_phone_number')}} 
                                <a href="tel:{{  Session::get('userTypePhoneData') }}" class="otp_info_number">
                                {{  Session::get('userTypePhoneData') }}
                                </a> 
                            </div>
                            
                            <form action="{{ route('check-otp') }}" method="post" class="otpRow " id="otp-form">
                                @csrf
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp1" oninput='digitValidate(this)' onkeyup='tabChange(0)' maxlength=1 >
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp2" oninput='digitValidate(this)' onkeyup='tabChange(1)' maxlength=1 >
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp3" oninput='digitValidate(this)' onkeyup='tabChange(2)' maxlength=1 >
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp4" oninput='digitValidate(this)' onkeyup='tabChange(3)' maxlength=1 >
                                    <input class="@error('fullOtp') is-invalid @enderror" type="hidden">
                                    
                                </form>
                                @if ($errors->has('fullOtp'))
                                    <span class="text-danger text-center"> {{ $errors->first('fullOtp') }}  </span>
                                @endif
                                <button type="submit" class="btn secondary-btn w-100 submit" onclick="document.getElementById('otp-form').submit();">{{trans('messages.verify')}}</button>
                                  
                        <div class="dont_have_ac">
                            <form id="resendOtpForm" action="{{route('verify-mobile')}}" method="post">
                                @csrf
                                <input type="hidden" value="{{  Session::get('userTypePhoneData') }}" name="phone_number">
                                <a href="javascript:void(0)" onclick="myFunction()">  {{trans('messages.resend_otp')}}</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @stop

    <style>
        .tabchange{
            /* margin-left: 22px; */
        }
    </style>

    @section('extraJsLinks')
        <script src="{{ asset('public/frontend/js/drop-down.min.js') }}"></script>
    @stop

    @section('scriptCode')

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
