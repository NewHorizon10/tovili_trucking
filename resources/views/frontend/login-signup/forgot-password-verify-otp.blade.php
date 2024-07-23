@extends('frontend.layouts.default')

@section('extraCssLinks')
@stop
@section('backgroundImage')

    <body class="login-wrapper">
        <div class="form_section_bg" style="background-image: url({{ asset('public/frontend/img/login-bg.png') }});">
        </div>
    @stop

    @section('content')
       
        <section class="form_section">
            <div class="container">
                <div class="outer_form_box">
                    <div class="form_box otp_verification client_form_theme">
                        <h1 class="form_page_title">{{trans('messages.verify')}} {{trans('messages.Phone Number')}} </h1>
                        <div class="otp_info">
                        {{trans('messages.please_enter_a_4_digit_code_sent_to_your_phone_number')}} 
                            <a href="#" class="otp_info_number">{{ $validUser->phone_number ?? '' }}</a> 
                        </div>
                                <form action="{{ route('forgot-password-verify-otp', $validUser->forgot_password_validate_string) }}" method="post" class="otpRow" id="otp-form">
                                    @csrf
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp1" oninput='digitValidate(this)' onkeyup='tabChange(0)' maxlength='1' >
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp2" oninput='digitValidate(this)' onkeyup='tabChange(1)' maxlength='1' >
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp3" oninput='digitValidate(this)' onkeyup='tabChange(2)' maxlength='1' >
                                    <input class="otp form-control dark-form-control tabchange" type="text" name="otp4" oninput='digitValidate(this)' onkeyup='tabChange(3)' maxlength='1' >
                                    
                                </form>
                                <div class="invalid-feedback d-block otpError"></div>

                                @if ($errors->has('fullOtp')) 
                                <input class="@error('fullOtp') is-invalid @enderror" type="hidden">
                                <div class="invalid-feedback"> {{ $errors->first('fullOtp') }}</div>
                                @endif

                                {{-- onclick="document.getElementById('otp-form').submit();" --}}
                                <button type="button" class="btn secondary-btn w-100 submit submitButton" >{{trans('messages.verify')}}</button>
                                <div class="dont_have_ac">
                                    <form action="{{route('forgot-password')}}" method="post" id="resendOtpForm">
                                        @csrf
                                         <input type="hidden" name="phone_number" value="{{$validUser->phone_number ?? ''}}">
                                    </form>
                                    <a href="javascript:void(0);" id="resendOtpFormButton"> {{trans('messages.resend_otp')}}</a>
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

    $(document).ready(function(){
        $(".submitButton").on('click', function(event){
            event.preventDefault();
            let isValid = true;

            $(".otp").each(function(){
                if ($(this).val() === '') {
                    isValid = false;

                    $(".otpError").html('{{trans('messages.this_otp_field_is_required')}}');
                } else {
                    $(".otpError").html('');
                }
            });
            
            if (isValid) {
                $("#otp-form").submit();
            }

        });
    });


$("#resendOtpFormButton").on("click", function(){
    $("#resendOtpForm").submit();
});

        let digitValidate = function(ele){
            ele.value = ele.value.replace(/[^0-9]/g,'');
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
