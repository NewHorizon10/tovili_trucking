@extends('frontend.layouts.default')
@section('extraCssLinks')
<!-- Custom Responsive CSS -->
<link rel="stylesheet" href="{{asset('public/frontend/css/responsive.css')}}">
<!-- Dashboard CSS-->
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard.css')}}">
<!-- Dashboard Responsive CSS-->
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard-responsive.css')}}">

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
                    {{-- @if($ShipmentDetails->ShipmentOffers->count())
                        <a href="#!" class="transpor_request_message">
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
                        </a>
                    @endif --}}
                </div>
                <div class="dashboardRight_block_wrapper dashborard_box_shodow ">
                    <h2 class="RightBlockTitle">{{trans("messages.transport_request_number")}}
                        {{$ShipmentDetails->request_number}}
                    </h2>
                    
                    <div class="request_editeDelite" style="position: absolute;top: -10px;left: -13px;">
                        <!-- <a href="#!" class="request_edite_btn">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0 13.4592V17.001H3.54184L13.9877 6.55519L10.4458 3.01335L0 13.4592ZM16.7264 3.81646C17.0926 3.44757 17.0926 2.85273 16.7264 2.48521L14.5158 0.274622C14.1469 -0.0915406 13.5521 -0.0915406 13.1846 0.274622L11.4558 2.00334L14.9977 5.54518L16.7264 3.81646Z"
                                    fill="currentcolor" />
                            </svg>
                        </a> -->
                        @if($ShipmentDetails->ShipmentOffers->count())
                            <a href="javascript:void(0)" class="request_delite_btn cantDelete">
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
                            </a>
                        @else
                            <a href="{{route('private-shipment-request-details-delete',$ShipmentDetails->request_number)}}" class="request_delite_btn confirmDelete">
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
                            </a>
                        @endif
                    </div>
                    <div class="offer_details_content requestCom_deta mt-4 mb-4">
                      
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans("messages.admin_common_Status")}}</span>
                            
                            @php
                                $className = '';
                                $shipmentStatus = '';
                                if($ShipmentDetails->status == "shipment"){
                                    $className = 'orange_btn';
                                    $shipmentStatus = 'in_process';
                                }elseif($ShipmentDetails->status == "offer_chosen"){
                                    $shipmentStatus = 'offer_chosen';
                                    $className = 'green_btn';
                                }elseif($ShipmentDetails->status == "new"){
                                    $className = 'orange_btn';
                                    $shipmentStatus = 'new';
                                }elseif($ShipmentDetails->status == "offers" && $ShipmentDetails->ShipmentOffers->isNotEmpty()){
                                    $className = 'blue_btn';
                                    $shipmentStatus = 'in_offer';
                                }elseif($ShipmentDetails->ShipmentOffers->isEmpty()){
                                    $className = 'orange_btn';
                                    $shipmentStatus = 'new';
                                }
                            @endphp
                            <h3 class="offreDetails_cont">
                            <a href="#!" class="{{$className}} dashboard_tableBtn"><span>{{trans("messages.".$shipmentStatus)}}</span></a>
                            </h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.request_type')}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->TruckTypeDescriptionsPrivate->name }}</h3>
                        </div>
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.request_date')}}</span>
                            <span class="date_label offreDetails_cont" style="justify-content: start;">
                                <h3 class=" offreDetails_cont">{{ \Carbon\Carbon::createFromFormat('Y-m-d',
                                    ($ShipmentDetails->request_date))->format(config("Reading.date_format")) }} :<br/>
                                    {{$ShipmentDetails->RequestTimeDescription->code ?? "" }}</h3>
                            </span>
                        </div>
                        
                        
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans("messages.flexibility_days")}}</span>
                            <h3 class="offreDetails_cont">: {{ $ShipmentDetails->request_date_flexibility }} {{ trans('messages.'.($ShipmentDetails->request_date_flexibility >1 ?  'days' : 'day'))}}</h3>
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
                        
                        @if($ShipmentDetails->shipment_type != 1)
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
                        
                        @if($ShipmentDetails->shipment_type != 1)
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
                        @if($ShipmentDetails->shipment_type == 1)
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
                                            <a class="fancybox-buttons" data-fancybox-group="button" href="{{$attchement->attachment}}" target="_blank">
                                                <div class="upload_img_item tabel_img">
                                                    <img width="100px" height="80" src="{{$attchement->attachment}}" alt="">
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
                    </div>
                </div>
            </div>
            <div class="company_details_block">
                @if($ShipmentDetails->SelectedShipmentOffers)
                <div class="dashboardRight_block_wrapper company_details_block">
                        <div>
                            <h3 class="transpor_request_box_title" style="color:#1535b9">{{trans('messages.selected_offer_details')}}</h3>
                        </div>
                        <div class="dashboard_notofication_main List_objectives_table">
                            <div class="transportation_request_block offer_details_content">
                                <div class="company_offreDetails">
                                    <span class="company_offreDetails_label">{{trans('messages.the_date_of_transport')}}</span>
                                    <span>
                                        <h3 class="date_label offreDetails_cont">{{ \Carbon\Carbon::createFromFormat('Y-m-d', ($ShipmentDetails->request_date))->format(config("Reading.date_format"))  }} :<br/>
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
                                </div>
                                 --}}
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
                                {{-- <div class="company_offreDetails">
                                    <span class="company_offreDetails_label">{{trans('messages.duration_in_hours')}}</span>
                                    <h3 class="offreDetails_cont">: {{$shipmentOffer->duration_in_hours}}
                                </div> --}}
                                <div class="company_offreDetails">
                                    <span class="company_offreDetails_label">{{trans('messages.duration_in_hours')}}</span>
                                    <h3 class="offreDetails_cont">: {{$shipmentOffer->duration}}</h3>
                                </div>
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
                                {{--
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.Truck Number')}}</span>
                                        <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->truck_system_number}}
                                    </div>
                                --}}
                                <div class="company_offreDetails">
                                    <span class="company_offreDetails_label">{{trans('messages.admin_common_Status')}}</span>
                                    <h3 class="offreDetails_cont">: {{ trans("messages.$shipmentOffer->status") }}
                                </div>
                                
                                <!--  --------------------------------------------  -->
                                <div>
                                    
                                    <div class="approved_offer_box">
                                        @if($shipmentOffer->status == 'selected') 
                                            <a href="javascript:void(0)" class="transportRequestBtn"> <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M18.5849 6.45695C18.501 6.37235 18.4012 6.30519 18.2912 6.25936C18.1812 6.21354 18.0632 6.18994 17.944 6.18994C17.8249 6.18994 17.7069 6.21354 17.5969 6.25936C17.4869 6.30519 17.3871 6.37235 17.3031 6.45695L10.5782 13.1909L7.75282 10.3565C7.66569 10.2723 7.56284 10.2062 7.45013 10.1618C7.33743 10.1173 7.21708 10.0956 7.09596 10.0977C6.97484 10.0997 6.85532 10.1257 6.74422 10.174C6.63312 10.2223 6.53262 10.292 6.44845 10.3791C6.36429 10.4662 6.29811 10.5691 6.25369 10.6818C6.20928 10.7945 6.1875 10.9148 6.18959 11.0359C6.19169 11.1571 6.21762 11.2766 6.26591 11.3877C6.31419 11.4988 6.38389 11.5993 6.47102 11.6834L9.9373 15.1497C10.0212 15.2343 10.121 15.3015 10.231 15.3473C10.341 15.3931 10.459 15.4167 10.5782 15.4167C10.6974 15.4167 10.8153 15.3931 10.9253 15.3473C11.0353 15.3015 11.1352 15.2343 11.2191 15.1497L18.5849 7.78389C18.6766 7.69936 18.7497 7.59677 18.7997 7.48258C18.8497 7.36839 18.8755 7.24508 18.8755 7.12042C18.8755 6.99576 18.8497 6.87245 18.7997 6.75826C18.7497 6.64407 18.6766 6.54148 18.5849 6.45695Z"
                                                        fill="white" />
                                                    <circle cx="11.6058" cy="11.6058" r="10.6058" stroke="white" stroke-width="2" />
                                                </svg> {{trans('messages.waiting_for_approval')}}
                                            </a>
                                        @else
                                            <a href="{{route('private-shipment-offer-approved',[$shipmentOffer->system_id])}}" class="transportRequestBtn"> <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M18.5849 6.45695C18.501 6.37235 18.4012 6.30519 18.2912 6.25936C18.1812 6.21354 18.0632 6.18994 17.944 6.18994C17.8249 6.18994 17.7069 6.21354 17.5969 6.25936C17.4869 6.30519 17.3871 6.37235 17.3031 6.45695L10.5782 13.1909L7.75282 10.3565C7.66569 10.2723 7.56284 10.2062 7.45013 10.1618C7.33743 10.1173 7.21708 10.0956 7.09596 10.0977C6.97484 10.0997 6.85532 10.1257 6.74422 10.174C6.63312 10.2223 6.53262 10.292 6.44845 10.3791C6.36429 10.4662 6.29811 10.5691 6.25369 10.6818C6.20928 10.7945 6.1875 10.9148 6.18959 11.0359C6.19169 11.1571 6.21762 11.2766 6.26591 11.3877C6.31419 11.4988 6.38389 11.5993 6.47102 11.6834L9.9373 15.1497C10.0212 15.2343 10.121 15.3015 10.231 15.3473C10.341 15.3931 10.459 15.4167 10.5782 15.4167C10.6974 15.4167 10.8153 15.3931 10.9253 15.3473C11.0353 15.3015 11.1352 15.2343 11.2191 15.1497L18.5849 7.78389C18.6766 7.69936 18.7497 7.59677 18.7997 7.48258C18.8497 7.36839 18.8755 7.24508 18.8755 7.12042C18.8755 6.99576 18.8497 6.87245 18.7997 6.75826C18.7497 6.64407 18.6766 6.54148 18.5849 6.45695Z"
                                                        fill="white" />
                                                    <circle cx="11.6058" cy="11.6058" r="10.6058" stroke="white" stroke-width="2" />
                                                </svg> {{trans('messages.approved_offer')}}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboardRight_block_wrapper admin_right_page company_details_block">
                        <!-- <h3 class="RightBlockTitle mb-4">{{trans("messages.selected_offer")}}</h3> -->
                        <div class="dashboard_notofication_main  List_objectives_table">
                            <div class="transportation_request_block">
                                <div class="transpor_request_box">
                                    <h3 class="transpor_request_box_title" style="color:#1535b9">{{trans("messages.Transportation")}}</h3>
                                </div>
                                <div class="coordinator_name_box">
                                    <div class="company_name_logo_box">
                                        <img src="{{$shipmentOffer->companyUser->userCompanyInformation->company_logo}}" alt="">
                                    </div>
                                    <div class="coordinator_name_title">: {{$shipmentOffer->companyUser->userCompanyInformation->company_name}}</div>
                                </div>
                                {{-- <p class="company_details_desc">: {{$shipmentOffer->companyUser->userCompanyInformation->company_description}}</p> --}}
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
                        </div>
            
                    </div>
                    <div class="dashboardRight_block_wrapper company_details_block">
                        <div class="dashboard_notofication_main  List_objectives_table">
                            <div class="transportation_request_block">
                            <h3 class="transpor_request_box_title">{{ trans('messages.contact_person_details') }}</h3>
                                <div class="transport_coordinator_block">
                                
                                    <div class="coordinator_name_box">
                                        <img src="{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_picture}}" alt="">
                                        <h3 class="coordinator_name_title">: {{$shipmentOffer->companyUser->userCompanyInformation->contact_person_name}}</h3>
                                    </div>
                                    <div class="coordinator_phoneMess_box">
                                        @if($ShipmentDetails->SelectedShipmentOffers->show_chat_icon == 1)
                                        <a href="#!" onclick="singelChatModal('{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_picture}}','{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_name}}','{{ $shipmentOffer->companyUser->userCompanyInformation->user_id }}')" class="table_message_btn">
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
                                        
                                        {{--<a href="tel:{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_phone_number}}" class="coordinator_phone_box">
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
                        </div>
                    </div>

                    @php
                    $driverName = false ;
                    $driverNumber = false ;
                    if($ShipmentDetails->shipmentDriverScheduleDetails && $ShipmentDetails->shipmentDriverScheduleDetails->truckDriver){
                        $driverName = $ShipmentDetails->shipmentDriverScheduleDetails->truckDriver->name;
                        $driverNumber = $ShipmentDetails->shipmentDriverScheduleDetails->truckDriver->phone_number;
                    }else if($shipmentOffer->TruckDetail && $shipmentOffer->TruckDetail->truckDriver){
                        $driverName = $shipmentOffer->TruckDetail->truckDriver->name;
                        $driverNumber = $shipmentOffer->TruckDetail->truckDriver->phone_number;
                    }
                    @endphp
                    @if($driverName)
                    <div class="dashboardRight_block_wrapper company_details_block">
                        <div class="dashboard_notofication_main  List_objectives_table">
                            <div class="transportation_request_block">
                                <div class="transport_coordinator_block">
                                    <div class="transportation_request_block">
                                    <h3 class="transpor_request_box_title">{{ trans('messages.driver_details') }}</h3>
                                        <div class="company_offreDetails">
                                            <h3 class="offreDetails_cont">{{trans('messages.name')}}</h3>
                                            <span class="company_offreDetails_label"> : {{ $driverName }}</span>
                                        </div>
                                        {{--<div class="company_offreDetails">
                                            <h3 class="offreDetails_cont">{{trans('messages.admin_number')}}</h3>
                                            <span class="company_offreDetails_label"> : {{ $driverNumber }}</span>
                                        </div>--}}
                                    </div>
                                    {{--<div class="coordinator_phoneMess_box">
                                        
                                        <a href="#!" class="table_message_btn">
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
                                        
                                    </div>--}}

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                @else
                    <div class="suggestions_customer_table">
                        <h3 class="RightBlockTitle mb-4">{{trans("messages.suggestions")}}</h3>
                        <div class="dashboard_notofication_main dahboard_whiteSpace List_objectives_table">
                            <div class="table-responsive dashboard_notofication">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{trans("messages.date")}}</th>
                                            <th scope="col">{{trans("messages.Company Name")}}</th>
                                            <th scope="col">{{trans("messages.bid")}}</th>
                                            <th scope="col">{{trans("messages.company_rating")}}</th>
                                            <th scope="col">{{trans("messages.choose_an_execution")}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($ShipmentDetails->ShipmentOffers->count())
                                            @foreach($ShipmentDetails->ShipmentOffers as $shipmentOffer )
                                                <tr>
                                                    <td>
                                                        <a class="date_label" href="{{route('private-shipment-offer-details',[$shipmentOffer->system_id])}}">
                                                             {{ $shipmentOffer->created_at->format(config("Reading.date_format"))  }}<br/>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{route('private-shipment-offer-details',[$shipmentOffer->system_id])}}">
                                                            <div class="company_name_block"> <img src="{{$shipmentOffer->companyUser->userCompanyInformation->company_logo}}"
                                                                alt=""> <span> {{$shipmentOffer->companyUser->userCompanyInformation->company_name}}</span></div>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{route('private-shipment-offer-details',[$shipmentOffer->system_id])}}">
                                                             {{$shipmentOffer->price}} {{Config('constants.CURRENCY_SIGN')}}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{route('private-shipment-offer-details',[$shipmentOffer->system_id])}}">
                                                            <div class="company_ratting_box">
                                                                @for($i = 1 ; $i <= 5 ; $i++)
                                                                    @if($i <= $shipmentOffer->rating->overall_rating)
                                                                        <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#1535B9"/>
                                                                        </svg>
                                                                    @else
                                                                        <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#B6B5B5"/>
                                                                        </svg>
                                                                    @endif
                                                                @endfor
                                                                <!-- <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#1535B9"/>
                                                                    </svg>
                                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#1535B9"/>
                                                                    </svg>
                                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#1535B9"/>
                                                                    </svg>
                                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#B6B5B5"/>
                                                                    </svg>
                                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#B6B5B5"/>
                                                                    </svg>
                                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#B6B5B5"/>
                                                                    </svg>
                                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#B6B5B5"/>
                                                                    </svg>
                                                                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M15.3772 17.6069C14.7176 18.0767 10.4855 15.0987 9.675 15.0922C8.86447 15.0857 4.58477 17.9952 3.9329 17.5148C3.28104 17.0345 4.81355 12.1007 4.56932 11.3301C4.3251 10.5594 0.22755 7.3998 0.484223 6.63316C0.740953 5.86651 5.92014 5.7953 6.57971 5.32551C7.23927 4.85577 8.98658 -0.00648238 9.79718 6.48883e-06C10.6077 0.00655178 12.276 4.89628 12.9279 5.37663C13.5798 5.85692 18.7572 6.01152 19.0015 6.78217C19.2457 7.55283 15.0976 10.6461 14.8409 11.4127C14.5842 12.1794 16.0367 17.1371 15.3772 17.6069Z" fill="#B6B5B5"/>
                                                                    </svg>-->
                                                                    
                                                            </div>
                                                            </a>
                                                    </td>
                                                    <td>
                                                        <div class="choose_execution_box">
                                                        @if($shipmentOffer->show_chat_icon == 1)
                                                            <a href="{{route('private-shipment-offer-details',[$shipmentOffer->system_id])}}" class="table_message_btn">
                                                                <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M19.25 11.0002C19.25 16.0628 15.3325 20.1668 10.5 20.1668C7.88659 20.1668 1.75 20.1668 1.75 20.1668C1.75 20.1668 1.75 13.3249 1.75 11.0002C1.75 5.93755 5.66751 1.8335 10.5 1.8335C15.3325 1.8335 19.25 5.93755 19.25 11.0002Z"
                                                                        fill="white" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M10.5 2.521C6.02994 2.521 2.40625 6.31725 2.40625 11.0002V19.4793H10.5C14.9701 19.4793 18.5938 15.6831 18.5938 11.0002C18.5938 6.31725 14.9701 2.521 10.5 2.521ZM1.75 20.1668L1.09375 20.1667V11.0002C1.09375 5.55785 5.30507 1.146 10.5 1.146C15.6949 1.146 19.9062 5.55785 19.9062 11.0002C19.9062 16.4425 15.6949 20.8543 10.5 20.8543H1.75007L1.75 20.1668ZM1.75 20.1668L1.75007 20.8543C1.38763 20.8543 1.09375 20.5464 1.09375 20.1667L1.75 20.1668Z"
                                                                        fill="white" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M5.46875 8.25C5.46875 7.8703 5.76256 7.5625 6.125 7.5625H14C14.3624 7.5625 14.6562 7.8703 14.6562 8.25C14.6562 8.6297 14.3624 8.9375 14 8.9375H6.125C5.76256 8.9375 5.46875 8.6297 5.46875 8.25Z"
                                                                        fill="currentcolor" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M5.46875 11.9165C5.46875 11.5368 5.76256 11.229 6.125 11.229H14C14.3624 11.229 14.6562 11.5368 14.6562 11.9165C14.6562 12.2962 14.3624 12.604 14 12.604H6.125C5.76256 12.604 5.46875 12.2962 5.46875 11.9165Z"
                                                                        fill="currentcolor" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M5.46875 15.5835C5.46875 15.2038 5.76256 14.896 6.125 14.896H10.5C10.8624 14.896 11.1562 15.2038 11.1562 15.5835C11.1562 15.9632 10.8624 16.271 10.5 16.271H6.125C5.76256 16.271 5.46875 15.9632 5.46875 15.5835Z"
                                                                        fill="currentcolor" />
                                                                </svg>
                                                            </a>
                                                            @endif
                                                        <a href="{{route('private-shipment-offer-details',[$shipmentOffer->system_id])}}">
                                                            <div class="choose_table_btn">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M18.6357 7.09953C18.5518 7.01492 18.452 6.94777 18.342 6.90194C18.232 6.85611 18.114 6.83252 17.9948 6.83252C17.8757 6.83252 17.7577 6.85611 17.6477 6.90194C17.5377 6.94777 17.4378 7.01492 17.3539 7.09953L10.629 13.8335L7.8036 10.9991C7.71647 10.9149 7.61362 10.8487 7.50092 10.8043C7.38821 10.7599 7.26786 10.7381 7.14674 10.7402C7.02562 10.7423 6.9061 10.7683 6.795 10.8165C6.6839 10.8648 6.5834 10.9345 6.49923 11.0217C6.41507 11.1088 6.34889 11.2116 6.30447 11.3243C6.26006 11.437 6.23828 11.5574 6.24037 11.6785C6.24247 11.7996 6.2684 11.9192 6.31669 12.0303C6.36497 12.1414 6.43467 12.2419 6.5218 12.326L9.98808 15.7923C10.072 15.8769 10.1718 15.9441 10.2818 15.9899C10.3918 16.0357 10.5098 16.0593 10.629 16.0593C10.7481 16.0593 10.8661 16.0357 10.9761 15.9899C11.0861 15.9441 11.186 15.8769 11.2699 15.7923L18.6357 8.42646C18.7273 8.34194 18.8005 8.23934 18.8505 8.12516C18.9005 8.01097 18.9263 7.88766 18.9263 7.763C18.9263 7.63834 18.9005 7.51503 18.8505 7.40084C18.8005 7.28665 18.7273 7.18406 18.6357 7.09953Z"
                                                                        fill="white" />
                                                                    <circle cx="11.6566" cy="12.2484" r="10.6058" stroke="white"
                                                                        stroke-width="2" />
                                                                </svg>
                                                                {{trans("messages.choose")}}
                                                            </div>
                                                            </a>
                                                            <a href="{{route('private-shipment-request-destroy',[$shipmentOffer->system_id])}}" class="choose_trus_btn confirmDelete">
                                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <g clip-path="url(#clip0_872_51382)">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M2.8125 3.75C2.8125 3.43934 3.06434 3.1875 3.375 3.1875H14.625C14.9357 3.1875 15.1875 3.43934 15.1875 3.75V16.5C15.1875 16.8107 14.9357 17.0625 14.625 17.0625H3.375C3.06434 17.0625 2.8125 16.8107 2.8125 16.5V3.75ZM3.9375 4.3125V15.9375H14.0625V4.3125H3.9375Z"
                                                                            fill="currentcolor " />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M7.5 6.9375C7.81066 6.9375 8.0625 7.18934 8.0625 7.5V12.375C8.0625 12.6857 7.81066 12.9375 7.5 12.9375C7.18934 12.9375 6.9375 12.6857 6.9375 12.375V7.5C6.9375 7.18934 7.18934 6.9375 7.5 6.9375Z"
                                                                            fill="currentcolor " />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M10.5 6.9375C10.8107 6.9375 11.0625 7.18934 11.0625 7.5V12.375C11.0625 12.6857 10.8107 12.9375 10.5 12.9375C10.1893 12.9375 9.9375 12.6857 9.9375 12.375V7.5C9.9375 7.18934 10.1893 6.9375 10.5 6.9375Z"
                                                                            fill="currentcolor" />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M0.9375 3.75C0.9375 3.43934 1.18934 3.1875 1.5 3.1875H16.5C16.8107 3.1875 17.0625 3.43934 17.0625 3.75C17.0625 4.06066 16.8107 4.3125 16.5 4.3125H1.5C1.18934 4.3125 0.9375 4.06066 0.9375 3.75Z"
                                                                            fill="currentcolor " />
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M6.74012 1.22962C6.83887 1.04948 7.02795 0.9375 7.23337 0.9375H10.7914C10.9986 0.9375 11.1889 1.05134 11.2869 1.23382L12.4955 3.48382C12.5892 3.65815 12.5843 3.86885 12.4828 4.03869C12.3812 4.20852 12.1979 4.3125 12 4.3125H6C5.80126 4.3125 5.61728 4.20763 5.51601 4.03663C5.41474 3.86563 5.41122 3.65389 5.50675 3.47962L6.74012 1.22962ZM7.5665 2.0625L6.94981 3.1875H11.0593L10.455 2.0625H7.5665Z"
                                                                            fill="currentcolor " />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_872_51382">
                                                                            <rect width="18" height="18" fill="white" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">
                                                    <h3 style="text-align: center;">{{trans('messages.offer_not_found')}}</h3>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </section>
    @stop
    @section('scriptCode')
        @include('frontend.chat.commonChatModal')
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