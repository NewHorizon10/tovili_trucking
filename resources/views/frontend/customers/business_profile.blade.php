@extends('frontend.layouts.customers')
@section('extraCssLinks')
@stop
@section('backgroundImage')

<body class="dashbord_page">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="img/logo.png" alt="">
        </div>
    </div>
    @stop
    @section('content')
    <div class="col-md-12 col-lg-9 col-sm-12">
        <div class="dashboardRight_block_wrapper">
            <div class="pageTopTitle">
                <h2 class="RightBlockTitle">{{trans("messages.My Profiles")}}</h2>
               
                <a href="{{ route('change-password') }}" class="transportRequestBtn">
                    {{trans("messages.Change Password")}}
                </a>
            </div>
            <form class="profile-form" method="post" action="{{route('business-customers-profile-update')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-12">
                        <h3 class="myProfile_label">{{trans('messages.Company Image')}}</h3>
                    </div>
                    <div class="col-md-8 col-lg-8 order-1">
                        <h3 class="profile-title">{{trans("messages.Company Info")}}</h3>
                        <div class="row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name" class="form-label">{{trans("messages.Company Name")}}</label>
                                    <input type="text" class="form-control" name="company_name" value="{{ old() ? old('company_name') : $user_company_informations->company_name }}"  id="company_name" placeholder="{{trans("messages.Company Name")}}">
                                    @if ($errors->has('company_name'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_name') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_type" class="form-label">{{trans("messages.company type")}}</label>

                                    <select class="form-select" name="company_type" id="company_type" aria-label="Default select example">
                                        @foreach($companyType as $compType)
                                        <option value="{{$compType->id}}" {{ (old('company_type') ?? $user_company_informations->company_type) == $compType->id ? "selected" : "" }}>{{$compType->code}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('company_type'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_type') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_number" class="form-label">{{trans('messages.company_number')}} (H.P.)</label>
                                    <div class="input-group only_left mb-3">
                                        <!-- <div class="input-group-prepend">
                                            <span class="form-control input-group-text form-number">+972</span>
                                        </div> -->
                                        <!-- value="{{$user_company_informations->company_number}}" -->
                                        <input type="text" class="form-control" name="company_number" id="company_number" oninput="validateOnlyNumber(this);" onpaste="validateOnlyNumber(this);" value="{{ old() ? old('company_number') : $user_company_informations->company_hp_number }}"   placeholder="{{trans("messages.Company Mobile Number")}}">
                                    </div>
                                    @if ($errors->has('company_number'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_number') }}
                                    </div>
                                    @endif
                                </div>

                            </div>

                            <!-- <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">{{trans("messages.Phone Number")}}</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">+972</span>
                                                </div>
                                                <input type="number" name="phone_number" class="form-control form-control-solid form-control-lg @error('phone_number') is-invalid @enderror" value="{{old('phone_number')}}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                                                @if ($errors->has('phone_number'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('phone_number') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div> -->
                            <div class="col-md-6">
                                <div class="form-group company-location">
                                    <label for="company_location" class="form-label">{{trans("messages.Company Location")}}</label>
                                    <input type="text" class="form-control" name="company_location" id="company_location" value="{{ old() ? old('company_location') : $user_company_informations->company_location }}" placeholder="{{trans("messages.Company Location")}}">
                                    <span class="loaction_icon">
                                        <svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.61436 0C3.36318 0 0.726562 2.46071 0.726562 5.49498C0.726562 9.61621 6.61436 15.7008 6.61436 15.7008C6.61436 15.7008 12.5022 9.61621 12.5022 5.49498C12.5022 2.46071 9.86769 0 6.61436 0ZM6.61436 7.45833C5.45399 7.45833 4.51065 6.57993 4.51065 5.49498C4.51065 4.41002 5.45184 3.53162 6.61436 3.53162C7.77688 3.53162 8.71806 4.41002 8.71806 5.49498C8.71806 6.57993 7.77688 7.45833 6.61436 7.45833Z" fill="#1535B9" />
                                        </svg>
                                    </span>
                                    @if ($errors->has('company_location'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_location') }}
                                    </div>
                                    @endif
                                </div>
                                <input type="hidden" name="current_lat" id="current_lat" value="{{ old() ? old('current_lat') : $user->current_lat }}">
                                <input type="hidden" name="current_lng" id="current_lng" value="{{ old() ? old('current_lng') : $user->current_lng }}">

                            </div>

                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 order-md-1">
                        <div class="avtarEdit-main">
                            <div class="avatar-edit">
                                <input type='file' id="company_logo" name="company_logo" accept=".png, .jpg, .jpeg">
                                <label for="company_logo"></label>
                            </div>
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <div id="imagePreview" style="background-image: url({{$user_company_informations->company_logo}});">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-12">
                        <h3 class="myProfile_label">{{ trans('messages.User Image') }}</h3>
                    </div>
                    <div class="col-md-8 col-lg-8 order-2">
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_person_name" class="form-label">{{trans("messages.company_owner_name")}}</label>
                                    <input type="text" name="owner_person_name" value="{{ old() ? old('owner_person_name') : $user->name }}" class="form-control" id="owner_person_name" placeholder="{{trans("messages.company_owner_name")}}">
                                    @if ($errors->has('owner_person_name'))
                                    <div class="text-danger">
                                        {{ $errors->first('owner_person_name') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_person_phone_number" class="form-label">{{trans('messages.company_phone_number')}}</label>
                                    <!-- <input type="text" class="form-control" name="owner_person_phone_number" value="{{$user->phone_number}}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);"  placeholder="{{trans("messages.company_phone_number")}}">
                                    @if ($errors->has('owner_person_phone_number'))
                                    <div class="text-danger">
                                        {{ $errors->first('owner_person_phone_number') }}
                                    </div>
                                    @endif -->
                                    <div class="input-group only_left mb-3">
                                        <!-- <div class="input-group-prepend">
                                            <span class="form-control input-group-text form-number">+972</span>
                                        </div> -->
                                        <!-- value="{{$user->phone_number}}" -->
                                        <input type="text" class="form-control" name="owner_person_phone_number" value="{{ old() ? old('owner_person_phone_number') : $user->phone_number }}"   id="owner_person_phone_number" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" placeholder="{{trans("messages.company_phone_number")}}">
                                    </div>
                                    @if ($errors->has('owner_person_phone_number'))
                                    <div class="text-danger">
                                        {{ $errors->first('owner_person_phone_number') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_person_email" class="form-label">{{trans("messages.company_email_address")}}</label>
                                    <input type="email" name="owner_person_email" value="{{ old() ? old('owner_person_email') : $user->email }}"  class="form-control" id="owner_person_email" placeholder="{{trans("messages.company_email_address")}}">
                                    @if ($errors->has('owner_person_email'))
                                    <div class="text-danger">
                                        {{ $errors->first('owner_person_email') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 order-md-2">
                        <div class="avtarEdit-main">
                            <div class="avatar-edit">
                                <input type='file' id="owner_person_picture" name="owner_person_picture" accept=".png, .jpg, .jpeg">
                                <label for="owner_person_picture"></label>
                            </div>
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <div id="imagePreview" class="previewProfile1" style="background-image: url({{asset($user->image)}});">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="seprator"></div>
                <h3 class="profile-title">{{trans("messages.Person_Details")}}</h3>
                <div class="row">
                <div class="col-12">
                        <h3 class="myProfile_label mt-0">{{ trans('messages.Contact Person Image') }}</h3>
                    </div>
                    <div class="col-md-8 col-lg-8 order-2">
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_name" class="form-label">{{trans("messages.Contact Person Name")}} </label>
                                    <input type="text" name="contact_person_name" value="{{ old() ? old('contact_person_name') : $user_company_informations->contact_person_name }}"  class="form-control" id="contact_person_name" placeholder="{{trans("messages.Contact Person Name")}}">
                                    @if ($errors->has('contact_person_name'))
                                    <div class="text-danger">
                                        {{ $errors->first('contact_person_name') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_phone_number" class="form-label">{{trans("messages.admin_Contact_Person_Number")}}</label>
                                    <!-- <input type="text" class="form-control" name="contact_person_phone_number" value="{{$user_company_informations->contact_person_phone_number}}" id="contact_person_mobile_number" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" placeholder="{{trans("messages.admin_Contact_Person_Number")}}">
                                    @if ($errors->has('contact_person_phone_number'))
                                    <div class="text-danger">
                                        {{ $errors->first('contact_person_phone_number') }}
                                    </div>
                                    @endif -->
                                    <div class="input-group only_left mb-3">
                                        <!-- <div class="input-group-prepend">
                                            <span class="form-control input-group-text form-number">+972</span>
                                        </div> -->
                                        <!-- value="{{$user_company_informations->contact_person_phone_number}}" -->
                                        <input type="text" class="form-control" name="contact_person_phone_number" id="contact_person_mobile_number" value="{{ old() ? old('contact_person_phone_number') : $user_company_informations->contact_person_phone_number }}"  oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" placeholder="{{trans("messages.admin_Contact_Person_Number")}}">
                                    </div>
                                    @if ($errors->has('contact_person_phone_number'))
                                    <div class="text-danger">
                                        {{ $errors->first('contact_person_phone_number') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_email" class="form-label">{{trans("messages.Contact Person Email")}}</label>
                                    <input type="email" name="contact_person_email" value="{{ old() ? old('contact_person_email') : $user_company_informations->contact_person_email }}"  class="form-control" id="contact_person_email" placeholder="{{trans("messages.Contact Person Email")}}">
                                    @if ($errors->has('contact_person_email'))
                                    <div class="text-danger">
                                        {{ $errors->first('contact_person_email') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 order-md-2">
                        <div class="avtarEdit-main">
                            <div class="avatar-edit">
                                <input type='file' id="contact_person_picture" name="contact_person_picture" accept=".png, .jpg, .jpeg">
                                <label for="contact_person_picture"></label>
                            </div>
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <div id="imagePreview" class="previewProfile" style="background-image: url({{$user_company_informations->contact_person_picture}});">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="save-updateBtn">
                {{trans("messages.Save & Update")}} 
                </button>
            </form>
        </div>
    </div>
    @stop
    @section('scriptCode')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#company_logo").change(function() {
            readURL(this);
        });

        $("#contact_person_picture").change(function() {

            readURLProfile(this);
        });

        function readURLProfile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.previewProfile').css('background-image', 'url(' + e.target.result + ')');
                    $('.previewProfile').hide();
                    $('.previewProfile').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#owner_person_picture").change(function() {
            readURLProfile1(this);
        });


        function readURLProfile1(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.previewProfile1').css('background-image', 'url(' + e.target.result + ')');
                    $('.previewProfile1').hide();
                    $('.previewProfile1').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function initMap() {
            var ac = new google.maps.places.Autocomplete(document.getElementById('company_location'), {
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

                $("#current_lat").val(lat);
                $("#current_lng").val(lng);

            
            });
        }
    </script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"></script>
    @stop