@extends('admin.layouts.login_layout')
@section('content')
<div class="d-flex flex-column flex-root">
    <div class="login login-4 wizard d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="login-container d-flex flex-center bgi-size-cover bgi-no-repeat flex-row-fluid p-8">
            <div class="login-content d-flex flex-column card p-5 p-md-10">
                <a href="{{route('adminpnlx')}}" class="login-logo pb-8 text-center">
                    <img src="{{asset('/public/img/logo.png')}}" class="max-h-80px" alt="" />
                </a>
                <div class="login-form">
                    <form action="{{route('forgetPassword')}}" method="post" class="form" id="kt_login_singin_form">
                        @csrf
                        <div class="mb-12 text-center">
                            <h3 class="font-weight-bold text-dark">{{trans('messages.Forgot password')}}</h3>
                            <p>{{trans('messages.admin_common_Enter_your_email_to_reset_your_password')}}</p>
                        </div>
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark">{{trans('messages.admin_common_Your_Email')}}</label>
                            <input type="text" name="email" placeholder="{{trans('messages.email')}}" class="form-control form-control-solid h-auto py-5 px-6 rounded-lg border-0 @error('email') is-invalid @enderror" value="{{old('email')}}">
                            @if ($errors->has('email'))
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <a href="javascript:void(0);" id="kt_login_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">{{trans('messages.submit')}}</a>
                            <a href="{{route('adminpnlx')}}" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">{{trans('messages.admin_Back_To_Login')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        $('input').keypress(function(e) {
            if (e.which == 13) {
                $("#kt_login_singin_form").submit();
            }
        });

        $("#kt_login_submit").click(function(e) {
            $("#kt_login_singin_form").submit();
        });
    });
</script>
@stop