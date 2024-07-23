@extends('frontend.layouts.default')
@section('extraCssLinks')
@stop
@section('backgroundImage')
<body class="ogin-wrapper driver_page">
    
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dropzone.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dashboard-dropzone.css')}}">
@stop
@section('content')
    <section class="form_section">
        <div class="container">
            <div class="customer_verification_box">
            <div class="form_box otp_verification client_form_theme">
                <h1 class="form_page_title">{{trans('messages.verify_phone_number')}}   </h1>
                <div class="otp_info">
                    {{trans('messages.please_enter_a_4_digit_code_sent_to_your_phone_number')}} <a href="#" class="otp_info_number">{{  Session::get('userTypePhoneData') }}</a> 
                </div>
                <form class="otpRow" method="post" id="otp-form" action="{{ route('private.check.shipment.otp') }}">
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
                <button type="button" class="btn secondary-btn w-100 submit" onclick="document.getElementById('otp-form').submit();">Verify</button>
                <div class="dont_have_ac">
                        <a href="{{ route('private.shipment.verify.mobile','resend_otp=resend') }}">{{trans('messages.didnt_get_a_code_resending')}}</a>
                </div>
            </div>
            </div>
        </div>
    </section>
@stop

@section('scriptCode')
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"> </script> 
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script>
   
   $(document).ready(function(){

        function initMap() {
            var ac = new google.maps.places.Autocomplete(document.getElementById('dropoff_city'), {
                types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
            });
            ac.addListener('place_changed', () => {
                var place = ac.getPlace();
                console.log(place);

                // Extract the address components
                var addressComponents = place.address_components;
                var city, state, zipCode;

                // Loop through the address components and find the city, state, and zip code
                for (var i = 0; i < addressComponents.length; i++) {
                    var component = addressComponents[i];
                    var componentTypes = component.types;

                    // Check if the component is a city
                    if (componentTypes.includes('locality')) {
                        city = component.long_name;
                    }

                    // Check if the component is a state
                    if (componentTypes.includes('administrative_area_level_1')) {
                        state = component.long_name;
                    }

                    // Check if the component is a zip code
                    if (componentTypes.includes('postal_code')) {
                        zipCode = component.long_name;
                    }
                }

                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();

                // $("#dropoff_city").val(city);
                $("#dropoff_zip_code").val(zipCode);
                $("#dropoff_latitude").val(lat);
                $("#dropoff_longitude").val(lng);

            
            });

            var ac2 = new google.maps.places.Autocomplete(document.getElementById('company_city'), {
                types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
            });

            ac2.addListener('place_changed', () => {
                var place = ac2.getPlace();

                // Extract the address components
                var addressComponents = place.address_components;
                var city2, state2, zipCode2;

                // Loop through the address components and find the city, state, and zip code
                for (var i = 0; i < addressComponents.length; i++) {
                    var component = addressComponents[i];
                    var componentTypes = component.types;

                    // Check if the component is a city
                    if (componentTypes.includes('locality')) {
                        city2 = component.long_name;
                    }

                    // Check if the component is a state
                    if (componentTypes.includes('administrative_area_level_1')) {
                        state2 = component.long_name;
                    }

                    // Check if the component is a zip code
                    if (componentTypes.includes('postal_code')) {
                        zipCode2 = component.long_name;
                    }
                }

                var lat2 = place.geometry.location.lat();
                var lat2 = place.geometry.location.lng();

                // $("#company_city").val(city2);
                $("#company_zip_code").val(zipCode2);
                $("#company_latitude").val(lat2);
                $("#company_longitude").val(lat2);

            
            });
        }
    });

        let digitValidate = function(ele){
            console.log(ele.value);
            ele.value = ele.value.replace(/[^0-9]/g,'');
            }

        let tabChange = function(val){
            let ele = $('.tabchange');
            if(ele[val].value != ''){
            ele[val+1].focus();
            }else if(ele[val].value == ''){
            ele[val-1]-focus();
            ele[val-1]-select();
            }    
        }


        // Custom Dropdown
        function showMe(evt) {
            console.log("evt.value ", evt.value);
        }

        function makeDd() {
            'use strict';
            let json = new Function(`return ${document.getElementById('json_data').innerHTML}`)();
            /*  new MsDropdown("#json_dropdown", {
                  byJson: {
                      data: json, selectedIndex:1
                  }
              })*/
            MsDropdown.make("#json_dropdown", {
                byJson: {
                    data: json,
                    selectedIndex: 0
                }
            });
        }

    </script>
@stop
