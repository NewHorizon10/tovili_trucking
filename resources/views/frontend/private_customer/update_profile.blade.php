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
                <h2 class="RightBlockTitle">My Profile</h2>
            </div>
            <div class="container">
                <form action="{{route('private-customer-profile-update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success">Upload</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="name">Name</label><span class="text-danger"> * </span>
                                <input type="text" name="name" class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror" value="{{$userDetails->name ?? old('name')}}">
                                @if ($errors->has('name'))
                                <div class=" invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                                @endif
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="email">Email ID</label><span class="text-danger"> </span>
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
                                    <label>Phone Number</label><span class="text-danger"> * </span>
                                    <div class="input-group only_left mb-3">
                                        <!-- <div class="input-group-prepend">
                                            <span class="form-control input-group-text form-number">+972</span>
                                        </div> -->
                                        <input type="number" name="phone_number" class="form-control form-control-solid form-control-lg  @error('phone_number') is-invalid @enderror" value="{{old('phone_number') ?? $userDetails->phone_number}}">
                                    </div>
                                    @if ($errors->has('phone_number'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('phone_number') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="contact_person_email">Location</label><span class="text-danger"> * </span>
                                    <input type="text" name="location" class="form-control form-control-solid form-control-lg  @error('location') is-invalid @enderror" value="{{ $userDetails->location ?? old('location') }}">
                                    @if ($errors->has('location'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('location') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                            <div>
                                <button button type="submit" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection