@extends('frontend.layouts.default')
@section('extraCssLinks')
@stop
@section('backgroundImage')

    <body class="ogin-wrapper driver_page">
        <!-- loader  -->
        <div class="loader-wrapper" style="display: none;">
            <div class="loader">
                <img src="img/logo.png" alt="">
            </div>
        </div>
        <link rel="stylesheet" type="text/css" href="{{ asset('public/frontend/css/dropzone.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('public/frontend/css/dashboard-dropzone.css') }}">
    @stop
    @section('content')
        <section class="form_section">
            <div class="container">
                <div class="outer_companyform_box customers_box">
                    <div class="track_company_box track_company_page">
                        <div class="white_form_theme">
                            <h1 class="form_page_title">
                                <span class="">{{ trans('messages.create_a_new_delivery_request') }}</span>
                            </h1>
                            <div class="companyFormBox">
                                <form method="post" id="private-shipment-request-form" autocomplete="off">
                                    @csrf
                                    <div class="row">

                                        <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail2" class="form-label">Choose Service</label>
                                                    <input type="text" class="form-control" id="exampleInputEmail2"
                                                        aria-describedby="exampleInputEmail2">
                                                </div>
                                            </div>       -->

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipment_servise"
                                                    class="form-label">{{ trans('messages.choose_service') }}</label>
                                                <select id="shipment_servise" class="form-select 1-form-fields"
                                                    data-is-required="1" aria-label="Default select example"
                                                    name="shipment_servise">
                                                    <option selected value="">{{ trans('messages.Select') }}</option>
                                                    @foreach ($truckTypes as $row)
                                                        <option value="{{ $row->id }}">
                                                            {{ $row->truckTypeDiscription->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-danger text-danger-text error-shipment_servise"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group enter-floor" style="display:none;">
                                                <label for="number_of_items"
                                                    class="form-label">{{ trans('messages.number_of_items') }}</label>
                                                <input type="text" class="form-control 1-form-fields"
                                                    data-is-required="0" id="number_of_items"
                                                    aria-describedby="number_of_items" name="number_of_items"
                                                    placeholder="{{ trans('messages.enter_number_of_items') }}">
                                                <small class="text-danger text-danger-text error-number_of_items"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-12"></div>

                                        
                                        <div class="col-md-6">
                                            <div class="form-group enter-floor" style="display:none;">
                                                <label for="how_many_floor"
                                                    class="form-label">{{ trans('messages.floor') }}</label>
                                                <input type="text" placeholder="{{ trans('messages.floor') }}"
                                                    class="form-control 1-form-fields" data-is-required="0"
                                                    id="how_many_floor" aria-describedby="how_many_floor"
                                                    name="how_many_floor"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);">
                                                <small class="text-danger text-danger-text error-how_many_floor"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" style="display:none">
                                                <label
                                                    class="form-label">{{ trans('messages.is_there_an_elevator_in_binin') }}</label>
                                                <div class="customRadio">
                                                    <span class="radioLabelrow">
                                                        <input type="radio" id="elevators_yes" name="elevators"
                                                            value="yes">
                                                        <label for="elevators_yes">{{ trans('messages.yes') }}</label>
                                                    </span>
                                                    <sapn class="radioLabelrow">
                                                        <input type="radio" id="elevators_no" name="elevators"
                                                            value="no" checked>
                                                        <label for="elevators_no">{{ trans('messages.no') }}</label>
                                                    </sapn>
                                                </div>
                                                <small class="text-danger text-danger-text error-elevators"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group enter-rooms" style="display:none;">
                                                <label for="pickup_rooms"
                                                    class="form-label">{{ trans('messages.how_many_rooms') }}</label>
                                                <input type="text" class="form-control 1-form-fields"
                                                    data-is-required="0" placeholder="{{ trans('messages.enter_rooms') }}"
                                                    id="pickup_rooms" aria-describedby="pickup_rooms" name="how_many_rooms"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);">
                                                <small class="text-danger text-danger-text error-how_many_rooms"></small>
                                            </div>
                                        </div>

                                        <div class="col-md-12"></div>
                                        <span style="display:none;" class="description-number-of-items"></span>
                                        <div class="col-md-12"></div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date_of_transport"
                                                    class="form-label">{{ trans('messages.date_of_transport') }}</label>
                                                <div class="withIconInput">
                                                    <svg class="useIcon calendar-icon" width="20" height="22"
                                                        viewBox="0 0 20 22" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M17 2.45265H15V1.36939C15 1.08209 14.8946 0.806563 14.7071 0.603412C14.5196 0.400262 14.2652 0.286133 14 0.286133C13.7348 0.286133 13.4804 0.400262 13.2929 0.603412C13.1054 0.806563 13 1.08209 13 1.36939V2.45265H7V1.36939C7 1.08209 6.89464 0.806563 6.70711 0.603412C6.51957 0.400262 6.26522 0.286133 6 0.286133C5.73478 0.286133 5.48043 0.400262 5.29289 0.603412C5.10536 0.806563 5 1.08209 5 1.36939V2.45265H3C2.20435 2.45265 1.44129 2.79504 0.87868 3.40449C0.316071 4.01394 0 4.84054 0 5.70243V18.7015C0 19.5634 0.316071 20.39 0.87868 20.9995C1.44129 21.6089 2.20435 21.9513 3 21.9513H17C17.7956 21.9513 18.5587 21.6089 19.1213 20.9995C19.6839 20.39 20 19.5634 20 18.7015V5.70243C20 4.84054 19.6839 4.01394 19.1213 3.40449C18.5587 2.79504 17.7956 2.45265 17 2.45265ZM18 18.7015C18 18.9888 17.8946 19.2644 17.7071 19.4675C17.5196 19.6707 17.2652 19.7848 17 19.7848H3C2.73478 19.7848 2.48043 19.6707 2.29289 19.4675C2.10536 19.2644 2 18.9888 2 18.7015V11.1187H18V18.7015ZM18 8.95221H2V5.70243C2 5.41513 2.10536 5.1396 2.29289 4.93645C2.48043 4.7333 2.73478 4.61917 3 4.61917H5V5.70243C5 5.98973 5.10536 6.26526 5.29289 6.46841C5.48043 6.67156 5.73478 6.78569 6 6.78569C6.26522 6.78569 6.51957 6.67156 6.70711 6.46841C6.89464 6.26526 7 5.98973 7 5.70243V4.61917H13V5.70243C13 5.98973 13.1054 6.26526 13.2929 6.46841C13.4804 6.67156 13.7348 6.78569 14 6.78569C14.2652 6.78569 14.5196 6.67156 14.7071 6.46841C14.8946 6.26526 15 5.98973 15 5.70243V4.61917H17C17.2652 4.61917 17.5196 4.7333 17.7071 4.93645C17.8946 5.1396 18 5.41513 18 5.70243V8.95221Z"
                                                            fill="#1535B9" />
                                                    </svg>
                                                    <input type="text"
                                                        class="form-control inputStartPadding 1-form-fields"
                                                        data-is-required="1" id="date_of_transport" value=""
                                                        readonly aria-describedby="date_of_transport" name="request_date"
                                                        placeholder="{{ trans('messages.date_of_transport') }}">
                                                    <div class="qty-container">
                                                        <button class="qty-btn-plus btn-light" type="button"><svg
                                                                width="13" height="13" viewBox="0 0 13 13"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M0.874707 7.15002H5.59971V11.875C5.59971 12.054 5.67082 12.2257 5.79741 12.3523C5.924 12.4789 6.09569 12.55 6.27471 12.55C6.45373 12.55 6.62542 12.4789 6.752 12.3523C6.87859 12.2257 6.94971 12.054 6.94971 11.875V7.15002H11.6747C11.8537 7.15002 12.0254 7.07891 12.152 6.95232C12.2786 6.82573 12.3497 6.65405 12.3497 6.47502C12.3497 6.296 12.2786 6.12431 12.152 5.99773C12.0254 5.87114 11.8537 5.80002 11.6747 5.80002H6.94971V1.07502C6.94971 0.896003 6.87859 0.724314 6.752 0.597727C6.62542 0.47114 6.45373 0.400024 6.27471 0.400024C6.09569 0.400024 5.924 0.47114 5.79741 0.597727C5.67082 0.724314 5.59971 0.896003 5.59971 1.07502V5.80002H0.874707C0.695686 5.80002 0.523997 5.87114 0.39741 5.99773C0.270823 6.12431 0.199707 6.296 0.199707 6.47502C0.199707 6.65405 0.270823 6.82573 0.39741 6.95232C0.523997 7.07891 0.695686 7.15002 0.874707 7.15002Z"
                                                                    fill="white" />
                                                            </svg>
                                                        </button>
                                                        <input type="number" name="qty" value="0"
                                                            class="input-qty" />
                                                        <button class="qty-btn-minus btn-light" type="button"><svg
                                                                width="12" height="2" viewBox="0 0 12 2"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M10.9004 1.70035H1.10039C0.914739 1.70035 0.736692 1.6266 0.605416 1.49533C0.474141 1.36405 0.400391 1.18601 0.400391 1.00035C0.400391 0.814703 0.474141 0.636655 0.605416 0.505379C0.736692 0.374104 0.914739 0.300354 1.10039 0.300354H10.9004C11.086 0.300354 11.2641 0.374104 11.3954 0.505379C11.5266 0.636655 11.6004 0.814703 11.6004 1.00035C11.6004 1.18601 11.5266 1.36405 11.3954 1.49533C11.2641 1.6266 11.086 1.70035 10.9004 1.70035Z"
                                                                    fill="white" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="text-danger text-danger-text error-request_date"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="choose_time"
                                                    class="form-label">{{ trans('messages.choose_time') }}</label>
                                                <select id="choose_time" class="form-select 1-form-fields"
                                                    data-is-required="0" aria-label="Default select example"
                                                    name="request_time">
                                                    <option selected value="">{{ trans('messages.Select') }}
                                                    </option>
                                                    @foreach ($shipmentTime as $row)
                                                        <option value="{{ $row->id }}">
                                                            {{ $row->lookupDiscription->code }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-danger text-danger-text error-request_time"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="need-packaging"
                                                    class="form-label">{{ trans('messages.need_packaging') }}</label>
                                                <div class="customRadio">
                                                    <span class="radioLabelrow">
                                                        <input type="radio" id="needpackagingyes" name="need_packaging"
                                                            value="yes">
                                                        <label for="needpackagingyes">{{ trans('messages.yes') }}</label>
                                                    </span>
                                                    <sapn class="radioLabelrow">
                                                        <input type="radio" id="needpackagingno" name="need_packaging"
                                                            value="no" checked>
                                                        <label for="needpackagingno">{{ trans('messages.no') }}</label>
                                                    </sapn>
                                                </div>
                                                <small class="text-danger text-danger-text error-need_packaging"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-12">

                                            <!-- <label for="exampleInputEmail2" class="form-label">Please upload pdf, doc. or images</label> -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="dropzone" id="drop-areaa">
                                                            <div class="GalleryImagesAppends ">
                                                                <div
                                                                    class="gallery_item itemsappends d-inline-flex add_img_upload">
                                                                    <label class="text-center dz-message needsclick"
                                                                        for="ImageUploads" style="color: #fff;">
                                                                        <div class="dz-message needsclick">
                                                                            <span class="dropzoneText">
                                                                                <span class="svg-files">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        height="32" fill="none"
                                                                                        viewBox="0 0 24 24"
                                                                                        stroke-width="1.5"
                                                                                        stroke="currentColor"
                                                                                        class="w-6 h-6">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                                                    </svg>
                                                                                </span>
                                                                            </span>
                                                                        </div> <span
                                                                            style="color:#0f30b6;">{{ trans('messages.upload_here') }}</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <input
                                                                style="visibility: hidden;height:0px; position: absolute;"
                                                                type="file" accept=".png, .jpg, .jpeg, .pdf, .doc" onchange="GalleryImages(this.files)"
                                                                name="" multiple id="ImageUploads">
                                                            <progress hidden id="progress-bar" max=100 value=0></progress>
                                                        </div>
                                                        <div class="file-message" style="color: #fff;">
                                                            <svg width="32" height="32" viewBox="0 0 32 32"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_11_12822)">
                                                                    <path
                                                                        d="M27.9109 0H4.08906C1.83431 0 0 1.83438 0 4.08913V27.9109C0 30.1656 1.83431 32 4.08906 32H27.9109C30.1657 32 32 30.1656 32 27.9109V4.08913C32 1.83438 30.1657 0 27.9109 0ZM30.1151 27.9109C30.1151 29.1264 29.1263 30.1151 27.9109 30.1151H4.08906C2.87363 30.1151 1.88481 29.1263 1.88481 27.9109V24.473L8.08825 19.1948C8.31463 19.0022 8.64487 19.0004 8.87337 19.1901L12.7592 22.4169C13.1339 22.7279 13.6839 22.7024 14.0282 22.3577L23.2613 13.1106C23.4282 12.9434 23.6227 12.9272 23.7243 12.9324C23.8256 12.9376 24.0177 12.9737 24.1667 13.1571L30.1152 20.4815L30.1151 27.9109ZM30.1151 17.4916L25.6297 11.9687C25.1849 11.4209 24.5256 11.0861 23.8209 11.0499C23.1166 11.0143 22.4261 11.2794 21.9275 11.7788L13.302 20.4176L10.0776 17.7401C9.14269 16.9638 7.79244 16.9718 6.86681 17.7594L1.88481 21.9982V4.08913C1.88481 2.87369 2.87363 1.88488 4.08906 1.88488H27.9109C29.1264 1.88488 30.1151 2.87369 30.1151 4.08913V17.4916Z"
                                                                        fill="#C7E9FF" />
                                                                    <path
                                                                        d="M10.0739 3.93726C7.56792 3.93726 5.5293 5.97601 5.5293 8.48182C5.5293 10.9877 7.56798 13.0264 10.0739 13.0264C12.5797 13.0264 14.6184 10.9877 14.6184 8.48182C14.6184 5.97594 12.5798 3.93726 10.0739 3.93726ZM10.0739 11.1416C8.60723 11.1416 7.41411 9.94838 7.41411 8.48182C7.41411 7.01519 8.60723 5.82207 10.0739 5.82207C11.5405 5.82207 12.7336 7.01526 12.7336 8.48182C12.7336 9.94838 11.5405 11.1416 10.0739 11.1416Z"
                                                                        fill="#C7E9FF" />
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_11_12822">
                                                                        <rect width="32" height="32"
                                                                            fill="white" />
                                                                    </clipPath>
                                                                </defs>
                                                            </svg> {{ trans('messages.please_upload_pdf_doc_or_images') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--  -->
                                        <fieldset class="col-md-12 row">
                                            <legend>{{ trans('messages.pickup_detail') }} </legend>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_city"
                                                        class="form-label">{{ trans('messages.pickup_city') }}</label>
                                                    <div class="withIconInput">
                                                        <svg class="useIcon" width="13" height="16"
                                                            viewBox="0 0 13 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z"
                                                                fill="#1535B9" />
                                                        </svg>
                                                        <input type="text" name="company_city" id="company_city" class="form-control 1-form-fields" data-is-required="1" placeholder="{{ trans('messages.pickup_city') }}" aria-describedby="pickup_city">
                                                        <input type="hidden" name="company_zip_code" id="company_zip_code">
                                                        <input type="hidden" name="company_latitude" class="1-form-fields" data-type="gLocation" data-target-name="company_city" id="company_latitude">
                                                        <input type="hidden" name="company_longitude" class="1-form-fields" data-type="gLocation" data-target-name="company_city" id="company_longitude">
                                                    </div>
                                                    <small class="text-danger text-danger-text error-company_city"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_location"
                                                        class="form-label">{{ trans('messages.pickup_location') }}</label>
                                                    <div class="withIconInput">
                                                        <!-- <svg class="useIcon" width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z" fill="#1535B9"/>
                                                            </svg> -->
                                                        <input type="text" class="form-control 1-form-fields"
                                                            data-is-required="1" id="pickup_location"
                                                            placeholder="{{ trans('messages.pickup_location') }}"
                                                            aria-describedby="pickup_location" name="pickup_location">
                                                        <!-- <input type="hidden" name="company_zip_code" id="company_zip_code">
                                                        <input type="hidden" name="company_latitude" id="company_latitude">
                                                        <input type="hidden" name="company_longitude" id="company_longitude"> -->
                                                    </div>
                                                    <small
                                                        class="text-danger text-danger-text error-pickup_location"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <!--  -->
                                            <!-- <div class="col-md-6">
                                                    <div class="form-group" style="display:none;">
                                                        <label for="" class="form-label">{{ trans('messages.floor') }}?</label>
                                                        <div class="customRadio">
                                                            <span class="radioLabelrow">
                                                                <input type="radio" id="pickup_floor_yes" name="pickup_floor" value="yes">
                                                                <label for="pickup_floor_yes">{{ trans('messages.yes') }}</label>
                                                            </span>
                                                            <sapn class="radioLabelrow">
                                                                <input type="radio" id="pickup_floor_no" name="pickup_floor" value="no" checked>
                                                                <label for="pickup_floor_no">{{ trans('messages.no') }}</label>
                                                            </sapn>
                                                        </div>
                                                    <small class="text-danger text-danger-text error-pickup_floor"></small>
                                                    </div>
                                                </div>          -->

                                            <!--  -->
                                            <!--  -->
                                            <!-- <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail2" class="form-label">Do you need a crane?</label>
                                                        <div class="customRadio">
                                                            <span class="radioLabelrow">
                                                                <input type="radio" id="craneyes" name="craneRadio" value="yes">
                                                                <label for="craneyes">Yes</label>
                                                            </span>
                                                            <sapn class="radioLabelrow">
                                                                <input type="radio" id="craneno" name="craneRadio" value="no" checked>
                                                                <label for="craneno">No</label>
                                                            </sapn>
                                                        </div>
                                                    </div>
                                                </div>          -->
                                            <!--  -->
                                            <!-- <div class="col-md-6">
                                                    <div class="form-group" style="display:none">
                                                        <label for="exampleInputEmail2" class="form-label">Rooms?</label>
                                                        <div class="customRadio">
                                                            <span class="radioLabelrow">
                                                                <input type="radio" id="roomyes" name="roomRadio" value="yes">
                                                                <label for="roomyes">Yes</label>
                                                            </span>
                                                            <sapn class="radioLabelrow">
                                                                <input type="radio" id="roomno" name="roomRadio" value="no" checked>
                                                                <label for="roomno">No</label>
                                                            </sapn>
                                                        </div>
                                                    </div>
                                                </div> -->
                                        </fieldset>
                                        <fieldset class="col-md-12 row">
                                            <legend>{{ trans('messages.drop_detail') }}</legend>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_city"
                                                        class="form-label">{{ trans('messages.drop_city') }}</label>
                                                    <div class="withIconInput">
                                                        <svg class="useIcon" width="13" height="16"
                                                            viewBox="0 0 13 16" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z"
                                                                fill="#1535B9" />
                                                        </svg>
                                                        <input type="text" name="drop_city" id="drop_city" class="form-control 1-form-fields" data-is-required="1" placeholder="{{ trans('messages.drop_city') }}" aria-describedby="drop_city">
                                                        <input type="hidden" name="drop_zip_code" id="drop_zip_code">
                                                        <input type="hidden" name="drop_latitude" class="1-form-fields" data-type="gLocation" data-target-name="drop_city" id="drop_latitude">
                                                        <input type="hidden" name="drop_longitude" class="1-form-fields" data-type="gLocation" data-target-name="drop_city" id="drop_longitude">
                                                    </div>
                                                    <small class="text-danger text-danger-text error-drop_city"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="drop_location"
                                                        class="form-label">{{ trans('messages.drop_location') }}</label>
                                                    <div class="withIconInput">
                                                        <!-- <svg class="useIcon" width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z" fill="#1535B9"/>
                                                            </svg> -->
                                                        <input type="text" class="form-control 1-form-fields"
                                                            data-is-required="1" id="drop_location"
                                                            placeholder="{{ trans('messages.drop_location') }}"
                                                            aria-describedby="drop_location" name="drop_location">
                                                        <!-- <input type="hidden" name="drop_zip_code" id="drop_zip_code">
                                                            <input type="hidden" name="drop_latitude" id="drop_latitude">
                                                            <input type="hidden" name="drop_longitude" id="drop_longitude"> -->
                                                    </div>
                                                    <small
                                                        class="text-danger text-danger-text error-drop_location"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-12"></div>
                                            <!--  -->
                                            <!-- <div class="col-md-6">
                                                    <div class="form-group" style="display:none;">
                                                        <label for="" class="form-label">{{ trans('messages.floor') }}?</label>
                                                        <div class="customRadio">
                                                            <span class="radioLabelrow">
                                                                <input type="radio" id="drop_floor_yes" name="drop_floor" value="yes">
                                                                <label for="drop_floor_yes">{{ trans('messages.yes') }}</label>
                                                            </span>
                                                            <sapn class="radioLabelrow">
                                                                <input type="radio" id="drop_floor_no" name="drop_floor" value="no" checked>
                                                                <label for="drop_floor_no">{{ trans('messages.no') }}</label>
                                                            </sapn>
                                                        </div>
                                                    <small class="text-danger text-danger-text error-drop_floor"></small>
                                                    </div>
                                                </div>          -->
                                            <div class="col-md-6">
                                                <div class="form-group enter-floor" style="display:none;">
                                                    <label for="drop_floor_no"
                                                        class="form-label">{{ trans('messages.floor') }}</label>
                                                    <input type="text" placeholder="{{ trans('messages.floor') }}"
                                                        class="form-control 1-form-fields" data-is-required="0"
                                                        id="drop_floor_no" aria-describedby="drop_floor_no"
                                                        name="drop_how_many_floor"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);">
                                                    <small
                                                        class="text-danger text-danger-text error-drop_how_many_floor"></small>
                                                </div>
                                            </div>
                                            <!--  -->

                                            <div class="col-md-6">
                                                <div class="form-group" style="display:none">
                                                    <label
                                                        class="form-label">{{ trans('messages.is_there_an_elevator_in_binin') }}</label>
                                                    <div class="customRadio">
                                                        <span class="radioLabelrow">
                                                            <input type="radio" id="drop_elevator_yes"
                                                                name="drop_elevator" value="yes">
                                                            <label
                                                                for="drop_elevator_yes">{{ trans('messages.yes') }}</label>
                                                        </span>
                                                        <sapn class="radioLabelrow">
                                                            <input type="radio" id="drop_elevator_no"
                                                                name="drop_elevator" value="no" checked>
                                                            <label
                                                                for="drop_elevator_no">{{ trans('messages.no') }}</label>
                                                        </sapn>
                                                    </div>
                                                    <small
                                                        class="text-danger text-danger-text error-drop_elevator"></small>
                                                </div>
                                            </div>
                                            <!--  -->
                                            <!-- <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail2" class="form-label">Do you need a crane?</label>
                                                        <div class="customRadio">
                                                            <span class="radioLabelrow">
                                                                <input type="radio" id="craneyes" name="craneRadio" value="yes">
                                                                <label for="craneyes">Yes</label>
                                                            </span>
                                                            <sapn class="radioLabelrow">
                                                                <input type="radio" id="craneno" name="craneRadio" value="no" checked>
                                                                <label for="craneno">No</label>
                                                            </sapn>
                                                        </div>
                                                    </div>
                                                </div>          -->
                                            <!--  -->
                                            <!-- <div class="col-md-6">
                                                    <div class="form-group" style="display:none">
                                                        <label for="exampleInputEmail2" class="form-label">Rooms?</label>
                                                        <div class="customRadio">
                                                            <span class="radioLabelrow">
                                                                <input type="radio" id="roomyes" name="roomRadio" value="yes">
                                                                <label for="roomyes">Yes</label>
                                                            </span>
                                                            <sapn class="radioLabelrow">
                                                                <input type="radio" id="roomno" name="roomRadio" value="no" checked>
                                                                <label for="roomno">No</label>
                                                            </sapn>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            <div class="col-md-6">
                                                <div class="form-group enter-rooms" style="display:none;">
                                                    <label for="drop_rooms"
                                                        class="form-label">{{ trans('messages.how_many_rooms') }}</label>
                                                    <input type="text" class="form-control 1-form-fields"
                                                        data-is-required="0"
                                                        placeholder="{{ trans('messages.enter_rooms') }}"
                                                        id="drop_how_many_rooms" aria-describedby="drop_how_many_rooms"
                                                        name="drop_how_many_rooms"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);">
                                                    <small
                                                        class="text-danger text-danger-text error-drop_how_many_rooms"></small>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <!-- Drop Details  -->
                                        <!--  -->


                                        <!-- <div class="uploadLinkRow">
                                                    
                                                    <div class="form-group">

                                                        <label class="picture" for="picture__input" tabIndex="0">
                                                            <span class="picture__image">
                                                                <svg width="28" height="34" viewBox="0 0 28 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M8.333 25.5839H20.1114V13.8411H27.962L14.2222 0.142822L0.482422 13.8411H8.333V25.5839ZM0.482422 29.4973H27.9646V33.4107H0.482422V29.4973Z" fill="#728BF2"/>
                                                                </svg>
                                                                </span>
                                                        </label>
                                                        
                                                        <input type="file" name="picture__input" id="picture__input">
                                                    </div>
                                                    <div class="form-group">
                                                    <label class="morePictureUploadcard" for="picture__input" tabIndex="0">
                                                        <span class="picture__image">
                                                            <img src="img/pdf-icon.png" alt="">
                                                                </span>
                                                        </label>
                                                        
                                                        <input type="file" name="picture__input" id="picture__input">
                                                    </div>
                                                    <div class="form-group">

                                                        <label class="morePictureUploadcard" for="picture__input" tabIndex="0">
                                                            <span class="picture__image">
                                                            <img src="img/google-docs.png" alt="">
                                                                </span>
                                                        </label>
                                                        <input type="file" name="picture__input" id="picture__input">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="morePictureUploadcard" for="picture__input" tabIndex="0">
                                                            <span class="picture__image">
                                                            <img src="img/mask-group.png" alt="">
                                                                </span>
                                                        </label>
                                                        
                                                        <input type="file" name="picture__input" id="picture__input">
                                                    </div>
                                                    <div class="form-group">
                                                            <label class="morePictureUploadcard" for="picture__input" tabIndex="0">
                                                                <span class="picture__image">
                                                                    <img src="img/mask-group.png" alt="">
                                                                </span>
                                                            </label>
                                                            
                                                            <input type="file" name="picture__input" id="picture__input">
                                                        </div>
                                                    </div> -->
                                    </div>
                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <button type="button"
                                                class="btn primary-btn w-100 submit submit-private-shipment-request">{{ trans('messages.send_a_transport_request') }}</button>
                                        </div>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
    @stop

    @section('scriptCode')
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script>
            // function initMap(latitude,longitude){


            //    var ac = new google.maps.places.Autocomplete(document.getElementById('exampleInputaddress'));

            //    if((latitude == undefined || longitude == undefined) || (latitude == null || longitude == null)){
            //        var latitude = "26.8289443";
            //        var longitude = "75.8056178";  
            //    }

            //    ac.addListener('place_changed', () => {
            //            $('#map').show()
            //            var place = ac.getPlace();
            //            const latitude = place.geometry.location.lat();
            //            const longitude = place.geometry.location.lng();

            //            $('#lat').val(latitude)
            //            $('#lng').val(longitude)
            //            var latLng = new google.maps.LatLng(latitude,longitude);
            //            var mapOptions = {
            //            center: latLng,
            //            zoom: 10,
            //            zoomControl:true,
            //            scrollwheel:true,
            //            disableDoubleClickZoom:true,
            //            mapTypeId: google.maps.MapTypeId.ROADMAP
            //            };
            //            var map = new google.maps.Map(document.getElementById("map"), mapOptions);
            //            var marker = new google.maps.Marker({
            //            position: latLng,
            //            map: map,
            //            title: "Location Marker"
            //        });
            //    });

            //    var latLng = new google.maps.LatLng(latitude,longitude);
            //    var mapOptions = {
            //    center: latLng,
            //    zoom: 10,
            //    mapTypeId: google.maps.MapTypeId.ROADMAP
            //    };
            //    var map = new google.maps.Map(document.getElementById("map"), mapOptions);
            //    var marker = new google.maps.Marker({
            //        position: latLng,
            //        map: map,
            //        title: "Location Marker"
            //    });
            // }

            // document.addEventListener('DOMContentLoaded', function() {
            //     var body = document.querySelector('body');
            //     body.classList.add('track_company');
            // });
       
            var ShipemtnMapBoundation = "{{Config('Shipment.country_code_shipment_allowed')}}";
            let ShipmentBound         = ShipemtnMapBoundation.split(",").map(code =>   code.trim());  
          
            function initMap() {
                var ac = new google.maps.places.Autocomplete(document.getElementById('drop_city'), {
                    // types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
                    types: ['geocode'],
                    componentRestrictions: { country: ShipmentBound }
                });
                ac.addListener('place_changed', () => {
                    var place = ac.getPlace();
                    // Extract the address components
                    var addressComponents = place.address_components;
                    var city, state, zipCode;

                    // Loop through the address components and find the city, state, and zip code
                    for (var i = 0; i < addressComponents.length; i++) {
                        var component = addressComponents[i];
                        var componentTypes = component.types;

                        // Check if the component is a city
                        if (componentTypes.includes('locality')) {
                            city = component.long_name;
                        }

                        // Check if the component is a state
                        if (componentTypes.includes('administrative_area_level_1')) {
                            state = component.long_name;
                        }

                        // Check if the component is a zip code
                        if (componentTypes.includes('postal_code')) {
                            zipCode = component.long_name;
                        }
                    }

                    var lat = place.geometry.location.lat();
                    var lng = place.geometry.location.lng();

                    // $("#drop_city").val(city);
                    $("#drop_zip_code").val(zipCode);
                    $("#drop_latitude").val(lat);
                    $("#drop_longitude").val(lng);


                });

                var ac2 = new google.maps.places.Autocomplete(document.getElementById('company_city'), {
                    // types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
                    types: ['geocode'],
                    componentRestrictions: { country: ShipmentBound }
                });

                ac2.addListener('place_changed', () => {
                    var place = ac2.getPlace();

                    // Extract the address components
                    var addressComponents = place.address_components;
                    var city2, state2, zipCode2;

                    // Loop through the address components and find the city, state, and zip code
                    for (var i = 0; i < addressComponents.length; i++) {
                        var component = addressComponents[i];
                        var componentTypes = component.types;

                        // Check if the component is a city
                        if (componentTypes.includes('locality')) {
                            city2 = component.long_name;
                        }

                        // Check if the component is a state
                        if (componentTypes.includes('administrative_area_level_1')) {
                            state2 = component.long_name;
                        }

                        // Check if the component is a zip code
                        if (componentTypes.includes('postal_code')) {
                            zipCode2 = component.long_name;
                        }
                    }

                    var lat2 = place.geometry.location.lat();
                    var lng2 = place.geometry.location.lng();

                    // $("#company_city").val(city2);
                    $("#company_zip_code").val(zipCode2);
                    $("#company_latitude").val(lat2);
                    $("#company_longitude").val(lng2);


                });
            }
            // const inputFile = document.querySelector("#picture__input");
            // const pictureImage = document.querySelector(".picture__image");
            // const pictureImageTxt = "<svg width='28' height='34' viewBox='0 0 28 34' fill='currentcolor' xmlns='http://www.w3.org/2000/svg'><path d='M8.333 25.5839H20.1114V13.8411H27.962L14.2222 0.142822L0.482422 13.8411H8.333V25.5839ZM0.482422 29.4973H27.9646V33.4107H0.482422V29.4973Z'/></svg>";
            // pictureImage.innerHTML = pictureImageTxt;

            // inputFile.addEventListener("change", function (e) {
            // const inputTarget = e.target;
            // const file = inputTarget.files[0];

            //     if (file) {
            //         const reader = new FileReader();

            //         reader.addEventListener("load", function (e) {
            //         const readerTarget = e.target;

            //         const img = document.createElement("img");
            //         img.src = readerTarget.result;
            //         img.classList.add("picture__img");

            //         pictureImage.innerHTML = "";
            //         pictureImage.appendChild(img);
            //         });

            //         reader.readAsDataURL(file);
            //     } else {
            //         pictureImage.innerHTML = pictureImageTxt;
            //     }
            // });

            // const inputFile1 = document.querySelector("#picture__input1");
            // const pictureImage1 = document.querySelector(".picture__image1");
            // const pictureImageTxt1 = "<svg width='28' height='34' viewBox='0 0 28 34' fill='currentcolor' xmlns='http://www.w3.org/2000/svg'><path d='M8.333 25.5839H20.1114V13.8411H27.962L14.2222 0.142822L0.482422 13.8411H8.333V25.5839ZM0.482422 29.4973H27.9646V33.4107H0.482422V29.4973Z'/></svg>";
            // pictureImage1.innerHTML = pictureImageTxt1;

            // inputFile1.addEventListener("change", function (e) {
            // const inputTarget1 = e.target;
            // const file1 = inputTarget1.files[0];

            // if (file1) {
            //     const reader1 = new FileReader();

            //     reader1.addEventListener("load", function (e) {
            //     const readerTarget1 = e.target;

            //     const img1 = document.createElement("img");
            //     img1.src = readerTarget1.result;
            //     img1.classList.add("picture__img");

            //     pictureImage1.innerHTML = "";
            //     pictureImage1.appendChild(img1);
            //     });

            //     reader1.readAsDataURL(file1);
            // } else {
            //     pictureImage1.innerHTML = pictureImageTxt1;
            // }
            // });

            // (function () {

            //     'use strict';

            //     $('.input-file').each(function () {
            //         var $input = $(this),
            //             $label = $input.next('.js-labelFile'),
            //             labelVal = $label.html();

            //         $input.on('change', function (element) {
            //             var fileName = '';
            //             if (element.target.value) fileName = element.target.value.split('\\').pop();
            //             fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
            //         });
            //     });

            // })();



            // Custom Dropdown
            function showMe(evt) {
            }

            function makeDd() {
                'use strict';
                let json = new Function(`return ${document.getElementById('json_data').innerHTML}`)();
                /*  new MsDropdown("#json_dropdown", {
                      byJson: {
                          data: json, selectedIndex:1
                      }
                  })*/
                MsDropdown.make("#json_dropdown", {
                    byJson: {
                        data: json,
                        selectedIndex: 0
                    }
                });
            }
            var buttonPlus = $(".qty-btn-plus");
            var buttonMinus = $(".qty-btn-minus");

            var incrementPlus = buttonPlus.click(function() {
                var $n = $(this)
                    .parent(".qty-container")
                    .find(".input-qty");
                $n.val(Number($n.val()) + 1);
            });

            var incrementMinus = buttonMinus.click(function() {
                var $n = $(this)
                    .parent(".qty-container")
                    .find(".input-qty");
                var amount = Number($n.val());
                if (amount > 0) {
                    $n.val(amount - 1);
                }
            });
            $(document).ready(function() {
                $("body").on("change", "select[name=shipment_servise]", function() {
                    // if($(this).val() == 17){
                    if ($(this).val() == 2) {
                        $("input[name=how_many_rooms]").attr('data-is-required', 1).parent().show();
                        $("input[name=elevators]").attr('data-is-required', 1).parent().parent().parent()
                            .show();
                        // $("input[name=pickup_floor]").attr('data-is-required',1).parent().parent().parent().show();
                        // if($("input[name=pickup_floor]:checked").val() == 'yes'){
                        $("input[name=how_many_floor]").attr('data-is-required', 1).parent().show();
                        // }else{
                        //     $("input[name=how_many_floor]").attr('data-is-required',0).parent().hide();
                        // }
                        $("input[name=drop_how_many_rooms]").attr('data-is-required', 1).parent().show();
                        $("input[name=drop_elevator]").attr('data-is-required', 1).parent().parent().parent()
                            .show();
                        // $("input[name=drop_floor]").attr('data-is-required',1).parent().parent().parent().show();
                        // if($("input[name=drop_floor]:checked").val() == 'yes'){
                        $("input[name=drop_how_many_floor]").attr('data-is-required', 1).parent().show();
                        // }else{
                        //     $("input[name=drop_how_many_floor]").attr('data-is-required',0).parent().hide();
                        // }

                        $("input[name=number_of_items]").attr('data-is-required', 0).parent().hide();
                        $(".description-number-of-items").hide().find("textarea").attr('data-is-required', 0);
                        // }else if($(this).val() == 18){
                    } else if ($(this).val() == 3) {
                        $("input[name=how_many_rooms]").attr('data-is-required', 1).parent().show();
                        $("input[name=elevators]").attr('data-is-required', 1).parent().parent().parent()
                            .show();
                        // $("input[name=pickup_floor]").attr('data-is-required',1).parent().parent().parent().show();
                        // if($("input[name=pickup_floor]:checked").val() == 'yes'){
                        $("input[name=how_many_floor]").attr('data-is-required', 1).parent().show();
                        // }else{
                        //     $("input[name=how_many_floor]").attr('data-is-required',0).parent().hide();
                        // }
                        $("input[name=drop_how_many_rooms]").attr('data-is-required', 1).parent().show();
                        $("input[name=drop_elevator]").attr('data-is-required', 1).parent().parent().parent()
                            .show();
                        // $("input[name=drop_floor]").attr('data-is-required',1).parent().parent().parent().show();
                        // if($("input[name=drop_floor]:checked").val() == 'yes'){
                        $("input[name=drop_how_many_floor]").attr('data-is-required', 1).parent().show();
                        // }else{
                        //     $("input[name=drop_how_many_floor]").attr('data-is-required',0).parent().hide();
                        // }
                        $("input[name=number_of_items]").attr('data-is-required', 0).parent().hide();
                        $(".description-number-of-items").hide().find("textarea").attr('data-is-required', 0);
                        // }else if($(this).val() == 19){
                    } else if ($(this).val() == 1) {
                        $("input[name=number_of_items]").attr('data-is-required', 1).parent().show();
                        $(".description-number-of-items").show().find("textarea").attr('data-is-required', 1);
                        $("input[name=how_many_rooms]").attr('data-is-required', 0).parent().hide();
                        // $("input[name=pickup_floor]").attr('data-is-required',0).parent().parent().parent().hide();
                        $("input[name=how_many_floor]").attr('data-is-required', 0).parent().hide();
                        $("input[name=elevators]").attr('data-is-required', 0).parent().parent().parent()
                            .hide();
                        $("input[name=drop_how_many_rooms]").attr('data-is-required', 0).parent().hide();
                        // $("input[name=drop_floor]").attr('data-is-required',0).parent().parent().parent().hide();
                        $("input[name=drop_how_many_floor]").attr('data-is-required', 0).parent().hide();
                        $("input[name=drop_elevator]").attr('data-is-required', 0).parent().parent().parent()
                            .hide();

                    }
                });

                // $("body").on("click", "input[name=pickup_floor]", function() {
                //     if($(this).val() == 'yes'){
                //         $("input[name=how_many_floor]").attr('data-is-required',1).parent().show();
                //     }else{
                //         $("input[name=how_many_floor]").attr('data-is-required',0).parent().hide();
                //     }
                // });

                $("body").on("click", ".submit-private-shipment-request", function() {
                    $(".text-danger-text").html("");
                    $(".is-invalid").removeClass("is-invalid");
                    var flag = $.fn.checkValidation(1);
                    if (flag) {
                        $(".is-invalid:first").focus();
                        return false;
                    } else {
                        @if (Auth::user())
                            $("#private-shipment-request-form").submit();
                        @else
                            $("#verificationModal").modal("show");
                        @endif
                    }
                });

                $("body").on("click", ".private-shipment-request-confirm-button", function() {
                    $("#private-shipment-request-form").submit();
                });

                $.fn.checkValidation = function(nextStepNumber) {
                    // return false;
                    var flag = false;
                    $("." + nextStepNumber + "-form-fields").each(function() {
                        if ($(this).data("type") == "same") {
                            if ($(this).val() != $("input[name='" + $(this).data("same-with") + "']")
                                .val()) {
                                flag = true;
                                $(".error-" + $(this).attr("name")).html(
                                    "Sa valeur doit être la même que la valeur de " + ($(
                                        "input[name='" + $(this).data("same-with") + "']").data(
                                        "name")));
                                $(this).addClass("is-invalid");

                            }
                        }
                        if ($(this).data("type") == "gLocation" && $(this).val() == "" && $("input[name="+$(this).data("target-name")+"]").val() != "") {
                            flag = true;
                            $(".error-" + $(this).data("target-name")).html("{{ trans("messages.please_select_a_location_from_the_list_given") }}");
                            $("input[name="+$(this).data("target-name")+"]").addClass("is-invalid");
                        }
                        if (($(this).val() == "" && $(this).attr("data-is-required") == "1") || ($(this)
                                .attr('type') == "checkbox" && !$(this).is(':checked')) || ($(this).attr(
                                    'type') == "radio" && !$("input[name='" + $(this).attr('name') + "']")
                                .is(':checked'))) {
                            flag = true;
                            var str_name = $(this).attr("name");
                            str_name = str_name.replace('[', '').replace(']', '');
                            $(".error-" + str_name).html("{{ trans('messages.This field is required') }}");
                            $(this).addClass("is-invalid");
                        }
                    });
                    return flag;
                }

                // $("body").on("click", "input[name=drop_floor]", function() {
                //     if($(this).val() == 'yes'){
                //         $("input[name=drop_how_many_floor]").attr('data-is-required',1).parent().show();
                //     }else{
                //         $("input[name=drop_how_many_floor]").attr('data-is-required',0).parent().hide();
                //     }
                // });
                // $("body").on("click", "input[name=roomRadio]", function() {
                //     if($(this).val() == 'yes'){
                //         $("input[name=how_many_rooms]").parent().show();
                //     }else{
                //         $("input[name=how_many_rooms]").parent().hide();
                //     }f
                // });
                $("body").on("input", "input[name=number_of_items]", function() {
                    var number_of_items = $(this).val();
                    var htmlStr = ``;
                    for (var i = 0; i < number_of_items; i++) {
                        // alert();
                        htmlStr += `<div class="col-md-12 ">
                            <div class="form-group">
                                <label for="number_of_item_description_${i}" class="form-label">{{ trans('messages.specify_the_item') }} ${i + 1 }</label>
                                <textarea class="form-control h72 1-form-fields" data-is-required="1" id="number_of_item_description_${i}" rows="2" placeholder="{{ trans('messages.specify_here') }}" name="number_of_item_description[${i}]"></textarea>
                                <small class="text-danger text-danger-text error-number_of_item_description${i}"></small>
                            </div>
                        </div>`;
                    }
                    $(".description-number-of-items").html(htmlStr);
                });
                minDate = new Date();
                minDate.setHours(minDate.getHours() + 72)
                $("input[name=request_date]").datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    numberOfMonths: 1,
                    buttonImage: 'contact/calendar/calendar.gif',
                    buttonImageOnly: true,
                    minDate: minDate,
                    onSelect: function(selectedDate) {
                        // we can write code here 
                    }
                });
            });
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_KEY') }}&callback=initMap&libraries=places"></script>

        <!--  verification  Popup-->
        <div class="modal fade themeModal" id="verificationModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-340">
                <div class="modal-content">

                    <span class="modalCenterHeadIcon">
                        <svg width="17" height="18" viewBox="0 0 17 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.5 1.48534C11.2293 1.48534 13.4419 3.79712 13.4419 6.64885V14.2909H3.55814V6.64885C3.55814 3.79712 5.77068 1.48534 8.5 1.48534ZM14.6279 14.2909V6.64885C14.6279 3.1127 11.8844 0.246094 8.5 0.246094C5.11565 0.246094 2.37209 3.1127 2.37209 6.64885V14.2909H0.593023C0.265506 14.2909 0 14.5683 0 14.9105C0 15.2527 0.265506 15.5301 0.593023 15.5301H16.407C16.7345 15.5301 17 15.2527 17 14.9105C17 14.5683 16.7345 14.2909 16.407 14.2909H14.6279Z"
                                fill="#FF7C03" />
                            <path
                                d="M6.18164 15.262C6.18164 14.9658 6.42115 14.7257 6.71661 14.7257H10.283C10.5785 14.7257 10.818 14.9658 10.818 15.262V15.6195C10.818 16.9029 9.78011 17.9434 8.49982 17.9434C7.21953 17.9434 6.18164 16.9029 6.18164 15.6195V15.262ZM7.26421 15.7983C7.35074 16.4047 7.87098 16.8708 8.49982 16.8708C9.12866 16.8708 9.6489 16.4047 9.73543 15.7983H7.26421Z"
                                fill="#FF7C03" />
                        </svg>
                    </span>
                    <div class="modal-header">
                        <button type="button" class="themeBtn-close" data-bs-dismiss="modal" aria-label="Close"><svg
                                width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg></button>
                    </div>
                    <div class="modal-body">
                        <p class="themePopupText">
                            {{ trans('messages.in_these_moments_a_verification_code_will_be_sent_to_the_phone_number_you_entered') }}
                        </p>
                        <div class="text-center d-block">
                            <button type="button"
                                class="themeModalBtn private-shipment-request-confirm-button">{{ trans('messages.confirm') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  verification  Popup-->
    @stop
