@extends('frontend.layouts.truckCompanyLayout')
@section('extraCssLinks')
@stop
@section('content')
<section class="form_section">
    <div class="container">
        <div class="outer_companyform_box">
            <div class="track_company_box track_company_page">
                <div class="white_form_theme">
                    <h1 class="form_page_title">
                        <span class="">{{trans('messages.Account Registration') }}</span>
                    </h1>
                    <div class="stepsProgressBar">
                      <ul class="list-unstyled multi-steps">
                        <li id="step-1" class="is-active">
                         <!--    <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div> -->
                        </li>
                        <li id="step-2" class="">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        <li id="step-3" class="">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        <li id="step-4" class="">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        <li id="step-5">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li>
                        {{-- <li id="step-6">
                            <div class="progress-bar progress-bar--success">
                            <div class="progress-bar__bar"></div>
                        </li> --}}
                      </ul>
                    </div>
                    <!-- <button onClick=next()>Next Step</button> -->
                    <p class="company_page_subtitle">{{trans('messages.Company Details') }}</p>
                    <div class="companyFormBox">
                        <form method="post" id="truckRegistrationstep2" action="{{ route('truckRegistrationstep2')}}" >
                            <div id="myForm" class="row">          
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputname" class="form-label">{{trans('messages.company')}} {{trans('messages.name')}}</label> <span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <input placeholder="{{trans('messages.company')}} {{trans('messages.name')}}" type="text" class="form-control" name="company_name" id="exampleInputname"
                                            aria-describedby="exampleInputname">
                                            <small class="text-danger"></small>
                                    </div>
                                </div>                          
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputemail" class="form-label">{{trans('messages.company')}} {{trans('messages.email')}}</label>
                                        <input type="email" placeholder="{{trans('messages.company')}} {{trans('messages.email')}}" class="form-control" name="company_email" id="exampleInputemail"
                                            aria-describedby="exampleInputemail">
                                            <small class="text-danger"></small>
                                    </div>
                                </div>                       -->
                                
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputemailType" class="form-label">{{trans('messages.company')}} {{trans('messages.type')}}</label>
                                        <select class="form-select" name="Company_type" id="exampleInputemailType"
                                            aria-describedby="exampleInputemailType">
                                            @foreach($companyType as $row)
                                                <option value="{{$row->id}}">{{$row->lookupDiscription->code}}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>                       -->

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputaddress" class="form-label">{{trans('messages.company')}} {{trans('messages.Address')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <div class="withIconInput">
                                            <svg class="useIcon" width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z" fill="#1535B9"/>
                                            </svg>
                                            <input type="hidden" id="lat" name="lat" value="" aria-describedby="lat_gLocation" class="1-form-fields" data-type="gLocation" data-target-name="company_address" >
                                            <input type="hidden" id="lng" name="lng" value="" aria-describedby="lng_gLocation" class="1-form-fields" data-type="gLocation" data-target-name="company_address" >
                                            <input placeholder="{{trans('messages.company')}} {{trans('messages.Address')}}" type="text" name="company_address" class="form-control" id="exampleInputaddress" value="" aria-describedby="exampleInputaddress">
                                        </div>
                                        <small class="text-danger"></small>
                                        <div class="my-3" id="map"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputnumber" class="form-label">{{trans('messages.company_number')}} (H.P.)</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <div class="input-group only_left mb-3" style="margin-bottom: 0px !important;">
                                            <!-- <div class="input-group-prepend">
                                                <span class="form-control input-group-text form-number">+972</span>
                                            </div> -->
                                            <input type="text" placeholder="{{trans('messages.company_number')}} (H.P.)" name="company_number"  class="form-control" id="exampleInputnumber" aria-describedby="exampleInputnumber" oninput="validateOnlyNumber(this);" onpaste="validateOnlyNumber(this);">
                                        </div>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>       
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControldesc" class="form-label">{{trans('messages.company')}} {{trans('messages.description')}}
                                        <br>
                                        <small class="text-warning p-0 m-0">{{trans("messages.update_details_in_profile_later")}}</small>
                                        </label>
                                        <textarea class="form-control" placeholder="{{trans('messages.company')}} {{trans('messages.description')}}" name="company_description" id="exampleFormControldesc" rows="3" placeholder="Description here.."></textarea>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>     
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlTerms" class="form-label">{{trans('messages.Terms & Conditions')}} </label>
                                        <textarea class="form-control" placeholder="{{trans('messages.Terms & Conditions')}} " name="company_terms" id="exampleFormControlTerms" rows="3"></textarea>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>     
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="picture__input" class="form-label"> {{trans('messages.Company Logo')}}
                                        <br>
                                        <small class="text-warning p-0 m-0">{{trans("messages.update_details_in_profile_later")}}</small>
                                        </label>
                                        <label class="picture" for="picture__input" tabIndex="0">
                                        <span class="picture__image">
                                            <svg width="28" height="34" viewBox="0 0 28 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.333 25.5839H20.1114V13.8411H27.962L14.2222 0.142822L0.482422 13.8411H8.333V25.5839ZM0.482422 29.4973H27.9646V33.4107H0.482422V29.4973Z" fill="#728BF2"/>
                                            </svg>
                                            </span>
                                        </label>
                                        
                                        <input type="file" name="picture__input" accept=".png,.jpg,.jpeg" id="picture__input">
                                        <small class="text-danger"></small>
                                    </div>
                                </div>   
                                <div class="col-md-6">
                                    <!-- <div class="form-group">
                                        <label for="exampleInputRefueling" class="form-label">{{trans('messages.refueling_method')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <input type="text" name="ContactRefueling" class="form-control"
                                            id="exampleInputRefueling"
                                            aria-describedby="exampleInputRefueling">
                                            <small class="text-danger"></small>
                                    </div> -->

                                    <div class="form-group">
                                        <label for="exampleInputRefueling" class="form-label">{{trans('messages.select_refueling_method')}}
                                        <br>
                                        <small class="text-warning p-0 m-0">{{trans("messages.update_details_in_profile_later")}}</small>
                                        </label><span class="text-dangers" style="color: #dc3545!important;">  </span>
                                        <select id="exampleInputRefueling" class="form-select" data-is-required="1" aria-label="Default select example" name="ContactRefueling" aria-describedby="exampleInputRefueling">
                                            <option value="">{{trans('messages.select_refueling_method')}}</option>
                                            @foreach($fuelingType as $row)
                                                <option value="{{$row->id}}">{{$row->lookupDiscription->code}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback error-fueling-methods">{{ $errors->first('fueling-methods') }}</div>
                                    </div>
                                </div>                          
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputTidaluk" class="form-label"> {{trans('messages.tidaluk')}} {{trans('messages.company')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span> -->
                                        <!-- <input type="text" name="ContactTidaluk" class="form-control" id="exampleInputTidaluk"
                                            aria-describedby="exampleInputTidaluk">
                                            <small class="text-danger"></small>-->
                                    <!-- </div>
                                </div>    -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputTidaluk" class="form-label"> {{trans('messages.select_tidaluk_company')}}
                                        <br>
                                        <small class="text-warning p-0 m-0">{{trans("messages.update_details_in_profile_later")}}</small>
                                        </label><span class="text-dangers" style="color: #dc3545!important;">  </span>
                                        <select class="form-select" name="ContactTidaluk" id="exampleInputTidaluk"
                                            aria-describedby="exampleInputTidaluk">
                                                <option value="">{{trans('messages.select_tidaluk_company')}} </option>
                                            @foreach($tidalukCompanyType as $row)
                                                <option value="{{$row->id}}">{{$row->lookupDiscription->code}}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>  
                               
                            </div>
                      
                            <div class="row" id="myForm1">               
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputcontactname" class="form-label">{{trans('messages.contact')}} {{trans('messages.name')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <input type="text" class="form-control" name="Contactname" id="exampleInputcontactname" placeholder="{{trans('messages.contact')}} {{trans('messages.name')}}"
                                            aria-describedby="exampleInputcontactname">
                                            <small class="text-danger"></small>
                                    </div>
                                </div>                          
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputcontactPhone" class="form-label">{{trans('messages.Mobile Number')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <div class="input-group only_left mb-3" style="margin-bottom: 0px !important;">
                                            <!-- <div class="input-group-prepend">
                                                <span class="form-control input-group-text form-number">+972</span>
                                            </div> -->
                                            <input type="text" class="form-control" placeholder="{{trans('messages.Mobile Number')}}" name="Contactphone" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" id="exampleInputcontactPhone"
                                            aria-describedby="exampleInputcontactPhone">
                                        </div>
                                        <small class="text-danger"></small>
                                    </div>
                                </div>        

                               
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputcontactPassword" class="form-label">{{trans('messages.Password')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <input type="password" class="form-control" placeholder="{{trans('messages.Password')}}"   name="Password" id="exampleInputcontactPassword"
                                            aria-describedby="exampleInputcontactPassword" oninput="validatePassword(this);" onpaste="validatePassword(this);">
                                            <small class="text-danger"></small>
                                    </div>
                                </div>                          
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputcontactConfirm" class="form-label">{{trans('messages.confirm_password')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <input type="password" class="form-control" name="ConfirmPassword"   id="exampleInputcontactConfirm"
                                            aria-describedby="exampleInputcontactConfirm" oninput="validatePassword(this);" onpaste="validatePassword(this);">
                                            <small class="text-danger"></small>
                                    </div>
                                </div>        

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputcontactemail" class="form-label">{{trans('messages.contact')}} {{trans('messages.email')}}</label><span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                        <input type="email" class="form-control" placeholder="{{trans('messages.contact')}} {{trans('messages.email')}}" name="Contactemail" id="exampleInputcontactemail"
                                            aria-describedby="exampleInputcontactemail">
                                            <small class="text-danger"></small>
                                    </div>
                                </div>        
                                

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="picture__input1" class="form-label">{{trans('messages.contact_person_profile_picture')}}</label>

                                            <label class="picture" for="picture__input1" tabIndex="0">
                                                <span class="picture__image1">
                                                    <svg width="28" height="34" viewBox="0 0 28 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.333 25.5839H20.1114V13.8411H27.962L14.2222 0.142822L0.482422 13.8411H8.333V25.5839ZM0.482422 29.4973H27.9646V33.4107H0.482422V29.4973Z" fill="#728BF2"/>
                                                    </svg>
                                                    </span>
                                                </label>
                                                
                                                <input type="file" accept=".png,.jpg,.jpeg" name="picture__input1" id="picture__input1">
                                                <small class="text-danger"></small>
                                    </div>
                                </div>                                                                        
                            </div>

                            <div class="col-md-12">
                                <div class="text-center">
                                    <button type="button" next="0" class="btn secondary-btn w-100 submit nextStep" onclick="nextStep()">{{trans('messages.next')}}</button>
                                    <a href="javascript:void(0)"  back="0" onclick="backStep()" class="backLink backStep" >{{trans('messages.back_to')}}</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('scriptCode')
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"> </script> 
<script>
    function initMap(latitude,longitude){
           var ac = new google.maps.places.Autocomplete(document.getElementById('exampleInputaddress'));
           if((latitude == undefined || longitude == undefined) || (latitude == null || longitude == null)){
               var latitude = "26.8289443";
               var longitude = "75.8056178";  
           }
           ac.addListener('place_changed', () => {
                   $('#map').show()
                   var place = ac.getPlace();
                   const latitude = place.geometry.location.lat();
                   const longitude = place.geometry.location.lng();
                   $('#lat').val(latitude)
                   $('#lng').val(longitude)
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
</script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script>    
    // $(document).ready(function(){
    //     function initMap() {
    //         alert();
    //         var ac = new google.maps.places.Autocomplete(document.getElementById('dropoff_city'), {
    //             types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
    //         });
    //         ac.addListener('place_changed', () => {
    //             var place = ac.getPlace();
    //             console.log(place);
    //             // Extract the address components
    //             var addressComponents = place.address_components;
    //             var city, state, zipCode;
    //             // Loop through the address components and find the city, state, and zip code
    //             for (var i = 0; i < addressComponents.length; i++) {
    //                 var component = addressComponents[i];
    //                 var componentTypes = component.types;
    //                 // Check if the component is a city
    //                 if (componentTypes.includes('locality')) {
    //                     city = component.long_name;
    //                 }
    //                 // Check if the component is a state
    //                 if (componentTypes.includes('administrative_area_level_1')) {
    //                     state = component.long_name;
    //                 }
    //                 // Check if the component is a zip code
    //                 if (componentTypes.includes('postal_code')) {
    //                     zipCode = component.long_name;
    //                 }
    //             }
    //             var lat = place.geometry.location.lat();
    //             var lng = place.geometry.location.lng();
    //             // $("#dropoff_city").val(city);
    //             $("#dropoff_zip_code").val(zipCode);
    //             $("#dropoff_latitude").val(lat);
    //             $("#dropoff_longitude").val(lng);
    //         });
    //         var ac2 = new google.maps.places.Autocomplete(document.getElementById('company_city'), {
    //             types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
    //         });
    //         ac2.addListener('place_changed', () => {
    //             var place = ac2.getPlace();
    //             var addressComponents = place.address_components;
    //             var city2, state2, zipCode2;
    //             for (var i = 0; i < addressComponents.length; i++) {
    //                 var component = addressComponents[i];
    //                 var componentTypes = component.types;
    //                 if (componentTypes.includes('locality')) {
    //                     city2 = component.long_name;
    //                 }
    //                 if (componentTypes.includes('administrative_area_level_1')) {
    //                     state2 = component.long_name;
    //                 }
    //                 if (componentTypes.includes('postal_code')) {
    //                     zipCode2 = component.long_name;
    //                 }
    //             }
    //             var lat2 = place.geometry.location.lat();
    //             var lat2 = place.geometry.location.lng();
    //             $("#company_zip_code").val(zipCode2);
    //             $("#company_latitude").val(lat2);
    //             $("#company_longitude").val(lat2);
    //         });
    //     }
    // });

        document.addEventListener('DOMContentLoaded', function() {
        var body = document.querySelector('body');
        body.classList.add('track_company');
        });
       
        const inputFile = document.querySelector("#picture__input");
        const pictureImage = document.querySelector(".picture__image");
        const pictureImageTxt = "<svg width='28' height='34' viewBox='0 0 28 34' fill='currentcolor' xmlns='http://www.w3.org/2000/svg'><path d='M8.333 25.5839H20.1114V13.8411H27.962L14.2222 0.142822L0.482422 13.8411H8.333V25.5839ZM0.482422 29.4973H27.9646V33.4107H0.482422V29.4973Z'/></svg>";
        pictureImage.innerHTML = pictureImageTxt;

        inputFile.addEventListener("change", function (e) {
        const inputTarget = e.target;
        const file = inputTarget.files[0];

            if (file) {
                const reader = new FileReader();

                reader.addEventListener("load", function (e) {
                const readerTarget = e.target;

                const img = document.createElement("img");
                img.src = readerTarget.result;
                img.classList.add("picture__img");

                pictureImage.innerHTML = "";
                pictureImage.appendChild(img);
                });

                reader.readAsDataURL(file);
            } else {
                pictureImage.innerHTML = pictureImageTxt;
            }
        });

        const inputFile1 = document.querySelector("#picture__input1");
        const pictureImage1 = document.querySelector(".picture__image1");
        const pictureImageTxt1 = "<svg width='28' height='34' viewBox='0 0 28 34' fill='currentcolor' xmlns='http://www.w3.org/2000/svg'><path d='M8.333 25.5839H20.1114V13.8411H27.962L14.2222 0.142822L0.482422 13.8411H8.333V25.5839ZM0.482422 29.4973H27.9646V33.4107H0.482422V29.4973Z'/></svg>";
        pictureImage1.innerHTML = pictureImageTxt1;

        inputFile1.addEventListener("change", function (e) {
        const inputTarget1 = e.target;
        const file1 = inputTarget1.files[0];

        if (file1) {
            const reader1 = new FileReader();

            reader1.addEventListener("load", function (e) {
            const readerTarget1 = e.target;

            const img1 = document.createElement("img");
            img1.src = readerTarget1.result;
            img1.classList.add("picture__img");

            pictureImage1.innerHTML = "";
            pictureImage1.appendChild(img1);
            });

            reader1.readAsDataURL(file1);
        } else {
            pictureImage1.innerHTML = pictureImageTxt1;
        }
        });

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

<script>
    $('#myForm1').hide()
    function nextStep() {
        var nextStepVal =  $('.nextStep').attr('next');
        if(nextStepVal == 0){
            const companyNameInput = document.getElementById('exampleInputname');
            // const companyEmailInput = document.getElementById('exampleInputemail');
            const companyAddressInput = document.getElementById('exampleInputaddress');
            const companylatGLocationInput = document.getElementById('lat');
            const companylngGLocationInput = document.getElementById('lng');
            const companyNumberInput = document.getElementById('exampleInputnumber');
            const companyDescriptionInput = document.getElementById('exampleFormControldesc');
            const companyTermsInput = document.getElementById('exampleFormControlTerms');
            const picture__input = document.getElementById('picture__input');
            const exampleInputRefueling = document.getElementById('exampleInputRefueling');
            const exampleInputTidaluk = document.getElementById('exampleInputTidaluk');

            if (companyNameInput.value.trim() === '') {
                companyNameInput.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            } else {
                companyNameInput.nextElementSibling.innerText = '';
            }

            // if (picture__input.value.trim() === '') {
            //     picture__input.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            // } else {
            //     picture__input.nextElementSibling.innerText = '';
            // }

            // if (companyEmailInput.value.trim() === '') {
            //     companyEmailInput.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            // } else {
            //     companyEmailInput.nextElementSibling.innerText = '';
            // }
            if (companyAddressInput.value.trim() === '') {
                $("input[name='company_address']").parent().parent().find(".text-danger").html("{{ trans("messages.This field is required") }}");
                // companyAddressInput.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            } else if( companylatGLocationInput.value.trim() === '' && companylngGLocationInput.value.trim() === '' ) {
                $("input[name='company_address']").parent().parent().find(".text-danger").html("{{ trans("messages.please_select_a_location_from_the_list_given") }}");
            }else{
                $("input[name='company_address']").parent().parent().find(".text-danger").html("");
                // companyAddressInput.nextElementSibling.innerText = '';
            }

            if (companyNumberInput.value.trim() === '') {
                $("input[name='company_number']").parent().parent().find(".text-danger").html("{{ trans("messages.This field is required") }}");
                // companyNumberInput.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            } 
            else {
                // if(companyNumberInput.value.length != 10 || companyNumberInput.value.charAt(0) !== '0' ){
                //     $("input[name='company_number']").parent().parent().find(".text-danger").html("{{ trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0") }}");
                //     // companyNumberInput.nextElementSibling.innerText = "{{ trans("messages.Phone number should be 10 digits") }}";
                // }else{
                    $("input[name='company_number']").parent().parent().find(".text-danger").html("");
                    // companyNumberInput.nextElementSibling.innerText = '';
                // }
            }


            if (exampleInputRefueling.value.trim() === '') {
                exampleInputRefueling.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            } else {
                exampleInputRefueling.nextElementSibling.innerText = '';
            }

            if (exampleInputTidaluk.value.trim() === '' && false) {
                exampleInputTidaluk.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            } else {
                exampleInputTidaluk.nextElementSibling.innerText = '';
            }
            // if (companyDescriptionInput.value.trim() === '') {
            //     companyDescriptionInput.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            // } else {
            //     companyDescriptionInput.nextElementSibling.innerText = '';
            // }

            // if (companyTermsInput.value.trim() === '') {
            //     companyTermsInput.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            // } else {
            //     companyTermsInput.nextElementSibling.innerText = '';
            // }
            if(
                companyNameInput.value.trim() !== '' &&
                // companyEmailInput.value.trim() !== '' &&
                companyAddressInput.value.trim() !== '' &&
                // picture__input.value.trim() !== '' &&
                // companyTermsInput.value.trim() !== '' &&
                // companyDescriptionInput.value.trim() !== '' &&
                // exampleInputRefueling.value.trim() !== '' &&
                // exampleInputTidaluk.value.trim() !== '' &&
                companyNumberInput.value.trim() !== ''
                // && companyNumberInput.value.length == 10
            ){
                // var form = $('#truckRegistrationstep2');
                // var csrfToken = $('meta[name="csrf-token"]').attr('content');
                // var formData = new FormData(form[0]);
                // formData.append('_token', csrfToken);
                // $.ajax({
                //     url: '{{ route('truckRegistrationstep2checkMobile') }}',
                //     type: 'POST',
                //     data: formData,
                //     processData: false,
                //     contentType: false,
                //     beforeSend: function() {
                //         $('.loader-wrapper').show();
                //     },
                //     headers: {
                //         'X-CSRF-TOKEN': csrfToken,
                //         'Accept-Language':'{{session()->get('admin_applocale')}}',
                //     },
                //     success: function(response) {
                //         // window.location.href = response.redirectUrl;
                //         $('.nextStep').attr('next',1);
                //         $('.span_val').text('2');
                //         $('.company_page_subtitle').text('Contact Details');
                //         $('#myForm').hide();
                //         $('#myForm1').show();
                //         $('#myForm1').show();
                //         $('#step-2').addClass('is-active');
                //     },
                //     error: function(xhr, status, error) {
                //         console.error('Error:', error);
                //         if (xhr.status === 422) {
                //             var errors = xhr.responseJSON.errors;
                //             $('.text-danger').empty();
                //             $.each(errors, function(field, errorMessages) {
                //                 var errorContainer = $('[name="' + field + '"]').closest('.form-group').find('.text-danger');
                //                 $.each(errorMessages, function(index, errorMessage) {
                //                     errorContainer.append('<span>' + errorMessage + '</span>');
                //                 });
                //             });
                //         }

                //     },
                //     complete: function() {
                //         $('.loader-wrapper').hide();
                //     }
                // });
                $('.nextStep').attr('next','1');
                $('.span_val').text('2');
                $('.company_page_subtitle').text('Contact Details');
                $('#myForm').hide();
                $('#myForm1').show();
                $('#myForm1').show();
                $('#step-2').addClass('is-active');
                return;
            }
        }else if(nextStepVal == 1){
            nextStepVal1()
        }

        function nextStepVal1(){
            $('.nextStep').attr('next','1');
            $('.span_val').text('2');
            $('.company_page_subtitle').text('Contact Details');
            $('#myForm').hide();
            $('#myForm1').show();
            
            const exampleInputcontactname = document.getElementById('exampleInputcontactname');
            const exampleInputcontactPhone = document.getElementById('exampleInputcontactPhone');
            // const exampleInputRefueling = document.getElementById('exampleInputRefueling');
            // const exampleInputTidaluk = document.getElementById('exampleInputTidaluk');
            const exampleInputcontactPassword = document.getElementById('exampleInputcontactPassword');
            const exampleInputcontactConfirm = document.getElementById('exampleInputcontactConfirm');
            const exampleInputcontactemail = document.getElementById('exampleInputcontactemail');
            const picture__input1 = document.getElementById('picture__input1');
           

            if (exampleInputcontactname.value.trim() === '') {
                exampleInputcontactname.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            } else {
                exampleInputcontactname.nextElementSibling.innerText = '';
            }

            // if (picture__input1.value.trim() === '') {
            //     picture__input1.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            // } else {
            //     picture__input1.nextElementSibling.innerText = '';
            // }

            if (exampleInputcontactPhone.value.trim() === '') {
                // exampleInputcontactPhone.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
                $("input[name='Contactphone']").parent().parent().find(".text-danger").html("{{ trans("messages.This field is required") }}");
                
            } else {
                if(exampleInputcontactPhone.value.length != 10 || exampleInputcontactPhone.value.charAt(0) !== '0' ){
                    $("input[name='Contactphone']").parent().parent().find(".text-danger").html("{{ trans("messages.phone_number_should_be_10_digits_and_should_be_start_with_0") }}");
                }else{
                    // exampleInputcontactPhone.nextElementSibling.innerText = '';
                    $("input[name='Contactphone']").parent().parent().find(".text-danger").html("");
                }
            }

            // if (exampleInputRefueling.value.trim() === '') {
            //     exampleInputRefueling.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            // } else {
            //     exampleInputRefueling.nextElementSibling.innerText = '';
            // }

            // if (exampleInputTidaluk.value.trim() === '') {
            //     exampleInputTidaluk.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            // } else {
            //     exampleInputTidaluk.nextElementSibling.innerText = '';
            // }

            const password = exampleInputcontactPassword.value.trim();
            const Confirmpassword = exampleInputcontactConfirm.value.trim();
            var error = 0;
            console.log(Confirmpassword.length,'dsfa')

            if (exampleInputcontactPassword.value.trim() === '') {
                exampleInputcontactPassword.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            }else if(password.length < 4){
                error = 1;
                exampleInputcontactPassword.nextElementSibling.innerText = "{{ trans("messages.must be 4") }}";
            } else {
                exampleInputcontactPassword.nextElementSibling.innerText = '';
            }


            if (exampleInputcontactConfirm.value.trim() === '') {
                exampleInputcontactConfirm.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            }else if(Confirmpassword.length < 4){
                error = 1;
                exampleInputcontactConfirm.nextElementSibling.innerText = "{{ trans("messages.must be 4") }}";
            }else if(exampleInputcontactConfirm.value.trim() !== exampleInputcontactPassword.value.trim()){
                error = 1;
                exampleInputcontactConfirm.nextElementSibling.innerText = "{{ trans("messages.confirm password not match") }}";
            } else {
                exampleInputcontactConfirm.nextElementSibling.innerText = '';
            }
            
            if (exampleInputcontactemail.value.trim() === '') {
                exampleInputcontactemail.nextElementSibling.innerText = "{{ trans("messages.This field is required") }}";
            } else if (!(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(exampleInputcontactemail.value))) {
                exampleInputcontactemail.nextElementSibling.innerText = "{{ trans("messages.The email must be a valid email address") }}";
            } else {
                exampleInputcontactemail.nextElementSibling.innerText = '';
            }

            if(
                exampleInputcontactname.value.trim() !== '' &&
                exampleInputcontactPhone.value.trim() !== '' &&
                exampleInputcontactPhone.value.length == 10 &&
                // exampleInputRefueling.value.trim() !== '' &&
                // exampleInputTidaluk.value.trim() !== '' &&
                exampleInputcontactPassword.value.trim() !== '' &&
                exampleInputcontactConfirm.value.trim() !== '' && 
                // picture__input.value.trim() !== '' &&
                exampleInputcontactemail.value !== '' &&  error == 0 ){
                var form = $('#truckRegistrationstep2');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var formData = new FormData(form[0]);
                formData.append('_token', csrfToken);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.loader-wrapper').show();
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept-Language':'{{session()->get('admin_applocale')}}',
                    },
                    success: function(response) {
                    
                        window.location.href = response.redirectUrl;
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.text-danger').empty();
                            $.each(errors, function(field, errorMessages) {
                                var errorContainer = $('[name="' + field + '"]').closest('.form-group').find('.text-danger');
                                $.each(errorMessages, function(index, errorMessage) {
                                    errorContainer.append('<span>' + errorMessage + '</span>');
                                });
                            });
                        }

                    },
                    complete: function() {
                        $('.loader-wrapper').hide();
                    }
                });
            }
        }
        
    }

    function backStep(){
        var nextStepVal1 =  $('.nextStep').attr('next');
        console.log('d00f')
        if(nextStepVal1 == 0){
            console.log('df')
            location.href = "/";
        }else{
            $('.nextStep').attr('next','0');
            $('.span_val').text('1');
            $('#step-2').removeClass('is-active');
            $('.company_page_subtitle').text('Company Details');
            $('#myForm1').hide();   
            $('#myForm').show();
        }
    }

</script>


@stop