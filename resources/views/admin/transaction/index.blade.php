@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans("messages.admin_common_transaction")}}
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
                                            <label>{{trans("messages.payment_type")}}</label>
                                            <select name="payment_type" class="form-control select2init paymentType" value="{{$searchVariable['payment_type'] ?? ''}}">
                                                <option value="">{{trans("messages.admin_All")}}</option>
                                                <option value="0" {{ isset($searchVariable['payment_type']) && $searchVariable['payment_type'] == '0' ? 'selected': '' }} >{{trans("messages.Free")}}</option>
                                                <option value="1" {{ isset($searchVariable['payment_type']) && $searchVariable['payment_type'] == '1' ? 'selected': '' }} >{{trans("messages.paid")}}</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 mb-lg-5 mb-6">
                                            <label>{{trans("messages.admin_common_Status")}}</label>
                                            <select name="status" class="form-control select2init" value="{{$searchVariable['status'] ?? ''}}">
                                                <option value="">{{trans("messages.admin_All")}}</option>
                                                <option value="pending" {{ isset($searchVariable['status']) && $searchVariable['status'] == 'pending' ? 'selected': '' }} >{{trans("messages.pending")}}</option>
                                                <option value="process" {{ isset($searchVariable['status']) && $searchVariable['status'] == 'process' ? 'selected': '' }} >{{trans("messages.process")}}</option>
                                                <option value="success" {{ isset($searchVariable['status']) && $searchVariable['status'] == 'success' ? 'selected': '' }} >{{trans("messages.Success")}}</option>
                                                <option value="failed"  {{ isset($searchVariable['status']) && $searchVariable['status']   == 'failed' ? 'selected': '' }} >{{trans("messages.failed")}}</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-lg-3 mb-lg-5 mb-6">
                                            <label>{{trans("messages.admin_transaction_id")}}</label>
                                            <input type="text" class="form-control" name="transaction_id" placeholder="{{trans("messages.admin_transaction_id")}}" value="{{$searchVariable['transaction_id'] ?? '' }}">
                                        </div>

                                        <div class="col-lg-3 mb-lg-5 mb-6">
                                            <label>{{trans("messages.admin_Truck_Company")}}</label>
                                            <input type="text" class="form-control" name="truck_company" placeholder="{{trans("messages.admin_Truck_Company")}}" value="{{$searchVariable['truck_company'] ?? '' }}">
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
                                            <div class="range-slider">
                                                <span class="d-flex">
                                                    <input type="number" name="minprice" class="form-control priceInput"   value="{{$searchVariable['minprice'] ?? ''}}" placeholder="{{trans('messages.admin_min_price')}}" min="0" max="1000" step="0.01"/>
                                                    <input type="number" name="maxprice" class="form-control priceInput" value="{{$searchVariable['maxprice'] ?? ''}}" min="0" max="1000" step="0.01" placeholder="{{trans('messages.admin_max_price')}}" />
                                                </span>
                                            <span class="errorMessage text-danger"></span>
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
                        <div class="dataTables_wrapper-fake-top-scroll">
                            <div>&nbsp;</div>
                        </div>
                        <div class="dataTables_wrapper ">
                            <div class="table-responsive table-responsive-new">
                                <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th class="{{(($sortBy == 'transaction_id' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'transaction_id' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                <a href="{{route($model.'.index',array(	'sortBy' => 'transaction_id','order' => ($sortBy == 'transaction_id' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_transaction_id")}}</a>
                                               </th>
                                               <th class="{{(($sortBy == 'truck_company_id' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_company_id' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                <a href="{{route($model.'.index',array(	'sortBy' => 'truck_company_id',
                                                   'order' => ($sortBy == 'truck_company_id' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_Truck_Company")}}</a>
                                               </th>
                                                <th class="{{(($sortBy == 'plan_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'plan_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                <a href="{{route($model.'.index',array( 'sortBy' => 'plan_name',
                                                   'order' => ($sortBy == 'plan_name' && $order == 'desc') ? 'asc' : 'desc', 
                                                   $query_string))}}">{{trans("messages.admin_plan_name")}}</a>
                                               </th>
                                               <th class="{{(($sortBy == 'company_subscription_plan_id' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'company_subscription_plan_id' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                <a href="{{route($model.'.index',array(	'sortBy' => 'company_subscription_plan_id',
                                                   'order' => ($sortBy == 'company_subscription_plan_id' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_common_Plan_Duration")}}</a>
                                               </th>
                                               <th class="{{(($sortBy == 'amount' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'amount' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                <a href="{{route($model.'.index',array(	'sortBy' => 'amount',
                                                   'order' => ($sortBy == 'amount' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.price")}}</a>
                                               </th>
                                               <th class="{{(($sortBy == 'status' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'status' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                <a href="{{route($model.'.index',array(	'sortBy' => 'status',
                                                   'order' => ($sortBy == 'status' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_common_Status")}}</a>
                                               </th>
                                                   </tr>
                                               </thead>
                                               <tbody>
                                                @if(!$results->isEmpty())
                                                @foreach($results as $result)
                                                <tr>
                                                   
                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                            {{ $result->transaction_id ?? '' }}
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">

                                                        <a href="{{ route('truck-company.show', base64_encode($result->truckCompanyName ?->id)) }}" target="_blank">
                                                            {{ $result->CompanyName ?->company_name ?? '---' }}
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
                                                        @php 
                                                        $typeData = '';

                                                            if ($result->plan_type == '0') {
                                                                $typeData = trans('messages.monthly');
                                                            } elseif ($result->plan_type == '1') {
                                                                $typeData = trans('messages.quarterly');
                                                            } elseif ($result->plan_type == '2') {
                                                                $typeData = trans('messages.Half Yearly');
                                                            } elseif ($result->plan_type == '3') {
                                                                $typeData = trans('messages.Yearly');
                                                            }

                                                            echo $typeData;
                                                            @endphp

                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                               @if($result->amount == 0)
                                                                 {{ trans('messages.Free') }}
                                                               @else
                                                                <img src="{{asset('public/img/plan-icon.png')}}" alt="" width="15px"> 
                                                                {{ $result->amount ?? '' }}
                                                               @endif
                                                        </div>
                                                    </td>

                                                    <td>
                                                        @if($result->status  == 'success')
                                                        <span class="label label-lg label-light-success label-inline">{{trans("messages.Success")}}</span>
                                                        @elseif($result->status  == 'pending')
                                                        <span class="label label-lg label-light-warning label-inline">{{trans("messages.pending")}}</span>
                                                        @elseif($result->status  == 'failed')
                                                        <span class="label label-lg label-light-danger label-inline">{{trans("messages.failed")}}</span>
                                                        @elseif($result->status  == 'process')
                                                        <span class="label label-lg label-light-warning label-inline">{{trans("messages.process")}}</span>
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

$(document).ready(function(){
    var FreeInput = "{{$searchVariable['payment_type'] ?? ''}}";

    if(FreeInput === '0'){
           $(".priceInput").val('');
           $(".priceInput").prop("disabled", true);
    }
    
    $(".paymentType").on("change", function(){
        if($(this).val() == 0){
           $(".priceInput").val('');
           $(".priceInput").prop("disabled", true);
        }else{
           $(".priceInput").prop("disabled", false);
        }
        if($(this).val() == ''){
            $(".priceInput").prop("disabled", false);
        }
    });
});

</script>
@stop