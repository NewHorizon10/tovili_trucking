@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<h5 class="text-dark font-weight-bold my-1 mr-5">
					{{trans("messages.Truck List")}}
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
			<form action="{{ route($model.'.all-truck-list')}}" method="get" clas="kt-form kt-form--fit mb-0" autocomplete="off">
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
									<a href='{{route($model.".truck_create", ['from_page' => 'truck_list'])}}' class="btn btn-primary mr-2">
										{{trans("messages.admin_Add_new_truck")}}
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
													<select name="is_active" class="form-control select2init" value="{{$searchVariable['is_active'] ?? ''}}">
														<option value="">{{trans("messages.admin_All")}}</option>
														<option value="1" {{ isset($searchVariable['is_active']) && $searchVariable['is_active'] == 1 ? 'selected': '' }}>{{trans("messages.admin_common_Activated")}}</option>
														<option value="0" {{ isset($searchVariable['is_active']) && $searchVariable['is_active'] == 0 ? 'selected': '' }}>{{trans("messages.admin_common_Deactivated")}}</option>
													</select>
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.Type_of_truck")}}</label>
                                                    <select name="truck_type" class="form-control select2init  @error('company_type') is-invalid @enderror" >
                                                        <option value="">{{trans("messages.admin_common_select_truck_type")}}</option>
                                                        @foreach($truckType as $key => $row)
                                                            <option value="{{$key}}" {{$searchVariable && isset($searchVariable['truck_type']) && $searchVariable['truck_type'] == $key ? 'selected' : '' }} >{{$row}}</option>
                                                        @endforeach
                                                    </select>
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.Truck Number")}}</label>
													<input type="text" name="truck_system_number" class="form-control" placeholder="{{trans("messages.Truck Number")}}" value="{{Request::all('truck_system_number')["truck_system_number"] ??  ''}}">
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.admin_Tidaluk_Company")}}</label>
													 <select name="company_tidaluk" class="form-control select2init  @error('company_type') is-invalid @enderror" >
                                                        <option value="">{{trans('messages.Select')}} {{trans("messages.admin_Tidaluk_Company")}}</option>
                                                        @foreach($tidalukCompanies as $value)
                                                            <option value="{{$value->parent_id}}" {{$searchVariable && isset($searchVariable['company_tidaluk']) && $searchVariable['company_tidaluk'] == $value->parent_id ? 'selected' : '' }} >{{$value->code}}</option>
                                                        @endforeach
                                                    </select>
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.refueling_method")}}</label>
													<select name="company_refueling" class="form-control select2init  @error('company_type') is-invalid @enderror" >
                                                        <option value="">{{trans('messages.Select')}} {{trans("messages.refueling_method")}}</option>
                                                        @foreach($fuelingMethods as $value)
                                                            <option value="{{$value->parent_id}}" {{$searchVariable && isset($searchVariable['company_refueling']) && $searchVariable['company_refueling'] == $value->parent_id ? 'selected' : '' }} >{{$value->code}}</option>
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
													<label>{{trans("messages.end_of_insurance_date_from")}}</label>
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
													<label>{{trans("messages.end_of_insurance_date_to")}}</label>
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
													<a href='{{ route($model.'.all-truck-list')}}' class="btn btn-secondary btn-secondary--icon">
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
													
													<th class="{{(($sortBy == 'type_of_truck' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'type_of_truck' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.all-truck-list',array(	'sortBy' => 'type_of_truck',
													'order' => ($sortBy == 'type_of_truck' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Type_of_truck")}}</a>
													</th>
                                                    <th class="{{(($sortBy == 'truck_system_number' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_system_number' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.all-truck-list',array(	'sortBy' => 'truck_system_number',
													'order' => ($sortBy == 'truck_system_number' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Truck Number")}}</a>
													</th>
													<th class="{{(($sortBy == 'company_tidaluk' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'company_tidaluk' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.all-truck-list',array(	'sortBy' => 'company_tidaluk',
													'order' => ($sortBy == 'company_tidaluk' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Company Name")}}</a>
													</th>

													<th
														class="{{(($sortBy == 'truck_drivers' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_drivers' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.all-truck-list',array(	'sortBy' => 'truck_drivers',
															'order' => ($sortBy == 'truck_drivers' && $order == 'desc') ? 'asc' : 'desc',	
															$query_string))}}">{{trans("messages.admin_Truck_Driver")}}</a>
													</th>
													<th
														class="{{(($sortBy == 'truck_insurance_expiration_date' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_insurance_expiration_date' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.all-truck-list',array(	'sortBy' => 'truck_insurance_expiration_date',
															'order' => ($sortBy == 'truck_insurance_expiration_date' && $order == 'desc') ? 'asc' : 'desc',	
															$query_string))}}">{{trans("messages.end_of_insurance_date")}}</a>
													</th>
													<th
														class="{{(($sortBy == 'truck_licence_expiration_date' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_licence_expiration_date' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.all-truck-list',array(	'sortBy' => 'truck_licence_expiration_date',
															'order' => ($sortBy == 'truck_licence_expiration_date' && $order == 'desc') ? 'asc' : 'desc',	
															$query_string))}}">{{trans("messages.end_of_license_date")}}</a>
													</th>
													<th class="{{(($sortBy == 'is_active' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'is_active' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.all-truck-list',array(	'sortBy' => 'is_active',
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
														<div class="text-dark-75 mb-1 font-size-lg">
															{{$result->name ?? '--'}}
														</div>
													</td>

													<td>
		                                                <div class="text-dark-75 mb-1 font-size-lg">
		                                                 	{{ $result->truck_insurance_expiration_date ? date(config("Reading.date_format"),strtotime($result->truck_insurance_expiration_date)) : "" }}
		                                                </div>
		                                            </td>
		                                            <td>
		                                                <div class="text-dark-75 mb-1 font-size-lg">
		                                                    {{ $result->truck_licence_expiration_date ? date(config("Reading.date_format"),strtotime($result->truck_licence_expiration_date)) : "" }}
		                                                </div>
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
														<a title="{{trans("messages.admin_common_Click_To_Deactivate")}}" href='{{route($model.".status_truck",array($result->id,1))}}' class="btn btn-icon btn-light btn-hover-danger btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Deactivate">
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
														<a title="{{trans("messages.admin_common_Click_To_Activate")}}" href='{{route($model.".status_truck",array($result->id,0))}}' class="btn btn-icon btn-light btn-hover-success btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Activate">
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
														<a href="{{route($model.'.edit_truck', [base64_encode($result->id), 'from_page' => 'truck_list'])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_Edit")}}">
															<span class="svg-icon svg-icon-md svg-icon-primary">
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
																		<path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
																	</g>
																</svg>
															</span>
														</a>
														<a href="{{route($model.'.delete_truck', [base64_encode($result->id), 'from_page' => 'truck_list'])}}" class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_Delete")}}">                            
														<span class="svg-icon svg-icon-md svg-icon-danger">
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero" />
																		<path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3" />
																	</g>
																</svg>
															</span>
														</a>

														<a target="_blank" href="{{route($model.'.show_truck',base64_encode($result->id))}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_View")}}">
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