@extends('frontend.layouts.default')

@section('extraCssLinks')
@stop
@section('backgroundImage')
<body class="login-wrapper">
    <div class="form_section_bg" style="background-image: url({{asset('public/frontend/img/login-bg.png')}});">
    </div>
@stop

@section('content')

<section class="form_section">
        <div class="container">
            <div class="outer_form_box">
                <div class="form_box">
                    <form action="{{route('customersCreateNewPassword')}}" method="post" class="client_form_theme">
                        @csrf
                        <h1 class="form_page_title">Create New Password</h1> 
                        {{  Session::get('userTypeOtpData') }}    {{  Session::get('userTypeData') }}
                        <div class="form-group">
                            <label  class="form-label">New Password</label>
                            <div class="password_box">
                                <input type="password" name="new_password" class="form-control id_password_icon @error('new_password') is-invalid @enderror" oninput="validatePassword(this);" onpaste="validatePassword(this);">
                                <span>
                                    <i class="far fa-eye togglePassword" style="margin-left: -30px; cursor: pointer;"></i>
                                </span>
                            </span>
                            @if ($errors->has('new_password'))
                            <div class="invalid-feedback">
                               {{ $errors->first('new_password') }}
                             </div>
                           @endif
                            </div>
                        </div>
                        <div class="form-group ">
                            <label  class="form-label">Confirm New Password</label>
                            <div class="password_box">
                                <input type="password" name="confirm_password" class="form-control id_password_icon2 @error('confirm_password') is-invalid @enderror" oninput="validatePassword(this);" onpaste="validatePassword(this);">
                                <span>
                                    <i class="far fa-eye togglePassword2" style="margin-left: -30px; cursor: pointer;"></i>
                                </span>
                                @if ($errors->has('confirm_password'))
                                <div class="invalid-feedback">
                                   {{ $errors->first('confirm_password') }}
                                 </div>
                               @endif
                            </div>
                        </div>
                        <button type="submit" class="btn secondary-btn w-100 submit">Update Password</button>
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