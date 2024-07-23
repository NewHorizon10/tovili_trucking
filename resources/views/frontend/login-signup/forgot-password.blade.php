
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
                    <form action="{{route('forgot-password')}}" method="post" class="client_form_theme">
                        @csrf
                        <h1 class="form_page_title">{{trans('messages.Forgot password')}}</h1>
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label">{{trans('messages.Phone Number')}}</label>
                            <div class="input-group only_left mb-3">
                                <!-- <div class="input-group-prepend">
                                    <span class="form-control input-group-text form-number">+972</span>
                                </div> -->
                                <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" id="exampleInputEmail1"  oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" >
                                @if ($errors->has('phone_number'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('phone_number') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn secondary-btn w-100 submit">{{trans('messages.next')}}</button>
                        <div class="dont_have_ac">
                        {{trans('messages.back_to')}} <a href="{{route('login')}}">{{trans('messages.login')}} </a>
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