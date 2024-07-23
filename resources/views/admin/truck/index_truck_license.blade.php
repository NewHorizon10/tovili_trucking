@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<h5 class="text-dark font-weight-bold my-1 mr-5">
					{{trans("messages.Truck License List")}}
					</h5>
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
						</li>
					</ul>
				</div>
			</div>
			@include("admin.elements.quick_links")
		</div>
	</div>
	<div class="d-flex flex-column-fluid">
		<div class=" container ">
			<form action="{{ route($model.'.license.index')}}" method="get" clas="kt-form kt-form--fit mb-0" autocomplete="off">
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
									<a href='{{route("$model.license.export")}}' class="btn btn-primary">
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
													<label>{{trans("messages.Type_of_truck")}}</label>
                                                    <select name="truck_type" class="form-control form-control-solid form-control-lg  @error('company_type') is-invalid @enderror" >
                                                        <option value="">{{trans("messages.admin_common_select_truck_type")}}</option>
                                                        @foreach($truckType as $row)
                                                            <option value="{{$row->id}}" {{$searchVariable && ($searchVariable['truck_type'] ?? '') == $row->id ? 'selected' : '' }} >{{$row->name}}</option>
                                                        @endforeach
                                                    </select>
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.Type_of_truck_expiry")}}</label>
                                                    <select name="select_truck_expiry_type" class="form-control form-control-solid form-control-lg  @error('company_type') is-invalid @enderror" >
                                                        <option value="">{{trans("messages.select_truck_expiry_type")}}</option>
														<option value="expired_truck_license" {{$searchVariable && ($searchVariable['select_truck_expiry_type'] ?? '') == "expired_truck_license" ? 'selected' : '' }} >{{trans('messages.expired_truck_license')}}</option>
														<option value="near_to_expiry_truck_license" {{$searchVariable && ($searchVariable['select_truck_expiry_type'] ?? '') == "near_to_expiry_truck_license" ? 'selected' : '' }} >{{trans('messages.near_to_expiry_truck_license')}}</option>
                                                    </select>
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.Truck Number")}}</label>
													<input type="text" name="truck_system_number" class="form-control" placeholder="{{trans("messages.Truck Number")}}" value="{{Request::all('truck_system_number')["truck_system_number"] ??  ''}}">
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.Company Name")}}</label>
													<input type="text" name="company_name" class="form-control" placeholder="{{trans("messages.Company Name")}}" value="{{Request::all('company_name')["company_name"] ??  ''}}">
												</div>
												 <div class="col-lg-3 mb-lg-5 mb-6">
		                                            <label>Date From</label>
		                                            <div class="input-group date" id="datepickerfrom" data-target-input="nearest">
		                                                {{ Form::text('date_from',((isset($searchVariable['date_from'])) ? $searchVariable['date_from'] : ''), ['class' => ' form-control datetimepicker-input','placeholder'=>'Date From','data-target'=>'#datepickerfrom','data-toggle'=>'datetimepicker']) }}
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
													<a href='{{ route($model.'.license.index')}}' class="btn btn-secondary btn-secondary--icon">
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

								<a href="{{ route('truck.licence.notification') }}" class="btn btn-primary mb-2 sendNotificationButton @if(!empty(Session::get('licence_expiry_ids'))) @else d-none @endif">{{trans("messages.Select")}} {{trans("messages.Notifications")}}</a>

                                <div class="dataTables_wrapper-fake-top-scroll">
                                    <div>&nbsp;</div>
                                </div>
								<div class="dataTables_wrapper ">
									<div class="table-responsive table-responsive-new">
										<table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
											<thead>
												<tr class="text-uppercase">
													<th>
                                                 	 <input type="checkbox" name="checkbox" class="subscriptionCheckbox" id="subscriptionCheckboxMain"  data-check-type="all-ids" value="allCheck" @if(!empty(Session::get('licence_expiry_ids'))) {{ (count(Session::get('licence_expiry_ids')) == $allResultCount) ? 'checked' : '' }} @endif>
                                             	 	</th>
													<th class="{{(($sortBy == 'type_of_truck' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'type_of_truck' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.license.index',array(	'sortBy' => 'type_of_truck',
													'order' => ($sortBy == 'type_of_truck' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Type_of_truck")}}</a>
													</th>
                                                    <th class="{{(($sortBy == 'truck_system_number' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_system_number' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.license.index',array(	'sortBy' => 'truck_system_number',
													'order' => ($sortBy == 'truck_system_number' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Truck Number")}}</a>
													</th>
													<th class="{{(($sortBy == 'company_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'company_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.license.index',array(	'sortBy' => 'company_name',
													'order' => ($sortBy == 'company_name' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Company Name")}}</a>
													</th>
													<th
														class="{{(($sortBy == 'truck_licence_expiration_date' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_licence_expiration_date' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.license.index',array(	'sortBy' => 'truck_licence_expiration_date',
															'order' => ($sortBy == 'truck_licence_expiration_date' && $order == 'desc') ? 'asc' : 'desc',	
															$query_string))}}">{{trans("messages.end_of_license_date")}}</a>
													</th>
													<th class="{{(($sortBy == 'is_active' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'is_active' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.license.index',array(	'sortBy' => 'is_active',
													'order' => ($sortBy == 'is_active' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}">{{trans("messages.admin_common_Status")}} </a>
													</th>
													<th class="text-right">{{trans("messages.admin_common_Action")}}</th>
												</tr>
											</thead>
											<tbody>
												@if(!$results->isEmpty())
												@foreach($results as $result)
												<tr>
													<td>
	                                                   <input type="checkbox" name="send_notification" class="subscriptionCheckbox" data-check-type="ids" value="{{$result->id}}" @if(!empty(Session::get('licence_expiry_ids'))) {{ (in_array($result->id, Session::get('licence_expiry_ids'))) ? 'checked' : '' }} @endif>
	                                                </td>
                                                    <td>
														<div class="text-dark-75 mb-1 font-size-lg">
															{{ $result->type_of_truck }}
														</div>
													</td>
													<td>
														<div class="text-dark-75 mb-1 font-size-lg">
															{{ $result->truck_system_number }}
														</div>
													</td>
                                                    <td>
														<div class="text-dark-75 mb-1 font-size-lg">
															{{ $result->company_name ?? '' }}
														</div>
													</td>
                                                    <td>
														@if($result->truck_licence_expiration_date == null || ($result->truck_licence_expiration_date != null && \Carbon\Carbon::parse($result->truck_licence_expiration_date)->lte(now())  ))
															<div class="text-danger mb-1 font-size-lg">
																{{ $result->truck_licence_expiration_date ? date(config("Reading.date_format"),strtotime($result->truck_licence_expiration_date)) : "" }}
															</div>
															<span class="label label-lg label-light-danger label-inline">{{trans("messages.admin_common_Expired")}}</span>
															@else
															<div class="text-dark-75 mb-1 font-size-lg">
																{{ $result->truck_licence_expiration_date ? date(config("Reading.date_format"),strtotime($result->truck_licence_expiration_date)) : "" }}
															</div>
														@endif
		                                            </td>
													
													
													<td>
														@if($result->is_active == 1)
														<span class="label label-lg label-light-success label-inline">{{trans("messages.admin_common_Activated")}}</span>
														@else
														<span class="label label-lg label-light-danger label-inline">{{trans("messages.admin_common_Deactivated")}}</span>
														@endif
													</td> 
													<td class="text-right pr-2">
														@if($result->is_active == 1)
                                                        <a title="{{trans("messages.admin_common_Click_To_Deactivate")}}" href='{{route("truck-company.status_truck",array($result->id,1))}}' class="btn btn-icon btn-light btn-hover-danger btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Deactivate">
                                                            <span class="svg-icon svg-icon-md svg-icon-danger">
                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                        <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                                                                            <rect x="0" y="7" width="16" height="2" rx="1" />
                                                                            <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1" />
                                                                        </g>
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        @else
                                                        <a title="{{trans("messages.admin_common_Click_To_Activate")}}" href='{{route("truck-company.status_truck",array($result->id,0))}}' class="btn btn-icon btn-light btn-hover-success btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Activate">
                                                            <span class="svg-icon svg-icon-md svg-icon-success">
                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) " />
                                                                        <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) " />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                        @endif
													</td>
												</tr>
												@endforeach
												@else
												<tr>
													<td colspan="6" style="text-align:center;"> {{trans("messages.admin_Data_Not_Found")}}</td>
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
    $(document).ready(function() {

    	var SessionArr = '@if(!empty(Session::get("licence_expiry_ids"))){{ Count(Session::get("licence_expiry_ids")) }} @endif';

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
                url: '{{ route("truck.licence.notification") }}',
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