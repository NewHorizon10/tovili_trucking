@extends('frontend.layouts.customers')
@section('extraCssLinks')
@stop
@section('backgroundImage')

<body class="dashbord_page privateCustomer_page">
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
            <form class="profile-form" method="post" action="{{route('private-customers-profile-update')}}" enctype="multipart/form-data">
                @csrf
                <div class="row ">
                    <div class="col-md-8 col-lg-8 order-2">
                        <!-- <h3 class="profile-title">Contact Person Info</h3> -->
                        <div class="row Person_info_row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">{{trans("messages.Email Address")}}</label>
                                    <input type="email" class="form-control"  id="email" name="email" value="{{ old() ? old('email') : $user->email }}" placeholder="{{trans("messages.Email Address")}}">
                                    @if ($errors->has('email'))
                                    <small class="text-danger">
                                        {{ $errors->first('email') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">{{trans("messages.Your Name")}}</label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old() ? old('name') : $user->name }}"  placeholder="{{trans("messages.Your Name")}}">
                                    @if ($errors->has('name'))
                                    <small class="text-danger">
                                        {{ $errors->first('name') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="form-label">{{trans("messages.Mobile Number")}}</label>
                                    <div class="input-group only_left mb-3">
                                        <!-- <div class="input-group-prepend">
                                            <span class="form-control input-group-text form-number">+972</span>
                                        </div> -->
                                        <input type="text" class="form-control" name="phone_number"  id="phone_number" value="{{ old() ? old('phone_number') : $user->phone_number }}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);" placeholder="{{trans("messages.Mobile Number")}}">
                                    </div>
                                    @if ($errors->has('phone_number'))
                                    <small class="text-danger">
                                        {{ $errors->first('phone_number') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group company-location">
                                    <label for="address" class="form-label">{{trans("messages.Address")}}</label>
                                    <input type="text" class="form-control" id="company_location" name="location" value="{{ old() ? old('location') : $user->location  }}"  id="location" placeholder="{{trans("messages.Address")}}">
                                    <span class="loaction_icon">
                                        <svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.61436 0C3.36318 0 0.726562 2.46071 0.726562 5.49498C0.726562 9.61621 6.61436 15.7008 6.61436 15.7008C6.61436 15.7008 12.5022 9.61621 12.5022 5.49498C12.5022 2.46071 9.86769 0 6.61436 0ZM6.61436 7.45833C5.45399 7.45833 4.51065 6.57993 4.51065 5.49498C4.51065 4.41002 5.45184 3.53162 6.61436 3.53162C7.77688 3.53162 8.71806 4.41002 8.71806 5.49498C8.71806 6.57993 7.77688 7.45833 6.61436 7.45833Z" fill="#1535B9"></path>
                                        </svg>
                                    </span>
                                    @if ($errors->has('location'))
                                    <small class="text-danger">
                                        {{ $errors->first('location') }}
                                    </small>
                                    @endif
                                </div>
                                <input type="hidden" name="current_lat" id="current_lat" value="{{ old() ? old('current_lat') : '' }}">
                                <input type="hidden" name="current_lng" id="current_lng" value="{{ old() ? old('current_lng') : '' }}">
                            </div> --}}

                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="contact_person_email">{{trans("messages.Address")}}</label>
                                    <input type="text" name="location" id="company_location" class="form-control form-control-solid form-control-lg  @error('location') is-invalid @enderror" value="{{ old() ? old('location') : $user->location  }}">
                                    @if ($errors->has('location'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('location') }}
                                    </div>
                                    @endif
                                </div>
                                <input type="hidden" name="current_lat" id="current_lat" value="{{ old() ? old('current_lat') : $user->current_lat }}">
                                <input type="hidden" name="current_lng" id="current_lng" value="{{ old() ? old('current_lng') : $user->current_lng }}">
                            </div>


                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 order-md-2">
                        <div class="avtarEdit-main">
                            <div class="avatar-edit">
                                <input type="file" id="imageUpload" name="image" accept=".png, .jpg, .jpeg" >
                                <label for="imageUpload"></label>
                            </div>
                 
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <div id="imagePreview" style="background-image: url({{asset($user->image)}});">
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
        $(document).ready(function(){
            $("#company_location").on("change", function(){
                $("#current_lng").val('');
                $("#current_lat").val('');
            });
        });
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
        $("#imageUpload").change(function() {
            readURL(this);
        });

       

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

    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"></script>

    @stop