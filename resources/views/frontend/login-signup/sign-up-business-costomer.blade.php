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
                <div class="form_box business_page">
                    <div class="client_form_theme">
                        <h1 class="form_page_title">
                            <a href="#" class="back_btn">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 16L0 8L8 0L9.425 1.4L3.825 7H16V9H3.825L9.425 14.6L8 16Z" fill="white" />
                                </svg>
                            </a>
                            <span class="">{{trans('messages.Sign Up')}}</span>
                        </h1>
                        <p class="form_page_subtitle">
                            <span class="user-ac-icon">
                                <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 10.239V8.50943C10 8.02516 9.92857 7.54088 9.64286 7.0566C9.35714 6.57233 9 6.15723 8.5 5.8805C8 5.53459 6.92857 5.46541 6.42857 5.46541L5.28571 6.64151L5.71429 7.54088V9.61635L5 10.3774L4.28571 9.61635V7.54088L4.78571 6.64151L3.57143 5.46541C3 5.46541 1.92857 5.53459 1.42857 5.8805C0.928571 6.15723 0.642857 6.57233 0.357143 7.0566C0.0714286 7.54088 0 7.95597 0 8.50943V10.239C0 10.239 1.85714 11 5 11C8.14286 11 10 10.239 10 10.239ZM5 0C3.64286 0 2.85714 1.24528 3.07143 2.62893C3.28571 4.01258 4 4.98113 5 4.98113C6 4.98113 6.71429 4.01258 6.92857 2.62893C7.14286 1.1761 6.35714 0 5 0Z" fill="white" />
                                </svg>
                            </span>
                            {{trans('messages.business_customer')}}
                        </p>
                        <ul class="nav nav-pills form_list_nav mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item form_list_item" role="presentation">
                                <button class="nav-link form_list_link tab-active-1 active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true" onclick="nextStepNumber=1">{{trans('messages.Company Info')}}</button>
                            </li>
                            <li class="nav-item form_list_item" role="presentation">
                                <button class="nav-link form_list_link tab-active-2" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" onclick="nextStepNumber=2">{{trans('messages.Contact Person Info')}}</button>
                            </li>
                        </ul>
                        <form method="post" id="sign-up-business-costomer-form" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active form_list_tab" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_type" class="form-label">{{trans('messages.company type')}}</label>
                                                <select id="company_type" class="form-select @error('company_type') is-invalid @enderror 1-form-fields" data-is-required="1" aria-label="Default select example" name="company_type">
                                                    <option value="">{{trans('messages.Select')}}</option>
                                                    @foreach($companyType as $row)
                                                        <option value="{{$row->id}}" {{old('company_type') == $row->id ? 'selected' : '' }} >{{$row->lookupDiscriptionList[0]->code}}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback error-company_type">{{ $errors->first('company_type') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name" class="form-label">{{trans('messages.Company Name')}}</label>
                                                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror 1-form-fields" data-is-required="1" id="company_name" aria-describedby="company_name" value="{{old('company_name')}}">
                                                <div class="invalid-feedback error-company_name">{{ $errors->first('company_name') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <span class="form-label d-block">{{trans('messages.Company Logo')}}</span>
                                                <div class="logo_select_box">
                                                    <input type="file" accept="image/png, image/gif, image/jpeg" class="input-file form-control d-none @error('company_logo') is-invalid @enderror 1-form-fields" data-is-required="1" id="company_logo" name="company_logo" aria-describedby="company_logo">
                                                    <label for="company_logo" class="logo_select_input form-control js-labelFile">
                                                        <span class="js-fileName"></span>
                                                        <span class="browseFile">{{trans('messages.Browse file')}}</span>
                                                    </label>
                                                    <div class="invalid-feedback error-company_logo">{{ $errors->first('company_logo') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_hp_number" class="form-label">{{trans('messages.company_number')}} (H.P.)</label>
                                                <div class="input-group only_left mb-3">
                                                    <!-- <div class="input-group-prepend">
                                                        <span class="form-control input-group-text form-number">+972</span>
                                                    </div> -->
                                                    <input type="text"   class="form-control @error('company_hp_number') is-invalid @enderror 1-form-fields" data-is-required="1"  id="company_hp_number" oninput="validateOnlyNumber(this);" onpaste="validateOnlyNumber(this);" aria-describedby="company_hp_number" name="company_hp_number" value="{{old('company_hp_number')}}">
                                                    <div class="invalid-feedback error-company_hp_number">{{ $errors->first('company_hp_number') }}</div>
                                                </div>
                                                
                                            </div>
                                        </div>



                                          
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="company_location" class="form-label">{{trans('messages.Company Location')}}</label>
                                                <input type="text" class="form-control @error('company_location') is-invalid @enderror 1-form-fields" data-is-required="1" id="company_location" name="company_location" aria-describedby="company_location" value="{{old('company_location')}}">
                                                <div class="invalid-feedback error-company_location">{{ $errors->first('company_location') }}</div>
                                                <input type="hidden" name="current_lat" id="current_lat">
                                                <input type="hidden" name="current_lng" id="current_lng">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade form_list_tab" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <span class="form-label d-block">{{trans('messages.Contact Person Picture')}}</span>
                                                <div class="logo_select_box">
                                                    <input type="file" accept="image/png, image/gif, image/jpeg" class="input-file form-control d-none @error('contact_person_picture') is-invalid @enderror 2-form-fields" data-is-required="1" name="contact_person_picture" id="logo_select_input2" aria-describedby="logo_select_input2">
                                                    <label for="logo_select_input2" class="logo_select_input form-control js-labelFile">
                                                        <span class="js-fileName"></span>
                                                        <span class="browseFile">{{trans('messages.Browse file')}}</span>
                                                    </label>
                                                    <div class="invalid-feedback error-contact_person_picture">{{ $errors->first('contact_person_picture') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail5" class="form-label">{{trans('messages.Contact Person Name')}}</label>
                                                <input type="text" class="form-control @error('contact_person_name') is-invalid @enderror 2-form-fields" data-is-required="1" id="exampleInputEmail5" name="contact_person_name" aria-describedby="exampleInputEmail5" value="{{old('contact_person_name')}}">
                                                <div class="invalid-feedback error-contact_person_name">{{ $errors->first('contact_person_name') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail6" class="form-label">{{trans('messages.Contact Person Phone Number')}}</label>
                                                <div class="input-group only_left mb-3">
                                                    <!-- <div class="input-group-prepend">
                                                        <span class="form-control input-group-text form-number">+972</span>
                                                    </div> -->
                                                    <input type="text"  oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" class="form-control @error('contact_person_phone_number') is-invalid @enderror 2-form-fields" data-is-required="1" data-type="number" id="exampleInputEmail6" name="contact_person_phone_number" aria-describedby="exampleInputEmail6" value="{{old('contact_person_phone_number')}}">
                                                    <div class="invalid-feedback error-contact_person_phone_number">{{ $errors->first('contact_person_phone_number') }}</div>
                                                </div>
                                                
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail7" class="form-label">{{trans('messages.Contact Person Email')}}</label>
                                                <input type="email" class="form-control @error('contact_person_email') is-invalid @enderror 2-form-fields" data-is-required="1" data-type="email" id="exampleInputEmail7" name="contact_person_email" aria-describedby="exampleInputEmail7" value="{{old('contact_person_email')}}">
                                                <div class="invalid-feedback error-contact_person_email">{{ $errors->first('contact_person_email') }}</div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-12">
                                            <div class="mb-3 forgot_box">
                                                <div class="form-group custom_checkbox d-flex ">
                                                    <input type="checkbox" name="check2" id="check2" checked="">
                                                    <label for="check2">I accept terms & conditions</label>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn secondary-btn w-100 submit">Next</button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 forgot_box">
                                    <div class="form-group custom_checkbox d-flex ">
                                    <input type="checkbox" name="check1" id="check1" class="1-form-fields" required>
                                        <label for="check1">{{trans('messages.I accept')}} 
                                            <a href="{{route('home.term-condition')}}" target="_blank">{{trans('messages.Terms & Conditions')}}</a>
                                            <div class="invalid-feedback error-check1" style="display: block;">{{ $errors->first('contact_person_email') }}</div>
                                        </label>
                                    </div>

                                </div>
                                <button type="button" class="btn secondary-btn w-100 submit next-step-btn">{{trans('messages.next')}}</button>
                            </div>
                        </form>
                        <div class="dont_have_ac">
                        {{trans('messages.already_have_an_account')}}? <a href="{{route('login')}}"> {{trans('messages.login')}} </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @stop

    @section('extraJsLinks')
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"> </script> 

    <script src="{{ asset('public/frontend/js/drop-down.min.js') }}"></script>
    @stop

    @section('scriptCode')
    <script>

        // function initMap() {
        //     var ac = new google.maps.places.Autocomplete(document.getElementById('company_location'), {
        //         types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
        //     });
        //     ac.addListener('place_changed', () => {
        //         var place = ac.getPlace();

        //         // Extract the address components
        //         var addressComponents = place.address_components;
        //         var city, state;

        //         var lat = place.geometry.location.lat();

        //         var lng = place.geometry.location.lng();

        //         // $("#dropoff_city").val(city);
        //         $("#current_lat").val(lat);
        //         $("#current_lng").val(lng);

            
        //     });

        //     var ac2 = new google.maps.places.Autocomplete(document.getElementById('company_location'), {
        //         types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
        //     });

        //     ac2.addListener('place_changed', () => {
        //         var place = ac2.getPlace();

        //         var addressComponents = place.address_components;
        //         var city2, state2;

        //         var lat2 = place.geometry.location.lat();
        //         var lng2 = place.geometry.location.lng();

        //         $("#current_lat").val(lat2);
        //         $("#current_lng").val(lng2);
            
        //     });
        // }


        function initMap(latitude,longitude){
           var ac = new google.maps.places.Autocomplete(document.getElementById('company_location'));
           if((latitude == undefined || longitude == undefined) || (latitude == null || longitude == null)){
               var latitude = "26.8289443";
               var longitude = "75.8056178";  
           }
           ac.addListener('place_changed', () => {
                   $('#map').show()
                   var place = ac.getPlace();
                   const latitude = place.geometry.location.lat();
                   const longitude = place.geometry.location.lng();
                   $('#current_lat').val(latitude)
                   $('#current_lng').val(longitude)
                   var latLng = new google.maps.LatLng(latitude,longitude);
                   var mapOptions = {
                   center: latLng,
                   zoom: 10,
                   zoomControl:true,
                   scrollwheel:true,
                   disableDoubleClickZoom:true,
                   mapTypeId: google.maps.MapTypeId.ROADMAP
                   };
                   var map = new google.maps.Map(document.getElementById("map"), mapOptions);
                   var marker = new google.maps.Marker({
                   position: latLng,
                   map: map,
                   title: "Location Marker"
               });
           });
           var latLng = new google.maps.LatLng(latitude,longitude);
           var mapOptions = {
           center: latLng,
           zoom: 10,
           mapTypeId: google.maps.MapTypeId.ROADMAP
           };
           var map = new google.maps.Map(document.getElementById("map"), mapOptions);
           var marker = new google.maps.Marker({
               position: latLng,
               map: map,
               title: "Location Marker"
           });
        }

         (function () {

            'use strict';

            $('.input-file').each(function () {
                var $input = $(this),
                    $label = $input.next('.js-labelFile'),
                    labelVal = $label.html();

                $input.on('change', function (element) {
                    var fileName = '';
                    if (element.target.value) fileName = element.target.value.split('\\').pop();
                    fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
                });
            });

        })();
        $(document).ready(function() {
            $(".userTypeBox").click(function() {
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
        var nextStepNumber = 1;
        $("body").on("click", ".next-step-btn", function() {
            $(".invalid-feedback").html("");
            $(".is-invalid").removeClass("is-invalid");
            if(nextStepNumber == 2 ){
                var flag = $.fn.checkValidation(1);
                if(flag){
                    $('input[name="check1"]').removeClass("2-form-fields").addClass("1-form-fields")
                    $(".tab-active-"+(1)).click();
                    nextStepNumber = 1;
                }
            }
            var flag = $.fn.checkValidation(nextStepNumber);
            if (flag) {
                $(".is-invalid:first").focus();
                return false;
            } else {
                if (nextStepNumber == 2) {
                    $( "#sign-up-business-costomer-form" ).trigger( "submit" );
                    return true;
                }else if(nextStepNumber == 1){
                    $('input[name="check1"]').removeClass("1-form-fields").addClass("2-form-fields")

                }

                $(".tab-active-"+(nextStepNumber+1)).click()
                $(window).scrollTop(0);
            }
        });
        // 

        $("body").on("click", ".tab-active-1", function() {
            $('input[name="check1"]').removeClass("2-form-fields").addClass("1-form-fields")
        });

        $("body").on("click", ".tab-active-2", function() {
            $('input[name="check1"]').removeClass("1-form-fields").addClass("2-form-fields")
        });
        $.fn.checkValidation = function(nextStepNumber) {
            // alert(nextStepNumber)
            // return false;
            var flag = false;
            $("." + nextStepNumber + "-form-fields").each(function() {
                if ($(this).attr("data-type") == "same") {
                    if ($(this).val() != $("input[name='" + $(this).attr("data-same-with") + "']").val()) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("{{trans('messages.Its value must be the same as the value of')}} " + ($("input[name='" + $(this).attr("data-same-with") + "']").attr("data-name")));
                        $(this).addClass("is-invalid");

                    }
                }
                if (($(this).val() == "" && $(this).attr("data-is-required") == "1") || ($(this).attr('type') == "checkbox" && !$(this).is(':checked')) || ($(this).attr('type') == "radio" && !$("input[name='"+$(this).attr('name')+"']").is(':checked'))) {
                    flag = true;
                    var str_name = $(this).attr("name");
                    str_name = str_name.replace('[', '').replace(']', '');
                    $(".error-" + str_name).html("{{trans('messages.This field is required')}}");
                    $(this).addClass("is-invalid");
                }
                if ($(this).val() != "" && $(this).attr("data-type") == "number") {
                    // var regEx = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
                    var regEx = /^0\d{9}$/;
                    var val = $(this).val();
                    if (!val.match(regEx)) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("{{trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0')}}");
                        $(this).addClass("is-invalid");
                    }
                }
                // if ($(this).val() != "" && $(this).attr("data-type") == "number") {
                //     var regEx = /^[0-9]{10}$/;
                //     var newValue = input.value.replace(/[^0-9]/g, '');
                //     var val = $(this).val();
                //     if (!val.match(regEx)) {
                //         flag = true;
                //         $(".error-" + $(this).attr("name")).html("{{trans('messages.Phone number should be 10 digits')}}");
                //         $(this).addClass("is-invalid");
                //     }
                // }

                if ($(this).val() !== "" && $(this).attr("data-type") === "number") {
                    var regEx = /^[0-9]{1}$/;
                    var newValue = $(this).val().replace(/[^0-9]/g, '');

                    if (!regEx.test(newValue) && newValue.charAt(0) !== '0') {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("{{trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0')}}");
                        $(this).addClass("is-invalid");
                    }
                }

                if ($(this).val() != "" && $(this).attr("data-type") == "email") {
                    var regEx = /^\w+([-+.'][^\s]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
                    var val = $(this).val();
                    if (!val.match(regEx)) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("{{trans('messages.The email must be a valid email address')}}");
                        $(this).addClass("is-invalid");
                    }
                }
                if ($(this).val() != "" && $(this).attr("data-type") == "url") {
                    var regEx = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;
                    var val = $(this).val();
                    if (!val.match(regEx)) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("{{trans('messages.Invalid URL number')}}");
                    }
                }
                if ($(this).val() != "" && $(this).attr("data-type") == "pincode") {
                    var regEx = /\b\d{6}\b/g;
                    var val = $(this).val();
                    if (!val.match(regEx)) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("{{trans('messages.Invalid pin code number')}}");
                    }
                }
                if ($(this).val() != "" && $(this).attr("data-type") == "to_from") {
                    var val = $(this).val();
                    var toNum = parseInt($(this).attr("min"));
                    var fromNum = parseInt($(this).attr("max"));
                    if (!(val >= toNum && val <= fromNum)) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("{{trans('messages.numbers_must_be_between_1_to_20')}}");
                        $(this).addClass("is-invalid");
                    }
                }
            });
            return flag;
        }

    </script>
    @stop