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
                <a href="javascript:void(0)" class="transportRequestBtn">
                    Change Password
                </a>
            </div>
            <form class="profile-form" method="post" action="{{route('customers-profile-update')}}" enctype="multipart/form-data">
                @csrf
                @if($user->customer_type == 'business')
                <div class="row">
                    <div class="col-md-8 col-lg-8 order-1">
                        <h3 class="profile-title">Company Info</h3>
                        <div class="row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" name="company_name" value="{{$user_company_informations->company_name}}" id="company_name" placeholder="Company Name">
                                    @if ($errors->has('company_name'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_name') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_type" class="form-label">Company Type</label>

                                    <select class="form-select" name="company_type" id="company_type" aria-label="Default select example">
                                        @foreach($companyType as $compType)
                                        <option value="{{$compType->id}}" {{ $user_company_informations->company_type == $compType->id ? "selected" : "" }}>{{$compType->code}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('company_type'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_type') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_mobile_number" class="form-label">Company Mobile Number</label>
                                    <input type="text" class="form-control" name="company_mobile_number" id="company_mobile_number" value="{{$user_company_informations->company_mobile_number}}" placeholder="Company Mobile Number">
                                    @if ($errors->has('company_mobile_number'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_mobile_number') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group company-location">
                                    <label for="company_location" class="form-label">Company Location</label>
                                    <input type="text" class="form-control" name="company_location" id="company_location" value="{{$user_company_informations->company_location}}" placeholder="Company Location">
                                    <span class="loaction_icon">
                                        <svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.61436 0C3.36318 0 0.726562 2.46071 0.726562 5.49498C0.726562 9.61621 6.61436 15.7008 6.61436 15.7008C6.61436 15.7008 12.5022 9.61621 12.5022 5.49498C12.5022 2.46071 9.86769 0 6.61436 0ZM6.61436 7.45833C5.45399 7.45833 4.51065 6.57993 4.51065 5.49498C4.51065 4.41002 5.45184 3.53162 6.61436 3.53162C7.77688 3.53162 8.71806 4.41002 8.71806 5.49498C8.71806 6.57993 7.77688 7.45833 6.61436 7.45833Z" fill="#1535B9" />
                                        </svg>
                                    </span>
                                    @if ($errors->has('company_location'))
                                    <div class="text-danger">
                                        {{ $errors->first('company_location') }}
                                    </div>
                                    @endif
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 order-md-1">
                        <div class="avtarEdit-main">
                            <div class="avatar-edit">
                                <input type='file' id="company_logo" name="company_logo" accept=".png, .jpg, .jpeg">
                                {{-- <label for="company_logo">Company Logo</label> --}}
                            </div>
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <div id="imagePreview" style="background-image: url({{asset(Config('constants.COMPANY_LOGO__IMAGE_ROOT_PATH').$user_company_informations->company_logo)}});">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="seprator"></div>
                <div class="row">
                    <div class="col-md-8 col-lg-8 order-2">
                        <h3 class="profile-title">Contact Person Info</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_name" class="form-label">Person Name</label>
                                    <input type="text" name="contact_person_name" value="{{$user_company_informations->contact_person_name}}" class="form-control" id="contact_person_name" placeholder="Person Name">
                                    @if ($errors->has('contact_person_name'))
                                    <div class="text-danger">
                                        {{ $errors->first('contact_person_name') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_phone_number" class="form-label">Mobile Number</label>
                                    <div class="input-group only_left mb-3">
                                        <!-- <div class="input-group-prepend">
                                            <span class="form-control input-group-text form-number">+972</span>
                                        </div> -->
                                        <input type="text" class="form-control" name="contact_person_phone_number" value="{{$user_company_informations->contact_person_phone_number}}" id="contact_person_mobile_number" placeholder="Mobile Number">
                                    </div>
                                    @if ($errors->has('contact_person_phone_number'))
                                    <div class="text-danger">
                                        {{ $errors->first('contact_person_phone_number') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_email" class="form-label">Email Address</label>
                                    <input type="email" name="contact_person_email" value="{{$user_company_informations->contact_person_email}}" class="form-control" id="contact_person_email" placeholder="Email Address">
                                    @if ($errors->has('contact_person_email'))
                                    <div class="text-danger">
                                        {{ $errors->first('contact_person_email') }}
                                    </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 order-md-2">
                        <div class="avtarEdit-main">
                            <div class="avatar-edit">
                                <input type='file' id="contact_person_picture" name="contact_person_picture" accept=".png, .jpg, .jpeg">
                                <label for="contact_person_picture"></label>
                            </div>
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <div id="imagePreview" class="previewProfile" style="background-image: url({{asset(Config('constants.CONTACT_PERSON_PROFILE_IMAGE_ROOT_PATH').$user_company_informations->contact_person_picture)}});">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="row ">
                    <div class="col-md-8 col-lg-8 order-2">
                        <!-- <h3 class="profile-title">Contact Person Info</h3> -->
                        <div class="row Person_info_row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email
                                        Address</label>
                                    <input type="email" class="form-control"  id="email" name="email" value="{{$user->email}}" placeholder="Email">
                                    @if ($errors->has('email'))
                                    <div class="text-danger">
                                        {{ $errors->first('email') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{$user->name}}"  placeholder="Name">
                                    @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <div class="input-group only_left mb-3">
                                        <!-- <div class="input-group-prepend">
                                            <span class="form-control input-group-text form-number">+972</span>
                                        </div> -->
                                        <input type="text" class="form-control" name="phone_number"  id="phone_number" value="{{$user->phone_number}}"  placeholder="Phone Number">
                                    </div>
                                    @if ($errors->has('phone_number'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('phone_number') }}
                                    </div>
                                    @endif
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group company-location">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" name="location" value="{{$user->location}}"  id="location" placeholder="Address">
                                    <span class="loaction_icon">
                                        <svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.61436 0C3.36318 0 0.726562 2.46071 0.726562 5.49498C0.726562 9.61621 6.61436 15.7008 6.61436 15.7008C6.61436 15.7008 12.5022 9.61621 12.5022 5.49498C12.5022 2.46071 9.86769 0 6.61436 0ZM6.61436 7.45833C5.45399 7.45833 4.51065 6.57993 4.51065 5.49498C4.51065 4.41002 5.45184 3.53162 6.61436 3.53162C7.77688 3.53162 8.71806 4.41002 8.71806 5.49498C8.71806 6.57993 7.77688 7.45833 6.61436 7.45833Z" fill="#1535B9"></path>
                                        </svg>
                                    </span>
                                    @if ($errors->has('location'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('location') }}
                                    </div>
                                    @endif
                                </div>
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
                                    <div id="imagePreview" style="background-image: url({{asset(Config('constants.CUSTOMER_IMAGE_ROOT_PATH').$user->image)}});">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <button type="submit" class="save-updateBtn">
                    Save & Update
                </button>
            </form>
        </div>
    </div>
    @stop
    @section('scriptCode')
    <script>
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

        $("#company_logo").change(function() {
            readURL(this);
        });

        $("#contact_person_picture").change(function() {

            readURLProfile(this);
        });

        function readURLProfile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.previewProfile').css('background-image', 'url(' + e.target.result + ')');
                    $('.previewProfile').hide();
                    $('.previewProfile').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @stop