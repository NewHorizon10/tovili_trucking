@extends('frontend.layouts.customers')
@section('extraCssLinks')
@stop
@section('backgroundImage')

<body class="dashbord_page @if($user->customer_type == 'private') privateCustomer_page @endif">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="{{asset('public/frontend/img/logo.png')}}" alt="">
        </div>
    </div>
@stop
    @section('content')
    <div class="col-md-12 col-lg-9 col-sm-12">
        <div class="dashboardRight_block_wrapper">
           
            <form action="{{route('customerchangepassword')}}" class="profile-form" method="post" action="" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                                <div class="pageTopTitle">
                                    <h2 class="RightBlockTitle">{{ trans("messages.Change Password") }}</h2>
                                    </a>

                                </div>

                                <form method="POST" action="{{ route('change-password') }}">
                                    @csrf
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{ trans("messages.admin_common_Old_Password") }}</label><span class="text-danger"> * </span>
                                            <input type="password" name="old_password" class="form-control form-control-solid form-control-lg @error('old_password') is-invalid @enderror">
                                            @error('old_password')
                                            <div class="alert invalid-feedback admin_login_alert">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{ trans("messages.new_password") }}</label><span class="text-danger"> * </span>
                                            <input type="password" name="password" class="form-control form-control-solid form-control-lg @error('password') is-invalid @enderror">
                                            @error('password')
                                            <div class="alert invalid-feedback admin_login_alert">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{ trans("messages.confirm_password") }}</label><span class="text-danger"> * </span>
                                            <input type="password" name="confirm_password" class="form-control form-control-solid form-control-lg @error('confirm_password') is-invalid @enderror">
                                            @error('confirm_password')
                                            <div class="alert invalid-feedback admin_login_alert">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ trans("messages.Change Password") }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                
              
            </form>
        </div>
    </div>
    @stop
    @section('scriptCode')
  
    @stop