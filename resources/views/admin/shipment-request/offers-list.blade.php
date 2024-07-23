@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        {{trans("messages.shipment_offers")}}
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
   
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route('shipment-request-offers-list')}}" method="get" class="kt-form kt-form--fit mb-0" autocomplete="off">
                <input type="hidden" name="display">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card card-custom card-stretch card-shadowless">
                            <div class="card-header">
                                <div class="card-title">

                                </div>
                                <div class="card-toolbar">
                                    <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-toggle="collapse" data-target="#collapseOne6">
                                    {{trans("messages.admin_common_Search")}}
                                    </a>

                                    <a href="{{route('shipment-request-offers.export')}}" class="btn btn-primary mr-2" >
                                    {{trans("messages.export")}}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                                    <div id="collapseOne6" class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
                                        <div>
                                            <div class="row mb-6">
                                                <div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>{{trans("messages.request_number")}}</label>
                                                    <input type="number" class="form-control" name="request_number" placeholder="{{trans("messages.request_number")}}" value="{{$searchVariable['request_number'] ?? '' }}">
                                                </div>

                                                <div class="col-lg-3  mb-lg-5 mb-6">
                                                    <label>{{trans("messages.admin_common_Status")}}</label>
                                                    <select name = "status" class="form-control select2init">
                                                        <option value = "" {{($searchVariable['status'] ?? '') == '' ? 'selected' : '' }}>{{trans("messages.admin_All")}}</option>
                                                        <option value = "waiting" {{($searchVariable['status'] ?? '') == 'waiting' ? 'selected' : '' }}>{{trans("messages.waiting")}}</option>
                                                        <option value = "selected" {{($searchVariable['status'] ?? '') == 'selected' ? 'selected' : '' }}>{{trans("messages.selected")}}</option>
                                                        <option value = "approved_from_company" {{($searchVariable['status'] ?? '') == 'approved_from_company' ? 'selected' : '' }}>{{trans("messages.approved_from_company")}}</option>
                                                        <option value = "rejected" {{($searchVariable['status'] ?? '') == 'rejected' ? 'selected' : '' }}>{{trans("messages.rejected")}}</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>{{trans("messages.Company Name")}}</label>
                                                    <input type="text" class="form-control" name="company_name" placeholder="{{trans("messages.Company Name")}}" value="{{$searchVariable['company_name'] ?? '' }}">
                                                </div>
                                                <div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>{{trans("messages.customers_name")}}</label>
                                                    <input type="text" class="form-control" name="name" placeholder="{{trans("messages.name")}}" value="{{$searchVariable['name'] ?? '' }}">
                                                </div>
                                               
                                                <div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>{{trans("messages.request_type")}}</label>
                                                    <select name="request_types" class="form-control select2init @error('request_types') is-invalid @enderror" >
                                                        <option value="">{{trans("messages.select_request_type")}}</option>
                                                        @foreach($requesttypes as $value)
                                                            <option value="{{$value->parent_id}}" {{ isset($searchVariable['request_types']) && $searchVariable['request_types'] == $value->parent_id ? 'selected': '' }} >{{$value->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>{{trans("messages.city_Name")}}</label>
                                                    <input type="text" class="form-control" name="city_name" placeholder="{{trans('messages.city_Name')}}" id="company_location" value="{{$searchVariable['city_name'] ?? '' }}">
                                                    <input type="hidden" name="current_lat" id="current_lat" value="{{$searchVariable['current_lat'] ?? '' }}">
                                                    <input type="hidden" name="current_lng" id="current_lng" value="{{$searchVariable['current_lng'] ?? '' }}">
                                                </div>
                                               
                                                <div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>{{trans("messages.admin_Date_From")}}</label>
                                                    <div class="input-group date" id="datepickerfrom" data-target-input="nearest">
                                                        {{ Form::text('date_from',((isset($searchVariable['date_from'])) ? $searchVariable['date_from'] : ''), ['class' => ' form-control datetimepicker-input','placeholder'=>trans("messages.admin_Date_From"),'data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker']) }}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="ki ki-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-lg-5 mb-6">
                                                    <label>{{trans("messages.admin_Date_To")}}</label>
                                                    <div class="input-group date" id="datepickerto" data-target-input="nearest">
                                                        {{ Form::text('date_to',((isset($searchVariable['date_to'])) ? $searchVariable['date_to'] : ''), ['class' => ' form-control  datetimepicker-input','placeholder'=>trans("messages.admin_Date_To"),'data-target'=>'#datepickerto','data-toggle'=>'datetimepicker']) }}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="ki ki-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-8">
                                                <div class="col-lg-12">
                                                    <button class="btn btn-primary btn-primary--icon" id="kt_search">
                                                        <span>
                                                            <i class="la la-search"></i>
                                                            <span>{{trans("messages.admin_common_Search")}}</span>
                                                        </span>
                                                    </button>
                                                    &nbsp;&nbsp;
                                                    <a href='{{ route("shipment-request-offers-list")}}' class="btn btn-secondary btn-secondary--icon">
                                                        <span>
                                                            <i class="la la-close"></i>
                                                            <span>{{trans("messages.admin_common_Clear_Search")}}</span>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                <div class="dataTables_wrapper-fake-top-scroll">
                                    <div>&nbsp;</div>
                                </div>
                                <div class="dataTables_wrapper ">
                                    <div class="table-responsive table-responsive-new">
                                        <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
                                            <thead>
                                                
                                                <tr class="text-uppercase">
                                                    <th class="{{(($sortBy == 'request_number' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'request_number' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                        <a href="{{route($model.'.index',array(	'sortBy' => 'request_number',
													'order' => ($sortBy == 'request_number' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}">{{trans("messages.request_number")}}</a>
                                                    </th>

                                                    <th class="{{(($sortBy == 'company_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'company_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                        <a href="{{route('shipment-request-offers-list',array(	'sortBy' => 'company_name',
													'order' => ($sortBy == 'company_name' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}">{{trans("messages.Company Name")}}</a>
                                                    </th>
                                                    <th class="{{(($sortBy == 'user_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'user_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                        <a href="{{route('shipment-request-offers-list',array(	'sortBy' => 'user_name',
													'order' => ($sortBy == 'user_name' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}">{{trans("messages.customers_name")}}</a>
                                                    </th>
                                                    <th class="{{(($sortBy == 'request_types' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'request_types' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                        <a href="{{route('shipment-request-offers-list',array(	'sortBy' => 'request_types',
													'order' => ($sortBy == 'request_types' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}">{{trans("messages.request_type")}}</a>
                                                    </th>

                                                    <th class="{{(($sortBy == 'request_date' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'request_date' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                        <a href="{{route('shipment-request-offers-list',array(	'sortBy' => 'request_date',
													'order' => ($sortBy == 'request_date' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}">{{trans("messages.request_date")}}</a>
                                                    </th>
                                                   
                                                    <th class="{{(($sortBy == 'status' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'status' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                        <a href="{{route('shipment-request-offers-list',array(	'sortBy' => 'status',
													'order' => ($sortBy == 'status' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}">{{trans("messages.admin_common_Status")}}</a>
                                                    </th>
                                                    <th class="text-right">{{trans("messages.admin_common_Action")}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!$results->isEmpty())
                                                @foreach($results as $result)
                                                <tr>
                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                            {{ $result->request_number}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                            {{ $result->company_name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                            {{ $result->user_name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                            {{ $result->request_types}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                            {{  date(config("Reading.date_format"),strtotime($result->request_offer_date)) }}
                                                        </div>
                                                    </td>
                                                    
                                                    <td>
                                                        @if($result->shipment_status)
                                                            @if($result->shipment_status == 'start')
                                                                {{ trans('messages.active') }}
                                                            @else
                                                                {{ trans('messages.'.$result->shipment_status) }}
                                                            @endif
                                                        @else
                                                            @if($result->status=='rejected')
                                                                {{ trans('messages.rejected') }}
                                                            @else
                                                                {{ trans('messages.'.$result->status) }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="text-right pr-2">
                                                        <a href="{{route('list-offer-details.show',[$result->system_id,'offers'])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_View')}}">
                                                            <span class="svg-icon svg-icon-md svg-icon-primary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24" height="24" />
                                                                        <path d="M12.8434797,16 L11.1565203,16 L10.9852159,16.6393167 C10.3352654,19.064965 7.84199997,20.5044524 5.41635172,19.8545019 C2.99070348,19.2045514 1.55121603,16.711286 2.20116652,14.2856378 L3.92086709,7.86762789 C4.57081758,5.44197964 7.06408298,4.00249219 9.48973122,4.65244268 C10.5421727,4.93444352 11.4089671,5.56345262 12,6.38338695 C12.5910329,5.56345262 13.4578273,4.93444352 14.5102688,4.65244268 C16.935917,4.00249219 19.4291824,5.44197964 20.0791329,7.86762789 L21.7988335,14.2856378 C22.448784,16.711286 21.0092965,19.2045514 18.5836483,19.8545019 C16.158,20.5044524 13.6647346,19.064965 13.0147841,16.6393167 L12.8434797,16 Z M17.4563502,18.1051865 C18.9630797,18.1051865 20.1845253,16.8377967 20.1845253,15.2743923 C20.1845253,13.7109878 18.9630797,12.4435981 17.4563502,12.4435981 C15.9496207,12.4435981 14.7281751,13.7109878 14.7281751,15.2743923 C14.7281751,16.8377967 15.9496207,18.1051865 17.4563502,18.1051865 Z M6.54364977,18.1051865 C8.05037928,18.1051865 9.27182488,16.8377967 9.27182488,15.2743923 C9.27182488,13.7109878 8.05037928,12.4435981 6.54364977,12.4435981 C5.03692026,12.4435981 3.81547465,13.7109878 3.81547465,15.2743923 C3.81547465,16.8377967 5.03692026,18.1051865 6.54364977,18.1051865 Z" fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="6" style="text-align:center;">{{trans("messages.admin_common_Record_not_found")}}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @include('pagination.default', ['results' => $results])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    
<script>
      function initMap() {
            var ac = new google.maps.places.Autocomplete(document.getElementById('company_location'), {
                types: ['(regions)'] 
            });
            ac.addListener('place_changed', () => {
                var place = ac.getPlace();

                // Extract the address components
                var addressComponents = place.address_components;
                var city, state;

                var lat = place.geometry.location.lat();

                var lng = place.geometry.location.lng();
                $("#current_lat").val(lat);
                $("#current_lng").val(lng);

            
            });
        }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"> </script> 
    @stop