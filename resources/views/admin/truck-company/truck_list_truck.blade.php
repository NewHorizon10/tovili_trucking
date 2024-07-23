@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<h5 class="text-dark font-weight-bold my-1 mr-5">
					{{trans("messages.Truck Details")}}
					</h5>
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{route('truck-company.index')}}" class="text-muted">{{trans("messages.admin_Truck_Company")}}</a>
						</li>
					</ul>
				</div>
			</div>
			@include("admin.elements.quick_links")
		</div>
	</div>
	<div class="d-flex flex-column-fluid">
		<div class=" container ">
			<form action="{{ route($model.'.truck_list')}}" method="get" clas="kt-form kt-form--fit mb-0" autocomplete="off">
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
                                                            <option value="{{$row->id}}" {{$searchVariable && $searchVariable['truck_type'] == $row->id ? 'selected' : '' }} >{{$row->name}}</option>
                                                        @endforeach
                                                    </select>
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.Truck Number")}}</label>
													<input type="text" name="truck_system_number" class="form-control" placeholder="{{trans("messages.Truck Number")}}" value="{{Request::all('truck_system_number')["truck_system_number"] ??  ''}}">
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.admin_Tidaluk_Company")}}</label>
													<input type="text" name="company_tidaluk" class="form-control" placeholder="{{trans("messages.admin_Tidaluk_Company")}}" value="{{Request::all('company_tidaluk')["company_tidaluk"] ??  ''}}">
												</div>
												<div class="col-lg-3 mb-lg-5 mb-6">
													<label>{{trans("messages.refueling_method")}}</label>
													<input type="text" name="company_refueling" class="form-control" placeholder="{{trans("messages.refueling_method")}}" value="{{Request::all('refueling_method')["refueling_method"] ??  ''}}">
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
													<a href='{{ route($model.'.truck_list')}}' class="btn btn-secondary btn-secondary--icon">
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
														<a href="{{route($model.'.index',array(	'sortBy' => 'type_of_truck',
													'order' => ($sortBy == 'type_of_truck' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Type_of_truck")}}</a>
													</th>
                                                    <th class="{{(($sortBy == 'truck_system_number' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_system_number' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.index',array(	'sortBy' => 'truck_system_number',
													'order' => ($sortBy == 'truck_system_number' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Truck Number")}}</a>
													</th>
													<th class="{{(($sortBy == 'company_tidaluk' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'company_tidaluk' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.index',array(	'sortBy' => 'company_tidaluk',
													'order' => ($sortBy == 'company_tidaluk' && $order == 'desc') ? 'asc' : 'desc',	
													$query_string))}}"> {{trans("messages.Company Name")}}</a>
													</th>

													<th
														class="{{(($sortBy == 'truck_drivers' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_drivers' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.index',array(	'sortBy' => 'truck_drivers',
															'order' => ($sortBy == 'truck_drivers' && $order == 'desc') ? 'asc' : 'desc',	
															$query_string))}}">{{trans("messages.admin_Truck_Driver")}}</a>
													</th>
													<th
														class="{{(($sortBy == 'truck_insurance_expiration_date' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_insurance_expiration_date' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.index',array(	'sortBy' => 'truck_insurance_expiration_date',
															'order' => ($sortBy == 'truck_insurance_expiration_date' && $order == 'desc') ? 'asc' : 'desc',	
															$query_string))}}">{{trans("messages.end_of_insurance_date")}}</a>
													</th>
													<th
														class="{{(($sortBy == 'truck_licence_expiration_date' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_licence_expiration_date' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.index',array(	'sortBy' => 'truck_licence_expiration_date',
															'order' => ($sortBy == 'truck_licence_expiration_date' && $order == 'desc') ? 'asc' : 'desc',	
															$query_string))}}">{{trans("messages.end_of_license_date")}}</a>
													</th>
													<th class="{{(($sortBy == 'is_active' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'is_active' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
														<a href="{{route($model.'.index',array(	'sortBy' => 'is_active',
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
														
														<a href="{{route($model.'.show_truck',base64_encode($result->id))}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_View")}}">
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
@stop