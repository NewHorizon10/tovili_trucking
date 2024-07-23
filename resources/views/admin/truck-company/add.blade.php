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
                            <a href="{{ route($model.'.index')}}" class="text-muted">{{trans('messages.admin_Truck_Company')}}</a>
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
                <input type="hidden" name="from_page" value="{{request('from_page')}}">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
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
                                                    <input type="text" name="company_number" class="form-control form-control-solid form-control-lg @error('company_number') is-invalid @enderror" oninput="validateOnlyNumber(this);" onpaste="validateOnlyNumber(this);" value="{{old('company_number')}}">
                                                    @if ($errors->has('company_number'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('company_number') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <label for="company_description">{{trans("messages.admin_common_company_description")}}</label>
                                                <textarea rows="3" type="number" name="company_description" class="form-control form-control-solid form-control-lg  @error('company_description') is-invalid @enderror" value="">{{old('company_description')}}</textarea>
                                                @if ($errors->has('company_description'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('company_description') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
    
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <label for="company_terms">{{trans("messages.admin_Company_Terms_&_Conditions")}}</label>
                                                <textarea rows="3" type="number" name="company_terms" class="form-control form-control-solid form-control-lg  @error('company_terms') is-invalid @enderror" value="">{{old('company_terms')}}</textarea>
                                                @if ($errors->has('company_terms'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('company_terms') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="company_refulling">{{trans("messages.refueling_method")}}</label><span class="text-danger"> * </span>
                                                <select name="company_refulling" class="form-control select2init  @error('company_refulling') is-invalid @enderror" >
                                                    <option value="">{{trans("messages.select_refueling_method")}} </option>
                                                    @foreach($fuelingType as $row)
                                                        <option value="{{$row->id}}" {{old('company_refulling') == $row->id ? 'selected' : '' }} >{{$row->lookupDiscription->code}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('company_refulling'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('company_refulling') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
    
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="company_tidaluk">{{trans("messages.admin_common_Company_Tidaluk")}}</label><span class="text-danger"> * </span>
                                                <select name="company_tidaluk" class="form-control select2init  @error('company_tidaluk') is-invalid @enderror" >
                                                    <option value="">{{trans("messages.select_company_tidaluk")}}</option>
                                                    @foreach($tidalukCompanyType as $row)
                                                        <option value="{{$row->id}}" {{old('company_tidaluk') == $row->id ? 'selected' : '' }} >{{$row->lookupDiscription->code}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('company_tidaluk'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('company_tidaluk') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="contact_person_email">
                                                    {{trans("messages.Company Location")}}
                                                </label><span class="text-danger"> * </span>
                                                <input type="text" name="company_location" id="company_location" class="form-control form-control-solid form-control-lg  @error('company_location') is-invalid @enderror" value="{{old('company_location')}}" >
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
                                                <label for="company_logo">{{trans("messages.Company Logo")}}</label>
                                                <input type="file" name="company_logo" class="form-control form-control-solid form-control-lg  @error('company_logo') is-invalid @enderror" value="{{old('company_logo')}}">
                                                @if ($errors->has('company_logo'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('company_logo') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="mb-10 font-weight-bold text-dark">
                                    {{trans("messages.Contact Person Info")}}
                                    </h3>
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="name">{{trans("messages.Contact Person Name")}}</label><span class="text-danger"> * </span>
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
                                                <label for="email">{{trans("messages.Contact Person Email")}}</label><span class="text-danger"> </span>
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
                                                <label for="phone_number">{{trans("messages.admin_Contact_Person_Number")}}</label><span class="text-danger"> * </span>
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
                                                <input type="password" name="password" class="form-control form-control-solid form-control-lg  @error('password') is-invalid @enderror" oninput="validatePassword(this);" onpaste="validatePassword(this);">
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
                                                <input type="password" name="confirm_password" class="form-control form-control-solid form-control-lg  @error('confirm_password') is-invalid @enderror" oninput="validatePassword(this);" onpaste="validatePassword(this);">
                                                @if ($errors->has('confirm_password'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('confirm_password') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="contact_person_picture">{{trans("messages.admin_Contact_Person_Profile")}}</label>
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
                                                <label for="as_driver">{{trans("messages.customer_as_driver")}}</label>
                                                <input checked="checked" name="as_driver" type="checkbox" id="as_driver">
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
                                                <a type="button" href="@if (request('from_page') == 'tidaluk_company')
                                                {{ route('truck-company.tidaluk-company') }}
                                                @elseif (request('from_page') == 'fueling_company')
                                                    {{ route('truck-company.fueling-methods') }}
                                                @else
                                                    {{ route('truck-company.index') }}
                                                @endif" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
                                                    {{trans("messages.admin_cancel")}}
                                                </a>
                                            </div>
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

@stop
@section('css')
<style type="text/css">
    .profilePreview, .avatarPreview{display:none;height: 120px;margin-bottom: 15px;}
</style>
@stop
@section('script')
@include('common.googleLocation') 
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