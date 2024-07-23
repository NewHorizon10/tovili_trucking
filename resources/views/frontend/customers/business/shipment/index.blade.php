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
                <h2 class="RightBlockTitle m-0">All Shipment</h2>
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
                                        {{trans('messages.type')}}
                                    </th>
                                    <th scope="col">
                                        {{trans('messages.date')}}
                                    </th>
                                    <th scope="col">
                                        {{trans('messages.origin_address')}}
                                    </th>
                                    <th scope="col">
                                        {{trans('messages.destination_address')}}
                                    </th>
                                    <th scope="col">
                                        {{trans('messages.suggestions')}}
                                    </th>
                                    <th scope="col">
                                        {{trans('messages.admin_common_Status')}}
                                    </th>
                                </tr>
                            </thead>
                            
                            <tbody>
                            @if($ShipmentList->count())
                                @foreach($ShipmentList as $key => $shipment)
                                <tr class="table-tr"  onclick="window.location = '{{route('business-shipment-details',[$shipment->request_number])}}'" style="cursor: pointer;">
                                    <td>
                                        {{$shipment->request_number}}
                                    </td>
                                    <td>
                                        {{$shipment->TruckTypeDescriptions->name}}
                                    </td>
                                    <td class="date_label">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', ($shipment->request_date))->format(config("Reading.date_format"))  }}<br/>
                                        {{$shipment->RequestTimeDescription->code ?? "" }}
                                    </td>
                                    <td>
                                        <div class="address-box">
                                            {{$shipment->pickup_city}}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="address-box2">
                                            @if($shipment->ShipmentStop->count()>1)
                                                {{trans('messages.multiple_destinations')}}
                                            @else
                                                @foreach($shipment->ShipmentStop as $ShipmentStop )
                                                    {{$ShipmentStop->dropoff_city}}
                                                    @break
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        {{$shipment->ShipmentOffers->count()}}
                                    </td>

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
                                    <td>
                                        <a href="javascript:void(0)" class="{{$className}} dashboard_tableBtn">
                                            {{trans("messages.".$shipmentStatus)}}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="8">
                                        <h3 style="text-align: center;">{{trans('messages.shipment_not_found')}}</h3>
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
    @section('scriptCode')
    @stop