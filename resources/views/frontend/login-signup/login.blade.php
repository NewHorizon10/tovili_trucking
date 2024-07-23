@extends('frontend.layouts.default')

@section('extraCssLinks')
@stop
@section('backgroundImage')
<body class="login-wrapper">
    <div class="form_section_bg" style="background-image: url({{asset('public/frontend/img/login-bg.png')}});">
    </div>
@stop
<?php 
$remember_me_box 	= (!empty($_COOKIE["tovilli_login_remember_me"])) ? $_COOKIE["tovilli_login_remember_me"] : "";
$remember_number 	= 	(!empty($_COOKIE["tovilli_login__number"])) ? $_COOKIE["tovilli_login__number"] : "";
$remember_password 	= (!empty($_COOKIE["tovilli_login__password"])) ? $_COOKIE["tovilli_login__password"] : "";
$userNumber=($remember_number!='')?$remember_number:old('phone_number');
?>
@section('content')
    <section class="form_section">
        <div class="container">
            <div class="outer_form_box">
                <div class="form_box">
                    <form action="{{route('login')}}" method="post" class="client_form_theme">
                        @csrf
                        <h1 class="form_page_title">{{trans('messages.login')}}</h1>
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label">{{trans('messages.Phone Number')}}</label>
                            <div class="input-group only_left mb-3">
                                <!-- <div class="input-group-prepend">
                                    <span class="form-control input-group-text form-number">+972</span>
                                </div> -->
                                <input type="text" name="phone_number" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" class="form-control @error('phone_number') is-invalid @enderror" id="exampleInputEmail1" value='{{$userNumber}}' >
                                @if ($errors->has('phone_number'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('phone_number') }} 
                                </div>
                                @endif
                                
                            </div>
                           
                            <!-- <input type="text" name="phone_number" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" class="form-control @error('phone_number') is-invalid @enderror" id="exampleInputEmail1" >
                                @if ($errors->has('phone_number'))
                                 <div class="invalid-feedback">
                                {{ $errors->first('phone_number') }}
                            @endif -->
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword1" class="form-label">{{trans('messages.Password')}}</label>
                            <input type="password" name="password" oninput="validatePassword(this);" onpaste="validatePassword(this);" class="form-control @error('password') is-invalid @enderror" id="exampleInputPassword1" value="{{ $remember_password  }}">
                            @if ($errors->has('password'))
                             <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                              </div>
                            @endif
                        </div>

                        <div class="mb-3 forgot_box">
                            <div class="form-group custom_checkbox d-flex " style="width: auto;">
                                {{-- <input type="checkbox" name="" id="check1" > --}}
                                <input type="checkbox" name="remember_me" id="check1" value="1" {{ $remember_me_box == "1" ? "checked" : "" }} ?>

                                <label for="check1">{{trans('messages.Remember me')}}</label>
                            </div>
                            <a href="{{route('forgot-password')}}" class="forgot_title">
                                <bdi>{{trans('messages.Forgot password')}}?</bdi>
                            </a>
                        </div>

                        <button type="submit" class="btn secondary-btn w-100 submit">{{trans('messages.Let’s Start')}}</button>
                        
                        <div class="dont_have_ac">
                        {{trans('messages.Don’t have an account')}}? <a href="{{route('sign-up')}}"> {{trans('messages.Sign Up')}} </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop

@section('extraJsLinks')
    <script src="{{asset('public/frontend/js/drop-down.min.js')}}"></script>
@stop

@section('scriptCode')
    <script>
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