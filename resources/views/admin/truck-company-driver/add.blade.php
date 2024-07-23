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
                        {{trans("messages.admin_common_Add_New")}} {{trans("messages.admin_common_Driver")}} 
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index',($TCuserid? ["truck_id=".$TCuserid] :[]))}}" class="text-muted">{{trans("messages.admin_common_Driver")}}</a>
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
            <form action="{{route($model.'.save')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
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
                                            <select name="truck_company_id" class="form-control select2init  @error('truck_company_id') is-invalid @enderror" >
                                                <option value="">{{trans("messages.admin_common_select_company")}}</option>
                                                @foreach($companies as $row)
                                                    <option value="{{$row->id}}"
                                                        {{ old('truck_company_id') ? (old('truck_company_id') == $row->id ? 'selected' : '') : ($TCuserid == $row->id ? 'selected' : '') }}
                                                    
                                                    >{{$row->userCompanyInformation->company_name ?? ''}}</option>
                                                @endforeach
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
                                            <label for="driver_picture">{{trans("messages.admin_common_Driver_Picture")}}</label><span class="text-danger"> * </span>
                                            <input type="file" name="driver_picture" accept="image/*" class="form-control form-control-solid form-control-lg  @error('driver_picture') is-invalid @enderror" value="{{old('driver_picture')}}">
                                            @if ($errors->has('driver_picture'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('driver_picture') }}
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
</script>
@stop