@extends('frontend.layouts.customers')
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
    <div class="modal fade shipment-stops-certificates-modal allpopupsame" id="shipment-stops-certificates-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <div class="coordinator_name_box">
                        <h3 class="coordinator_name_title chat_name text-white"> Certificates </h3>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="profile-form" method="post" action="{{route('business-shipment-details',[$shipment->request_number])}}" id="business-shipment-request-form" enctype="multipart/form-data">
                        @csrf
                        @php
                            $shipmentStopFlag = false;
                        @endphp
                        @foreach($shipment->ShipmentStop as $key => $ShipmentStop)
                            @if($ShipmentStop->request_certificate == "" && $ShipmentStop->request_certificate_type ==  "digital")
                            @php
                                $shipmentStopFlag = true;
                            @endphp
                                <div class="row add-destination-section-stops">
                                    <div class="col-md-12" style="border-bottom: 1px solid #11223354;margin-bottom: 10PX;">( {{$ShipmentStop->dropoff_city}} ,{{$ShipmentStop->dropoff_address}} ) {{trans("messages.stop")}} {{$key+1}}</div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail2" class="form-label">{{trans('messages.need_a_delivery_note')}}</label>
                                            <div class="customRadio digital_certificate_radio_button" style="display: grid;display: grid;" data-certificate-row="{{$ShipmentStop->id}}">
                                                <span class="radioLabelrow">
                                                    <input type="radio" id="digital_certificate_{{$ShipmentStop->id}}" name="delivery_note[{{$ShipmentStop->id}}]" value="digital_certificate" {{$ShipmentStop->request_certificate_type == "digital" ? "checked" : ""}} >
                                                    <label for="digital_certificate_{{$ShipmentStop->id}}" style="color: #6A6A6A;">{{trans('messages.digital_certificate')}}</label>
                                                </span>
                                                <span class="radioLabelrow">
                                                    <input type="radio" id="physical_certificate_{{$ShipmentStop->id}}" name="delivery_note[{{$ShipmentStop->id}}]" value="physical_certificate" {{$ShipmentStop->request_certificate_type == "physical" ? "checked" : ""}}>
                                                    <label for="physical_certificate_{{$ShipmentStop->id}}" style="color: #6A6A6A;">{{trans('messages.physical_certificate')}}</label>
                                                </span>
                                                <span class="radioLabelrow">
                                                    <input type="radio" id="delivery_note_no_{{$ShipmentStop->id}}" name="delivery_note[{{$ShipmentStop->id}}]" value="no" {{$ShipmentStop->request_certificate_type == "no" ? "checked" : ""}}>
                                                    <label for="delivery_note_no_{{$ShipmentStop->id}}" style="color: #6A6A6A;">{{trans('messages.no')}}</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 certificate_number_file certificate_number_file_{{$ShipmentStop->id}}" style="display: {{$ShipmentStop->request_certificate_type == "no" ? "none" : "block"}};">
                                        <div class="form-group">
                                            <label for="certificate_number" class="form-label">{{trans('messages.certificate')}}</label>
                                            <input type="file" class="form-control file-input 1-form-fields" data-is-required="{{$ShipmentStop->request_certificate_type == "digital" ? "1" : "0"}}" id="certificate_number" aria-describedby="certificate_number" placeholder="xxxxxxxxx" name="certificate_number[{{$ShipmentStop->id}}]" >
                                            <small class="text-danger text-danger-text error-certificate_number{{$ShipmentStop->id}}"></small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <button type="button" class="save-updateBtn mt-4 shipment-request-submit">{{trans('messages.Continue')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-xl-9 col-xxl-9 col-sm-12">
        <div class="dashboardRight_block_wrapper admin_right_page customer-transport-request ">
            <div class="row">
                @if($shipment->shipmentDriverScheduleDetails == null)
                    <div class="request_editeDelite business_delete_request">
                        <a href="{{route('business-shipment-request-cancel',$shipment->request_number)}}" class="request_delite_btn confirmCancel ">
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
                    </div>
                @endif
                <div class="col-lg-8">
                    <div class="pageTopTitle">
                        <h2 class="RightBlockTitle">{{trans("messages.transport_request_number")}}
                            {{$shipment->request_number}} </h2>
                    </div>
                    <form class="profile-form">
                        <div class="row ">
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"
                                        class="form-label">{{trans("messages.delivery_date")}}</label>
                                    <span class="transport-request-lable"> <svg width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M19 4H17V3C17 2.73478 16.8946 2.48043 16.7071 2.29289C16.5196 2.10536 16.2652 2 16 2C15.7348 2 15.4804 2.10536 15.2929 2.29289C15.1054 2.48043 15 2.73478 15 3V4H9V3C9 2.73478 8.89464 2.48043 8.70711 2.29289C8.51957 2.10536 8.26522 2 8 2C7.73478 2 7.48043 2.10536 7.29289 2.29289C7.10536 2.48043 7 2.73478 7 3V4H5C4.20435 4 3.44129 4.31607 2.87868 4.87868C2.31607 5.44129 2 6.20435 2 7V19C2 19.7956 2.31607 20.5587 2.87868 21.1213C3.44129 21.6839 4.20435 22 5 22H19C19.7956 22 20.5587 21.6839 21.1213 21.1213C21.6839 20.5587 22 19.7956 22 19V7C22 6.20435 21.6839 5.44129 21.1213 4.87868C20.5587 4.31607 19.7956 4 19 4ZM20 19C20 19.2652 19.8946 19.5196 19.7071 19.7071C19.5196 19.8946 19.2652 20 19 20H5C4.73478 20 4.48043 19.8946 4.29289 19.7071C4.10536 19.5196 4 19.2652 4 19V12H20V19ZM20 10H4V7C4 6.73478 4.10536 6.48043 4.29289 6.29289C4.48043 6.10536 4.73478 6 5 6H7V7C7 7.26522 7.10536 7.51957 7.29289 7.70711C7.48043 7.89464 7.73478 8 8 8C8.26522 8 8.51957 7.89464 8.70711 7.70711C8.89464 7.51957 9 7.26522 9 7V6H15V7C15 7.26522 15.1054 7.51957 15.2929 7.70711C15.4804 7.89464 15.7348 8 16 8C16.2652 8 16.5196 7.89464 16.7071 7.70711C16.8946 7.51957 17 7.26522 17 7V6H19C19.2652 6 19.5196 6.10536 19.7071 6.29289C19.8946 6.48043 20 6.73478 20 7V10Z"
                                                fill="#1535B9" />
                                        </svg><span>{{$shipment->RequestTimeDescription->code ?? "" }}</span> <span>{{
                                            \Carbon\Carbon::createFromFormat('Y-m-d',
                                            ($shipment->request_date))->format(config("Reading.date_format")) }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"
                                        class="form-label">{{trans("messages.flexibility_days")}}</label>
                                    <span class="transport-request-lable">{{$shipment->request_date_flexibility}} {{ trans('messages.'.($shipment->request_date_flexibility >1 ?  'days' : 'day'))}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail2"
                                        class="form-label">{{trans('messages.pickup_city')}}</label>
                                    <span class="transport-request-lable">
                                        {{$shipment->pickup_city}}
                                    </span>
                                </div>
                            </div>
                            @if($shipment->ShipmentStop->count()==1)
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail2"
                                        class="form-label">{{trans('messages.admin_destination_city')}}</label>
                                    <span class="transport-request-lable">
                                    @foreach($shipment->ShipmentStop as $ShipmentStop )
                                        {{$ShipmentStop->dropoff_city}}
                                        @break
                                    @endforeach
                                    </span>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail2"
                                        class="form-label">{{trans("messages.collection_address")}}</label>
                                    <span class="transport-request-lable"><svg width="12" height="16"
                                            viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6 0C2.68686 0 0 2.5076 0 5.59969C0 9.79946 6 16 6 16C6 16 12 9.79946 12 5.59969C12 2.5076 9.31533 0 6 0ZM6 7.60046C4.81752 7.60046 3.8562 6.70533 3.8562 5.59969C3.8562 4.49406 4.81533 3.59893 6 3.59893C7.18467 3.59893 8.1438 4.49406 8.1438 5.59969C8.1438 6.70533 7.18467 7.60046 6 7.60046Z"
                                                fill="#1535B9" />
                                        </svg>
                                        {{$shipment->pickup_address}}</span>
                                </div>
                            </div>
                            @if($shipment->ShipmentStop->count()==1)
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"
                                        class="form-label">{{trans("messages.destination_address")}}</label>
                                    <span class="transport-request-lable"><svg width="12" height="16"
                                            viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6 0C2.68686 0 0 2.5076 0 5.59969C0 9.79946 6 16 6 16C6 16 12 9.79946 12 5.59969C12 2.5076 9.31533 0 6 0ZM6 7.60046C4.81752 7.60046 3.8562 6.70533 3.8562 5.59969C3.8562 4.49406 4.81533 3.59893 6 3.59893C7.18467 3.59893 8.1438 4.49406 8.1438 5.59969C8.1438 6.70533 7.18467 7.60046 6 7.60046Z"
                                                fill="#FF7C03" />
                                        </svg>
                                        @foreach($shipment->ShipmentStop as $ShipmentStop )
                                        {{$ShipmentStop->dropoff_address}}
                                        @break
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"
                                        class="form-label">{{trans("messages.description")}}</label>
                                    <span
                                        class="transport-request-lable trans_description ">{{$shipment->description}}</span>
                                </div>
                            </div>


                            @php
                            $locationFeedbackCount = $shipment->ShipmentStop->filter(function($stop) {
                                return $stop->location_feedback !== null;
                            })->count();
                            @endphp
                            
                            @if (!empty($locationFeedbackCount) && $locationFeedbackCount == 1)
                              @foreach ($shipment->ShipmentStop as $ShipmentStop)
                                @if ($ShipmentStop->location_feedback)
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">{{ trans("messages.location_feedback") }}</label><br>
                                            {{ $ShipmentStop->location_feedback }}
                                        </div>
                                    </div>
                                    @break
                                @endif
                             @endforeach
                           @endif

                            <div class="col-md-12 col-sm-12"></div>
                            @if($shipment->ShipmentStop->count()==1)
                            @foreach($shipment->ShipmentStop as $ShipmentStop )
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail2"
                                        class="form-label">{{trans("messages.name_of_the_receiver")}}</label>
                                    <span
                                        class="transport-request-lable">{{$ShipmentStop->request_dropoff_contact_person_name}}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"
                                        class="form-label">{{trans("messages.recipient_phone_number")}}</label>
                                    <span
                                        class="transport-request-lable" style="height: 50px, width:50px;">{{$ShipmentStop->request_dropoff_contact_person_phone_number}}
                                    </span>
                                </div>
                            </div>
                            @break
                            @endforeach
                            @endif

                            @if($ShipmentStop->request_digital_signature != "")
                               <div class="col-md-6 col-sm-6">
                                  <div class="form-group">
                                     <label for="exampleInputEmail1"
                                        class="form-label">{{trans("messages.signature")}}</label>
                                     <span class="transport-request-lable">
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="{{$ShipmentStop->request_digital_signature}}">
                                                    <img height="50" width="80" class="sig_image" src="{{$ShipmentStop->request_digital_signature}}" />
                                                </a>
                                    </span>
                                 </div>
                              </div>
                            @endif
                            
                            @if($ShipmentStop->request_certificate != "")
                            <div class="col-md-6 col-sm-6">
                               <div class="form-group">
                                  <label for="exampleInputEmail1"
                                     class="form-label">{{trans("messages.request_certificate")}}</label>
                                  <span class="transport-request-lable">
                                             <a href="{{$ShipmentStop->request_certificate}}" target="_blank">
                                                <img src="{{url('public/frontend/img/file-icon.png')}}" style="height:100px;">
                                            </a>
                                 </span>
                              </div>
                           </div>
                         @endif

                         @if($shipment->status == 'end' && $shipment->status && $shipment->invoice_file != '')
                         <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                               <label for="exampleInputEmail1"
                                  class="form-label">{{trans("messages.invoice_file")}}</label>
                               <span class="transport-request-lable">
                                            
                         <a  href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$shipment->invoice_file) }}" target="_blank">

                                             <img src="{{url('public/frontend/img/file-icon.png')}}" style="height:100px;">
                                         </a>
                              </span>
                           </div>
                        </div>

                         <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                               <label for="exampleInputEmail1"
                                  class="form-label">{{trans("messages.invoice_price")}}</label>
                              <span
                                        class="transport-request-lable" style="height: 50px, width:50px;">{{$shipment->invoice_price}}
                                    </span>
                           </div>
                        </div>
                      @endif


                        </div>
                    </form>
                </div>
                <div class="col-lg-4">
                    <div class="request_transport_block">
                        <div class="request_transport_head_box">
                            <h3 class="request_tranStatus_title">{{trans("messages.request_status")}}</h3>
                            @php
                            $className = '';
                           $shipmentStatus = '';
                           if($shipment->status == 'shipment' && $shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->shipment_status == 'start'){
                               $className = 'lightgray_btn';
                               $shipmentStatus = 'active';
                           }elseif ($shipment->status == 'shipment') {
                               $className = 'orange_btn';
                               $shipmentStatus = 'shipment';
                            } elseif ($shipment->status == 'end') {
                               $className = 'blue_btn';
                               $shipmentStatus = 'end';
                           }
                          @endphp
                            <a href="#!" class="{{$className}} dashboard_tableBtn" style="width:50%">{{trans("messages.".$shipmentStatus)}}</a>
                            @if($shipment->status == 'end')
		                        @if($shipment->shipmentRatingReviews)
                                    <a href="javascript:void(0)" class="transportRequestBtn give-rating-button" onclick="$.fn.view_review_modal({{ $shipment->id }})">{{ trans('messages.view_rating') }} </a>
                                @else
                                    <a href="javascript:void(0)" class="transportRequestBtn give-rating-button" onclick="$.fn.review_modal({{ $shipment->id }})">{{ trans('messages.give_rating') }} </a>
                                @endif
                            @endif
                        </div>

                        <form class="profile-form type_transport_box">
                            <div class="row ">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail2"
                                            class="form-label">{{trans("messages.request_type")}}</label>
                                        <span class="transport-request-lable">
                                            {{$shipment->TruckTypeDescriptions->name}}
                                        </span>
                                    </div>
                                </div>
                                @php
                                $shipment->request_pickup_details = json_decode($shipment->request_pickup_details,true);
                                foreach($shipment->request_pickup_details as $key => $value){
                                $truck_type_question_descriptions =
                                DB::table('truck_type_question_descriptions')->where('parent_id',$key)->where('language_id',getAppLocaleId())->first();
                                @endphp
                                @if(isset($truck_type_question_descriptions->name))
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"
                                            class="form-label">{{$truck_type_question_descriptions->name}}</label>
                                        <span class="transport-request-lable">
                                            @php
                                            if(is_array($value)){
                                            $opyionvalueStr = '';
                                            foreach($value as $valueKey => $opyionvalue){
                                            $input_description_array =
                                            explode(",",$truck_type_question_descriptions->input_description);
                                            if(isset($input_description_array[$opyionvalue])){
                                                echo ($opyionvalueStr == '' ? '' : ', ').$input_description_array[$opyionvalue];
                                            }
                                            $opyionvalueStr = 'in';
                                            }
                                            }else{
                                            echo $value;
                                            }
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                                @endif
                                @php
                                }
                                @endphp
                            </div>
                        </form>
                    </div>
                </div>
                @if($shipment->shipment_attchement->count())
                <div class="seprator"></div>
                <h3 class="profile-title">{{trans('messages.attachments')}}</h3>
                <div class="attachment-elements">
                    @foreach($shipment->shipment_attchement as $attchement)
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
                
                <div class="col-lg-12">
                    <div class="seprator"></div>
                    @if($shipment->ShipmentStop->count()>1)
                    <h3 class="profile-title">
                        {{trans("messages.admin_list_of_destination")}}
                    </h3>
                    <div class="dashboard_notofication_main dahboard_whiteSpace List_objectives_table company_details_block">
                        <div class="table-responsive dashboard_notofication">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{trans("messages.name_of_the_receiver")}}</th>
                                        @foreach ($shipment->ShipmentStop as $stop)
                                        @if ($stop->location_feedback != "")
                                        <th scope="col">{{trans("messages.location_feedback")}}</th>
                                        @endif
                                        @break
                                        @endforeach
                                        <th scope="col">{{trans("messages.destination_address")}}</th>
                                        <th scope="col">{{trans("messages.recipients_phone_number")}}</th>
                                        <th scope="col">{{trans("messages.delivery_note")}}</th>
                                        <th scope="col">{{trans("messages.certificate")}}</th>
                                        @if ($shipment->status == 'end')
                                        @foreach ($shipment->ShipmentStop as $stop)
                                            @if ($stop->request_digital_signature != "")
                                            <th scope="col">{{trans("messages.signature")}}</th>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($shipment->ShipmentStop->count())
                                    @foreach($shipment->ShipmentStop as $ShipmentStop )
                                    <tr>
                                        <td>{{$ShipmentStop->request_dropoff_contact_person_name}}</td>
                                        
                                        <td>@if($ShipmentStop->location_feedback != "")
                                            {{$ShipmentStop->location_feedback}}
                                                   @endif
                                        
                                        <td>
                                            <div class="destination_add">{{$ShipmentStop->dropoff_address . ", " . $ShipmentStop->dropoff_city}} </div>
                                        </td>
                                        <td>
                                            {{$ShipmentStop->request_dropoff_contact_person_phone_number}}
                                         </td>
                                        <td>
                                          {{trans("messages.".$ShipmentStop->request_certificate_type)}}
                                       </td>
                                       <td>
                                          @if($ShipmentStop->request_certificate_type != "no" && $ShipmentStop->request_certificate != "")
                                          <div >
                                                <a href="{{$ShipmentStop->request_certificate}}" target="_blank">
                                                    <img src="{{url('public/frontend/img/file-icon.png')}}" style="height:58px;">
                                                </a>
                                          </div>
                                          @else
                                             ---
                                          @endif
                                       </td>
                                       <td>
                                         @if($shipment->status = 'end' && $ShipmentStop->request_digital_signature != "")
                                         <div>
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="{{$ShipmentStop->request_digital_signature}}">
                                                    <img height="50" width="80" src="{{$ShipmentStop->request_digital_signature}}" />
                                                </a>
                                                  
                                                </div>

                                                    @endif
                                                </td>

                                                <td>
                                              
                                        
                                                </tr>
                                    @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">
                                                <h3 style="text-align: center;">{{trans('messages.stop_not_found')}}</h3>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                        <div class="dashboardRight_block_wrapper company_details_block">
                            <div>
                                <h3 class="transpor_request_box_title">{{trans('messages.selected_offer_details')}}</h3>
                            </div>
                            <div class="dashboard_notofication_main List_objectives_table">
                                <div class="transportation_request_block offer_details_content">
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.the_date_of_transport')}}</span>
                                        <span>
                                         <h3 class="date_label offreDetails_cont"> {{ \Carbon\Carbon::createFromFormat('Y-m-d', ($shipment->request_date))->format(config("Reading.date_format"))  }} :<br/>
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
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{ trans('messages.location_feedback') }}</span>
                                        <h3 class="offreDetails_cont">
                                            @foreach ($shipment->ShipmentStop as $ShipmentStop)
                                                @if ($ShipmentStop->location_feedback)
                                                    {{ $ShipmentStop->location_feedback }}
                                                   @if (!$loop->last) ,
                                                    @endif
                                                @endif
                                            @endforeach
                                        </h3>
                                    </div> 

                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.admin_common_Price')}}</span>
                                        <h3 class="offreDetails_cont">: {{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->price}}</h3>
                                    </div>
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.extra_time_price')}}</span>
                                        <h3 class="offreDetails_cont">: {{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->extra_time_price}}</h3>
                                    </div>
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.duration')}}</span>
                                        <h3 class="offreDetails_cont">: {{$shipmentOffer->duration}}</h3>
                                    </div>
                                    @if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->shipment_status == "end")
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.estimated_hours')}}</span>
                                        <h3 class="offreDetails_cont">: {{$shipment->shipmentDriverScheduleDetails->time_taken_to_complete_shipment}}</h3>
                                    </div>
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.end_time')}}</span>
                                        <h3 class="offreDetails_cont">: {{date(Config('Reading.date_time_format'),strtotime($shipment->shipmentDriverScheduleDetails->shipment_actual_end_time))}}</h3>
                                    </div>
                                    @endif
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.shipment_note')}}</span>
                                        <h3 class="offreDetails_cont">: {{$shipmentOffer->description}}</h3>
                                    </div>
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.payment_condition')}}</span>
                                        <h3 class="offreDetails_cont">: {{$shipmentOffer->payment_condition}}</h3>
                                    </div>
                                    
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.Type_of_truck')}}</span>
                                        <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->type_of_truck ?? ""}}</h3>
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
                                        <h3 class="offreDetails_cont">: {{$shipmentOffer->TruckDetail->truck_system_number ?? ""}}</h3>
                                    </div>
                                    <div class="company_offreDetails">
                                        <span class="company_offreDetails_label">{{trans('messages.admin_common_Status')}}</span>
                                        <h3 class="offreDetails_cont">: {{ trans("messages.$shipmentOffer->status") }}</h3>
                                    </div>
                                    <!--  --------------------------------------------  -->
                                </div>
                            </div>
                        </div>
                        <div class="dashboardRight_block_wrapper admin_right_page company_details_block">
                            <div class="dashboard_notofication_main  List_objectives_table">
                                <div class="transportation_request_block">
                                    <div class="transpor_request_box">
                                        <h3 class="transpor_request_box_title">{{trans("messages.Transportation")}}</h3>
                                    </div>
                                    <div class="coordinator_name_box">
                                        <div class="company_name_logo_box">
                                            <img src="{{$shipmentOffer->companyUser->userCompanyInformation->company_logo}}" alt="">
                                        </div>
                                        <div class="coordinator_name_title">{{$shipmentOffer->companyUser->userCompanyInformation->company_name}}</div>
                                    </div>
                                    <div class="transportation_request_block">
                                        <div class="company_offreDetails">
                                            <h3 class="offreDetails_cont">{{trans('messages.admin_common_company_description')}}</h3>
                                            <span class="company_offreDetails_label">: {{$shipmentOffer->companyUser->userCompanyInformation->company_description}}</span>
                                        </div>
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
                            $driverDetails = false ;
                            if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->truckDriver){
                                $driverName     = $shipment->shipmentDriverScheduleDetails->truckDriver->name;
                                $driverNumber   = $shipment->shipmentDriverScheduleDetails->truckDriver->phone_number;
                                $driverDetails  = $shipment->shipmentDriverScheduleDetails->truckDriver;
                                $driverDetails->userDriverDetail->driver_picture = url(Config('constants.DRIVER_PICTURE_PATH') . '/' . $driverDetails->userDriverDetail->driver_picture);
                            }else if($shipmentOffer->TruckDetail->truckDriver){
                                $driverName     = $shipmentOffer->TruckDetail->truckDriver->name;
                                $driverDetails  = $shipmentOffer->TruckDetail->truckDriver;
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
                                            @if($driverNumber)
                                                <div class="company_offreDetails">
                                                    <h3 class="offreDetails_cont">{{trans('messages.admin_phone_number')}}</h3>
                                                    <span class="company_offreDetails_label"> : {{ $driverNumber }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if($driverNumber)
                                        <div class="coordinator_phoneMess_box">
                                            <a href="#!" onclick="singelChatModal('{{$driverDetails?->userDriverDetail?->driver_picture}}','{{$driverDetails?->name}}','{{ $driverDetails?->id }}')" class="table_message_btn">
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
                                        </div>
                                        @endif
                                    </div>
                                    <div class="current_location_map">
                                      
                                        @if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->shipment_status == "start" )
                                            <div class="company_offreDetails">
                                                <h3 class="offreDetails_cont">{{trans("messages.current_location")}}</h3>
                                                <span class="company_offreDetails_label"> {{$shipment->shipmentDriverScheduleDetails->truckDriver->current_location}}</span>
                                            </div>
                                            <div id="map" style="height: 300px;width: 100%; border-radius: 10px ;"></div>
                                        @endif
                                    </div>
                              </div>
                               @endif
                        
                              </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    @stop
    @section('scriptCode')
        @include('frontend.chat.commonChatModal')
        @if($shipment->status == 'end')
            @if($shipment->shipmentRatingReviews)
                @include('frontend.customers.commonViewRatingModal')
            @else
                @include('frontend.customers.commonRatingModal')
            @endif
        @endif

        @if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->shipment_status == "start" )
            @include('common.trackingMap')
        @endif
        
    <script>
  $(document).ready(function() {  

    var requestType = '{{request()->type ?? ''}}';
    var shipmentStopFlag = '<?php echo $shipmentStopFlag ?>';
    if(requestType == 'upload_certificate' && shipmentStopFlag){
        $("#shipment-stops-certificates-modal").modal('show');
    }

    $("body").on("click", ".shipment-request-submit", function() {
            var flag = $.fn.checkValidation(1);
            
            if (flag) {
                $(".is-invalid:first").focus();
                return false;
            }
            $("#business-shipment-request-form").submit();
        });
        $("body").on("change", ".digital_certificate_radio_button span input", function() {
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
                .attr("data-is-required",'1');
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
                if ($(this).data("type") == "same") {
                    if ($(this).val() != $("input[name='" + $(this).data("same-with") + "']").val()) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("Sa valeur doit être la même que la valeur de " + ($("input[name='" + $(this).data("same-with") + "']").data("name")));
                        $(this).addClass("is-invalid");

                    }
                }
                if (($(this).val() == "" && $(this).attr("data-is-required") == "1") || ($(this).attr('type') == "checkbox" && $("input[name='"+$(this).attr('name')+"']:checked").length == 0 && $(this).attr("data-is-required") == "1") ) {
                    flag = true;
                    var str_name = $(this).attr("name");
                    str_name = str_name.replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '');
                    $(".error-" + str_name).html("This field is required.");
                    $(this).addClass("is-invalid");
                }
                if($(this).val() != "" && $(this).data("type") == "email"){
                    var regEx = /^(\w+([-+.'][^\s]\w+)*)?@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
                    var val = $(this).val();
                    if (!val.match(regEx)) {
                        flag = true;
                        $(".error-"+$(this).attr("name")).html("Invalid email");
                    }
                }
            });
            return flag;
        }
        

        $(".confirmCancel").click(function(e) {
                e.stopImmediatePropagation();
                url = $(this).attr('href');
                Swal.fire({
                    title: "{{trans('messages.admin_common_Are_you_sure')}}",
                    text: "{{trans('messages.admin_Want_to_cancel_this')}}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{trans('messages.admin_Yes_cancel_it')}}",
                    cancelButtonText: "{{trans('messages.admin_No_cancel')}}",
                    reverseButtons: true
                }).then(function(result) {
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
  });
</script>

<style>
    img.sig_image {
    border: 2px solid black;
    width: 75%;
    height: 20%;
}
</style>

        
    @stop
