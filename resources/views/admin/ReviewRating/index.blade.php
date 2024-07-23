@extends('admin.layouts.layout')
@section('content')

<link rel="stylesheet" href="{{asset('./public/js/sumoselect.min.css')}}">
<script src="{{ asset('./public/js/jquery.sumoselect.min.js')}} "></script>

<script type="text/javascript"> 
    
    jQuery(document).ready(function(){
        $(".question_category_id1").SumoSelect({
            search: true, 
            searchText: 'Enter Here',
            placeholder: 'Select Country',
        });
    });

</script>

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
                    {{trans("messages.review_rating")}} </h5>
                    <!--end::Page Title-->

                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
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
            {{ Form::open(['method' => 'get','role' => 'form','route' => ["$modelName.index"],'class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"]) }}
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
                                <a href="{{route('ReviewRating.export')}}" class="btn btn-primary">
                                        Export 
                                        
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
                                           
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>{{trans("messages.review_by")}}</label>
                                                {{ Form::text('user_name',((isset($searchVariable['user_name'])) ? $searchVariable['user_name'] : ''), ['class' => ' form-control','placeholder'=>trans("messages.review_by")]) }}
                                            </div>
                                           
                                            <div class="col-lg-4 mb-lg-5 mb-6">
                                                <label>{{trans("messages.Company Name")}}</label>
                                                {{ Form::text('company_name',((isset($searchVariable['company_name'])) ? $searchVariable['company_name'] : ''), ['class' => ' form-control','placeholder'=>trans("messages.Company Name")]) }}
                                            </div>
                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.admin_Date_From")}}</label>
                                                <div class="input-group date" id="datepickerfrom" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" name="date_from" placeholder="{{trans("messages.admin_Date_From")}}" data-target="#datepickerfrom" data-toggle="datetimepicker" value="{{ $searchVariable['date_from'] ?? '' }}">

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
                                                    <input type="text" class="form-control datetimepicker-input" name="date_to" placeholder="{{trans("messages.admin_Date_To")}}" data-target="#datepickerto" data-toggle="datetimepicker" value="{{ $searchVariable['date_to'] ?? '' }}">

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

                                                <a href='{{ route("$modelName.index")}}'
                                                    class="btn btn-secondary btn-secondary--icon">
                                                    <span>
                                                        <i class="la la-close"></i>
                                                        <span>{{trans("messages.admin_common_Clear_Search")}}</span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                        <!--begin: Datatable-->
                                        <hr>
                                    </div>
                                </div>
                            </div>
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
                                             
                                                <th class="{{(($sortBy == 'created_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'created_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.reviewed_on"),array('sortBy' => 'created_at','order' => ($sortBy == 'created_at' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
                                                </th>
                                                
                                                <th class="{{(($sortBy == 'customer_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'customer_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.review_by"),array('sortBy' => 'customer_name','order' => ($sortBy == 'customer_name' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
                                                </th>

                                                <th class="{{(($sortBy == 'truck_company_name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'truck_company_name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.reviewed_to"),array('sortBy' => 'truck_company_name','order' => ($sortBy == 'truck_company_name' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
                                                </th>
                                                <th class="{{(($sortBy == 'overall_rating' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'overall_rating' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.overall_rating"),array('sortBy' => 'overall_rating','order' => ($sortBy == 'overall_rating' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
                                                </th>
                                                <th class="{{(($sortBy == 'driver_rating' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'driver_rating' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.driver_rating"),array('sortBy' => 'driver_rating','order' => ($sortBy == 'overall_rating' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
                                                </th>
                                                <th class="{{(($sortBy == 'professionality' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'professionality' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.professionality"),array('sortBy' => 'professionality','order' => ($sortBy == 'professionality' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
                                                </th>
                                                <th class="{{(($sortBy == 'meet_schedule' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'meet_schedule' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.meet_schedule"),array('sortBy' => 'meet_schedule','order' => ($sortBy == 'meet_schedule' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
                                                </th>
                                                <th class="{{(($sortBy == 'review' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'review' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    {{
														link_to_route("$modelName.index",trans("messages.reviews"),array('sortBy' => 'review','order' => ($sortBy == 'review' && $order == 'desc') ? 'asc' : 'desc',
															$query_string))	}}
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
                                                    {{ date(config("Reading.date_format"),strtotime($result->created_at)) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{ $result->customer_name ?? '' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{$result->truck_company_name  }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        @if(!empty($result->overall_rating))

                                                        @for ($j = 1; $j <= 5; $j++)

                                                            @if ($j <= $result->overall_rating)
                                                                <i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
                                                            @else
                                                                <i aria-hidden="true" class="fa fa-star" style="color:black "></i>
                                                            @endif
                                                        @endfor

                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        @if(!empty($result->driver_rating))

                                                        @for ($j = 1; $j <= 5; $j++)

                                                            @if ($j <= $result->driver_rating)
                                                                <i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
                                                            @else
                                                                <i aria-hidden="true" class="fa fa-star" style="color:black "></i>
                                                            @endif
                                                        @endfor

                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        @if(!empty($result->professionality))

                                                        @for ($j = 1; $j <= 5; $j++)

                                                        @if ($j <= $result->professionality)
                                                        <i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
                                                        @else
                                                        <i aria-hidden="true" class="fa fa-star" style="color:black "></i>
                                                        @endif
                                                        @endfor

                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        @if(!empty($result->meet_schedule))

                                                        @for ($j = 1; $j <= 5; $j++)

                                                        @if ($j <= $result->meet_schedule)
                                                        <i aria-hidden="true" class="fa fa-star" style="color:orange "></i>
                                                        @else
                                                        <i aria-hidden="true" class="fa fa-star" style="color:black "></i>
                                                        @endif
                                                        @endfor

                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                @php
                                                    $desiredFieldArray = $result->getPhotos->map(function ($item) {
                                                        return $item->photo; 
                                                    })->toArray();

                                                @endphp
                                                <a href="javascript:void(0)" onclick="myodalOpen('{{ $result->review }}','{{json_encode($desiredFieldArray)}}')" >{{trans('messages.view_review')}} </a>
                                                  <div id="myModal" class="modal fade " role="dialog">
                                                     <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Reviews</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="myodalOpenReview" style="white-space: break-spaces;"></p>
                                                                <div class="myodalOpenReviewPhotos row"></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                       </div>
                                                     </div>
                                                    </div>
                                                </td>

                                                <td class="text-right pr-2">
                                                      
                                                        <a href='{{route("$modelName.show","$result->id")}}'
                                                                class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                                data-toggle="tooltip" data-placement="top"
                                                                data-container="body" data-boundary="window" title=""
                                                                data-original-title="{{trans('messages.admin_common_View')}}">
                                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24" />
                                                                            <path
                                                                                d="M12.8434797,16 L11.1565203,16 L10.9852159,16.6393167 C10.3352654,19.064965 7.84199997,20.5044524 5.41635172,19.8545019 C2.99070348,19.2045514 1.55121603,16.711286 2.20116652,14.2856378 L3.92086709,7.86762789 C4.57081758,5.44197964 7.06408298,4.00249219 9.48973122,4.65244268 C10.5421727,4.93444352 11.4089671,5.56345262 12,6.38338695 C12.5910329,5.56345262 13.4578273,4.93444352 14.5102688,4.65244268 C16.935917,4.00249219 19.4291824,5.44197964 20.0791329,7.86762789 L21.7988335,14.2856378 C22.448784,16.711286 21.0092965,19.2045514 18.5836483,19.8545019 C16.158,20.5044524 13.6647346,19.064965 13.0147841,16.6393167 L12.8434797,16 Z M17.4563502,18.1051865 C18.9630797,18.1051865 20.1845253,16.8377967 20.1845253,15.2743923 C20.1845253,13.7109878 18.9630797,12.4435981 17.4563502,12.4435981 C15.9496207,12.4435981 14.7281751,13.7109878 14.7281751,15.2743923 C14.7281751,16.8377967 15.9496207,18.1051865 17.4563502,18.1051865 Z M6.54364977,18.1051865 C8.05037928,18.1051865 9.27182488,16.8377967 9.27182488,15.2743923 C9.27182488,13.7109878 8.05037928,12.4435981 6.54364977,12.4435981 C5.03692026,12.4435981 3.81547465,13.7109878 3.81547465,15.2743923 C3.81547465,16.8377967 5.03692026,18.1051865 6.54364977,18.1051865 Z"
                                                                                fill="#000000" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                             <a href="{{ route($modelName . '.edit', base64_encode($result->id)) }}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="Edit">
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
                                                        
                                                        </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr><td colspan="6" style="text-align:center;">{{ trans("messages.admin_common_Record_not_found") }}</td></tr>
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
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<script>
$(document).ready(function() {
    $('#datepickerfrom').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#datepickerto').datetimepicker({
        format: 'YYYY-MM-DD'
    });

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

    $(".status_any_item").click(function(e) {
        e.stopImmediatePropagation();
        url = $(this).attr('href');
        Swal.fire({
            title: "Are you sure?",
            text: "Want to change status this ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, change it",
            cancelButtonText: "No, cancel",
            reverseButtons: true
        }).then(function(result) {
            if (result.value) {
                window.location.replace(url);
            }
        });
        e.preventDefault();
    });
});

function page_limit() {
    $("form").submit();
}

function myodalOpen(review,files){
    filesObj =  $.parseJSON(files);
    var photosObj = ``;
    for (let i = 0; i < filesObj.length; i++) {
        photosObj += `<div class=" text-dark-75 mb-3 font-size-lg col-md-3 col-4">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="${filesObj[i]}">
                            <img class="rating-popup-imgs" src="${filesObj[i]}">
                            </a>															
                    </div>`;
    }


    $('.myodalOpenReviewPhotos').html(photosObj);
    

    $('.myodalOpenReview').text(review);
    $('#myModal').modal('show');
    $('.magnific-image').magnificPopup({
                type: 'image'
            });
            $('.fancybox-buttons').magnificPopup({
                type: 'image'
            });
}
</script>

<style>
.label.label-inline.label-lg {
    padding: 1.1rem 0.75rem;
}
</style>

@stop