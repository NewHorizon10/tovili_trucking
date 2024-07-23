@extends('frontend.layouts.customers')
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

    <div class="col-lg-9 col-xl-9 col-xxl-9 col-sm-12">
    <div class="dashboardRight_block_wrapper company_details_block">
            <div>
                <h3 class="transpor_request_box_title">{{trans('messages.selected_offer_details')}}</h3>
            </div>
            <div class="dashboard_notofication_main List_objectives_table">
                <div class="transportation_request_block offer_details_content">
                    <div class="company_offreDetails ">
                        <span class="company_offreDetails_label">{{trans('messages.the_date_of_transport')}}</span>
                        <span>
                            <h3 class=" date_label offreDetails_cont" > {{ \Carbon\Carbon::createFromFormat('Y-m-d', ($shipment->request_date))->format(config("Reading.date_format"))  }} :<br/>
                            {{$shipment->RequestTimeDescription->code ?? "" }}</h3>
                        </span>
                    </div>
                    
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.pickup_city')}}</span>
                        <h3 class="offreDetails_cont">: {{$shipment->pickup_city}}</h3>
                    </div>
                    <div class="company_offreDetails">
                    
                        <span class="company_offreDetails_label">{{trans('messages.original_address')}}</span>
                        <h3 class="offreDetails_cont">: {{$shipment->pickup_address}}</h3>
                    </div>
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.admin_destination_city')}}</span>
                        <h3 class="offreDetails_cont">: @foreach($shipment->ShipmentStop as $ShipmentStop )
                            {{$ShipmentStop->dropoff_city}}
                            @break
                        @endforeach</h3>
                    </div>
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.destination_address')}}</span>
                        <h3 class="offreDetails_cont">: @foreach($shipment->ShipmentStop as $ShipmentStop )
                            {{$ShipmentStop->dropoff_address}}
                            @break
                        @endforeach</h3>
                    </div>
                   
                    <!-- <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.description')}}</span>
                        <h3 class="offreDetails_cont">: {{$shipment->description}}
                            
                    </div> -->
                    <!--  --------------------------------------------  -->
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.admin_common_Price')}}</span>
                        <h3 class="offreDetails_cont">: {{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->price}}</h3>
                    </div>
                    <div class="company_offreDetails">
                   
                        <span class="company_offreDetails_label">{{trans('messages.extra_time_price')}}</span>
                        <h3 class="offreDetails_cont">: {{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->extra_time_price}}</h3>
                    </div>
                    {{--
                        <div class="company_offreDetails">
                            <span class="company_offreDetails_label">{{trans('messages.duration_in_hours')}}</span>
                            <h3 class="offreDetails_cont">: {{$shipmentOffer->duration_in_hours}}</h3>
                        </div>
                    --}}
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.duration')}}</span>
                        <h3 class="offreDetails_cont">: {{$shipmentOffer->duration}}</h3>
                    </div>
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.shipment_note')}}</span>
                        <h3 class="offreDetails_cont">: {{$shipmentOffer->description}}</h3>
                    </div>
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.payment_condition')}}</span>
                        <h3 class="offreDetails_cont">: {{$shipmentOffer->payment_condition}}</h3>
                    </div>
                    <!-- <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.Type_of_truck')}}</span>
                        <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->type_of_truck ?? ""}}</h3>
                    </div> -->
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
                        <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->truck_system_number ?? ""}}</h3>
                    </div>
                    <div class="company_offreDetails">
                        <span class="company_offreDetails_label">{{trans('messages.admin_common_Status')}}</span>
                        <h3 class="offreDetails_cont">: {{ trans("messages.$shipmentOffer->status") }}</h3>
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
                        @elseif($shipmentOffer->status == 'approved_from_company')
                            <a href="javascript:void(0)" class="transportRequestBtn"> <svg width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M18.5849 6.45695C18.501 6.37235 18.4012 6.30519 18.2912 6.25936C18.1812 6.21354 18.0632 6.18994 17.944 6.18994C17.8249 6.18994 17.7069 6.21354 17.5969 6.25936C17.4869 6.30519 17.3871 6.37235 17.3031 6.45695L10.5782 13.1909L7.75282 10.3565C7.66569 10.2723 7.56284 10.2062 7.45013 10.1618C7.33743 10.1173 7.21708 10.0956 7.09596 10.0977C6.97484 10.0997 6.85532 10.1257 6.74422 10.174C6.63312 10.2223 6.53262 10.292 6.44845 10.3791C6.36429 10.4662 6.29811 10.5691 6.25369 10.6818C6.20928 10.7945 6.1875 10.9148 6.18959 11.0359C6.19169 11.1571 6.21762 11.2766 6.26591 11.3877C6.31419 11.4988 6.38389 11.5993 6.47102 11.6834L9.9373 15.1497C10.0212 15.2343 10.121 15.3015 10.231 15.3473C10.341 15.3931 10.459 15.4167 10.5782 15.4167C10.6974 15.4167 10.8153 15.3931 10.9253 15.3473C11.0353 15.3015 11.1352 15.2343 11.2191 15.1497L18.5849 7.78389C18.6766 7.69936 18.7497 7.59677 18.7997 7.48258C18.8497 7.36839 18.8755 7.24508 18.8755 7.12042C18.8755 6.99576 18.8497 6.87245 18.7997 6.75826C18.7497 6.64407 18.6766 6.54148 18.5849 6.45695Z"
                                        fill="white" />
                                    <circle cx="11.6058" cy="11.6058" r="10.6058" stroke="white" stroke-width="2" />
                                </svg> {{trans('messages.approved_offer')}}
                            </a>
                        @elseif($shipmentOffer->status == 'rejected')
                            <a href="javascript:void(0)" class="transportRequestBtn" style="background-color: red;">
                                {{trans('messages.offer_rejected')}}
                            </a>
                        @else
                            

                            <a href="{{route('business-shipment-offer-approved',[$shipmentOffer->system_id])}}" class="transportRequestBtn"> <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.5849 6.45695C18.501 6.37235 18.4012 6.30519 18.2912 6.25936C18.1812 6.21354 18.0632 6.18994 17.944 6.18994C17.8249 6.18994 17.7069 6.21354 17.5969 6.25936C17.4869 6.30519 17.3871 6.37235 17.3031 6.45695L10.5782 13.1909L7.75282 10.3565C7.66569 10.2723 7.56284 10.2062 7.45013 10.1618C7.33743 10.1173 7.21708 10.0956 7.09596 10.0977C6.97484 10.0997 6.85532 10.1257 6.74422 10.174C6.63312 10.2223 6.53262 10.292 6.44845 10.3791C6.36429 10.4662 6.29811 10.5691 6.25369 10.6818C6.20928 10.7945 6.1875 10.9148 6.18959 11.0359C6.19169 11.1571 6.21762 11.2766 6.26591 11.3877C6.31419 11.4988 6.38389 11.5993 6.47102 11.6834L9.9373 15.1497C10.0212 15.2343 10.121 15.3015 10.231 15.3473C10.341 15.3931 10.459 15.4167 10.5782 15.4167C10.6974 15.4167 10.8153 15.3931 10.9253 15.3473C11.0353 15.3015 11.1352 15.2343 11.2191 15.1497L18.5849 7.78389C18.6766 7.69936 18.7497 7.59677 18.7997 7.48258C18.8497 7.36839 18.8755 7.24508 18.8755 7.12042C18.8755 6.99576 18.8497 6.87245 18.7997 6.75826C18.7497 6.64407 18.6766 6.54148 18.5849 6.45695Z" fill="white" />
                                    <circle cx="11.6058" cy="11.6058" r="10.6058" stroke="white" stroke-width="2" />
                                </svg> {{trans('messages.approve_offer')}}
                            </a>                            
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboardRight_block_wrapper admin_right_page company_details_block">
            <div class="dashboard_notofication_main  List_objectives_table">
                <div class="transportation_request_block">
                    <div class="transpor_request_box">
                        <h3 class="transpor_request_box_title">{{ trans('messages.your_transportation_request') }}</h3>
                        <!-- <a href="#!" class="transpor_request_message">
                            <svg width="19" height="20" viewBox="0 0 21 22" fill="none"
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
                        </a> -->
                    </div>
                    <div class="coordinator_name_box">
                        <div class="company_name_logo_box">
                            <img src="{{$shipmentOffer->companyUser->userCompanyInformation->company_logo}}" alt="">
                        </div>
                        <div class="coordinator_name_title">{{$shipmentOffer->companyUser->userCompanyInformation->company_name}}</div>
                    </div>
                    {{-- <p class="company_details_desc">{{$shipmentOffer->companyUser->userCompanyInformation->company_description}}</p> --}}
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

                <h3 class="transpor_request_box_title">{{ trans('messages.contact_person_details') }}</h3>
                    <div class="transport_coordinator_block">
                        <div class="coordinator_name_box">
                            <img src="{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_picture}}" alt="">
                            <h3 class="coordinator_name_title">{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_name}}</h3>
                        </div>
                        <div class="coordinator_phoneMess_box">
                        @if($shipmentOffer->show_chat_icon == 1)
                            <a href="javascript:void(0)" onclick="singelChatModal('{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_picture}}','{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_name}}','{{ $shipmentOffer->companyUser->userCompanyInformation->user_id }}')" class="table_message_btn">
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
                            
                            @endif
                            
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
                        </div>

                    </div>
        <hr>

        @php
        $driverName = false ;
        $driverNumber = false ;
        if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->truckDriver){
            $driverName = $shipment->shipmentDriverScheduleDetails->truckDriver->name;
            $driverNumber = $shipment->shipmentDriverScheduleDetails->truckDriver->phone_number;
        }else if($shipmentOffer->TruckDetail && $shipmentOffer->TruckDetail->truckDriver){
            $driverName = $shipmentOffer->TruckDetail->truckDriver->name;
            $driverNumber = $shipmentOffer->TruckDetail->truckDriver->phone_number;
        }
        @endphp
        @if($driverName)
                <div class="transportation_request_block">
                    <div class="transport_coordinator_block">
                        <div class="transportation_request_block">
                        <h3 class="transpor_request_box_title">{{ trans('messages.driver_details') }}</h3>
                            <div class="company_offreDetails">
                                <h3 class="offreDetails_cont">{{trans('messages.name')}}</h3>
                                <span class="company_offreDetails_label"> : {{ $driverName }}</span>
                            </div>
                           {{-- <div class="company_offreDetails">
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
                            <a href="tel:{{$driverNumber}}" class="coordinator_phone_box">
                                <svg width="26" height="28" viewBox="0 0 26 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.59186 5.68772C5.71851 5.68772 5.17373 6.49357 5.42322 7.25497C6.3178 9.98497 7.98487 14.0918 10.4709 16.6518C12.957 19.2118 16.9453 20.9283 19.5966 21.8495C20.3359 22.1064 21.1186 21.5455 21.1186 20.6461V17.8741C21.1186 17.7749 21.0664 17.6835 20.9822 17.6353L18.6372 16.2928C18.5607 16.249 18.468 16.2469 18.3897 16.2873L15.8889 17.5748C15.7326 17.6553 15.5548 17.6805 15.3831 17.6466L15.533 16.8417C15.3831 17.6466 15.3833 17.6467 15.3831 17.6466L15.3808 17.6462L15.3778 17.6456L15.3698 17.6439L15.3458 17.6388C15.3263 17.6345 15.2999 17.6285 15.2672 17.6205C15.2017 17.6046 15.1107 17.5808 14.9984 17.5475C14.7743 17.4809 14.4638 17.3755 14.1029 17.2171C13.3842 16.9018 12.4461 16.3676 11.5965 15.4928C10.747 14.618 10.2268 13.6506 9.91914 12.9096C9.76462 12.5374 9.66152 12.217 9.59625 11.9859C9.56357 11.8701 9.54022 11.7762 9.52457 11.7087C9.51675 11.675 9.51083 11.6478 9.50663 11.6277L9.50157 11.603L9.49995 11.5948L9.49937 11.5917L9.49913 11.5905C9.49908 11.5902 9.49892 11.5894 10.2802 11.4329L9.49913 11.5905C9.46561 11.413 9.4898 11.2281 9.5683 11.0664L10.8188 8.49091C10.858 8.41033 10.856 8.31495 10.8135 8.23609L9.51547 5.82836C9.51546 5.82834 9.51547 5.82837 9.51547 5.82836C9.46864 5.74155 9.3798 5.68772 9.28349 5.68772H6.59186ZM11.1244 11.5269C11.1245 11.5274 11.1247 11.5278 11.1248 11.5283C11.1749 11.7056 11.2572 11.9626 11.3828 12.2652C11.6355 12.8739 12.0549 13.6467 12.7221 14.3337C12.7221 14.3337 12.7221 14.3337 12.7221 14.3337L12.1593 14.9132M12.7221 14.3337C13.3892 15.0206 14.138 15.4507 14.727 15.7092C15.0199 15.8377 15.2684 15.9216 15.4399 15.9726C15.4402 15.9727 15.4395 15.9725 15.4399 15.9726L17.6777 14.8212C17.6777 14.8212 17.6777 14.8212 17.6777 14.8212C18.2262 14.5388 18.8748 14.5534 19.4107 14.8602L21.7557 16.2027C22.345 16.5401 22.7104 17.1798 22.7104 17.8741V20.6461C22.7104 22.5701 20.9462 24.0484 19.0877 23.4026C16.4031 22.4699 12.1089 20.6566 9.34533 17.8108C6.58173 14.9651 4.82075 10.5433 3.91493 7.77902C3.28783 5.86523 4.72338 4.04858 6.59186 4.04858H9.28349C9.95815 4.04858 10.5797 4.42535 10.9072 5.03269L12.2053 7.44052C12.5027 7.99217 12.5167 8.65948 12.2426 9.22394C12.2426 9.22396 12.2426 9.22392 12.2426 9.22394L11.1244 11.5269"
                                        fill="currentcolor" />
                                </svg>
                            </a>
                        </div>--}}

                    </div>
                </div>
        </div>
        @endif
        
    </div>
    </div>
    @stop
    @section('scriptCode')
        @include('frontend.chat.commonChatModal')
    @stop