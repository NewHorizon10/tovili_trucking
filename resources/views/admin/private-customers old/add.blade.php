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
                        Add New </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">
                            Private Customers</a>
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
                                Private Customer Information
                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">Name</label><span class="text-danger"> * </span>
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
                                            <label for="email">Email ID</label><span class="text-danger"> </span>
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
                                            <label>Phone Number</label><span class="text-danger"> * </span>
                                            <input type="number" name="phone_number" class="form-control form-control-solid form-control-lg  @error('phone_number') is-invalid @enderror" value="{{old('phone_number')}}">
                                            @if ($errors->has('phone_number'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('phone_number') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Password</label><span class="text-danger"> * </span>
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
                                            <label>Confirm Password</label><span class="text-danger"> * </span>
                                            <input type="password" name="confirm_password" class="form-control form-control-solid form-control-lg  @error('confirm_password') is-invalid @enderror">
                                            @if ($errors->has('confirm_password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('confirm_password') }}
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
                                            <label for="company_mobile_number">Company Mobile Number</label><span class="text-danger"> * </span>
                                            <input type="number" name="company_mobile_number" class="form-control form-control-solid form-control-lg  @error('company_mobile_number') is-invalid @enderror" value="{{old('company_mobile_number')}}">
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
                                            <label for="contact_person_email">Contact Person Email</label><span class="text-danger"> * </span>
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
                                            <label for="contact_person_phone_number">Contact Person Number</label><span class="text-danger"> * </span>
                                            <input type="number" name="contact_person_phone_number" class="form-control form-control-solid form-control-lg  @error('contact_person_phone_number') is-invalid @enderror" value="{{old('contact_person_phone_number')}}">
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
                                            <label for="contact_person_email">Company Location</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_location" class="form-control form-control-solid form-control-lg  @error('company_location') is-invalid @enderror" value="{{old('company_location')}}">
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
                                            <label for="company_logo">Company Logo</label><span class="text-danger"> * </span>
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