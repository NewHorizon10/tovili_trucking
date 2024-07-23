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
                    {{trans('messages.admin_common_Edit')}} {{trans("messages.admin_common_Driver")}}</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('truck-company.index')}}" class="text-muted"> {{trans("messages.admin_Truck_Company")}}</a>
                        </li>
                        @if(request('from_page') != ''  && request('from_page') == 'tc_edit')
                        <li class="breadcrumb-item">
                            <a href="{{ route('truck-company.edit', [base64_encode($TCuserid), 'tabs=driver_detail'])}}" class="text-muted">{{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}</a>
                        </li>
                        @elseif(request('from_page') != '' && request('from_page') == 'tc_view')
                        <li class="breadcrumb-item">
                            <a href="{{ route('truck-company.show', [base64_encode($TCuserid), 'tabs=driver_detail'])}}" class="text-muted">{{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}</a>
                        </li>
                        @endif
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
                @if($TCuserid)
                    <input type="hidden" name="truck_id" value="{{$TCuserid}}">
                    <input type="hidden" name="from_page" value="{{request('from_page') ?? ''}}">
                @endif
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                    {{trans("messages.admin_common_driver_information")}}
                                </h3>
                                <div class="row">


                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">{{trans("messages.admin_Truck_Company")}}</label><span class="text-danger"> * </span>
                                            <select name="truck_company_id" class="form-control form-control-solid form-control-lg  @error('truck_company_id') is-invalid @enderror" >
                                                <option value="">{{trans("messages.admin_common_select_company")}}</option>
                                                @if(!empty($companies))
                                                @foreach($companies as $row)
                                                    @if($row->userCompanyInformation)
                                                        <option value="{{$row->id}}" {{ ($userDetails->truck_company_id == $row->id ? 'selected' : '') }} >
                                                            {{$row->userCompanyInformation->company_name}}
                                                        </option>
                                                    @endif
                                                @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('truck_company_id'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('truck_company_id') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">{{trans("messages.name")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="name" class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror" value="{{$userDetails->name ?? old('name')}}">
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
                                            <input type="text" name="email" class="form-control form-control-solid form-control-lg  @error('email') is-invalid @enderror" value="{{$userDetails->email ?? old('email')}}">
                                            @if ($errors->has('email'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('email') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="phone_number">{{trans("messages.Phone Number")}}</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <input type="number" name="phone_number" class="form-control form-control-solid form-control-lg @error('phone_number') is-invalid @enderror" value="{{old('phone_number') ?? $userDetails->phone_number}}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
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
                                            <label for="driver_picture">{{trans("messages.admin_common_Driver_Picture")}}</label><span class="text-danger"> * </span>
                                            <input type="file" name="driver_picture"  accept="image/*" class="form-control form-control-solid form-control-lg  @error('driver_picture') is-invalid @enderror" value="{{old('driver_picture')}}">
                                            @if ($errors->has('driver_picture'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('driver_picture') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                     <div class="col-xl-6">
                                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Driver_Picture")}} :</label>
                                        <div class="col-8">

                                        <span class="form-control-plaintext font-weight-bolder">
                                        @if (!empty($userDetails->userDriverDetail->driver_picture))
                                            <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo Config('constants.DRIVER_PICTURE_PATH') . $userDetails->userDriverDetail->driver_picture; ?>">
                                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{Config('constants.DRIVER_PICTURE_PATH').$userDetails->userDriverDetail->driver_picture }}" />
                                            </a>
                                        @endif
                                        </span>
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
                                            <a type="button" href="{{route('truck-company-driver.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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