@extends('admin.layouts.layout')
@section('content')

<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<!--begin::Page Title-->
					<h5 class="text-dark font-weight-bold my-1 mr-5">
					{{trans("messages.view_review_rating")}} </h5>
					<!--end::Page Title-->

					<!--begin::Breadcrumb-->
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{ route($modelName.'.index')}}" class="text-muted">{{trans("messages.review_rating")}}</a>
						</li>
					</ul>
					<!--end::Breadcrumb-->
				</div>
				<!--end::Page Heading-->
			</div>
			<!--end::Info-->

			@include("admin.elements.quick_links")
		</div>
	</div>
	<!--end::Subheader-->

	<!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<!--begin::Container-->
		<div class=" container ">
			<div class="card card-custom gutter-b">
				<!--begin::Header-->
				<div class="card-header card-header-tabs-line">
					<div class="card-toolbar">
						<ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-bold nav-tabs-line-3x" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab" href="#kt_apps_contacts_view_tab_1">
									<span class="nav-text">
									{{trans("messages.review_rating_information")}}
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<!--end::Header-->

				<!--begin::Body-->
				<div class="card-body px-0">
					<div class="tab-content px-10">
						<!--begin::Tab Content-->
						<div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">

							<div class="form-group row my-2">
								<label class="col-4 col-form-label"> {{trans("messages.request_number")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									{{ $modelDetails->shipmentDetails->request_number ?? '' }}
								</span>
								</div>
							</div>
							<div class="form-group row my-2">
								<label class="col-4 col-form-label"> {{trans("messages.admin_customer_type")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									{{ ucfirst($modelDetails->getUser ?->customer_type) ?? '' }}
								</span>
								</div>
							</div>
							<div class="form-group row my-2">
								<label class="col-4 col-form-label"> {{trans("messages.review_by")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									{{ $modelDetails->getUser->name ?? '' }}
								</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label"> {{trans("messages.reviewed_to")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">

									{{ $modelDetails->getTruckCompany->name ?? '' }}
								</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">{{trans("messages.reviews")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									{{ $modelDetails->review ?? '' }}
								</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label"> {{trans("messages.shipment_date")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									{{ date(Config('Reading.date_format'), strtotime($modelDetails->shipmentDetails ?->request_date)) ?? '' }}
								</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">{{trans("messages.overall_rating")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									  @if(!empty($modelDetails->overall_rating))
										@for ($j = 1; $j <= 5; $j++)
										@if ($j <= $modelDetails->overall_rating)
										<i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
										@else
										<i aria-hidden="true" class="fa fa-star" style="color:black "></i>
										@endif
										@endfor
									   @endif
								</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">{{trans("messages.driver_rating")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									  @if(!empty($modelDetails->driver_rating))
										@for ($j = 1; $j <= 5; $j++)
										@if ($j <= $modelDetails->driver_rating)
										<i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
										@else
										<i aria-hidden="true" class="fa fa-star" style="color:black "></i>
										@endif
										@endfor
									   @endif
								</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">{{trans("messages.professionality")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									@if(!empty($modelDetails->professionality))
										@for ($j = 1; $j <= 5; $j++)
										@if ($j <= $modelDetails->professionality)
										<i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
										@else
										<i aria-hidden="true" class="fa fa-star" style="color:black "></i>
										@endif
										@endfor
									@endif
								</span>
								</div>
							</div>

							<div class="form-group row my-2">
								<label class="col-4 col-form-label">{{trans("messages.meet_schedule")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									@if(!empty($modelDetails->meet_schedule))
										@for ($j = 1; $j <= 5; $j++)
										@if ($j <= $modelDetails->meet_schedule)
										<i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
										@else
										<i aria-hidden="true" class="fa fa-star" style="color:black "></i>
										@endif
										@endfor
									@endif
								</span>
								</div>
							</div>



							<div class="form-group row my-2">
								<label class="col-4 col-form-label">{{trans("messages.reviewed_on")}}:</label>
								<div class="col-8">
									<span class="form-control-plaintext font-weight-bolder">
									{{ date(config("Reading.date_format"),strtotime($modelDetails->created_at)) }}
									</span>
								</div>
							</div>

							
							
						</div>

						<!--end::Tab Content-->
					</div>
				</div>
				<!--end::Body-->
			</div>

		</div>
		<!--end::Container-->
	</div>
	<!--end::Entry-->
</div>
<!--end::Content-->
<script>
	$(".confirmDelete").click(function(e) {
		e.stopImmediatePropagation();
		url = $(this).attr('href');
		Swal.fire({
			title: "Are you sure?",
			text: "Want to delete this ?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, delete it",
			cancelButtonText: "No, cancel",
			reverseButtons: true
		}).then(function(result) {
			if (result.value) {
				window.location.replace(url);
			} else if (result.dismiss === "cancel") {
				Swal.fire(
					"Cancelled",
					"Your imaginary file is safe :)",
					"error"
				)
			}
		});
		e.preventDefault();
	});
</script>
@stop