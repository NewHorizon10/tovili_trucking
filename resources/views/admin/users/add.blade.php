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
                    {{trans("messages.admin_common_Add_New")}} </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">{{trans("messages.admin_common_customers")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route('users.save')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                {{trans("messages.admin_common_customer_information")}}
                                </h3>
                                <div class="row">
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
                                            <label for="email">{{trans("messages.Email Address")}}</label><span class="text-danger"> * </span>
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
                                            <label>{{trans('messages.Password')}}</label><span class="text-danger"> * </span>
                                            <input type="password" name="password" class="form-control form-control-solid form-control-lg  @error('password') is-invalid @enderror">
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
                                            <input type="password" name="confirm_password" class="form-control form-control-solid form-control-lg  @error('confirm_password') is-invalid @enderror">
                                            @if ($errors->has('confirm_password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('confirm_password') }}
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
                                            <a type="button" href="{{route('users.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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