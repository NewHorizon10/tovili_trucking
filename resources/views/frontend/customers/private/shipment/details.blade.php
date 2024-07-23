@extends('frontend.layouts.default')
@section('extraCssLinks')
<!-- Custom Responsive CSS -->
<link rel="stylesheet" href="{{asset('public/frontend/css/responsive.css')}}">
<!-- Dashboard CSS-->
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard.css')}}">
<!-- Dashboard Responsive CSS-->
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard-responsive.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dropzone.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dashboard-dropzone.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/drop-down.css')}}">
@stop
@section('backgroundImage')
<body class="dashbord_page @if($user->customer_type == 'private') privateCustomer_page @endif">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="img/logo.png" alt="">
        </div>
    </div>
    @stop

    @section('content')
    <section class="form_section delivery_details_max company_max ">
        <div class="container">
        <div class="company_details_block">
                <div class="transpor_request_box mb-lg-3">
                    <h3 class="transpor_request_box_title delivery_color_theam mb-3 ">
                        {{trans("messages.shipment_details")}}</h3>
                    {{-- <a href="#!" class="transpor_request_message">
                        <svg width="19" height="20" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M19.25 11.0002C19.25 16.0628 15.3325 20.1668 10.5 20.1668C7.88659 20.1668 1.75 20.1668 1.75 20.1668C1.75 20.1668 1.75 13.3249 1.75 11.0002C1.75 5.93755 5.66751 1.8335 10.5 1.8335C15.3325 1.8335 19.25 5.93755 19.25 11.0002Z"
                                fill="white"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M10.5 2.521C6.02994 2.521 2.40625 6.31725 2.40625 11.0002V19.4793H10.5C14.9701 19.4793 18.5938 15.6831 18.5938 11.0002C18.5938 6.31725 14.9701 2.521 10.5 2.521ZM1.75 20.1668L1.09375 20.1667V11.0002C1.09375 5.55785 5.30507 1.146 10.5 1.146C15.6949 1.146 19.9062 5.55785 19.9062 11.0002C19.9062 16.4425 15.6949 20.8543 10.5 20.8543H1.75007L1.75 20.1668ZM1.75 20.1668L1.75007 20.8543C1.38763 20.8543 1.09375 20.5464 1.09375 20.1667L1.75 20.1668Z"
                                fill="white"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.46875 8.25C5.46875 7.8703 5.76256 7.5625 6.125 7.5625H14C14.3624 7.5625 14.6562 7.8703 14.6562 8.25C14.6562 8.6297 14.3624 8.9375 14 8.9375H6.125C5.76256 8.9375 5.46875 8.6297 5.46875 8.25Z"
                                fill="currentcolor"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.46875 11.9165C5.46875 11.5368 5.76256 11.229 6.125 11.229H14C14.3624 11.229 14.6562 11.5368 14.6562 11.9165C14.6562 12.2962 14.3624 12.604 14 12.604H6.125C5.76256 12.604 5.46875 12.2962 5.46875 11.9165Z"
                                fill="currentcolor"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.46875 15.5835C5.46875 15.2038 5.76256 14.896 6.125 14.896H10.5C10.8624 14.896 11.1562 15.2038 11.1562 15.5835C11.1562 15.9632 10.8624 16.271 10.5 16.271H6.125C5.76256 16.271 5.46875 15.9632 5.46875 15.5835Z"
                                fill="currentcolor"></path>
                        </svg>
                    </a> --}}
                </div>
                <div class="dashboardRight_block_wrapper dashborard_box_shodow ">
                    <h2 class="RightBlockTitle">{{trans("messages.transport_request_number")}}
                        {{$ShipmentDetails->request_number}}
                    </h2>
                    <div class="request_editeDelite">
                        {{-- <a href="#!" class="request_edite_btn">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0 13.4592V17.001H3.54184L13.9877 6.55519L10.4458 3.01335L0 13.4592ZM16.7264 3.81646C17.0926 3.44757 17.0926 2.85273 16.7264 2.48521L14.5158 0.274622C14.1469 -0.0915406 13.5521 -0.0915406 13.1846 0.274622L11.4558 2.00334L14.9977 5.54518L16.7264 3.81646Z"
                                    fill="currentcolor" />
                            </svg>
                        </a>
                        <a href="{{route('private-shipment-request-details-delete',base64_encode($user->id))}}"
                            class="request_delite_btn confirmDelete">
                            <svg width="12" height="16" viewBox="0 0 12 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1062_10614)">
                                    <path
                                        d="M0.857422 14.2222C0.860309 15.2028 1.62614 15.997 2.57171 16H9.42885C10.3744 15.997 11.1402 15.2028 11.1431 14.2222V3.55554H0.857422V14.2222Z"
                                        fill="currentcolor" />
                                    <path
                                        d="M9.00018 0.889263L8.14268 0H3.85732L2.99982 0.889263H0V2.66629H12V0.889263H9.00018Z"
                                        fill="currentcolor" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_1062_10614">
                                        <rect width="12" height="16" fill="currentcolor" />
                                    </clipPath>
                                </defs>
                            </svg>

                        </a> --}}
                    </div>
                    <div class="offer_details_content requestCom_deta mt-4 mb-4">
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans("messages.admin_common_Status")}}</span>
                            @php
                                             $className = '';
                                            $shipmentStatus = '';
                                            if($ShipmentDetails->status == 'shipment' && $ShipmentDetails->shipmentDriverScheduleDetails && $ShipmentDetails->shipmentDriverScheduleDetails->shipment_status == 'start'){
                                                $className = 'lightgray_btn';
                                                $shipmentStatus = 'active';
                                            }elseif ($ShipmentDetails->status == 'shipment') {
                                                $className = 'orange_btn';
                                                $shipmentStatus = 'shipment';
                                             } elseif ($ShipmentDetails->status == 'end') {
                                                $className = 'blue_btn';
                                                $shipmentStatus = 'end';
                                            }
                            @endphp
                            <h3 class="offreDetails_cont">
                                <a href="#!" class="{{$className}} dashboard_tableBtn"><span>{{trans("messages.".$shipmentStatus)}}</span></a>
                                @if($ShipmentDetails->status == 'end')
                                    @if($ShipmentDetails->shipmentRatingReviews)
                                        <a href="javascript:void(0)" class="transportRequestBtn give-rating-button" onclick="$.fn.view_review_modal({{ $ShipmentDetails->id }})">{{ trans('messages.view_rating') }} </a>
                                    @else
                                        <a href="javascript:void(0)" class="transportRequestBtn give-rating-button" onclick="$.fn.review_modal({{ $ShipmentDetails->id }})">{{ trans('messages.give_rating') }} </a>
                                    @endif
                                @endif
                            </h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.request_type')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->TruckTypeDescriptionsPrivate->name ?? ""}}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.request_date')}}</span>
                            <span class="date_label">
                                <h3 class=" offreDetails_cont">{{ \Carbon\Carbon::createFromFormat('Y-m-d',($ShipmentDetails->request_date))->format(config("Reading.date_format")) }} :<br/>
                                    {{$ShipmentDetails->RequestTimeDescription->code ?? "---"}}</h3>
                            </span>
                        </div>
                        
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans("messages.flexibility_days")}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->request_date_flexibility }} {{ trans('messages.'.($ShipmentDetails->request_date_flexibility > 1 ? 'days' : 'day'))}}</h3>
                        </div>
                        
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans("messages.admin_shipment_request_packaging")}}</span>
                            <h3 class="offreDetails_cont">: {{ trans("messages.".$ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->need_packaging) }}</h3>
                        </div>
                        <div class="seprator"></div>
                        <h3 class="profile-title">{{trans('messages.admin_shipment_request_pickup_location_details')}}</h3>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.admin_origin_city')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->pickup_city }}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans("messages.origin_address")}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation
                                ? $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->location : "" }}</h3>
                        </div>
                        
                        @if($ShipmentDetails->request_type != 19)
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.floor')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->how_many_floor }}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.admin_shipment_request_elevators')}}</span>
                            <h3 class="offreDetails_cont">: {{ trans("messages.".$ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->elevators) }}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.admin_shipment_request_rooms')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->how_many_rooms }}</h3>
                        </div>
                        @endif
                        <div class="seprator"></div>
                        <h3 class="profile-title">{{trans('messages.admin_shipment_request_drop_location_details')}}</h3>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.admin_destination_city')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->drop_city }}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans("messages.destination_address")}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation
                                ? $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->drop_location : "" }}</h3>
                        </div>
                        
                        @if($ShipmentDetails->request_type != 19)
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.floor')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->drop_how_many_floor }}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.admin_shipment_request_elevators')}}</span>
                            <h3 class="offreDetails_cont">: {{ trans("messages.".$ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->drop_elevators) }}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.admin_shipment_request_rooms')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->ShipmentPrivateCustomer_ExtraInformation->drop_how_many_rooms }}</h3>
                        </div>
                        @endif
                        @if($ShipmentDetails->request_type == 19)
                        <div class="seprator"></div>
                        <h3 class="profile-title">{{trans('messages.item_details')}}</h3>
                        @php
                            $ShipmentDetails->request_pickup_details = json_decode($ShipmentDetails->request_pickup_details)
                        @endphp
                        @foreach($ShipmentDetails->request_pickup_details as $key => $value)

                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.item')}} {{$key+1}}</span>
                                <h3 class="offreDetails_cont">: {{$value}}</h3>
                            </div>
                        @endforeach
                        @endif
                        @if($ShipmentDetails->shipment_attchement->count())
                            <div class="seprator"></div>
                            <h3 class="profile-title">{{trans('messages.attachments')}}</h3>
                            <div class="attachment-elements">
                                @foreach($ShipmentDetails->shipment_attchement as $attchement)
                                    @php
                                    $filename = $attchement->attachment;
                                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                    if($extension == "mp4" || $extension == "webm" || $extension == "avi" || $extension == "mov"){
                                        @endphp
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-src="{{$attchement->attachment}}" class="video-btn play_icon">
                                            <div class="upload_img_item tabel_img">
                                                <video src="{{$attchement->attachment}}" style="">
                                                    <source src="" type="video/mp4">
                                                </video>
                                                <span class="zoom_icon">
                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_884_49325)">
                                                            <path
                                                                d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                                fill="#1535B9" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_884_49325">
                                                                <rect width="21.0318" height="21.0318" fill="white"
                                                                    transform="translate(0.960938 0.341797)" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </span>
                                            </div>
                                        </a>
                                        @php
                                    }else if($extension == "png" || $extension == "jpg" || $extension == "jpeg"|| $extension == "svg"){
                                        @endphp 
                                            <a class="fancybox-buttons" data-fancybox-group="button" href="{{$attchement->attachment}}" >
                                                <div class="upload_img_item tabel_img">
                                                    <img  src="{{$attchement->attachment}}" alt="">
                                                    <span class="zoom_icon">
                                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_884_49325)">
                                                                <path
                                                                    d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                                    fill="#1535B9" />
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_884_49325">
                                                                    <rect width="21.0318" height="21.0318" fill="white"
                                                                        transform="translate(0.960938 0.341797)" />
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </a>
                                        @php
                                    }else if($extension == "docx" || $extension == "doc"){
                                        @endphp
                                        <a href="{{$attchement->attachment}}" target="_blank">
                                            <div class="upload_img_item tabel_img">
                                                <img src="{{url('/public/frontend/img/docx-icon.png')}}" alt="">
                                                <span class="zoom_icon">
                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_884_49325)">
                                                            <path
                                                                d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                                fill="#1535B9" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_884_49325">
                                                                <rect width="21.0318" height="21.0318" fill="white"
                                                                    transform="translate(0.960938 0.341797)" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </span>
                                            </div>
                                        </a>
                                        @php
                                    }else if($extension == "pdf"){
                                        @endphp
                                        <a href="{{$attchement->attachment}}" target="_blank">
                                            <div class="upload_img_item tabel_img">
                                                <img src="{{url('/public/frontend/img/pdf-icon.png')}}" alt="">
                                                <span class="zoom_icon">
                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_884_49325)">
                                                            <path
                                                                d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                                fill="#1535B9" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_884_49325">
                                                                <rect width="21.0318" height="21.0318" fill="white"
                                                                    transform="translate(0.960938 0.341797)" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </span>
                                            </div>
                                        </a>
                                        @php
                                    }
                                    @endphp
                                @endforeach
                            </div>
                        @endif
                        
                        @if($ShipmentDetails->status == 'end' && $ShipmentDetails->status && $ShipmentDetails->invoice_file != '')
                        <div class="seprator"></div>
                        <h3 class="profile-title">{{trans('messages.invoice_file')}}</h3>
                        <div class="attachment-elements">
                                @php
                                $filename = $ShipmentDetails->invoice_file;
                                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                if($extension == "mp4" || $extension == "webm" || $extension == "avi" || $extension == "mov"){
                                    @endphp
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-src="{{$ShipmentDetails->invoice_file}}" class="video-btn play_icon">
                                        <div class="upload_img_item tabel_img">
                                            <video src="{{$ShipmentDetails->invoice_file}}" style="">
                                                <source src="" type="video/mp4">
                                            </video>
                                            <span class="zoom_icon">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_884_49325)">
                                                        <path
                                                            d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                            fill="#1535B9" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_884_49325">
                                                            <rect width="21.0318" height="21.0318" fill="white"
                                                                transform="translate(0.960938 0.341797)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </span>
                                        </div>
                                    </a>
                                    @php
                                }else if($extension == "png" || $extension == "jpg" || $extension == "jpeg"|| $extension == "svg"){
                                    @endphp 
                                        <a class="fancybox-buttons" data-fancybox-group="button" href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$ShipmentDetails->invoice_file) }}" >
                                            <div class="upload_img_item tabel_img">
                                                <img  src="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$ShipmentDetails->invoice_file) }}" alt="">
                                                <span class="zoom_icon">
                                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_884_49325)">
                                                            <path
                                                                d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                                fill="#1535B9" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_884_49325">
                                                                <rect width="21.0318" height="21.0318" fill="white"
                                                                    transform="translate(0.960938 0.341797)" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </span>
                                            </div>
                                        </a>
                                    @php
                                }else if($extension == "docx" || $extension == "doc"){
                                    @endphp
                                    <a  href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$ShipmentDetails->invoice_file) }}" target="_blank">

                                        <div class="upload_img_item tabel_img">
                                            <img src="{{url('/public/frontend/img/docx-icon.png')}}" alt="">
                                            <span class="zoom_icon">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_884_49325)">
                                                        <path
                                                            d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                            fill="#1535B9" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_884_49325">
                                                            <rect width="21.0318" height="21.0318" fill="white"
                                                                transform="translate(0.960938 0.341797)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </span>
                                        </div>
                                    </a>
                                    @php
                                }else if($extension == "pdf"){
                                    @endphp
                                    <a  href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$ShipmentDetails->invoice_file) }}" target="_blank">

                                        <div class="upload_img_item tabel_img">
                                            <img src="{{url('/public/frontend/img/pdf-icon.png')}}" alt="">
                                            <span class="zoom_icon">
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_884_49325)">
                                                        <path
                                                            d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z"
                                                            fill="#1535B9" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_884_49325">
                                                            <rect width="21.0318" height="21.0318" fill="white"
                                                                transform="translate(0.960938 0.341797)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </span>
                                        </div>
                                    </a>
                                    @php
                                }
                                @endphp
                        </div>
                        @endif
                        

                    </div>
                </div>
            </div>
            <div class="company_details_block">
                <div class="dashboardRight_block_wrapper company_details_block">
                    <div>
                        <h3 class="transpor_request_box_title" style="color: #1535B9;">{{trans('messages.selected_offer_details')}}</h3>
                    </div>
                    <div class="dashboard_notofication_main List_objectives_table">
                        <div class="transportation_request_block offer_details_content">
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.the_date_of_transport')}}</span>
                                <span>
                                    <h3 class="date_label offreDetails_cont"> {{ \Carbon\Carbon::createFromFormat( 'Y-m-d', ( $ShipmentDetails->request_date))->format(config("Reading.date_format"))  }} :<br/>
                                            {{$ShipmentDetails->RequestTimeDescription->code ?? "" }}</h3>
                                </span>
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.pickup_city')}}</span>
                                <h3 class="offreDetails_cont">: {{$ShipmentDetails->pickup_city}}</h3>
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.pickup_address')}}</span>
                                <h3 class="offreDetails_cont">: {{$ShipmentDetails->pickup_address}}</h3>
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.admin_destination_city')}}</span>
                                <h3 class="offreDetails_cont">: @foreach($ShipmentDetails->ShipmentStop as $ShipmentStop )
                                    {{$ShipmentStop->dropoff_city}}
                                    @break
                                @endforeach</h3>
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.destination_address')}}</span>
                                <h3 class="offreDetails_cont">: @foreach($ShipmentDetails->ShipmentStop as $ShipmentStop )
                                    {{$ShipmentStop->dropoff_address}}
                                    @break
                                @endforeach</h3>
                            </div>
                            {{-- <div class="company_offreDetails">
                                
                                <span class="company_offreDetails_label">{{trans('messages.request_type')}}</span>
                                <h3 class="offreDetails_cont">: {{$ShipmentDetails->TruckTypeDescriptionsPrivate->name ?? ""}}</h3>
                            </div> --}}
                            
                            <!-- <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.description')}}</span>
                                <h3 class="offreDetails_cont">: {{$ShipmentDetails->description}}
                            </div> -->
                            <!--  --------------------------------------------  -->
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.admin_common_Price')}}</span>
                                <h3 class="offreDetails_cont">: {{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->price}}
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.addtional_hours_cost')}}</span>
                                <h3 class="offreDetails_cont">: {{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->extra_time_price}}
                            </div>
                            {{--
                                <div class="company_offreDetails">
                                    <span class="company_offreDetails_label">{{trans('messages.duration_in_hours')}}</span>
                                    <h3 class="offreDetails_cont">: {{$shipmentOffer->duration_in_hours}}
                                </div>
                            --}}
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.duration_in_hours')}}</span>
                                <h3 class="offreDetails_cont">: {{$shipmentOffer->duration}}</h3>
                            </div>
                            @if($ShipmentDetails->shipmentDriverScheduleDetails && $ShipmentDetails->shipmentDriverScheduleDetails->shipment_status == "end")
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.estimated_hours')}}</span>
                                <h3 class="offreDetails_cont">: {{$ShipmentDetails->shipmentDriverScheduleDetails->time_taken_to_complete_shipment}}</h3>
                            </div>
                            {{-- <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.start_time')}}</span>
                                <h3 class="offreDetails_cont">: {{date(Config('Reading.date_time_format'),strtotime($ShipmentDetails->shipmentDriverScheduleDetails->shipment_actual_start_time))}}</h3>
                            </div> --}}
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.end_time')}}</span>
                                <h3 class="offreDetails_cont">: {{date(Config('Reading.date_time_format'),strtotime($ShipmentDetails->shipmentDriverScheduleDetails->shipment_actual_end_time))}}</h3>
                            </div>
                            @endif
                            {{-- @php
                                if($ShipmentDetails->shipmentDriverScheduleDetails && $ShipmentDetails->shipmentDriverScheduleDetails->shipment_status == "end"){
                            
                                    $oneHour = $shipmentOffer->duration;
                                    $twoHour = $ShipmentDetails->shipmentDriverScheduleDetails->time_taken_to_complete_shipment;
                                    list($hours, $minutes) = explode(':', $twoHour);
                                    $twoHourInMinutes = $hours * 60 + $minutes;
                                    if ($oneHour * 60 < $twoHourInMinutes) {
                                        @endphp
                                            <div class="company_offreDetails">
                                                <span class="company_offreDetails_label">{{trans('messages.estimated_hours')}}</span>
                                                <h3 class="offreDetails_cont">: {{$ShipmentDetails->shipmentDriverScheduleDetails->time_taken_to_complete_shipment}}</h3>
                                            </div>
                                        @php
                                    }
                                }
                            @endphp --}}
                            
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.shipment_note')}}</span>
                                <h3 class="offreDetails_cont">: {{$shipmentOffer->description}}
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.payment_condition')}}</span>
                                <h3 class="offreDetails_cont">: {{$shipmentOffer->payment_condition}}
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.Type_of_truck')}}</span>
                                <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->type_of_truck?? ""}}</h3>
                            </div>
                            @if($shipmentOffer && $shipmentOffer->request_offer_date)
                                <div class="company_offreDetails ">
                                    <span class="company_offreDetails_label">{{trans('messages.request_offer_date')}}</span>
                                    <span>
                                        <h3 class=" date_label offreDetails_cont" > {{ (\Carbon\Carbon::createFromFormat('Y-m-d', ($shipmentOffer->request_offer_date))->format(config("Reading.date_format"))) ?? ""  }} :
                                    </h3>
                                    </span>
                                </div>
                            @else
                                <div class="company_offreDetails">
                                    <span class="company_offreDetails_label">{{trans('messages.request_offer_date')}}</span>
                                    <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->request_offer_date ?? ""}}</h3>
                                </div>
                            @endif
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.Truck Number')}}</span>
                                <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->truck_system_number  ?? ""}}
                            </div>
                            <div class="company_offreDetails">
                                <span class="company_offreDetails_label">{{trans('messages.admin_common_Status')}}</span>
                                <h3 class="offreDetails_cont">: {{ trans("messages.$shipmentOffer->status") }}
                            </div>
                            <!--  --------------------------------------------  -->
                        </div>
                    </div>
                </div>
                <div class="dashboardRight_block_wrapper admin_right_page company_details_block">
                    <h3 class="RightBlockTitle mb-4">{{trans("messages.selected_offer")}}</h3>
                    <div class="dashboard_notofication_main  List_objectives_table">
                        <div class="transportation_request_block">
                            <div class="transpor_request_box">
                                <h3 class="transpor_request_box_title" style="color: #1535b9;">{{trans("messages.Transportation")}}</h3>
                            </div>
                            <div class="coordinator_name_box" style="max-width: fit-content;">
                                <div class="company_name_logo_box">
                                    <img src="{{$shipmentOffer->companyUser->userCompanyInformation->company_logo}}" alt="">
                                </div>
                                <div class="coordinator_name_title">: {{$shipmentOffer->companyUser->userCompanyInformation->company_name}}</div>
                            </div>
                            <div class="transportation_request_block">
                                <div class="company_offreDetails">
                                    <h3 class="offreDetails_cont">{{trans('messages.admin_common_company_description')}}</h3>
                                    <span class="company_offreDetails_label">: {{$shipmentOffer->companyUser->userCompanyInformation->company_description}}</span>
                                </div>
                                {{--<div class="company_offreDetails">
                                    <h3 class="offreDetails_cont">{{trans('messages.refueling_method')}}</h3>
                                    <span class="company_offreDetails_label">: {{$shipmentOffer->companyUser->userCompanyInformation->getCompanyRefuelingDescription->code}}</span>
                                </div>
                                <div class="company_offreDetails">
                                    <h3 class="offreDetails_cont">{{trans('messages.admin_common_Company_Tidaluk')}}</h3>
                                    <span class="company_offreDetails_label">: {{$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk}}</span>
                                </div>
                                <div class="company_offreDetails">
                                    <h3 class="offreDetails_cont">{{trans('messages.admin_Company_Terms_&_Conditions')}}</h3>
                                    <span class="company_offreDetails_label">: {{$shipmentOffer->companyUser->userCompanyInformation->company_trms}}</span>
                                </div>--}}
                                <div class="company_offreDetails">
                                    <h3 class="offreDetails_cont">{{trans('messages.company_number')}} (H.P.)</h3>
                                    <span class="company_offreDetails_label">: {{$shipmentOffer->companyUser->userCompanyInformation->company_hp_number}}</span>
                                </div>
                                
                                <div class="company_offreDetails">
                                    <h3 class="offreDetails_cont">{{trans('messages.Company Location')}}</h3>
                                    <span class="company_offreDetails_label">: {{$shipmentOffer->companyUser->userCompanyInformation->company_location}}</span>
                                </div>
                                
            
                            </div>
                        </div>
                        <hr>
        
                        <div class="transportation_request_block">
                        <h3 class="transpor_request_box_title tranRequest_title">{{ trans('messages.contact_person_details') }}</h3>
                            <div class="transport_coordinator_block">
                                <div class="coordinator_name_box" style="max-width:fit-content;">
                                    <img src="{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_picture}}" alt="">
                                    <h3 class="coordinator_name_title"> {{$shipmentOffer->companyUser->userCompanyInformation->contact_person_name}}</h3>
                                </div>
                                <div class="coordinator_phoneMess_box">
                                    {{--@if($ShipmentDetails->SelectedShipmentOffers->show_chat_icon == 1)--}}
                                    @if($shipmentOffer && $shipmentOffer->TruckDetail && $shipmentOffer?->TruckDetail?->truckDriver && $shipmentOffer?->TruckDetail?->truckDriver?->userDriverDetail)
                                    <a href="#!" onclick="singelChatModal('{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_picture}}','{{$shipmentOffer?->companyUser->name}}','{{ $shipmentOffer?->TruckDetail?->truckDriver->id }}')" class="table_message_btn">
                                        <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M19.25 11.0002C19.25 16.0628 15.3325 20.1668 10.5 20.1668C7.88659 20.1668 1.75 20.1668 1.75 20.1668C1.75 20.1668 1.75 13.3249 1.75 11.0002C1.75 5.93755 5.66751 1.8335 10.5 1.8335C15.3325 1.8335 19.25 5.93755 19.25 11.0002Z"
                                                fill="white"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10.5 2.521C6.02994 2.521 2.40625 6.31725 2.40625 11.0002V19.4793H10.5C14.9701 19.4793 18.5938 15.6831 18.5938 11.0002C18.5938 6.31725 14.9701 2.521 10.5 2.521ZM1.75 20.1668L1.09375 20.1667V11.0002C1.09375 5.55785 5.30507 1.146 10.5 1.146C15.6949 1.146 19.9062 5.55785 19.9062 11.0002C19.9062 16.4425 15.6949 20.8543 10.5 20.8543H1.75007L1.75 20.1668ZM1.75 20.1668L1.75007 20.8543C1.38763 20.8543 1.09375 20.5464 1.09375 20.1667L1.75 20.1668Z"
                                                fill="white"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.46875 8.25C5.46875 7.8703 5.76256 7.5625 6.125 7.5625H14C14.3624 7.5625 14.6562 7.8703 14.6562 8.25C14.6562 8.6297 14.3624 8.9375 14 8.9375H6.125C5.76256 8.9375 5.46875 8.6297 5.46875 8.25Z"
                                                fill="currentcolor"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.46875 11.9165C5.46875 11.5368 5.76256 11.229 6.125 11.229H14C14.3624 11.229 14.6562 11.5368 14.6562 11.9165C14.6562 12.2962 14.3624 12.604 14 12.604H6.125C5.76256 12.604 5.46875 12.2962 5.46875 11.9165Z"
                                                fill="currentcolor"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.46875 15.5835C5.46875 15.2038 5.76256 14.896 6.125 14.896H10.5C10.8624 14.896 11.1562 15.2038 11.1562 15.5835C11.1562 15.9632 10.8624 16.271 10.5 16.271H6.125C5.76256 16.271 5.46875 15.9632 5.46875 15.5835Z"
                                                fill="currentcolor"></path>
                                        </svg>
                                    </a>
                                    {{--@endif
                                    <a href="tel:{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_phone_number}}" class="coordinator_phone_box">
                                        <svg width="26" height="28" viewBox="0 0 26 28" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M6.59186 5.68772C5.71851 5.68772 5.17373 6.49357 5.42322 7.25497C6.3178 9.98497 7.98487 14.0918 10.4709 16.6518C12.957 19.2118 16.9453 20.9283 19.5966 21.8495C20.3359 22.1064 21.1186 21.5455 21.1186 20.6461V17.8741C21.1186 17.7749 21.0664 17.6835 20.9822 17.6353L18.6372 16.2928C18.5607 16.249 18.468 16.2469 18.3897 16.2873L15.8889 17.5748C15.7326 17.6553 15.5548 17.6805 15.3831 17.6466L15.533 16.8417C15.3831 17.6466 15.3833 17.6467 15.3831 17.6466L15.3808 17.6462L15.3778 17.6456L15.3698 17.6439L15.3458 17.6388C15.3263 17.6345 15.2999 17.6285 15.2672 17.6205C15.2017 17.6046 15.1107 17.5808 14.9984 17.5475C14.7743 17.4809 14.4638 17.3755 14.1029 17.2171C13.3842 16.9018 12.4461 16.3676 11.5965 15.4928C10.747 14.618 10.2268 13.6506 9.91914 12.9096C9.76462 12.5374 9.66152 12.217 9.59625 11.9859C9.56357 11.8701 9.54022 11.7762 9.52457 11.7087C9.51675 11.675 9.51083 11.6478 9.50663 11.6277L9.50157 11.603L9.49995 11.5948L9.49937 11.5917L9.49913 11.5905C9.49908 11.5902 9.49892 11.5894 10.2802 11.4329L9.49913 11.5905C9.46561 11.413 9.4898 11.2281 9.5683 11.0664L10.8188 8.49091C10.858 8.41033 10.856 8.31495 10.8135 8.23609L9.51547 5.82836C9.51546 5.82834 9.51547 5.82837 9.51547 5.82836C9.46864 5.74155 9.3798 5.68772 9.28349 5.68772H6.59186ZM11.1244 11.5269C11.1245 11.5274 11.1247 11.5278 11.1248 11.5283C11.1749 11.7056 11.2572 11.9626 11.3828 12.2652C11.6355 12.8739 12.0549 13.6467 12.7221 14.3337C12.7221 14.3337 12.7221 14.3337 12.7221 14.3337L12.1593 14.9132M12.7221 14.3337C13.3892 15.0206 14.138 15.4507 14.727 15.7092C15.0199 15.8377 15.2684 15.9216 15.4399 15.9726C15.4402 15.9727 15.4395 15.9725 15.4399 15.9726L17.6777 14.8212C17.6777 14.8212 17.6777 14.8212 17.6777 14.8212C18.2262 14.5388 18.8748 14.5534 19.4107 14.8602L21.7557 16.2027C22.345 16.5401 22.7104 17.1798 22.7104 17.8741V20.6461C22.7104 22.5701 20.9462 24.0484 19.0877 23.4026C16.4031 22.4699 12.1089 20.6566 9.34533 17.8108C6.58173 14.9651 4.82075 10.5433 3.91493 7.77902C3.28783 5.86523 4.72338 4.04858 6.59186 4.04858H9.28349C9.95815 4.04858 10.5797 4.42535 10.9072 5.03269L12.2053 7.44052C12.5027 7.99217 12.5167 8.65948 12.2426 9.22394C12.2426 9.22396 12.2426 9.22392 12.2426 9.22394L11.1244 11.5269"
                                                fill="currentcolor" />
                                        </svg>
                                    </a>--}}
                                    <div class="nav-item dropdown">
                                        <a class="coordinator_phone_box" href="javascript:void(0);" id="company_phone_dropdown" role="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <svg width="26" height="28" viewBox="0 0 26 28" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M6.59186 5.68772C5.71851 5.68772 5.17373 6.49357 5.42322 7.25497C6.3178 9.98497 7.98487 14.0918 10.4709 16.6518C12.957 19.2118 16.9453 20.9283 19.5966 21.8495C20.3359 22.1064 21.1186 21.5455 21.1186 20.6461V17.8741C21.1186 17.7749 21.0664 17.6835 20.9822 17.6353L18.6372 16.2928C18.5607 16.249 18.468 16.2469 18.3897 16.2873L15.8889 17.5748C15.7326 17.6553 15.5548 17.6805 15.3831 17.6466L15.533 16.8417C15.3831 17.6466 15.3833 17.6467 15.3831 17.6466L15.3808 17.6462L15.3778 17.6456L15.3698 17.6439L15.3458 17.6388C15.3263 17.6345 15.2999 17.6285 15.2672 17.6205C15.2017 17.6046 15.1107 17.5808 14.9984 17.5475C14.7743 17.4809 14.4638 17.3755 14.1029 17.2171C13.3842 16.9018 12.4461 16.3676 11.5965 15.4928C10.747 14.618 10.2268 13.6506 9.91914 12.9096C9.76462 12.5374 9.66152 12.217 9.59625 11.9859C9.56357 11.8701 9.54022 11.7762 9.52457 11.7087C9.51675 11.675 9.51083 11.6478 9.50663 11.6277L9.50157 11.603L9.49995 11.5948L9.49937 11.5917L9.49913 11.5905C9.49908 11.5902 9.49892 11.5894 10.2802 11.4329L9.49913 11.5905C9.46561 11.413 9.4898 11.2281 9.5683 11.0664L10.8188 8.49091C10.858 8.41033 10.856 8.31495 10.8135 8.23609L9.51547 5.82836C9.51546 5.82834 9.51547 5.82837 9.51547 5.82836C9.46864 5.74155 9.3798 5.68772 9.28349 5.68772H6.59186ZM11.1244 11.5269C11.1245 11.5274 11.1247 11.5278 11.1248 11.5283C11.1749 11.7056 11.2572 11.9626 11.3828 12.2652C11.6355 12.8739 12.0549 13.6467 12.7221 14.3337C12.7221 14.3337 12.7221 14.3337 12.7221 14.3337L12.1593 14.9132M12.7221 14.3337C13.3892 15.0206 14.138 15.4507 14.727 15.7092C15.0199 15.8377 15.2684 15.9216 15.4399 15.9726C15.4402 15.9727 15.4395 15.9725 15.4399 15.9726L17.6777 14.8212C17.6777 14.8212 17.6777 14.8212 17.6777 14.8212C18.2262 14.5388 18.8748 14.5534 19.4107 14.8602L21.7557 16.2027C22.345 16.5401 22.7104 17.1798 22.7104 17.8741V20.6461C22.7104 22.5701 20.9462 24.0484 19.0877 23.4026C16.4031 22.4699 12.1089 20.6566 9.34533 17.8108C6.58173 14.9651 4.82075 10.5433 3.91493 7.77902C3.28783 5.86523 4.72338 4.04858 6.59186 4.04858H9.28349C9.95815 4.04858 10.5797 4.42535 10.9072 5.03269L12.2053 7.44052C12.5027 7.99217 12.5167 8.65948 12.2426 9.22394C12.2426 9.22396 12.2426 9.22392 12.2426 9.22394L11.1244 11.5269"
                                                    fill="currentcolor" />
                                            </svg>
                                            
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="company_phone_dropdown">
                                            <a href="tel:{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_phone_number}}">
                                                <div class="truck_company_dropdown_info px-2">
                                                    <div>{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_phone_number}}</div>
                                                    <div class="user_email">
                                                        <small>{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_name}}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                     </div>
                </div>
                <hr>
                @php
                $driverName = false ;
                $driverNumber = false ;
                $driverDetails = false ;
                if($ShipmentDetails->shipmentDriverScheduleDetails && $ShipmentDetails->shipmentDriverScheduleDetails->truckDriver){
                    $driverName = $ShipmentDetails?->shipmentDriverScheduleDetails?->truckDriver?->name;
                    $driverNumber = $ShipmentDetails?->shipmentDriverScheduleDetails?->truckDriver?->phone_number;
                    $driverDetails  = $ShipmentDetails?->shipmentDriverScheduleDetails?->truckDriver;
                    $driverDetails->userDriverDetail->driver_picture = url(Config('constants.DRIVER_PICTURE_PATH') . '/' . $driverDetails->userDriverDetail->driver_picture);
                }else if($shipmentOffer->TruckDetail && $shipmentOffer?->TruckDetail?->truckDriver){
                    $driverName = $shipmentOffer?->TruckDetail?->truckDriver->name;
                    $driverNumber = $shipmentOffer?->TruckDetail?->truckDriver->phone_number;
                    $driverDetails  = $shipmentOffer?->TruckDetail?->truckDriver;
                }
                @endphp
                @if($driverName)
                        <div class="transportation_request_block">
                            <div class="transport_coordinator_block">
                                <div class="transportation_request_block">
                                <h3 class="transpor_request_box_title tranRequest_title">{{ trans('messages.driver_details') }}</h3>
                                    <div class="company_offreDetails">
                                        <h3 class="offreDetails_cont">{{trans('messages.name')}}</h3>
                                        <span class="company_offreDetails_label"> : {{ $driverName }}</span>
                                    </div>
                                    @if($driverNumber)
                                    <div class="company_offreDetails">
                                        <h3 class="offreDetails_cont">{{trans('messages.admin_phone_number')}}</h3>
                                        <span class="company_offreDetails_label"> : {{ $driverNumber }}</span>
                                    </div>
                                    @endif
                                </div>
                                @if($driverNumber)
                                <div class="coordinator_phoneMess_box">
                                    <a href="#!" onclick="singelChatModal('{{$shipmentOffer?->TruckDetail?->truckDriver?->userDriverDetail?->driver_picture}}','{{$shipmentOffer?->TruckDetail?->truckDriver?->name}}','{{ $shipmentOffer?->TruckDetail?->truckDriver?->userDriverDetail->id }}')" class="table_message_btn">
                                        <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.25 11.0002C19.25 16.0628 15.3325 20.1668 10.5 20.1668C7.88659 20.1668 1.75 20.1668 1.75 20.1668C1.75 20.1668 1.75 13.3249 1.75 11.0002C1.75 5.93755 5.66751 1.8335 10.5 1.8335C15.3325 1.8335 19.25 5.93755 19.25 11.0002Z" fill="white"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 2.521C6.02994 2.521 2.40625 6.31725 2.40625 11.0002V19.4793H10.5C14.9701 19.4793 18.5938 15.6831 18.5938 11.0002C18.5938 6.31725 14.9701 2.521 10.5 2.521ZM1.75 20.1668L1.09375 20.1667V11.0002C1.09375 5.55785 5.30507 1.146 10.5 1.146C15.6949 1.146 19.9062 5.55785 19.9062 11.0002C19.9062 16.4425 15.6949 20.8543 10.5 20.8543H1.75007L1.75 20.1668ZM1.75 20.1668L1.75007 20.8543C1.38763 20.8543 1.09375 20.5464 1.09375 20.1667L1.75 20.1668Z" fill="white"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46875 8.25C5.46875 7.8703 5.76256 7.5625 6.125 7.5625H14C14.3624 7.5625 14.6562 7.8703 14.6562 8.25C14.6562 8.6297 14.3624 8.9375 14 8.9375H6.125C5.76256 8.9375 5.46875 8.6297 5.46875 8.25Z" fill="currentcolor"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46875 11.9165C5.46875 11.5368 5.76256 11.229 6.125 11.229H14C14.3624 11.229 14.6562 11.5368 14.6562 11.9165C14.6562 12.2962 14.3624 12.604 14 12.604H6.125C5.76256 12.604 5.46875 12.2962 5.46875 11.9165Z" fill="currentcolor"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46875 15.5835C5.46875 15.2038 5.76256 14.896 6.125 14.896H10.5C10.8624 14.896 11.1562 15.2038 11.1562 15.5835C11.1562 15.9632 10.8624 16.271 10.5 16.271H6.125C5.76256 16.271 5.46875 15.9632 5.46875 15.5835Z" fill="currentcolor"></path>
                                        </svg>
                                    </a>
                                    {{--<a href="tel:{{$driverNumber}}" class="coordinator_phone_box">
                                        <svg width="26" height="28" viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.59186 5.68772C5.71851 5.68772 5.17373 6.49357 5.42322 7.25497C6.3178 9.98497 7.98487 14.0918 10.4709 16.6518C12.957 19.2118 16.9453 20.9283 19.5966 21.8495C20.3359 22.1064 21.1186 21.5455 21.1186 20.6461V17.8741C21.1186 17.7749 21.0664 17.6835 20.9822 17.6353L18.6372 16.2928C18.5607 16.249 18.468 16.2469 18.3897 16.2873L15.8889 17.5748C15.7326 17.6553 15.5548 17.6805 15.3831 17.6466L15.533 16.8417C15.3831 17.6466 15.3833 17.6467 15.3831 17.6466L15.3808 17.6462L15.3778 17.6456L15.3698 17.6439L15.3458 17.6388C15.3263 17.6345 15.2999 17.6285 15.2672 17.6205C15.2017 17.6046 15.1107 17.5808 14.9984 17.5475C14.7743 17.4809 14.4638 17.3755 14.1029 17.2171C13.3842 16.9018 12.4461 16.3676 11.5965 15.4928C10.747 14.618 10.2268 13.6506 9.91914 12.9096C9.76462 12.5374 9.66152 12.217 9.59625 11.9859C9.56357 11.8701 9.54022 11.7762 9.52457 11.7087C9.51675 11.675 9.51083 11.6478 9.50663 11.6277L9.50157 11.603L9.49995 11.5948L9.49937 11.5917L9.49913 11.5905C9.49908 11.5902 9.49892 11.5894 10.2802 11.4329L9.49913 11.5905C9.46561 11.413 9.4898 11.2281 9.5683 11.0664L10.8188 8.49091C10.858 8.41033 10.856 8.31495 10.8135 8.23609L9.51547 5.82836C9.51546 5.82834 9.51547 5.82837 9.51547 5.82836C9.46864 5.74155 9.3798 5.68772 9.28349 5.68772H6.59186ZM11.1244 11.5269C11.1245 11.5274 11.1247 11.5278 11.1248 11.5283C11.1749 11.7056 11.2572 11.9626 11.3828 12.2652C11.6355 12.8739 12.0549 13.6467 12.7221 14.3337C12.7221 14.3337 12.7221 14.3337 12.7221 14.3337L12.1593 14.9132M12.7221 14.3337C13.3892 15.0206 14.138 15.4507 14.727 15.7092C15.0199 15.8377 15.2684 15.9216 15.4399 15.9726C15.4402 15.9727 15.4395 15.9725 15.4399 15.9726L17.6777 14.8212C17.6777 14.8212 17.6777 14.8212 17.6777 14.8212C18.2262 14.5388 18.8748 14.5534 19.4107 14.8602L21.7557 16.2027C22.345 16.5401 22.7104 17.1798 22.7104 17.8741V20.6461C22.7104 22.5701 20.9462 24.0484 19.0877 23.4026C16.4031 22.4699 12.1089 20.6566 9.34533 17.8108C6.58173 14.9651 4.82075 10.5433 3.91493 7.77902C3.28783 5.86523 4.72338 4.04858 6.59186 4.04858H9.28349C9.95815 4.04858 10.5797 4.42535 10.9072 5.03269L12.2053 7.44052C12.5027 7.99217 12.5167 8.65948 12.2426 9.22394C12.2426 9.22396 12.2426 9.22392 12.2426 9.22394L11.1244 11.5269" fill="currentcolor" />
                                        </svg>
                                    </a>--}}
                                    <div class="nav-item dropdown">
                                        <a class="coordinator_phone_box" href="javascript:void(0);" id="driver_phone_dropdown" role="button"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <svg width="26" height="28" viewBox="0 0 26 28" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M6.59186 5.68772C5.71851 5.68772 5.17373 6.49357 5.42322 7.25497C6.3178 9.98497 7.98487 14.0918 10.4709 16.6518C12.957 19.2118 16.9453 20.9283 19.5966 21.8495C20.3359 22.1064 21.1186 21.5455 21.1186 20.6461V17.8741C21.1186 17.7749 21.0664 17.6835 20.9822 17.6353L18.6372 16.2928C18.5607 16.249 18.468 16.2469 18.3897 16.2873L15.8889 17.5748C15.7326 17.6553 15.5548 17.6805 15.3831 17.6466L15.533 16.8417C15.3831 17.6466 15.3833 17.6467 15.3831 17.6466L15.3808 17.6462L15.3778 17.6456L15.3698 17.6439L15.3458 17.6388C15.3263 17.6345 15.2999 17.6285 15.2672 17.6205C15.2017 17.6046 15.1107 17.5808 14.9984 17.5475C14.7743 17.4809 14.4638 17.3755 14.1029 17.2171C13.3842 16.9018 12.4461 16.3676 11.5965 15.4928C10.747 14.618 10.2268 13.6506 9.91914 12.9096C9.76462 12.5374 9.66152 12.217 9.59625 11.9859C9.56357 11.8701 9.54022 11.7762 9.52457 11.7087C9.51675 11.675 9.51083 11.6478 9.50663 11.6277L9.50157 11.603L9.49995 11.5948L9.49937 11.5917L9.49913 11.5905C9.49908 11.5902 9.49892 11.5894 10.2802 11.4329L9.49913 11.5905C9.46561 11.413 9.4898 11.2281 9.5683 11.0664L10.8188 8.49091C10.858 8.41033 10.856 8.31495 10.8135 8.23609L9.51547 5.82836C9.51546 5.82834 9.51547 5.82837 9.51547 5.82836C9.46864 5.74155 9.3798 5.68772 9.28349 5.68772H6.59186ZM11.1244 11.5269C11.1245 11.5274 11.1247 11.5278 11.1248 11.5283C11.1749 11.7056 11.2572 11.9626 11.3828 12.2652C11.6355 12.8739 12.0549 13.6467 12.7221 14.3337C12.7221 14.3337 12.7221 14.3337 12.7221 14.3337L12.1593 14.9132M12.7221 14.3337C13.3892 15.0206 14.138 15.4507 14.727 15.7092C15.0199 15.8377 15.2684 15.9216 15.4399 15.9726C15.4402 15.9727 15.4395 15.9725 15.4399 15.9726L17.6777 14.8212C17.6777 14.8212 17.6777 14.8212 17.6777 14.8212C18.2262 14.5388 18.8748 14.5534 19.4107 14.8602L21.7557 16.2027C22.345 16.5401 22.7104 17.1798 22.7104 17.8741V20.6461C22.7104 22.5701 20.9462 24.0484 19.0877 23.4026C16.4031 22.4699 12.1089 20.6566 9.34533 17.8108C6.58173 14.9651 4.82075 10.5433 3.91493 7.77902C3.28783 5.86523 4.72338 4.04858 6.59186 4.04858H9.28349C9.95815 4.04858 10.5797 4.42535 10.9072 5.03269L12.2053 7.44052C12.5027 7.99217 12.5167 8.65948 12.2426 9.22394C12.2426 9.22396 12.2426 9.22392 12.2426 9.22394L11.1244 11.5269"
                                                    fill="currentcolor" />
                                            </svg>
                                            
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="driver_phone_dropdown">
                                            <a href="tel:{{$driverNumber}}">
                                                <div class="truck_driver_dropdown_info px-2">
                                                    <div>{{$driverNumber}}</div>
                                                    <div class="user_email">
                                                        <small>{{ $driverName }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                            <div class="current_location_map">
                                <!-- <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d113874.30006233433!2d75.70815711078538!3d26.88533996479717!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x396c4adf4c57e281%3A0xce1c63a0cf22e09!2sJaipur%2C%20Rajasthan%2C%20India!5e0!3m2!1sen!2sus!4v1693549576176!5m2!1sen!2sus"
                                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> -->
                                @if($ShipmentDetails->shipmentDriverScheduleDetails && $ShipmentDetails->shipmentDriverScheduleDetails->shipment_status == "start" )
                                    <div class="company_offreDetails">
                                        <h3 class="offreDetails_cont">{{trans("messages.current_location")}}</h3>
                                        {{-- @dd($ShipmentDetails->shipmentDriverScheduleDetails->truckDriver) --}}
                                        <span class="company_offreDetails_label"> {{$ShipmentDetails->shipmentDriverScheduleDetails->truckDriver->current_location}}</span>
                                    </div>
                                    <div id="map" style="height: 300px;width: 100%; border-radius: 10px ;"></div>
                                @endif
                            </div>
                        </div>
                @endif                        
            </div>
                </div>
        </div>

    </section>
    @stop
    @section('scriptCode')
    
        @include('frontend.chat.commonChatModal')
        @php
        $shipment = $ShipmentDetails;
        @endphp
        @if($shipment->shipmentRatingReviews)
            @include('frontend.customers.commonViewRatingModal')
        @else
            @include('frontend.customers.commonRatingModal')
        @endif

        @if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->shipment_status == "start" )
            @include('common.trackingMap')
        @endif
    
    <script>
        $(".confirmDelete").click(function (e) {
            e.stopImmediatePropagation();
            url = $(this).attr('href');
            Swal.fire({
                title: "{{trans('messages.admin_common_Are_you_sure')}}",
                text: "{{trans('messages.admin_Want_to_delete_this')}}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{trans('messages.admin_Yes_delete_it')}}",
                cancelButtonText: "{{trans('messages.admin_No_cancel')}}",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    window.location.replace(url);
                } else if (result.dismiss === "cancel") {
                    Swal.fire(
                        "{{trans('messages.admin_common_Cancelled')}}",
                        "{{trans('messages.admin_Your_imaginary_file_is_safe')}}",
                        "{{trans('messages.admin_common_error')}}"
                    )
                }
            });
            e.preventDefault();
        });
    </script>
    @stop