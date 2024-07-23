@extends('frontend.layouts.defaultpages')
@section('backgroundImage')
<body class="homePage">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="img/logo.png" alt="">
        </div>
    </div>
    @stop
    @section('content')
    <div class="page_height">
        <section class="contact-us">
            <div class="container">
                <div class="contact-background">
                    <div class="row">
                        <div class="col-lg-6 order-1">
                            <div class="send-message-parent">
                                <h3 class="send-form-title">{{ trans('messages.send_message') }}</h3>
                                <form action="{{route('home.contactEnquiry')}}" method="post" class="contact-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label" for="contact-name">{{ trans('messages.your_name')}}</label>
                                            <span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                            <input type="text" name="name" class="form-control mb-1 @error('name') is-invalid @enderror" id="contact-name" value="{{old('name')}}">
                                            @if ($errors->has('name'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('name') }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label" for="contact-email">{{ trans('messages.email') }}</label>
                                            <span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                            <input type="text" name="email" class="form-control mb-1 @error('email') is-invalid @enderror" id="contact-email" value="{{old('email')}}">
                                            @if ($errors->has('email'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('email') }}
                                            </div>
                                            @endif
                                        </div>
                                         <div class="col-6 mb-3">
                                            <label class="form-label" for="contact-phone">{{ trans('messages.admin_phone_number') }}</label>
                                            <span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                            <input type="number" name="phone_number" class="form-control mb-1 @error('phone_number') is-invalid @enderror" id="contact-phone" value="{{old('phone_number')}}">
                                            @if ($errors->has('phone_number'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('phone_number') }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="col-12">
                                            <label for="contact-textarea" class="form-label">{{ trans('messages.message') }}</label>
                                            <span class="text-dangers" style="color: #dc3545!important;"> * </span>
                                            <textarea class="form-control mb-1 @error('message') is-invalid @enderror" name="message" id="contact-textarea" rows="3">{{old('message')}}</textarea>
                                            @if ($errors->has('message'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('message') }}
                                            </div>
                                            @endif
                                        </div>
                                        <div class="col-12">
                                            <button class="btn send-message mt-3">{{ trans('messages.send_message') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6 order-lg-1">
                            <div class="contact-info">
                                <div class="blur-title contact-blur-title">{{ trans('messages.contact_us') }}</div>
                                <h3 class="section-heading contact-title">{{ trans('messages.contact_info') }}</h3>
                                <div class="detail-block">
                                    <div class="contact-card">
                                        <a href="{{ Config::get('Contact.google_location') }}" target="_blank">
                                            <div class="contact-icon-box">
                                                <svg width="18" height="25" viewBox="0 0 18 25" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M9.18574 0.958008C4.44855 0.958008 0.617188 4.56495 0.617188 9.02465C0.617188 15.0746 9.18574 24.0056 9.18574 24.0056C9.18574 24.0056 17.7543 15.0746 17.7543 9.02465C17.7543 4.56495 13.9229 0.958008 9.18574 0.958008ZM3.06534 9.02465C3.06534 5.84409 5.80728 3.26276 9.18574 3.26276C12.5642 3.26276 15.3061 5.84409 15.3061 9.02465C15.3061 12.3435 11.7808 17.3102 9.18574 20.4101C6.63965 17.3333 3.06534 12.3089 3.06534 9.02465Z"
                                                        fill="#2844CC" />
                                                    <path
                                                        d="M9.18324 11.9059C10.8733 11.9059 12.2434 10.6161 12.2434 9.02499C12.2434 7.43389 10.8733 6.14404 9.18324 6.14404C7.49314 6.14404 6.12305 7.43389 6.12305 9.02499C6.12305 10.6161 7.49314 11.9059 9.18324 11.9059Z"
                                                        fill="#2844CC" />
                                                </svg>
                                            </div>
                                        </a>
                                        <a href="{{ Config::get('Contact.google_location') }}" target="_blank">
                                            <div class="contact-card-data">
                                                <div class="contact-card-head">{{ trans('messages.location') }}</div>
                                                @if(Config::get('Contact.address'))
                                                <address class="contact-add">
                                                    {{Config::get('Contact.address')}}
                                                </address>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                    <div class="contact-card">
                                        <a href="mailto:{{ Config::get('Contact.email') }}">
                                            <div class="contact-icon-box">
                                                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M21.667 4.33337H4.33366C3.14199 4.33337 2.17783 5.30837 2.17783 6.50004L2.16699 19.5C2.16699 20.6917 3.14199 21.6667 4.33366 21.6667H21.667C22.8587 21.6667 23.8337 20.6917 23.8337 19.5V6.50004C23.8337 5.30837 22.8587 4.33337 21.667 4.33337ZM21.667 8.66671L13.0003 14.0834L4.33366 8.66671V6.50004L13.0003 11.9167L21.667 6.50004V8.66671Z"
                                                        fill="#2844CC" />
                                                </svg>
                                            </div>
                                        </a>
                                        <a href="mailto:{{ Config::get('Contact.email') }}">
                                            <div class="contact-card-data">
                                                <div class="contact-card-head">{{ trans('messages.email') }}</div>
                                                @if(Config::get('Contact.email'))
                                                <address class="contact-add">
                                                    {{ Config::get('Contact.email') }}
                                                </address>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                    <div class="contact-card">
                                        <a href="tel:{{ Config::get('Contact.phone') }}">
                                            <div class="contact-icon-box">
                                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23.3333 14C23.0222 14 22.7449 13.888 22.5015 13.664C22.2581 13.44 22.1177 13.1631 22.0803 12.8333C21.8276 11.025 21.0642 9.48383 19.7902 8.20983C18.5162 6.93583 16.975 6.17283 15.1667 5.92083C14.8361 5.88194 14.5588 5.74583 14.3348 5.5125C14.1108 5.27917 13.9992 4.99722 14 4.66667C14 4.33611 14.1167 4.05883 14.35 3.83483C14.5833 3.61083 14.8556 3.51867 15.1667 3.55833C17.6167 3.83056 19.7069 4.83194 21.4375 6.5625C23.1681 8.29306 24.1694 10.3833 24.4417 12.8333C24.4806 13.1444 24.388 13.4167 24.164 13.65C23.94 13.8833 23.6631 14 23.3333 14ZM18.4625 14C18.2097 14 17.9861 13.9125 17.7917 13.7375C17.5972 13.5625 17.4514 13.3292 17.3542 13.0375C17.1986 12.4736 16.9019 11.9727 16.464 11.5348C16.0261 11.0969 15.5256 10.8006 14.9625 10.6458C14.6708 10.5486 14.4375 10.4028 14.2625 10.2083C14.0875 10.0139 14 9.78055 14 9.50833C14 9.11944 14.1361 8.80328 14.4083 8.55983C14.6806 8.31639 14.9819 8.23394 15.3125 8.3125C16.4014 8.56528 17.3398 9.08561 18.1277 9.8735C18.9156 10.6614 19.4355 11.5994 19.6875 12.6875C19.7653 13.0181 19.6778 13.3194 19.425 13.5917C19.1722 13.8639 18.8514 14 18.4625 14ZM23.275 24.5C20.7667 24.5 18.3213 23.9408 15.939 22.8223C13.5567 21.7039 11.4469 20.2261 9.60983 18.389C7.77272 16.5519 6.29495 14.4422 5.1765 12.0598C4.05806 9.6775 3.49922 7.23256 3.5 4.725C3.5 4.375 3.61667 4.08333 3.85 3.85C4.08333 3.61667 4.375 3.5 4.725 3.5H9.45C9.72222 3.5 9.96528 3.5875 10.1792 3.7625C10.3931 3.9375 10.5194 4.16111 10.5583 4.43333L11.3167 8.51667C11.3556 8.78889 11.3505 9.037 11.3015 9.261C11.2525 9.485 11.1409 9.68411 10.9667 9.85833L8.16667 12.7167C8.98333 14.1167 10.0042 15.4292 11.2292 16.6542C12.4542 17.8792 13.8056 18.9389 15.2833 19.8333L18.025 17.0917C18.2 16.9167 18.4287 16.7856 18.711 16.6985C18.9933 16.6114 19.2702 16.5869 19.5417 16.625L23.5667 17.4417C23.8389 17.5 24.0625 17.6314 24.2375 17.836C24.4125 18.0406 24.5 18.2786 24.5 18.55V23.275C24.5 23.625 24.3833 23.9167 24.15 24.15C23.9167 24.3833 23.625 24.5 23.275 24.5Z"
                                                        fill="#2844CC" />
                                                </svg>
                                            </div>
                                        </a>
                                        <a href="tel:{{ Config::get('Contact.phone') }}">
                                            <div class="contact-card-data contact-phone">
                                                <div class="contact-card-head">{{ trans('messages.phone') }}</div>
                                                @if(Config::get('Contact.phone'))
                                                <div class="contact-add">
                                                    {{ Config::get('Contact.phone') }}
                                                </div>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @endsection