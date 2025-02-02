@extends('frontend.layouts.default')

@section('extraCssLinks')
@stop
@section('backgroundImage')
<body class="login-wrapper">
    <div class="form_section_bg" style="background-image: url({{asset('public/frontend/img/sigup_bg.jpg')}});">
    </div>
@stop

@section('content')

<section class="form_section">
        <div class="container">
            <div class="outer_form_box">
                <div class="form_box">
                    <form action="{{route('create-password')}}" method="post" class="client_form_theme">
                        @csrf
                        <h1 class="form_page_title">{{trans('messages.created_pasword')}} </h1> 
                        
                        <div class="form-group">
                            <label  class="form-label">{{trans('messages.Password')}}</label>
                            <div class="password_box">
                                <input type="password"  name="new_password" class="form-control id_password_icon" oninput="validatePassword(this);" onpaste="validatePassword(this);">
                                <span>
                                    <i onclick="showPassword(0)" class="far fa-eye togglePassword" style="margin-left: -30px; cursor: pointer;"></i>
                                </span>
                            </span>
                            @if ($errors->has('new_password'))
                            <div class="text-danger">
                               {{ $errors->first('new_password') }}
                             </div>
                           @endif
                            </div>
                        </div>
                        <div class="form-group ">
                            <label  class="form-label"> {{trans('messages.confirm_password')}}</label>
                            <div class="password_box">
                                <input type="password"   name="confirm_password" class="form-control id_password_icon2" oninput="validatePassword(this);" onpaste="validatePassword(this);">
                                <span>
                                    <i onclick="showPassword(1)" class="far fa-eye togglePassword2" style="margin-left: -30px; cursor: pointer;"></i>
                                </span>
                                @if ($errors->has('confirm_password'))
                                <div class="text-danger">
                                   {{ $errors->first('confirm_password') }}
                                 </div>
                               @endif
                            </div>
                        </div>
                        <button type="submit" class="btn secondary-btn w-100 submit">{{trans('messages.submit')}}</button>
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
        function showPassword(type){
            if(type == 0){
                var passwordInput = $(".id_password_icon");
  
                if (passwordInput.attr("type") === "password") {
                    passwordInput.attr("type", "text");
                } else {
                    passwordInput.attr("type", "password");
                }

            }else{
                var passwordInput = $(".id_password_icon2");
  
                if (passwordInput.attr("type") === "password") {
                    passwordInput.attr("type", "text");
                } else {
                    passwordInput.attr("type", "password");
                }
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