@extends('admin.layouts.layout')
@section('content')
<?php $counter = 0; ?>
<style>
    .invalid-feedback {
        display: inline;
    }
   
    .AClass{
    right:10px;
    position: absolute;
}
</style>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    Edit Private {{Config('constants.CUSTOMER.CUSTOMERS_TITLE')}}	</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted"> 
                            Private {{Config('constants.CUSTOMER.CUSTOMERS_TITLES')}} 
                        </a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route($model.'.update',array(base64_encode($userDetails->id)))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                    Private {{Config('constants.CUSTOMER.CUSTOMERS_TITLE')}} Information
                                </h3>
                                <div class="row">

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">Name</label><span class="text-danger"> * </span>
                                            <input type="text" name="name" class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror" 
                                            value="{{$userDetails->name ?? old('name')}}">
                                            @if ($errors->has('name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="email">Email ID</label><span class="text-danger"> </span>
                                            <input type="text" name="email" class="form-control form-control-solid form-control-lg  @error('email') is-invalid @enderror" 
                                            value="{{$userDetails->email ?? old('email')}}">
                                            @if ($errors->has('email'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('email') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Phone Number</label><span class="text-danger"> * </span>
                                            <input type="number" name="phone_number" class="form-control form-control-solid form-control-lg  @error('phone_number') is-invalid @enderror" 
                                            value="{{old('phone_number') ?? $userDetails->phone_number}}">
                                            @if ($errors->has('phone_number'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('phone_number') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    
                                </div>


                                <h3 class="mb-10 font-weight-bold text-dark">
                                    Company Information
                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">Company Name</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_name" class="form-control form-control-solid form-control-lg  @error('company_name') is-invalid @enderror"
                                             value="{{ $userDetails->userCompanyInformation->company_name ?? old('company_name') }}">
                                            @if ($errors->has('company_name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_mobile_number">Company Mobile Number</label><span class="text-danger"> * </span>
                                            <input type="number" name="company_mobile_number" class="form-control form-control-solid form-control-lg  @error('company_mobile_number') is-invalid @enderror"
                                             value="{{ $userDetails->userCompanyInformation->company_mobile_number ?? old('company_mobile_number') }}">
                                            @if ($errors->has('company_mobile_number'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_mobile_number') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_name">Contact Person Name</label><span class="text-danger"> * </span>
                                            <input type="text" name="contact_person_name" class="form-control form-control-solid form-control-lg  @error('contact_person_name') is-invalid @enderror" 
                                            value="{{ $userDetails->userCompanyInformation->contact_person_name ?? old('contact_person_name') }}">
                                            @if ($errors->has('contact_person_name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">Contact Person Email</label><span class="text-danger"> * </span>
                                            <input type="email" name="contact_person_email" class="form-control form-control-solid form-control-lg  @error('contact_person_email') is-invalid @enderror" 
                                            value="{{$userDetails->userCompanyInformation->contact_person_email ?? old('contact_person_email') }}">
                                            @if ($errors->has('contact_person_email'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_email') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_phone_number">Contact Person Number</label><span class="text-danger"> * </span>
                                            <input type="number" name="contact_person_phone_number" class="form-control form-control-solid form-control-lg  @error('contact_person_phone_number') is-invalid @enderror"
                                             value="{{ $userDetails->userCompanyInformation->contact_person_phone_number ?? old('contact_person_phone_number')}}">
                                            @if ($errors->has('contact_person_phone_number'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_phone_number') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_picture">Contact Person Profile</label><span class="text-danger"> * </span>
                                            <input type="file" name="contact_person_picture" class="form-control form-control-solid form-control-lg  @error('contact_person_picture') is-invalid @enderror">
                                            @if ($errors->has('contact_person_picture'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_picture') }}
                                            </div>
                                            @endif
                                            @if (!empty($userDetails->userCompanyInformation->contact_person_picture))
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo Config('constants.CONTACT_PERSON_PROFILE_IMAGE_PATH') . $userDetails->userCompanyInformation->contact_person_picture; ?>">
                                                    <img width="100px" height="80px" alt="{{ trans('messages.admin_common_Image') }}" src="{{ Config('constants.CONTACT_PERSON_PROFILE_IMAGE_PATH') . $userDetails->userCompanyInformation->contact_person_picture }}" />
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">Company Location</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_location" class="form-control form-control-solid form-control-lg  @error('company_location') is-invalid @enderror" 
                                            value="{{ $userDetails->userCompanyInformation->company_location ?? old('company_location') }}">
                                            @if ($errors->has('company_location'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_location') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">Company Type</label><span class="text-danger"> * </span>
                                            <select name="company_type" class="form-control select2init  @error('company_type') is-invalid @enderror" >
                                                <option value="">Select Company Type </option>
                                                @if(!empty($companyType))
                                                @foreach($companyType as $row)
                                                    <option value="{{$row->id}}" {{  ($userDetails->userCompanyInformation->company_type ?? '' == $row->id ? 'selected' : '') }} >{{$row->lookupDiscription->code}}</option>
                                                @endforeach
                                                @endif
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
                                            <label for="company_logo">Company Logo</label><span class="text-danger"> * </span>
                                            <input type="file" name="company_logo" class="form-control form-control-solid form-control-lg  @error('company_logo') is-invalid @enderror" 
                                            value="{{old('company_logo')}}">
                                            @if ($errors->has('company_logo'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_logo') }}
                                            </div>
                                            @endif
                                            @if (!empty($userDetails->userCompanyInformation->company_logo))
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo Config('constants.COMPANY_LOGO_IMAGE_PATH') . $userDetails->userCompanyInformation->company_logo; ?>">
                                                    <img width="100px" height="80px" alt="{{ trans('messages.admin_common_Image') }}" src="{{ Config('constants.COMPANY_LOGO_IMAGE_PATH') . $userDetails->userCompanyInformation->company_logo }}" />
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div>
                                        <button button type="submit" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                            Submit
                                        </button>
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
<script src="https://unpkg.com/@reactivex/rxjs@5.0.0-beta.7/dist/global/Rx.umd.js"></script>
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

    function doIt(e) {
        var e = e || event;
        if (e.keyCode == 32) return false;
    }
    window.onload = function() {
        var inp = document.getElementById("zip_code");

        inp.onkeydown = doIt;
    };

    function submit_form() {
        $(".mws-form").submit();
    }
    $('.chosenselect_country').select2({
        placeholder: "Select Country",
        allowClear: true
    });
</script>


@stop