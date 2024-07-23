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
   
    <div class="col-md-12 col-lg-9 col-sm-12">
        <div class="dashboardRight_block_wrapper all_requests_wrapper">
            <div class="pageTopTitle">
                <h2 class="RightBlockTitle m-0"> {{trans('messages.my_invoice')}}</h2>
            </div>
            <form class="pagination_form">
            <div class="dashboard_table">
                <div class="dashboard_notofication_main dahboard_whiteSpace">
                    <div class="table-responsive dashboard_notofication">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        {{trans('messages.request_id')}}
                                    </th>
                                    <th scope="col">
                                        {{trans('messages.Company Name')}}
                                    </th>
                                    <th scope="col">
                                        {{trans('messages.type')}}
                                    </th>

                                    <th scope="col">
                                        {{trans('messages.shipment_date')}}
                                    </th>
                                   
                                    <th scope="col">
                                        {{trans('messages.shipment_certificate')}}
                                    </th>

                                     <th scope="col">
                                        {{trans('messages.invoice_price')}}
                                    </th>
                                   
                                    <th scope="col">
                                        {{trans('messages.my_invoice')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($ShipmentList->count())
                                @foreach($ShipmentList as $key => $shipment)
                                <tr class="table-tr">
                                     <td>
                                        <a href="{{route('business-shipment-details',[$shipment->request_number])}}" style="text-decoration: none; color: #3498db; font-weight: bold; transition: color 0.3s ease;" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='#3498db'">
                                            {{$shipment->request_number ?? ''}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$shipment?->shipmentDriverSchedule?->companyInformation?->company_name ?? ''}}
                                    </td>
                                    <td>
                                        {{$shipment->TruckTypeDescriptions->name ?? ''}}
                                    </td>
                                  
                                
                                    <td>
                                        @if(!@empty($shipment->invoice_send_time))
                                          <a class="date_label" href="javascript:void(0)" style="float:right;">
                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', ($shipment->invoice_send_time))->format(config("Reading.date_time_format"))  }}<br/>
                                        </a>
                                        @else

                                        @endif
                                    </td>
                                    @if($shipment->ShipmentStop->count())
                                        @foreach( $shipment->ShipmentStop as $key => $ShipmentStop )
                                        <td>
                                            @php
                                                $filename = $ShipmentStop->request_certificate;
                                                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                                if($extension == "png" || $extension == "jpg" || $extension == "jpeg"|| $extension == "svg"){
                                            @endphp 
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="{{$ShipmentStop->request_certificate }}" >
                                                    <div class="upload_img_item tabel_img">
                                                        <img  src="{{$ShipmentStop->request_certificate }}" alt="" width="50" height="50">
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
                                                <a href="{{ $ShipmentStop->request_certificate }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                            @php
                                                }else if($extension == "docx" || $extension == "doc"){
                                            @endphp
                                                <a  href="{{$ShipmentStop->request_certificate }}" target="_blank">
                                                    <div class="upload_img_item tabel_img">
                                                        <img src="{{url('/public/frontend/img/docx-icon.png')}}" alt="" width="50" height="50">
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
                                                <a href="{{ $ShipmentStop->request_certificate }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                            @php
                                                }else if($extension == "pdf"){
                                            @endphp
                                                <a href="{{ $ShipmentStop->request_certificate }}" target="_blank">
                                                    <div class="upload_img_item tabel_img">
                                                        <img src="{{url('/public/frontend/img/pdf-icon.png')}}" alt="" width="50" height="50">
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
                                                    <a href="{{ $ShipmentStop->request_certificate }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                            @php
                                                }
                                            @endphp
                                        </td>
                                    @endforeach
                                    @else
                                        <td>---</td>
                                    @endif

                                    <td>
                                        @if($shipment?->invoice_price)
                                        {{$shipment?->invoice_price ?? ''}}{{Config('constants.CURRENCY_SIGN')}}
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $filename = $shipment->invoice_file;
                                            $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                            if($extension == "png" || $extension == "jpg" || $extension == "jpeg"|| $extension == "svg"){
                                        @endphp 
                                            <a class="fancybox-buttons" data-fancybox-group="button" href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$shipment->invoice_file) }}" >
                                                <div class="upload_img_item tabel_img">
                                                    <img  src="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$shipment->invoice_file) }}" alt="" width="50" height="50">
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
                                            <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                        @php
                                            }else if($extension == "docx" || $extension == "doc"){
                                        @endphp
                                            <a  href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$shipment->invoice_file) }}" target="_blank">
                                                <div class="upload_img_item tabel_img" style="padding: 15px;">
                                                    <img src="{{url('/public/frontend/img/docx-icon.png')}}" alt="" width="50" height="50">
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
                                            <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                        @php
                                            }else if($extension == "pdf"){
                                        @endphp
                                            <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" target="_blank">
                                                <div class="upload_img_item tabel_img">
                                                    <img src="{{url('/public/frontend/img/pdf-icon.png')}}" alt="" width="50" height="50">
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
                                                <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                        @php
                                            }
                                        @endphp
                                   </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">
                                            <h3 style="text-align: center;">{{trans('messages.invoice_not_found')}}</h3>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        @include('pagination.business-default', ['results' => $ShipmentList])
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    @stop
    <style>
        a.blue_btn.dashboard_tableBtn.downld{
            width: 100%;
    display: block;
    text-align: center;
    border-radius: 50px;
    color: #fff;
    font-weight: 400;
    font-size: 10px;
    padding: 3px 10px;
    white-space: nowrap;
}
.downld{
    margin-top: 10px;
}
 
.blue_btn {
    background-color: #1535B9;
    margin-top: 8px;
    width: fit-content !important;
}

.date_label_1{
    white-space: nowrap;
    text-align: right;
    direction: ltr;
}
 
    </style>
   
    @section('scriptCode')
    @stop