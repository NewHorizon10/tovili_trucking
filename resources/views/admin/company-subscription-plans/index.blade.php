@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        {{trans("messages.company_subscription_plans")}}
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
            {{ Form::open(['method' => 'get','role' => 'form','route' => "$model.index",'class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"]) }}
            {{ Form::hidden('display') }}
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
                                <a href='{{route("$model.export")}}' class="btn btn-primary">
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
                                                <label>{{trans("messages.admin_common_Status")}}</label>
                                                <select name="status" class="form-control select2init" value="{{$searchVariable['status'] ?? ''}}">
                                                    <option value="">{{trans("messages.admin_All")}}</option>
                                                      <option value="pending" {{ isset($searchVariable['status']) && $searchVariable['status'] == 'pending' ? 'selected': '' }}>
                                                        {{trans("messages.pending")}}
                                                    </option>
                                                    <option value="activate" {{ isset($searchVariable['status']) && $searchVariable['status'] == 'activate' ? 'selected': '' }}>
                                                        {{trans("messages.admin_common_Activate")}}
                                                    </option>
                                                    <option value="deactivate" {{ isset($searchVariable['status']) && $searchVariable['status'] == 'deactivate' ? 'selected': '' }}>
                                                        {{trans("messages.admin_common_Deactivate")}}
                                                    </option>
                                                </select>
                                            </div>

                                             <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.admin_common_Is_Free")}}</label>
                                                <select name="is_free" class="form-control select2init" value="{{$searchVariable['is_free'] ?? ''}}">
                                                    <option value="">{{trans("messages.admin_All")}}</option>
                                                    <option value="0" {{ isset($searchVariable['is_free']) && $searchVariable['is_free'] == 0 ? 'selected': '' }}>
                                                        {{trans("messages.paid")}}
                                                    </option>
                                                    <option value="1" {{ isset($searchVariable['is_free']) && $searchVariable['is_free'] == 1 ? 'selected': '' }}>
                                                        {{trans("messages.Free")}}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.admin_Truck_Company")}} {{trans('messages.name')}}</label>
                                                <input type="text" class="form-control" name="truck_company_name" placeholder="{{trans("messages.admin_Truck_Company")}} {{trans('messages.name')}}" value="{{$searchVariable['truck_company_name'] ?? '' }}">
                                            </div>
                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                            <label>{{trans("messages.admin_common_Plan_Duration")}}</label>

                                            <select name="plan_duration" class="form-control select2init" value="{{$searchVariable['plan_duration'] ?? ''}}">
                                                <option value="">{{trans("messages.admin_All")}}</option>
                                                <option value="0" {{ isset($searchVariable['plan_duration']) && $searchVariable['plan_duration'] == '0' ? 'selected': '' }}>{{ trans('messages.monthly') }}</option>
                                                <option value="1" {{ isset($searchVariable['plan_duration']) && $searchVariable['plan_duration'] == '1' ? 'selected': '' }}>{{ trans('messages.quarterly') }}</option>
                                                <option value="2" {{ isset($searchVariable['plan_duration']) && $searchVariable['plan_duration'] == '2' ? 'selected': '' }}>{{ trans('messages.half_yearly') }}</option>
                                                <option value="3" {{ isset($searchVariable['plan_duration']) && $searchVariable['plan_duration'] == '3' ? 'selected': '' }}>{{ trans('messages.Yearly') }}</option>
                                            </select>

                                        </div>
                                        <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.admin_plan_name")}}</label>
                                                <input type="text" class="form-control" name="plan_name" placeholder="{{trans("messages.admin_plan_name")}}" value="{{$searchVariable['plan_name'] ?? '' }}">
                                            </div>
                                             <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.price")}}</label>
                                                <input type="text" class="form-control" name="price" placeholder="{{trans("messages.price")}}" value="{{$searchVariable['price'] ?? '' }}">
                                            </div>
                                             <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.discount")}}</label>
                                                <input type="text" class="form-control" name="discount" placeholder="{{trans("messages.discount")}}" value="{{$searchVariable['discount'] ?? '' }}">
                                            </div>
                                             <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.total_price")}}</label>
                                                <input type="text" class="form-control" name="total_price" placeholder="{{trans("messages.total_price")}}" value="{{$searchVariable['total_price'] ?? '' }}">
                                            </div>



                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>Date From</label>
                                                <div class="input-group date" id="datepickerfrom" data-target-input="nearest">
                                                    {{ Form::text('date_from',((isset($searchVariable['date_from'])) ? $searchVariable['date_from'] : ''), ['class' => ' form-control datetimepicker-input','placeholder'=>'Date To','data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker']) }}
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            <i class="ki ki-calendar"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>Date To</label>
                                                <div class="input-group date" id="datepickerto" data-target-input="nearest">
                                                    {{ Form::text('date_to',((isset($searchVariable['date_to'])) ? $searchVariable['date_to'] : ''), ['class' => ' form-control  datetimepicker-input','placeholder'=>'Date To','data-target'=>'#datepickerto','data-toggle'=>'datetimepicker']) }}
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
                                                <a href='{{ route("$model.index")}}' class="btn btn-secondary btn-secondary--icon">
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
                            <a href="{{ route('truck.company.subscription.plans.notification') }}" class="btn btn-primary mb-2 sendNotificationButton @if(!empty(Session::get('company_subscription_plans_ids'))) @else d-none @endif">{{trans("messages.Select")}} {{trans("messages.Notifications")}}</a>

                            <div class="dataTables_wrapper-fake-top-scroll">
                                <div>&nbsp;</div>
                            </div>
                            <div class="dataTables_wrapper ">
                                <div class="table-responsive table-responsive-new">
                                    <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
                                        <thead>
                                            <tr class="text-uppercase">
                                                <th>
                                                     <input type="checkbox" name="checkbox" class="subscriptionCheckbox" id="subscriptionCheckboxMain"  data-check-type="all-ids" value="allCheck" @if(!empty(Session::get('company_subscription_plans_ids'))) {{ (count(Session::get('company_subscription_plans_ids')) == $allResultCount) ? 'checked' : '' }} @endif>
                                                    </th>
                                              

                                                <th class="{{(($sortBy == 'truck_company_id' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_company_id' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route($model.'.index',array(	'sortBy' => 'truck_company_id',
                                                   'order' => ($sortBy == 'truck_company_id' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_Truck_Company")}} {{trans('messages.name')}}</a>
                                                </th>

                                                  <th class="{{(($sortBy == 'plan_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'plan_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route($model.'.index',array( 'sortBy' => 'plan_name',
                                                   'order' => ($sortBy == 'plan_name' && $order == 'desc') ? 'asc' : 'desc', 
                                                   $query_string))}}">{{trans("messages.admin_plan_name")}}</a>
                                                </th>
 
                                                <th class="{{(($sortBy == 'type' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'type' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route($model.'.index',array(	'sortBy' => 'type',
                                                   'order' => ($sortBy == 'type' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_common_Plan_Duration")}}</a>
                                                </th>
                                                  <th class="{{(($sortBy == 'price' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'price' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route($model.'.index',array( 'sortBy' => 'price',
                                                   'order' => ($sortBy == 'price' && $order == 'desc') ? 'asc' : 'desc',  
                                                   $query_string))}}">{{trans("messages.price")}}</a>
                                                </th>
                                                <th class="{{(($sortBy == 'discount' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'discount' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                <a href="{{route($model.'.index',array(	'sortBy' => 'discount',
                                                           'order' => ($sortBy == 'discount' && $order == 'desc') ? 'asc' : 'desc',	
                                                           $query_string))}}">{{trans("messages.discount")}} (%)</a>
                                                </th> 
                                                <th class="{{(($sortBy == 'total_price' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'total_price' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route($model.'.index',array(	'sortBy' => 'total_price',
                                                           'order' => ($sortBy == 'total_price' && $order == 'desc') ? 'asc' : 'desc',	
                                                           $query_string))}}">{{trans("messages.total_price")}}</a>
                                                </th>
                                                <th class="{{(($sortBy == 'is_free' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'is_free' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route($model.'.index',array( 'sortBy' => 'is_free',
                                                           'order' => ($sortBy == 'is_free' && $order == 'desc') ? 'asc' : 'desc',    
                                                           $query_string))}}">{{trans("messages.admin_common_Is_Free")}}</a>
                                                </th>
                                                 <th class="{{(($sortBy == 'end_time' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'end_time' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route($model.'.index',array( 'sortBy' => 'end_time',
                                                           'order' => ($sortBy == 'end_time' && $order == 'desc') ? 'asc' : 'desc',    
                                                           $query_string))}}">{{trans("messages.end_time")}}</a>
                                                </th>
                                                <th class="text-right">{{trans("messages.admin_common_Action")}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!$results->isEmpty())
                                            @foreach($results as $result)
                                            <tr>
                                                <td>
                                                      <input type="checkbox" name="send_notification" class="subscriptionCheckbox" data-check-type="ids" value="{{$result->truck_company_id}}" @if(!empty(Session::get('company_subscription_plans_ids'))) {{ (in_array($result->truck_company_id, Session::get('company_subscription_plans_ids'))) ? 'checked' : '' }} @endif>
                                                    </td>
                                                <td>
                                                   <div class="text-dark-75 mb-1 font-size-lg">
                                                    <a href="{{route('truck-company.show', base64_encode($result->companyName ?->user_id))}}" target="_blank">
                                                       {{$result->companyName ?->company_name}}
                                                    </a>
                                                    </div>
                                                </td>
                                                  <td>
                                                   <div class="text-dark-75 mb-1 font-size-lg">
                                                   {{ $result->plan_name ?? '' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                    @if($result->type =='0')
                                                        {{trans("messages.monthly")}}
                                                    @elseif($result->type =='1') 
                                                        {{trans("messages.quarterly")}}
                                                    @elseif($result->type =='2')
                                                        {{trans("messages.half_yearly")}} 
                                                    @else
                                                        {{trans("messages.Yearly")}}
                                                    @endif
                                                    </div>
                                                </td>


                                                <td>
                                                   <div class="text-dark-75 mb-1 font-size-lg">
                                                       {{$result->price ?? '--'}}
                                                    </div>
                                                </td>
                                                <td>
                                                      <div class="text-dark-75 mb-1 font-size-lg">
                                                       {{$result->discount ?? '--'}}
                                                    </div>
                                                </td>
                                                <td>
                                                      <div class="text-dark-75 mb-1 font-size-lg">
                                                       {{$result->total_price ?? '--'}}
                                                      </div>
                                                </td>

                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        @if($result->is_free == 0)
                                                          {{trans('messages.paid')}}
                                                        @else 
                                                          {{trans('messages.Free')}}
                                                        @endif
                                                    </div>
                                                </td>

                                              <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                       {{date(Config('Reading.date_format'), strtotime($result->end_time)) ?? '--'}}
                                                    </div>
                                              </td> 
                                            <td>
                                                
                                                @if($result->status == 'activate')
                                                <span class="label label-lg label-light-success label-inline">{{trans("messages.admin_common_Activated")}}</span>
                                                @elseif($result->status == 'deactivate')
                                                <span class="label label-lg label-light-danger label-inline">{{trans("messages.admin_common_Deactivated")}}</span>
                                                @else 
                                                <span class="label label-lg label-light-warning label-inline">{{trans("messages.pending")}}</span>
                                                @endif
                                            
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
        {{ Form::close() }}
    </div>
</div>
</div>



<script>
    $(document).ready(function() {

        var SessionArr = '@if(!empty(Session::get("truck_insurance_ids"))){{ Count(Session::get("truck_insurance_ids")) }} @endif';

        if($('#subscriptionCheckboxMain').is(":checked")){
            $(".sendNotificationButton").removeClass("d-none");
        }else{
            $(".sendNotificationButton").addClass("d-none");
        }
        if($('.subscriptionCheckbox').on("change", function(){
            if($(this).data("check-type") == "ids" && $(this).is(":checked")){
                $(".sendNotificationButton").removeClass("d-none");
            }else{
                var checkedCheckboxes = $('.subscriptionCheckbox:checked').length;
                if(checkedCheckboxes <= 0){
                    $(".sendNotificationButton").addClass("d-none");
                }
            }
        }));

        if($('.subscriptionCheckbox:checked').length > 0){
                $(".sendNotificationButton").removeClass("d-none");
        }else if(SessionArr > 0){
                $(".sendNotificationButton").removeClass("d-none");
        }

        var allResultCount = '{{ $allResultCount ? $allResultCount : 0 }}';

        $('.subscriptionCheckbox').on('change', function() {
            var checkType = "id";
            var id = "";
            var idSelected = "";
            if($(this).data("check-type") == "all-ids"){
                if ($(this).is(":checked")) {
                    checkType = "allIdsSelected" ;
                    $(".sendNotificationButton").removeClass("d-none");
                } else {
                    checkType = "allIdsNotSelected" ;
                    $(".sendNotificationButton").addClass("d-none");
                }
            }else if($(this).data("check-type") == "ids"){
                id = $(this).attr("value");
                
                if ($(this).is(":checked")) {
                    idSelected = "IdSelected"
                } else {
                    idSelected = "IdNotSelected"
                }
            }

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var searchVariable = '{{ $searchVariable ? json_encode($searchVariable) : json_encode([])}}';
            // Send data via AJAX
            $.ajax({
                type: 'POST',
                url: '{{ route("truck.company.subscription.plans.notification") }}',
                data: {
                    checkType: checkType,
                    id: id,
                    idSelected: idSelected,
                    searchVariable:searchVariable,
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken 
                },
                
                success: function(response) {
                    if(response.status){
                        if(response.selected == "allIdsSelected" ){
                            $(".subscriptionCheckbox").prop("checked", true);
                        }else if(checkType == "allIdsNotSelected" ){
                            $(".subscriptionCheckbox").prop("checked", false);
                        }else if(response.allCount == allResultCount){
                            $("#subscriptionCheckboxMain").prop("checked", true);
                        }else if(response.allCount != allResultCount){
                            $("#subscriptionCheckboxMain").prop("checked", false);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@stop