@php
    use Carbon\Carbon;
@endphp
@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans('messages.refueling_method')}}
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

    <?php 
$createPermission   = functionCheckPermission("UsersController@create");
$viewPermission     = functionCheckPermission("UsersController@view");
$deletePermission   = functionCheckPermission("UsersController@delete");
$statusPermission   = functionCheckPermission("UsersController@changeStatus");
?>

    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            {{ Form::open(['method' => 'get','role' => 'form','route' => "$model.fueling-methods",'class' => 'kt-form kt-form--fit
            mb-0','autocomplete'=>"off"]) }}
            {{ Form::hidden('display') }}
            <div class="row">
                <div class="col-12">
                    <div class="card card-custom card-stretch card-shadowless">
                        <div class="card-header">
                            <div class="card-title">
                            </div>
                            <div class="card-toolbar">
                                <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2"
                                    data-toggle="collapse" data-target="#collapseOne6">
                                    {{trans("messages.admin_common_Search")}}
                                </a>

                                <a href='{{route("$model.create", ['from_page=fueling_company'])}}' class="btn btn-primary mr-2">
                                    {{trans("messages.add_new_truck_company")}}
                                </a>

                                <a href='{{route("$model.fueling-methods-export")}}' class="btn btn-primary">
                                    {{trans("messages.export")}}
                                </a>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                                <div id="collapseOne6"
                                    class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>"
                                    data-parent="#accordionExample6">
                                    <div>
                                        <div class="row mb-6">
                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.refueling_method")}}</label>
                                                <select name="refueling_method" class="form-control select2init" >
                                                    <option value="">{{trans("messages.admin_All")}}</option>
                                                    <option value="not_selected" {{($searchVariable['refueling_method'] ?? '') == "not_selected" ? "selected" : '' }}>{{trans('messages.not_selected')}}</option>
                                                    @foreach($refuelingMethodList as $refuelingMethod)
                                                        <option value="{{$refuelingMethod->id}}" {{($searchVariable['refueling_method'] ?? '') == $refuelingMethod->id ? "selected" : '' }}>{{$refuelingMethod->lookupDiscription->code}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.admin_System_Id")}}</label>
                                                <input type="text" class="form-control" name="system_id"
                                                    placeholder="{{trans('messages.admin_System_Id')}}"
                                                    value="{{$searchVariable['system_id'] ?? '' }}">
                                            </div>

                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.Company Name")}}</label>
                                                <input type="text" class="form-control" name="company_name"
                                                    placeholder="{{trans('messages.Company Name')}}"
                                                    value="{{$searchVariable['company_name'] ?? '' }}">
                                            </div>

                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans('messages.company_phone_number')}} </label>
                                                <input type="text" class="form-control" name="phone_number"
                                                    placeholder="{{trans('messages.company_phone_number')}}"
                                                    value="{{$searchVariable['phone_number'] ?? '' }}">
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
                                            <a href='{{ route("$model.fueling-methods")}}'
                                                class="btn btn-secondary btn-secondary--icon">
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
                        <a href="{{ route('truck.company.fueling.methods.notification') }}" class="btn btn-primary mb-2 sendNotificationButton @if(!empty(Session::get('refueling_methods_ids'))) @else d-none @endif">{{trans("messages.Select")}} {{trans("messages.Notifications")}}</a>
                        <div class="dataTables_wrapper-fake-top-scroll">
                            <div>&nbsp;</div>
                        </div>
                        <div class="dataTables_wrapper ">
                            <div class="table-responsive table-responsive-new">
                                <table
                                    class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center"
                                    id="taskTable">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th>
                                             <input type="checkbox" name="checkbox" class="subscriptionCheckbox" id="subscriptionCheckboxMain"  data-check-type="all-ids" value="allCheck" @if(!empty(Session::get('refueling_methods_ids'))) {{ (count(Session::get('refueling_methods_ids')) == $allResultCount) ? 'checked' : '' }} @endif>
                                            </th>
                                            <th
                                                class="{{(($sortBy == 'id' && $order == 'desc') ? '' : (($sortBy == 'id' && $order == 'asc') ? '' : ''))}}">
                                                <a href="{{route($model.'.fueling-methods',array(	'sortBy' => 'id',
                                                   'order' => ($sortBy == 'id' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_sys_id")}}</a>
                                            </th>

                                            <th
                                                class="{{(($sortBy == 'company_name' && $order == 'desc') ? '' : (($sortBy == 'company_name' && $order == 'asc') ? '' : ''))}}">
                                                <a href="{{route($model.'.fueling-methods',array(	'sortBy' => 'company_name',
                                                   'order' => ($sortBy == 'company_name' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.Company Name")}}</a>
                                            </th>

                                             <th
                                                class="{{(($sortBy == 'phone_number' && $order == 'desc') ? '' : (($sortBy == 'phone_number' && $order == 'asc') ? '' : ''))}}">
                                                <a href="{{route($model.'.fueling-methods',array( 'sortBy' => 'phone_number',
                                                   'order' => ($sortBy == 'phone_number' && $order == 'desc') ? 'asc' : 'desc', 
                                                   $query_string))}}">{{trans('messages.admin_common_Email')}}</a>
                                            </th>

                                             <th
                                                class="{{(($sortBy == 'phone_number' && $order == 'desc') ? '' : (($sortBy == 'phone_number' && $order == 'asc') ? '' : ''))}}">
                                                <a href="{{route($model.'.fueling-methods',array( 'sortBy' => 'phone_number',
                                                   'order' => ($sortBy == 'phone_number' && $order == 'desc') ? 'asc' : 'desc', 
                                                   $query_string))}}">{{trans("messages.admin_phone_number")}}</a>
                                            </th>

                                            <th class="{{(($sortBy == 'refueling_method' && $order == 'desc') ? '' : (($sortBy == 'refueling_method' && $order == 'asc') ? '' : ''))}}">
                                            <a href="{{route($model.'.fueling-methods',array( 'sortBy' => 'refueling_method',
                                                'order' => ($sortBy == 'refueling_method' && $order == 'desc') ? 'asc' : 'desc', 
                                                $query_string))}}">{{trans('messages.refueling_method')}}</a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!$results->isEmpty())
                                        @foreach($results as $result)
                                        <tr>
                                            <td>
                                              <input type="checkbox" name="send_notification" class="subscriptionCheckbox" data-check-type="ids" value="{{$result->id}}" @if(!empty(Session::get('refueling_methods_ids'))) {{ (in_array($result->id, Session::get('refueling_methods_ids'))) ? 'checked' : '' }} @endif>
                                            </td>
                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                    {{ $result->system_id ?? ''}}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                    {!! wordwrap($result->userCompanyInformation->company_name ?? '', 20, "<br>\n", true) !!}

                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                     {{ $result->email ?? ''}} 
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                    {{ $result->phone_number ?? '' }}
                                                </div>
                                            </td>
                                            <td>

                                                <div class="text-dark-75 mb-1 font-size-lg" {!! $result->userCompanyInformation ?->getCompanyRefuelingDescription == null ? 'style="color: #714b05 !important;"' : '' !!} >
                                                    {{ $result->userCompanyInformation ?->getCompanyRefuelingDescription ?->code ?? trans('messages.not_selected')}}
                                                </div>
                                            </td>

                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="6" style="text-align:center;"> 
                                                {{trans("messages.admin_common_Record_not_found")}}
                                            </td>
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




<script>
    $(document).ready(function() {

        var SessionArr = '@if(!empty(Session::get("refueling_methods_ids"))){{ Count(Session::get("refueling_methods_ids")) }} @endif';

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
                url: '{{ route("truck.company.fueling.methods.notification") }}',
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