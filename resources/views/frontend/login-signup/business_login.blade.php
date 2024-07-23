
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
                <form action="{{route('customersLogin')}}" method="post" class="client_form_theme">
                    @csrf
                    <h1 class="form_page_title">
                        <a href="#" class="back_btn">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 16L0 8L8 0L9.425 1.4L3.825 7H16V9H3.825L9.425 14.6L8 16Z" fill="white"/>
                            </svg>
                         </a>
                          <span class="">{{trans('messages.Sign Up')}}</span> </h1>
                        <p class="form_page_subtitle"> 
                        <span class="user-ac-icon"> 
                            <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.5 0C5.09674 0 5.66903 0.237053 6.09099 0.65901C6.51295 1.08097 6.75 1.65326 6.75 2.25C6.75 2.84674 6.51295 3.41903 6.09099 3.84099C5.66903 4.26295 5.09674 4.5 4.5 4.5C3.90326 4.5 3.33097 4.26295 2.90901 3.84099C2.48705 3.41903 2.25 2.84674 2.25 2.25C2.25 1.65326 2.48705 1.08097 2.90901 0.65901C3.33097 0.237053 3.90326 0 4.5 0ZM4.5 5.625C6.98625 5.625 9 6.63188 9 7.875V9H0V7.875C0 6.63188 2.01375 5.625 4.5 5.625Z" fill="white"/>
                            </svg>
                        </span>
                        Private Customer</p>
                    <div class="form-group">
                        <label for="exampleInputEmail1" class="form-label">{{trans('messages.Phone Number')}}</label>
                        <div class="input-group only_left mb-3">
                            <!-- <div class="input-group-prepend">
                                <span class="form-control input-group-text form-number">+972</span>
                            </div> -->
                            <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" id="exampleInputEmail1"
                            aria-describedby="exampleInputEmail1" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                            @if ($errors->has('phone_number'))
                            <span class="text-danger text-center">
                                {{ $errors->first('phone_number') }}
                            </span>
                            @endif
                        </div>
                       
                          
                    </div>

                    <button type="submit" class="btn secondary-btn w-100 submit">{{trans('messages.next')}}</button>
                    <div class="dont_have_ac">
                    {{trans('messages.already_have_an_account')}}? <a href="login.html"> {{trans('messages.login')}}</a>
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