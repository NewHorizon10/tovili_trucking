@extends('admin.layouts.layout')
@section('content')
<style>
    .invalid-feedback {
        display: inline;
    }
</style>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans("messages.admin_common_Add_New")}}</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">{{trans("messages.admin_Business_Customers")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route($model.'.save')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                {{trans("messages.admin_Business_Customers_Information")}}
                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="improfile_imageage">{{trans("messages.admin_common_Image")}}</label><span class="text-danger"> * </span>
                                            <input type="file" name="profile_image" class="form-control form-control-solid form-control-lg  @error('profile_image') is-invalid @enderror" accept="image/png, image/jpg, image/jpeg">
                                            @if ($errors->has('profile_image'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('profile_image') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">{{trans("messages.name")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="name" class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror" value="{{old('name')}}">
                                            @if ($errors->has('name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="email">{{trans("messages.Email Address")}}</label><span class="text-danger"> </span>
                                            <input type="text" name="email" class="form-control form-control-solid form-control-lg  @error('email') is-invalid @enderror" value="{{old('email')}}">
                                            @if ($errors->has('email'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('email') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">{{trans("messages.Phone Number")}}</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <input type="number" name="phone_number" class="form-control form-control-solid form-control-lg @error('phone_number') is-invalid @enderror" value="{{old('phone_number')}}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                                                @if ($errors->has('phone_number'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('phone_number') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                   

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{trans('messages.Password')}}</label><span class="text-danger"> * </span>
                                            <input type="password" name="password" class="form-control form-control-solid form-control-lg  @error('password') is-invalid @enderror" >
                                            @if ($errors->has('password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('password') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>                                    
                                    
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{trans("messages.confirm_password")}}</label><span class="text-danger"> * </span>
                                            <input type="password" name="confirm_password" class="form-control form-control-solid form-control-lg  @error('confirm_password') is-invalid @enderror" >
                                            @if ($errors->has('confirm_password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('confirm_password') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <h3 class="mb-10 font-weight-bold text-dark">
                                    {{trans("messages.admin_Company_Information")}}
                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">{{trans("messages.Company Name")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_name" class="form-control form-control-solid form-control-lg  @error('company_name') is-invalid @enderror" value="{{old('company_name')}}">
                                            @if ($errors->has('company_name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_number">{{trans('messages.company_number')}} (H.P.)</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <input type="text" name="company_number" class="form-control form-control-solid form-control-lg @error('company_number') is-invalid @enderror" value="{{old('company_number')}}" oninput="validateOnlyNumber(this);" onpaste="validateOnlyNumber(this);" >
                                                @if ($errors->has('company_number'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('company_number') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_name">{{trans("messages.Contact Person Name")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="contact_person_name" class="form-control form-control-solid form-control-lg  @error('contact_person_name') is-invalid @enderror" value="{{old('contact_person_name')}}">
                                            @if ($errors->has('contact_person_name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">{{trans("messages.Contact Person Email")}}</label><span class="text-danger"> * </span>
                                            <input type="email" name="contact_person_email" class="form-control form-control-solid form-control-lg  @error('contact_person_email') is-invalid @enderror" value="{{old('contact_person_email')}}">
                                            @if ($errors->has('contact_person_email'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_email') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_phone_number">{{trans("messages.admin_Contact_Person_Number")}}</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <input type="number" name="contact_person_phone_number" class="form-control form-control-solid form-control-lg @error('contact_person_phone_number') is-invalid @enderror" value="{{old('contact_person_phone_number')}}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                                                @if ($errors->has('contact_person_phone_number'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('contact_person_phone_number') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_picture">{{trans("messages.admin_Contact_Person_Profile")}}</label><span class="text-danger"> * </span>
                                            <input type="file" name="contact_person_picture" class="form-control form-control-solid form-control-lg  @error('contact_person_picture') is-invalid @enderror" value="{{old('contact_person_picture')}}">
                                            @if ($errors->has('contact_person_picture'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_picture') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">{{trans("messages.Company Location")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_location" id="company_location" class="form-control form-control-solid form-control-lg  @error('company_location') is-invalid @enderror" value="{{old('company_location')}}">
                                            @if ($errors->has('company_location'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_location') }}
                                            </div>
                                            @endif
                                        </div>
                                        <input type="hidden" name="current_lat" id="current_lat" value="{{ old() ? old('current_lat') : '' }}">
                                        <input type="hidden" name="current_lng" id="current_lng" value="{{ old() ? old('current_lng') : '' }}">
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">{{trans("messages.company type")}}</label><span class="text-danger"> * </span>
                                            <select name="company_type" class="form-control select2init  @error('company_type') is-invalid @enderror" >
                                                <option value="">{{trans("messages.admin_common_select_company_type")}}</option>
                                                @foreach($companyType as $row)
                                                    <option value="{{$row->id}}" {{old('company_type') == $row->id ? 'selected' : '' }} >{{$row->lookupDiscription->code}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('company_type'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_type') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_logo">{{trans("messages.Company Logo")}}</label><span class="text-danger"> * </span>
                                            <input type="file" name="company_logo" class="form-control form-control-solid form-control-lg  @error('company_logo') is-invalid @enderror" value="{{old('company_logo')}}">
                                            @if ($errors->has('company_logo'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_logo') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div class="row">
                                        <div class="col-6">
                                            <button button type="submit" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                                {{trans('messages.submit')}}
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a type="button" href="{{route('business-customers.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
                                                {{trans("messages.admin_cancel")}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@stop
@section('css')
<style type="text/css">
    .profilePreview, .avatarPreview{display:none;height: 120px;margin-bottom: 15px;}
</style>
@stop
@section('script')
<script>
    $(".profile_img").on('change',function(){
        
        if(jQuery.inArray(this.files[0].type, allowimagetypes) == -1){
            show_message('This file format is not allowed.','error');
            $('.profilePreview').attr('src','');
            $(this).val('');
            return false;
        }
        var img = window.URL.createObjectURL(this.files[0]);
        $('.profilePreview').attr('src',img);
        $('.profilePreview').show();
    })

    $(".avatar_img").on('change',function(){
        var img = window.URL.createObjectURL(this.files[0]);
        $('.avatarPreview').attr('src',img);
        $('.avatarPreview').show();
    })


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