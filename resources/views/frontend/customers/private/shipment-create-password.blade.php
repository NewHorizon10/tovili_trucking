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
                <div class="form_box client_form_theme form_box">
                    <h1 class="form_page_title">{{trans('messages.create_your_password')}}   </h1>
                    <form action="{{route('private.shipment-create-password')}}" method="post" class="client_form_theme">
                        @csrf
                        <div class="form-group">
                            <label  class="form-label">{{trans('messages.Password')}}</label>
                            <div class="password_box">
                                <input type="password" oninput="validatePassword(this);" onpaste="validatePassword(this);" name="new_password" class="form-control id_password_icon ">
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
                            <label  class="form-label">{{trans('messages.confirm_password')}}</label>
                            <div class="password_box">
                                <input type="password" oninput="validatePassword(this);" onpaste="validatePassword(this);" name="confirm_password" class="form-control id_password_icon2">
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

                        <button type="submit" class="btn secondary-btn w-100 submit">{{trans('messages.next')}}</button>
                    </form>
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
            ele[val-1].focus();
            ele[val-1].select();
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
