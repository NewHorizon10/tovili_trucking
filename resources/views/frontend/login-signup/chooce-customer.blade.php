
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
                    <form action="{{route('seleted-customer')}}" method="post" class="client_form_theme">
                        @csrf
                        <h1 class="form_page_title">{{trans('messages.Sign Up')}}</h1>
                        <p class="form_page_subtitle"> {{trans('messages.choose_user_type')}} </p>
                        <div class="userTypeOuterBox">

                            <div class="form-group userTypeBox signUpUser">
                                <label for="privateCustomer"  class="form-label">
                                    <span class="userType_icon">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 0C18.1217 0 20.1566 0.842854 21.6569 2.34315C23.1571 3.84344 24 5.87827 24 8C24 10.1217 23.1571 12.1566 21.6569 13.6569C20.1566 15.1571 18.1217 16 16 16C13.8783 16 11.8434 15.1571 10.3431 13.6569C8.84285 12.1566 8 10.1217 8 8C8 5.87827 8.84285 3.84344 10.3431 2.34315C11.8434 0.842854 13.8783 0 16 0ZM16 20C24.84 20 32 23.58 32 28V32H0V28C0 23.58 7.16 20 16 20Z" fill="white"/>
                                        </svg>
                                    </span>
                                </label>
                                <span class="userTypeTitle"> {{trans('messages.private_customer')}} </span>
                                <input type="radio" class="form-control d-none userTypeInput @error('userType') is-invalid @enderror" name="userType" id="privateCustomer" value="private" aria-describedby="privateCustomer" checked>
                            </div>
                            
                            <div class="form-group userTypeBox">
                                <label for="businessCustomer" class="form-label">
                                    <span class="userType_icon">
                                        <svg width="33" height="36" viewBox="0 0 33 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M33 33.5094V27.8491C33 26.2642 32.7643 24.6792 31.8214 23.0943C30.8786 21.5094 29.7 20.1509 28.05 19.2453C26.4 18.1132 22.8643 17.8868 21.2143 17.8868L17.4429 21.7359L18.8571 24.6792V31.4717L16.5 33.9623L14.1429 31.4717V24.6792L15.7929 21.7359L11.7857 17.8868C9.9 17.8868 6.36429 18.1132 4.71429 19.2453C3.06429 20.1509 2.12143 21.5094 1.17857 23.0943C0.235714 24.6792 0 26.0377 0 27.8491V33.5094C0 33.5094 6.12857 36 16.5 36C26.8714 36 33 33.5094 33 33.5094ZM16.5 0C12.0214 0 9.42857 4.07547 10.1357 8.60378C10.8429 13.1321 13.2 16.3019 16.5 16.3019C19.8 16.3019 22.1571 13.1321 22.8643 8.60378C23.5714 3.84906 20.9786 0 16.5 0Z" fill="white"/>
                                        </svg>
                                    </span>
                                </label>
                                <span class="userTypeTitle" > {{trans('messages.business_customer')}} </span>
                                <input type="radio" class="form-control userTypeInput d-none @error('userType') is-invalid @enderror" name="userType"  id="businessCustomer" value="business"
                                    aria-describedby="businessCustomer">
                            </div>
                        </div>
                            @if ($errors->has('userType'))
                                <span class="text-danger text-center">
                                {{ $errors->first('userType') }}
                            </span>
                               @endif
                               
                        <button type="submit" class="btn secondary-btn w-100 submit">{{trans('messages.next')}}</button>
                        <div class="dont_have_ac"> {{trans('messages.already_have_an_account')}}? 
                            <a href="{{route('login')}}">{{trans('messages.login')}}</a>
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

          $(document).ready(function () {
            $(".userTypeBox").click(function () {
                $(".userTypeBox").removeClass("signUpUser");
                $(this).addClass("signUpUser");
            })
        })

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