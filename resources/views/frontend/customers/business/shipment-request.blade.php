@extends('frontend.layouts.customers')
@section('extraCssLinks')
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dropzone.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dashboard-dropzone.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dashboard.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/dashboard-responsive.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/drop-down.css')}}">
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


<div class="col-lg-9 col-xl-9 col-xxl-9 col-sm-12">
    <div class="dashboardRight_block_wrapper admin_right_page">
        <div class="pageTopTitle">
            <h2 class="RightBlockTitle">{{trans("messages.create_a_new_request")}}</h2>
        </div>
        <form class="profile-form" method="post" id="business-shipment-request-form" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <div class="row ">
                <div class="col-md-6 left_border">
                    <div class="row">
                        <div class="col-md-12 col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="form-label"> {{trans('messages.choose_shipment')}}</label>
                                <select id="truck_types" class="form-select 1-form-fields" data-is-required="1" aria-label="Default select example" name="truck_type">
                                    <option selected value="" data-multiple-stop-allow="0">{{trans("messages.Select")}}</option>
                                        @foreach($truckTypeQuestionnaire as $row)
                                            <option value="{{$row->id}}" data-multiple-stop-allow="{{$row->multiple_stop_allow}}">{{$row->truckTypeDiscription->name}}</option>
                                        @endforeach
                                </select>
                                <input type="hidden" name="multiple_stop_allow_input" value="0">
                                <small class="text-danger text-danger-text error-truck_type"></small>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12"></div>
                        
                        @foreach($truckTypeQuestionnaire as $row)
                            <div class=" truckTypeQuestionnaireClass" id="truckTypeQuestionnaire{{$row->id}}" style="display: none;">
                                @foreach($row->TruckTypeQuestionsList as $questionsRow)
                                    <div class="col-md-12 col-sm-12 for-question question-tag-{{$questionsRow->id}}" data-question-id="{{$questionsRow->id}}" @if($questionsRow->id == '23') style="display:none;" @endif>
                                        <div class="form-group">
                                            <label for="ans{{$row->id}}" class="form-label">{{$questionsRow->TruckTypeQuestionDiscription->name}}</label>
                                            <p style="color: #000;font-size: 14px;margin: 0px;">{{$questionsRow->TruckTypeQuestionDiscription->question_descriptions}}</p>
                                            @if($questionsRow->input_type == "number")
                                                <input type="number" class="form-control 1-form-fields remSpinnersIcon" data-is-required="0" id="ans-{{$row->id}}-{{$questionsRow->id}}" value="" aria-describedby="ans{{$row->id}}" name="ans[{{$row->id}}][{{$questionsRow->id}}]">
                                                <small class="text-danger text-danger-text error-ans{{$row->id}}{{$questionsRow->id}}"></small>
                                            @elseif($questionsRow->input_type == "choice")
                                                @php
                                                    $input_description = explode(",",$questionsRow->TruckTypeQuestionDiscription->input_description);
                                                @endphp
                                                <div class="customRadio customRadioInline">
                                                    @foreach($input_description as $key => $option)
                                                    <span class="radioLabelrow">
                                                        <input type="checkbox" id="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}" name="ans[{{$row->id}}][{{$questionsRow->id}}][]" class="1-form-fields" data-is-required="0" value="{{$key}}">
                                                        <label for="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}">{{$option}}</label>
                                                    </span>
                                                    @endforeach
                                                    <small class="text-danger text-danger-text error-ans{{$row->id}}{{$questionsRow->id}}"></small>
                                                </div>
                                            @elseif($questionsRow->input_type == "text")
                                                    <input type="text" class="form-control 1-form-fields" data-is-required="0" id="ans{{$row->id}}" value="" aria-describedby="ans{{$row->id}}" name="ans[{{$row->id}}][{{$questionsRow->id}}]">
                                                    <small class="text-danger text-danger-text error-ans{{$row->id}}{{$questionsRow->id}}"></small>
                                            @elseif($questionsRow->input_type == 'radio')
                                                @php
                                                    $input_description = explode(",",$questionsRow->TruckTypeQuestionDiscription->input_description);
                                                @endphp
                                                <div class="customRadio customRadioInline">
                                                    @foreach($input_description as $key => $option)
                                                    <span class="radioLabelrow">
                                                        <input type="radio" id="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}" name="ans[{{$row->id}}][{{$questionsRow->id}}][]" {{ $key == 0 ? 'checked' : ''}}  value="{{$key}}">
                                                        <label for="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}">{{$option}}</label>
                                                    </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="date_of_transport" class="form-label">{{trans("messages.date_of_transport")}}</label>
                                <div class="withIconInput">
                                    <svg class="useIcon calendar-icon" width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 2.45265H15V1.36939C15 1.08209 14.8946 0.806563 14.7071 0.603412C14.5196 0.400262 14.2652 0.286133 14 0.286133C13.7348 0.286133 13.4804 0.400262 13.2929 0.603412C13.1054 0.806563 13 1.08209 13 1.36939V2.45265H7V1.36939C7 1.08209 6.89464 0.806563 6.70711 0.603412C6.51957 0.400262 6.26522 0.286133 6 0.286133C5.73478 0.286133 5.48043 0.400262 5.29289 0.603412C5.10536 0.806563 5 1.08209 5 1.36939V2.45265H3C2.20435 2.45265 1.44129 2.79504 0.87868 3.40449C0.316071 4.01394 0 4.84054 0 5.70243V18.7015C0 19.5634 0.316071 20.39 0.87868 20.9995C1.44129 21.6089 2.20435 21.9513 3 21.9513H17C17.7956 21.9513 18.5587 21.6089 19.1213 20.9995C19.6839 20.39 20 19.5634 20 18.7015V5.70243C20 4.84054 19.6839 4.01394 19.1213 3.40449C18.5587 2.79504 17.7956 2.45265 17 2.45265ZM18 18.7015C18 18.9888 17.8946 19.2644 17.7071 19.4675C17.5196 19.6707 17.2652 19.7848 17 19.7848H3C2.73478 19.7848 2.48043 19.6707 2.29289 19.4675C2.10536 19.2644 2 18.9888 2 18.7015V11.1187H18V18.7015ZM18 8.95221H2V5.70243C2 5.41513 2.10536 5.1396 2.29289 4.93645C2.48043 4.7333 2.73478 4.61917 3 4.61917H5V5.70243C5 5.98973 5.10536 6.26526 5.29289 6.46841C5.48043 6.67156 5.73478 6.78569 6 6.78569C6.26522 6.78569 6.51957 6.67156 6.70711 6.46841C6.89464 6.26526 7 5.98973 7 5.70243V4.61917H13V5.70243C13 5.98973 13.1054 6.26526 13.2929 6.46841C13.4804 6.67156 13.7348 6.78569 14 6.78569C14.2652 6.78569 14.5196 6.67156 14.7071 6.46841C14.8946 6.26526 15 5.98973 15 5.70243V4.61917H17C17.2652 4.61917 17.5196 4.7333 17.7071 4.93645C17.8946 5.1396 18 5.41513 18 5.70243V8.95221Z" fill="#1535B9"/>
                                        </svg>
                                    <input type="text" class="form-control inputStartPadding 1-form-fields" data-is-required="1" id="date_of_transport" value="" aria-describedby="date_of_transport" name="date_of_transport" readonly>
                                    <div class="qty-container">
                                        <button class="qty-btn-plus btn-light" type="button"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.874707 7.15002H5.59971V11.875C5.59971 12.054 5.67082 12.2257 5.79741 12.3523C5.924 12.4789 6.09569 12.55 6.27471 12.55C6.45373 12.55 6.62542 12.4789 6.752 12.3523C6.87859 12.2257 6.94971 12.054 6.94971 11.875V7.15002H11.6747C11.8537 7.15002 12.0254 7.07891 12.152 6.95232C12.2786 6.82573 12.3497 6.65405 12.3497 6.47502C12.3497 6.296 12.2786 6.12431 12.152 5.99773C12.0254 5.87114 11.8537 5.80002 11.6747 5.80002H6.94971V1.07502C6.94971 0.896003 6.87859 0.724314 6.752 0.597727C6.62542 0.47114 6.45373 0.400024 6.27471 0.400024C6.09569 0.400024 5.924 0.47114 5.79741 0.597727C5.67082 0.724314 5.59971 0.896003 5.59971 1.07502V5.80002H0.874707C0.695686 5.80002 0.523997 5.87114 0.39741 5.99773C0.270823 6.12431 0.199707 6.296 0.199707 6.47502C0.199707 6.65405 0.270823 6.82573 0.39741 6.95232C0.523997 7.07891 0.695686 7.15002 0.874707 7.15002Z" fill="white"/>
                                            </svg>
                                            </button>
                                            <input type="number" name="qty" value="0" class="input-qty"/>                                                        
                                        <button class="qty-btn-minus btn-light" type="button"><svg width="12" height="2" viewBox="0 0 12 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.9004 1.70035H1.10039C0.914739 1.70035 0.736692 1.6266 0.605416 1.49533C0.474141 1.36405 0.400391 1.18601 0.400391 1.00035C0.400391 0.814703 0.474141 0.636655 0.605416 0.505379C0.736692 0.374104 0.914739 0.300354 1.10039 0.300354H10.9004C11.086 0.300354 11.2641 0.374104 11.3954 0.505379C11.5266 0.636655 11.6004 0.814703 11.6004 1.00035C11.6004 1.18601 11.5266 1.36405 11.3954 1.49533C11.2641 1.6266 11.086 1.70035 10.9004 1.70035Z" fill="white"/>
                                            </svg>
                                            </button>
                                    </div>
                                </div>
                                <small class="text-danger text-danger-text error-date_of_transport"></small>
                            </div>
                        </div>
        
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="choose_time" class="form-label">{{trans("messages.choose_time")}}</label>
                                <select class="form-select 1-form-fields" data-is-required="0" aria-label="Default select example" name="choose_time">
                                    <option selected value="">{{trans("messages.Select")}}</option>
                                    @foreach($shipmentTime as $row)
                                        <option value="{{$row->id}}">{{$row->lookupDiscription->code}}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger text-danger-text error-choose_time"></small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="company_city" class="form-label">{{trans('messages.pickup_city')}}</label>
                                <div class="withIconInput">
                                    <svg class="useIcon" width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z" fill="#1535B9"/>
                                    </svg>
                                    <input type="text" name="company_city" class="form-control 1-form-fields" data-is-required="1"  id="company_city" placeholder="{{trans('messages.pickup_city')}}">
                                    <input type="hidden" name="company_zip_code" id="company_zip_code">
                                    <input type="hidden" name="company_latitude" class=" 1-form-fields" data-type="gLocation" data-target-name="company_city" id="company_latitude">
                                    <input type="hidden" name="company_longitude" class=" 1-form-fields" data-type="gLocation" data-target-name="company_city" id="company_longitude">
                                </div>
                                <small class="text-danger text-danger-text error-company_city"></small>
                            </div>
                            <div class="form-group">
                                <label for="company_address" class="form-label">{{trans('messages.pickup_address')}}</label>
                                <div class="withIconInput">
                                    <!-- <svg class="useIcon" width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z" fill="#1535B9"/>
                                    </svg> -->
                                    <input type="text" class="form-control 1-form-fields" id="company_address" data-is-required="1" value="" aria-describedby="company_address" name="company_address" placeholder="{{trans('messages.pickup_address')}}">
                                    <!-- <input type="hidden" name="company_zip_code" id="company_zip_code">
                                    <input type="hidden" name="company_latitude" id="company_latitude">
                                    <input type="hidden" name="company_longitude" id="company_longitude"> -->
        
                                </div>
                                <small class="text-danger text-danger-text error-company_address"></small>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="" class="form-label">{{trans('messages.description')}}</label>
                                <textarea class="form-control h72 1-form-fields" data-is-required="0" id="" rows="3" placeholder="{{trans('messages.description')}}" name="description"></textarea>
                                <small class="text-danger text-danger-text error-description"></small>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                
                {{-- <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="shipment_servise" class="form-label">{{trans('messages.choose_shipment')}}</label>
                        <select id="shipment_servise" class="form-select 1-form-fields" data-is-required="1" aria-label="Default select example" name="shipment">
                            <option selected value="">{{trans("messages.Select")}}</option>
                            @foreach($shipment as $row)
                                <option value="{{$row->id}}">{{$row->lookupDiscription->code}}</option>
                            @endforeach
                        </select>
                        <small class="text-danger text-danger-text error-shipment"></small>
                    </div>
                </div>  --}}

    
                
                
                <div class="col-md-12">
                    <!-- <label for="exampleInputEmail2" class="form-label">Please upload pdf, doc. or images</label> -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="dropzone" id="drop-areaa">
                                    <div class="GalleryImagesAppends ">
                                        <div class="gallery_item itemsappends d-inline-flex add_img_upload">
                                            <label class="text-center dz-message needsclick" for="ImageUploads" style="color: #fff;">
                                                <div class="dz-message needsclick">
                                                    <span class="dropzoneText">
                                                        <span class="svg-files">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="32" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                            </svg>

                                                        </span>
                                                    </span>
                                                </div> <span style="color:#0f30b6;">{{trans("messages.upload_here")}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <input style="visibility: hidden;height:0px; position: absolute;" type="file" accept=".png, .jpg, .jpeg, .pdf, .doc" onchange="GalleryImages(this.files)" name="" multiple id="ImageUploads">
                                    <progress hidden id="progress-bar" max=100 value=0></progress>
                                </div>
                                <div class="file-message" >
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_11_12822)">
                                            <path d="M27.9109 0H4.08906C1.83431 0 0 1.83438 0 4.08913V27.9109C0 30.1656 1.83431 32 4.08906 32H27.9109C30.1657 32 32 30.1656 32 27.9109V4.08913C32 1.83438 30.1657 0 27.9109 0ZM30.1151 27.9109C30.1151 29.1264 29.1263 30.1151 27.9109 30.1151H4.08906C2.87363 30.1151 1.88481 29.1263 1.88481 27.9109V24.473L8.08825 19.1948C8.31463 19.0022 8.64487 19.0004 8.87337 19.1901L12.7592 22.4169C13.1339 22.7279 13.6839 22.7024 14.0282 22.3577L23.2613 13.1106C23.4282 12.9434 23.6227 12.9272 23.7243 12.9324C23.8256 12.9376 24.0177 12.9737 24.1667 13.1571L30.1152 20.4815L30.1151 27.9109ZM30.1151 17.4916L25.6297 11.9687C25.1849 11.4209 24.5256 11.0861 23.8209 11.0499C23.1166 11.0143 22.4261 11.2794 21.9275 11.7788L13.302 20.4176L10.0776 17.7401C9.14269 16.9638 7.79244 16.9718 6.86681 17.7594L1.88481 21.9982V4.08913C1.88481 2.87369 2.87363 1.88488 4.08906 1.88488H27.9109C29.1264 1.88488 30.1151 2.87369 30.1151 4.08913V17.4916Z" fill="#C7E9FF" />
                                            <path d="M10.0739 3.93726C7.56792 3.93726 5.5293 5.97601 5.5293 8.48182C5.5293 10.9877 7.56798 13.0264 10.0739 13.0264C12.5797 13.0264 14.6184 10.9877 14.6184 8.48182C14.6184 5.97594 12.5798 3.93726 10.0739 3.93726ZM10.0739 11.1416C8.60723 11.1416 7.41411 9.94838 7.41411 8.48182C7.41411 7.01519 8.60723 5.82207 10.0739 5.82207C11.5405 5.82207 12.7336 7.01526 12.7336 8.48182C12.7336 9.94838 11.5405 11.1416 10.0739 11.1416Z" fill="#C7E9FF" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_11_12822">
                                                <rect width="32" height="32" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg> <span style="color:#0f30b6;">{{trans("messages.please_upload_pdf_doc_or_images")}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
            <div class="seprator new_destination-section"></div>
            <h3 class="profile-title">{{trans('messages.new_destination')}}</h3>
            <div class="row add-destination-section-stops">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dropoff_city" class="form-label">{{trans('messages.admin_destination_city')}}</label>
                        <div class="withIconInput">
                            <svg class="useIcon" width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z" fill="#1535B9"/>
                            </svg>
                            <input type="text" name="dropoff_city" id="dropoff_city" class="form-control 1-form-fields" data-is-required="1" aria-describedby=""  placeholder="{{trans('messages.admin_destination_city')}}">
                        </div>
                        <input type="hidden" name="dropoff_zip_code" id="dropoff_zip_code">
                        <input type="hidden" name="dropoff_latitude" class=" 1-form-fields" data-type="gLocation" data-target-name="dropoff_city" id="dropoff_latitude">
                        <input type="hidden" name="dropoff_longitude" class=" 1-form-fields" data-type="gLocation" data-target-name="dropoff_city" id="dropoff_longitude">
                        <small class="text-danger text-danger-text error-dropoff_city"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="destination_address" class="form-label">{{trans('messages.destination_address')}}</label>
                        <div class="withIconInput destination">
                            <!-- <svg class="useIcon" width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.53037 0C3.27919 0 0.642578 2.46071 0.642578 5.49498C0.642578 9.61621 6.53037 15.7008 6.53037 15.7008C6.53037 15.7008 12.4182 9.61621 12.4182 5.49498C12.4182 2.46071 9.7837 0 6.53037 0ZM6.53037 7.45833C5.37001 7.45833 4.42667 6.57993 4.42667 5.49498C4.42667 4.41002 5.36786 3.53162 6.53037 3.53162C7.69289 3.53162 8.63408 4.41002 8.63408 5.49498C8.63408 6.57993 7.69289 7.45833 6.53037 7.45833Z" fill="#1535B9"/>
                            </svg> -->
                            <input type="text" class="form-control 1-form-fields" data-is-required="1" id="destination_address" placeholder="{{trans('messages.shipping_address')}}" value="" aria-describedby="destination_address" name="destination_address">
                        </div>
                        <!-- <input type="hidden" name="dropoff_zip_code" id="dropoff_zip_code">
                        <input type="hidden" name="dropoff_latitude" id="dropoff_latitude">
                        <input type="hidden" name="dropoff_longitude" id="dropoff_longitude"> -->
                        <small class="text-danger text-danger-text error-destination_address"></small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="location_feedback" class="form-label"> {{trans('messages.location_feedback')}}</label>
                        <input type="text" class="form-control 1-form-fields" data-is-required="0" id="location_feedback" aria-describedby="location_feedback" placeholder="{{trans('messages.enter_the_location_feedback')}}" name="location_feedback">
                        <small class="text-danger text-danger-text error-location_feedback"></small>
                    </div>
                </div>
                <div class="col-md-6"></div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name_of_the_receiver" class="form-label"> {{trans('messages.name_of_the_receiver')}}</label>
                        <input type="text" class="form-control 1-form-fields" data-is-required="1" id="name_of_the_receiver" aria-describedby="name_of_the_receiver" placeholder="{{trans('messages.enter_the_recipient_name')}}" name="name_of_the_receiver">
                        <small class="text-danger text-danger-text error-name_of_the_receiver"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="recipients_phone_number" class="form-label">{{trans('messages.recipients_phone_number')}}</label>
                        <div class="input-group only_left ">
                            <!-- <div class="input-group-prepend">
                                <span class="form-control input-group-text form-number">+972</span>
                            </div> -->
                            <input type="text" class="form-control 1-form-fields" data-is-required="1" id="recipients_phone_number" aria-describedby="recipients_phone_number" placeholder="054 566 1234" name="recipients_phone_number" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                        </div>
                        <small class="text-danger text-danger-text error-recipients_phone_number"></small>

                    
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail2" class="form-label">{{trans('messages.need_a_delivery_note')}}</label>
                        <div class="customRadio">
                            <span class="radioLabelrow">
                                <input type="radio" id="digital_certificate" name="delivery_note" value="digital_certificate" checked>
                                <label for="digital_certificate">{{trans('messages.digital_certificate')}}</label>
                            </span>
                            <span class="radioLabelrow">
                                <input type="radio" id="physical_certificate" name="delivery_note" value="physical_certificate">
                                <label for="physical_certificate">{{trans('messages.physical_certificate')}}</label>
                            </span>
                            <span class="radioLabelrow">
                                <input type="radio" id="delivery_note_no" name="delivery_note" value="no">
                                <label for="delivery_note_no">{{trans('messages.no')}}</label>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 certificate_number_file">
                    <div class="form-group">
                        <label for="certificate_number" class="form-label">{{trans('messages.certificate')}}</label>
                        <input type="file" class="form-control file-input 1-form-fields" data-is-required="0" id="certificate_number" aria-describedby="certificate_number" placeholder="xxxxxxxxx" name="certificate_number" accept=".jpg, .jpeg, .png">
                        <small class="text-danger text-danger-text error-certificate_number"></small>
                    </div>
                </div>
                {{-- <!-- <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="dropzone" id="drop-areaa">
                                    <div class="GalleryImagesAppends "></div>
                                    <label class="text-center dz-message needsclick" for="ImageUploads" style="color: #fff;">
                                        <div class="dz-message needsclick">
                                            <span class="dropzoneText">
                                                <span class="svg-files">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="32" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                    </svg>

                                                </span>
                                            </span>
                                        </div>
                                        <span style="color:#0f30b6;">{{trans("messages.upload_here")}}</span>
                                    </label>
                                    <input style="visibility: hidden;height:0px; position: absolute;" type="file" onchange="GalleryImages(this.files)" name="" multiple id="ImageUploads">
                                    <progress hidden id="progress-bar" max=100 value=0></progress>
                                </div>
                                <div class="file-message" style="color: #fff;">
                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_11_12822)">
                                            <path d="M27.9109 0H4.08906C1.83431 0 0 1.83438 0 4.08913V27.9109C0 30.1656 1.83431 32 4.08906 32H27.9109C30.1657 32 32 30.1656 32 27.9109V4.08913C32 1.83438 30.1657 0 27.9109 0ZM30.1151 27.9109C30.1151 29.1264 29.1263 30.1151 27.9109 30.1151H4.08906C2.87363 30.1151 1.88481 29.1263 1.88481 27.9109V24.473L8.08825 19.1948C8.31463 19.0022 8.64487 19.0004 8.87337 19.1901L12.7592 22.4169C13.1339 22.7279 13.6839 22.7024 14.0282 22.3577L23.2613 13.1106C23.4282 12.9434 23.6227 12.9272 23.7243 12.9324C23.8256 12.9376 24.0177 12.9737 24.1667 13.1571L30.1152 20.4815L30.1151 27.9109ZM30.1151 17.4916L25.6297 11.9687C25.1849 11.4209 24.5256 11.0861 23.8209 11.0499C23.1166 11.0143 22.4261 11.2794 21.9275 11.7788L13.302 20.4176L10.0776 17.7401C9.14269 16.9638 7.79244 16.9718 6.86681 17.7594L1.88481 21.9982V4.08913C1.88481 2.87369 2.87363 1.88488 4.08906 1.88488H27.9109C29.1264 1.88488 30.1151 2.87369 30.1151 4.08913V17.4916Z" fill="#C7E9FF" />
                                            <path d="M10.0739 3.93726C7.56792 3.93726 5.5293 5.97601 5.5293 8.48182C5.5293 10.9877 7.56798 13.0264 10.0739 13.0264C12.5797 13.0264 14.6184 10.9877 14.6184 8.48182C14.6184 5.97594 12.5798 3.93726 10.0739 3.93726ZM10.0739 11.1416C8.60723 11.1416 7.41411 9.94838 7.41411 8.48182C7.41411 7.01519 8.60723 5.82207 10.0739 5.82207C11.5405 5.82207 12.7336 7.01526 12.7336 8.48182C12.7336 9.94838 11.5405 11.1416 10.0739 11.1416Z" fill="#C7E9FF" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_11_12822">
                                                <rect width="32" height="32" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <span style="color:#0f30b6;"> {{trans("messages.please_upload_pdf_doc_or_images")}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  --> --}}
                <div class="col-md-12">
                <small class="text-danger text-danger-text error-one-destination-required"></small>
                </div>
                <div class="col-md-12 add-destination-section">
                    <a href="javascript:void(0)" class="save-updateBtn add-a-destination" style="margin: 20px;">
                      {{trans('messages.add_a_destination')}}
                    </a>
                </div>
            </div>
            <div class="seprator add-destination-section"></div>

            <h3 class="profile-title add-destination-section">{{trans('messages.admin_list_of_destination')}}</h3>
            <div class="dashboard_notofication_main dahboard_whiteSpace add-destination-section">
                <div class="table-responsive dashboard_notofication">
                    <table class="table destination-table">
                        <thead>
                            <tr>
                                <th scope="col">{{trans('messages.name_of_the_receiver')}}</th>
                                <th scope="col">{{trans('messages.destination_address')}}</th>
                                <th scope="col">{{trans('messages.certificate')}}</th>
                                <!-- <th scope="col">{{trans('messages.the_photo_of_the_certificate')}}</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- <a href="javascript:void(0)" class="save-updateBtn mt-4">
                Send a request
            </a> -->
            <div class="for-remove-fixed-submit_request mt-4"></div>
            <button type="button" class="save-updateBtn shipment-request-submit">{{trans('messages.send_a_request')}}</button>
        </form>
    </div>
</div>
<style>
    .file-message {
        position: relative;
    }

    .file-preview {
        display: inline-block;
        margin-right: 10px;
    }

    .file-preview img {
        height: 20px;
        margin-bottom: 5px;
    }

    .remove-preview-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: none;
        border: none;
        color: #0f30b6;
        cursor: pointer;
    }

    .remove-preview-btn:hover {
        color: red;
    }
    .add-destination-section{
        display:none
    }

</style>

@stop

@section('scriptCode')
<script>
    var previewsContainer = document.getElementById('previewsContainer');

    function handleFileSelect(event) {
        event.stopPropagation();
        event.preventDefault();

        var files = event.target.files || event.dataTransfer.files;

        // Remove existing previews before adding new ones
        clearPreviews();

        // Process each file
        for (var i = 0, file; file = files[i]; i++) {
            // Perform necessary actions with the file (e.g., upload, display preview, etc.)
            displayFilePreview(file);
        }
    }

    function handleDragOver(event) {
        event.stopPropagation();
        event.preventDefault();
        event.dataTransfer.dropEffect = 'copy'; // Set the drop effect to copy
    }

    function displayFilePreview(file) {
        var reader = new FileReader();

        reader.onload = function(event) {
            var filePreview = document.createElement('div');
            filePreview.className = 'file-preview';

            // Check the file type and create appropriate preview elements
            if (file.type.startsWith('image/')) {
                var imagePreview = document.createElement('img');
                imagePreview.src = event.target.result;
                filePreview.appendChild(imagePreview);
            } else if (file.type.startsWith('video/')) {
                var videoPreview = document.createElement('video');
                videoPreview.src = event.target.result;
                videoPreview.controls = true;
                filePreview.appendChild(videoPreview);
            } else if (file.type === 'application/pdf') {
                var pdfPreview = document.createElement('embed');
                pdfPreview.src = event.target.result;
                pdfPreview.type = 'application/pdf';
                filePreview.appendChild(pdfPreview);
            }

            // Append the file preview to the previews container
            previewsContainer.appendChild(filePreview);
        };

        // Read the file as data URL
        reader.readAsDataURL(file);
    }

    function validatePhoneNumber(input) {
            input.value = input.value.replace(/^0+|[^0-9]/g, '').substring(0, 10);
        }

    function clearPreviews() {
        // Remove all file previews
        while (previewsContainer.firstChild) {
            previewsContainer.removeChild(previewsContainer.firstChild);
        }
    }

    // Add event listeners for file selection and drag and drop
    var dropArea = document.getElementById('drop-areaa');
    // dropArea.addEventListener('dragover', handleDragOver, false);
    // dropArea.addEventListener('drop', handleFileSelect, false);
    document.getElementById('ImageUploads').addEventListener('change', handleFileSelect, false);
    $(document).ready(function(){
        $("body").on("change input", ".for-question input", function() {
            var changedQuestionId = $(this).closest(".for-question").attr("data-question-id");
            // var changedQuestionRowId = $(this).closest(".for-question").attr("data-question-row-id");
            if(changedQuestionId == 21){
                if($(this).val() == 0){
                    $(".question-tag-22").show().find("input").attr("data-is-required",1);
                    $(".question-tag-23").hide().find("input").attr("data-is-required",0);
                }else if($(this).val() == 1){
                    $(".question-tag-22").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-23").show().find("input").attr("data-is-required",1);
                }
            }else if(changedQuestionId == 14){
                if($(this).val() == 0){
                    $(".question-tag-15").show().find("input").attr("data-is-required",1);
                    $(".question-tag-16").hide().find("input").attr("data-is-required",0);
                }else if($(this).val() == 1){
                    $(".question-tag-15").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-16").show().find("input").attr("data-is-required",1);
                }
            }else if(changedQuestionId == 17){
                if($(this).val() == 0){
                    $(".question-tag-18").show().find("input").attr("data-is-required",1);
                }else if($(this).val() == 1){
                    $(".question-tag-18").hide().find("input").attr("data-is-required",0);
                }
            }else if(changedQuestionId == 26){
                if($('table.destination-table tbody tr').length < $("#ans-14-26").val()){
                    $(".add-a-destination").show();
                }else{
                    $(".add-a-destination").hide();
                }

            }else if(changedQuestionId == 9){
                if($(this).val() == 0){
                    $('input[type="radio"][name^="ans[4][10][]"][value="1"]').prop("checked", true);
                }else if($(this).val() == 1){
                    $('input[type="radio"][name^="ans[4][10][]"][value="0"]').prop("checked", true);
                }
            }else if(changedQuestionId == 10){
                if($(this).val() == 0){
                    $('input[type="radio"][name^="ans[4][9][]"][value="1"]').prop("checked", true);
                }else if($(this).val() == 1){
                    $('input[type="radio"][name^="ans[4][9][]"][value="0"]').prop("checked", true);
                }
            }
        });
        minDate = new Date();
        minDate.setHours(minDate.getHours() + 24)
        $( "input[name=date_of_transport]" ).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            numberOfMonths: 1,
            buttonImage: 'contact/calendar/calendar.gif',
            buttonImageOnly: true,
            minDate:minDate,
            onSelect: function(selectedDate) {
                // we can write code here 
            }
        });

        $.fn.checkValidation = function(nextStepNumber) {
            // return false;
            $("." + nextStepNumber + "-form-fields").removeClass("is-invalid");
            var flag = false;
            $("." + nextStepNumber + "-form-fields").each(function() {
                var str_name = $(this).attr("name");
                str_name = str_name.replace('[', '').replace(']', '');
                str_name = str_name.replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '');
                $(".error-" + str_name).html("");
                if ($(this).data("type") == "gLocation" && $(this).val() == "" && $("input[name="+$(this).data("target-name")+"]").val() != "") {
                    flag = true;
                    $(".error-" + $(this).data("target-name")).html("{{ trans("messages.please_select_a_location_from_the_list_given") }}");
                    $("input[name="+$(this).data("target-name")+"]").addClass("is-invalid");
                }
                if (($(this).val() == "" && $(this).attr("data-is-required") == "1") || ($(this).attr('type') == "checkbox" && $("input[name='"+$(this).attr('name')+"']:checked").length == 0 && $(this).attr("data-is-required") == "1") ) {
                    flag = true;
                    var str_name = $(this).attr("name");
                    str_name = str_name.replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '');
                    $(".error-" + str_name).html("{{ trans("messages.This field is required") }}");
                    $(this).addClass("is-invalid");
                }
            });
            return flag;
        }
        $("body").on("change", "#truck_types", function() {
            $(".truckTypeQuestionnaireClass").hide();
            $(".truckTypeQuestionnaireClass input[type=number], .truckTypeQuestionnaireClass input[type=text], .truckTypeQuestionnaireClass input[type=checkbox]").attr("data-is-required",0);

            $("#truckTypeQuestionnaire"+$(this).val()).show();
            $("#truckTypeQuestionnaire"+$(this).val()+" input[type=number], #truckTypeQuestionnaire"+$(this).val()+" input[type=text], #truckTypeQuestionnaire"+$(this).val()+" input[type=checkbox]").attr("data-is-required",1);

            $("input[name=multiple_stop_allow_input]").val($(this).find('option:selected').attr('data-multiple-stop-allow'));
            if($(this).find('option:selected').attr('data-multiple-stop-allow') == 1){
                $(".add-destination-section").show();
                $(".add-destination-section-stops input.form-control").addClass("2-form-fields").removeClass("1-form-fields");

                $("#digital_certificate").click();
                $("#delivery_note_no").parent().hide();
                if($(this).val() == 14){
                    if($('table.destination-table tbody tr').length < $("#ans-14-26").val() || $("#ans-14-26").val() == ""){
                        $(".add-a-destination").show();
                    }else{
                        $(".add-a-destination").hide();
                    }
                }
            }else{
                $(".add-destination-section").hide();
                $(".add-destination-section-stops input.form-control").addClass("1-form-fields").removeClass("2-form-fields");

                $("#digital_certificate").click();
                $("#delivery_note_no").parent().show();
                $(".add-a-destination").show();
            }
            if($(this).val() == 9){
                var changedQuestionValue = $('input[name^="ans[9][21][]"]:checked').val();
                if(changedQuestionValue == 0){
                    $(".question-tag-22").show().find("input").attr("data-is-required",1);
                    $(".question-tag-23").hide().find("input").attr("data-is-required",0);
                }else if(changedQuestionValue == 1){
                    $(".question-tag-22").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-23").show().find("input").attr("data-is-required",1);
                }
            }else if($(this).val() == 7){
                var changedQuestionValue = $('input[name^="ans[7][14][]"]:checked').val();
                if(changedQuestionValue == 0){
                    $(".question-tag-15").show().find("input").attr("data-is-required",1);
                    $(".question-tag-16").hide().find("input").attr("data-is-required",0);
                }else if(changedQuestionValue == 1){
                    $(".question-tag-15").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-16").show().find("input").attr("data-is-required",1);
                }

                var changedQuestionValue = $('input[name^="ans[7][17][]"]:checked').val();
                if(changedQuestionValue == 0){
                    $(".question-tag-18").show().find("input").attr("data-is-required",1);
                }else if(changedQuestionValue == 1){
                    $(".question-tag-18").hide().find("input").attr("data-is-required",0);
                }
            }else if($(this).val() == 4){
                var changedQuestionValue = $('input[name^="ans[4][10][]"]:checked').val();
                if(changedQuestionValue == 0){
                    $('input[type="radio"][name^="ans[4][10][]"][value="1"]').prop("checked", true);
                }else if(changedQuestionValue == 1){
                    $('input[type="radio"][name^="ans[4][10][]"][value="0"]').prop("checked", true);
                }
            }


        });

        $("body").on("change", "#certificate_number", function() {
            var file = this.files[0];
            if (file) {
                var fileSizeInMB = file.size / (1024 * 1024); // Convert size from bytes to MB
                var fileName = file.name;
                if (fileSizeInMB > 10) {
                    toastr.error("'"+fileName + "' file is too large. Please select a file smaller than 10MB.");
                    $(this).val(''); // Clear the file input
                }
            }
        })
        
        $("body").on("click", ".add-a-destination", function() {
            
            var flag = $.fn.checkValidation(2);
            if (flag) {
                $(".is-invalid:first").focus();
                return false;
            }
            var dropoff_city = $("input[name=dropoff_city]").val();
            var dropoff_zip_code = $("#dropoff_zip_code").val();
            var dropoff_latitude = $("#dropoff_latitude").val();
            var dropoff_longitude = $("#dropoff_longitude").val();
            var destination_address = $('#destination_address').val();
            var name_of_the_receiver = $('#name_of_the_receiver').val();
            var location_feedback    = $('#location_feedback').val();
            var recipients_phone_number = $('#recipients_phone_number').val();
            var request_certificate_type = $('input[name=delivery_note]:checked').val();
            var certificate_number = $('#certificate_number').val();
            
            if(request_certificate_type == "physical_certificate"){
                request_certificate_type = "physical";
            }else if(request_certificate_type == "digital_certificate"){
                request_certificate_type = "digital";
            }else if(request_certificate_type == "no"){
                request_certificate_type = "no";
            }else{
                request_certificate_type = NULL;
            }
                        
            lengthcnt = $(".destination-table tbody tr").length; 
            var tableStr = `<tr>
                <td>
                    <input type="hidden" name="lst[${lengthcnt}][dropoff_city]" value="${dropoff_city}">
                    <input type="hidden" name="lst[${lengthcnt}][dropoff_zip_code]" value="${dropoff_zip_code}">
                    <input type="hidden" name="lst[${lengthcnt}][dropoff_latitude]" value="${dropoff_latitude}">
                    <input type="hidden" name="lst[${lengthcnt}][dropoff_longitude]" value="${dropoff_longitude}">
                    <input type="hidden" name="lst[${lengthcnt}][recipients_phone_number]" value="${recipients_phone_number}">
                    <input type="hidden" name="lst[${lengthcnt}][name_of_the_receiver]" value="${name_of_the_receiver}">
                    <input type="hidden" name="lst[${lengthcnt}][location_feedback]" value="${location_feedback}">
                    <input type="hidden" name="lst[${lengthcnt}][request_certificate_type]" value="${request_certificate_type}">
                    ${name_of_the_receiver}
                </td>
                <td>
                    <div class="destination_add">
                        <input type="hidden" name="lst[${lengthcnt}][destination_address]" value="${destination_address}">
                        ${destination_address}
                    </div>
                </td>
                <td>
                    <a href="" target="_blank" id="a_tag_certificate_number_${lengthcnt}">
                        <input type="hidden" name="lst[${lengthcnt}][certificate_number]" value="" id="list_certificate_number_${lengthcnt}">`;
                        if(!(request_certificate_type == "no") && $("#certificate_number").val()!=""){
                            tableStr += `
                                <div class="upload_img_item tabel_img"><img src="{{url('public/frontend/img/file-icon.png')}}" alt="" id="img_certificate_number_${lengthcnt}"></div>
                            `;
                        }
                        else if(certificate_number == ''){
                            tableStr += `---`;
                        }
                        tableStr += `
                    </a>
                </td>`;
            //     tableStr += `<td>
            //         <div class="upload_img_item tabel_img">`;
            //     $('.input_images_gallery_items').each(function () {
            //         tableStr += `
            //                 <input type="hidden" name="lst[${lengthcnt}][destinationImg][]" value="${$(this).val()}">
            //                 <img src="${url+'/public/uploads/gallery-image/'+$(this).val()}" alt="">
            //                 <span class="zoom_icon">
            //                     <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            //                         <g clip-path="url(#clip0_884_49325)">
            //                         <path d="M15.9994 13.5778H15.0499L14.7142 13.2536C17.526 9.97748 17.1512 5.0414 13.8728 2.22735C10.5966 -0.584399 5.66054 -0.207353 2.84649 3.06881C0.0347419 6.34497 0.411788 11.2811 3.68795 14.0951C6.61695 16.6103 10.9438 16.6103 13.8728 14.0951L14.197 14.4308V15.3803L20.2021 21.3739L21.9908 19.5853L15.9971 13.5801L15.9994 13.5778ZM8.79186 13.5778C5.80768 13.5778 3.38677 11.1569 3.38677 8.17272C3.38677 5.18854 5.80768 2.76763 8.79186 2.76763C11.776 2.76763 14.197 5.18854 14.197 8.17272C14.2016 11.1546 11.7875 13.5732 8.80566 13.5778C8.80106 13.5778 8.79646 13.5778 8.79186 13.5778ZM11.7944 8.77278H9.39192V11.1753H8.19181V8.77278H5.78929V7.57267H8.19181V5.17015H9.39192V7.57267H11.7944V8.77278Z" fill="#1535B9"/>
            //                         </g>
            //                         <defs>
            //                         <clipPath id="clip0_884_49325">
            //                         <rect width="21.0318" height="21.0318" fill="white" transform="translate(0.960938 0.341797)"/>
            //                         </clipPath>
            //                         </defs>
            //                         </svg>
            //                 </span>`;
            //     });
            // $(".GalleryImagesAppends").html("");
            // tableStr +=         `</div>
            //     </td>`;
            tableStr += `</tr>`;

            $(".destination-table tbody").append(tableStr);




            var form_data = new FormData();
            var totalfiles = document.getElementById('certificate_number').files.length;
            for (var index = 0; index < totalfiles; index++) {
                form_data.append("images[]", document.getElementById('certificate_number').files[index]);                  
            }
            form_data.append("_token",_token);
            form_data.append("path",'GALLERY_MEDIA_IMAGE_ROOT_PATH');
            $.ajax({
                type:'POST',
                url: url+"/gallery-images-uploads",
                data: form_data,
                contentType: false,
                processData: false,
                success: function(data) {
                    $.each(data,function(key,val) {
                        $("#list_certificate_number_"+lengthcnt).val(val.image); 
                        $("#a_tag_certificate_number_"+lengthcnt).attr("href",url+'/public/uploads/gallery-image/'+val.image)
                    });
                    $('#destination_address, #name_of_the_receiver, #recipients_phone_number, #certificate_number, input[name=dropoff_city], #location_feedback').val("");
                    $('input[value=digital_certificate]').prop('checked', true)
                        .closest(".add-destination-section-stops")
                        .find(".certificate_number_file")
                        .show()
                        .find("input")
                        .attr("data-is-required",'0');

                }
            });

            minDate = new Date();
            minDate.setHours(minDate.getHours() + 24)
            $( "input[name=date_of_transport]" ).datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                numberOfMonths: 1,
                buttonImage: 'contact/calendar/calendar.gif',
                buttonImageOnly: true,
                minDate:minDate,
                onSelect: function(selectedDate) {
                    // we can write code here 
                }
            });
            if($("#truck_types").val() == 14){
                if($('table.destination-table tbody tr').length >= $("#ans-14-26").val()){
                    $(".add-a-destination").hide();
                    // toastr.error("{{trans('messages.List Of Destination equivalent to distribution points')}}");
                    return false;
                }
            }
        });
        $("body").on("click", ".shipment-request-submit", function() {
            var flag = $.fn.checkValidation(1);

            $(".error-one-destination-required").html("");
            if($('table.destination-table tbody tr').length==0 && $('select[name="truck_type"]').find('option:selected').attr('data-multiple-stop-allow') == 1){
                $(".error-one-destination-required").html("{{ trans("messages.at_least_one_destination_is_required") }}");
                flag = true;
            }else if($("#truck_types").val() == 14 && $('table.destination-table tbody tr').length != $("#ans-14-26").val()){
                toastr.error("{{trans('messages.list_of_destination_equivalent_to_distribution_points')}}");
                flag = true;
            }

            if (flag) {
                // $(window).scrollTop(0);
                if($(".is-invalid").length){
                    $(".is-invalid:first").focus();
                }else if($(".error-one-destination-required").html() != "" ){
                    var headerHeight = $("#header").outerHeight();
                    var targetSection = $(".new_destination-section");
                    $('html, body').animate({
                        scrollTop: targetSection.offset().top - headerHeight
                    }, 500);


                }
                return false;
            }
            $("#business-shipment-request-form").submit();
            // $("#verificationModal").modal("show");
        });

        $("body").on("click", ".business-shipment-request-confirm-button", function() {
            $("#business-shipment-request-form").submit();
        });
        $("body").on("change", "input[name='delivery_note']", function() {
            if($(this).val()=='no'){
                $(this).closest(".add-destination-section-stops")
                .find(".certificate_number_file")
                .hide()
                .find("input")
                .attr("data-is-required",'0');
            }else if($(this).val()=='physical_certificate'){
                $(this).closest(".add-destination-section-stops")
                .find(".certificate_number_file")
                .hide()
                .find("input")
                .attr("data-is-required",'0');
            }else{
                $(this).closest(".add-destination-section-stops")
                .find(".certificate_number_file")
                .show()
                .find("input")
                .attr("data-is-required",'0');
            }
        });
        $(window).scroll(function() {
            var divPosition = $('.for-remove-fixed-submit_request').offset().top - $(window).scrollTop();
            if (divPosition < $(window).height()) {
                $(".submit_request").removeClass("submit_request");
            }else{
                $(".shipment-request-submit").addClass("submit_request");
            }
        });
        var divPosition = $('.for-remove-fixed-submit_request').offset().top - $(window).scrollTop();
            if (divPosition < $(window).height()) {
                $(".submit_request").removeClass("submit_request");
            }else{
                $(".shipment-request-submit").addClass("submit_request");
            }
        // $(".submit_request").removeClass("submit_request");
    });
</script>
<script>
          
            // input field File name
            (function () {
    
                'use strict';
    
                $('.input-file').each(function () {
                    var $input = $(this),
                        $label = $input.next('.js-labelFile'),
                        labelVal = $label.html();
    
                    $input.on('change', function (element) {
                        var fileName = '';
                        if (element.target.value) fileName = element.target.value.split('\\').pop();
                        fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
                    });
                });
    
            })();
    
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
            var buttonPlus  = $(".qty-btn-plus");
                var buttonMinus = $(".qty-btn-minus");
    
                var incrementPlus = buttonPlus.click(function() {
                var $n = $(this)
                .parent(".qty-container")
                .find(".input-qty");
                $n.val(Number($n.val())+1 );
                });
    
                var incrementMinus = buttonMinus.click(function() {
                var $n = $(this)
                .parent(".qty-container")
                .find(".input-qty");
                var amount = Number($n.val());
                if (amount > 0) {
                    $n.val(amount-1);
                }
                });
                $(document).ready(function () {
            $('.dashboardSideBar .dash-nav-li a').click(function () {
                $('.dashboardSideBar .dash-nav-li a').removeClass("active");
                $(this).addClass("active");
            });

            var newWindowWidth = $(window).width();
            if (newWindowWidth >= 991) {
                $('.theia-sticky').theiaStickySidebar({
                    //'containerSelector': '',
                    'additionalMarginTop': 130,
                        // 'additionalMarginBottom': 0,
                        // 'updateSidebarHeight': true,
                        // 'minWidth': 0,
                        // 'disableOnResponsiveLayouts': true,
                        // 'sidebarBehavior': 'modern',
                        // 'defaultPosition': 'relative',
                        // 'namespace': 'TSS'
                });
            }
        });

       
            var ShipemtnMapBoundation = "{{Config('Shipment.country_code_shipment_allowed')}}";
            let ShipmentBound         = ShipemtnMapBoundation.split(",").map(code =>   code.trim());  
          
        function initMap() {
            var ac = new google.maps.places.Autocomplete(document.getElementById('dropoff_city'), {
               // types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)

                // This is for multiple countries
                 types: ['geocode'],
                 componentRestrictions: { country: ShipmentBound }
                
                // This is for multiple countries
                // types: ['geocode'],
                // componentRestrictions: { country: ['IN', 'US'] }

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

                // $("#dropoff_city").val(city);
                $("#dropoff_zip_code").val(zipCode);
                $("#dropoff_latitude").val(lat);
                $("#dropoff_longitude").val(lng);

            
            });

            var ac2 = new google.maps.places.Autocomplete(document.getElementById('company_city'), {
                // types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
            
             // This is for multiple countries
                 types: ['geocode'],
                 componentRestrictions: { country: ShipmentBound }

                // This is for multiple countries
                // types: ['geocode'],
                // componentRestrictions: { country: ['IN', 'US'] }
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
        </script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places">
</script>

<script>
   
//    $(document).ready(function(){

        
    // });
   
</script>
<!--  verification  Popup-->
<div class="modal fade themeModal" id="verificationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-340">
        <div class="modal-content">
            
            <span class="modalCenterHeadIcon">
                <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.5 1.48534C11.2293 1.48534 13.4419 3.79712 13.4419 6.64885V14.2909H3.55814V6.64885C3.55814 3.79712 5.77068 1.48534 8.5 1.48534ZM14.6279 14.2909V6.64885C14.6279 3.1127 11.8844 0.246094 8.5 0.246094C5.11565 0.246094 2.37209 3.1127 2.37209 6.64885V14.2909H0.593023C0.265506 14.2909 0 14.5683 0 14.9105C0 15.2527 0.265506 15.5301 0.593023 15.5301H16.407C16.7345 15.5301 17 15.2527 17 14.9105C17 14.5683 16.7345 14.2909 16.407 14.2909H14.6279Z" fill="#FF7C03"/>
                    <path d="M6.18164 15.262C6.18164 14.9658 6.42115 14.7257 6.71661 14.7257H10.283C10.5785 14.7257 10.818 14.9658 10.818 15.262V15.6195C10.818 16.9029 9.78011 17.9434 8.49982 17.9434C7.21953 17.9434 6.18164 16.9029 6.18164 15.6195V15.262ZM7.26421 15.7983C7.35074 16.4047 7.87098 16.8708 8.49982 16.8708C9.12866 16.8708 9.6489 16.4047 9.73543 15.7983H7.26421Z" fill="#FF7C03"/>
                    </svg>
            </span>
            <div class="modal-header">
                <button type="button" class="themeBtn-close" data-bs-dismiss="modal" aria-label="Close"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                    <p class="themePopupText">{{trans('messages.in_these_moments_a_verification_code_will_be_sent_to_the_phone_number_you_entered')}}</p>
                    <div class="text-center d-block">
                        <button type="button" class="themeModalBtn business-shipment-request-confirm-button">{{trans('messages.confirm')}}</button>
                    </div>
            </div>
        </div>
    </div>
</div>
<!--  verification  Popup-->
@stop
