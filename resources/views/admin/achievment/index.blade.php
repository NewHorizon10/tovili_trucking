@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans("messages.admin_common_Achievments")}}
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
    $createPermission   = functionCheckPermission("FrontPagesController@achievmentAdd");
    $deletePermission   = functionCheckPermission("FrontPagesController@achievmentDelete");
    ?>

    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            {{ Form::open(['method' => 'get','role' => 'form','route' => "achievment.index",'class' => 'kt-form kt-form--fit mb-0','autocomplete'=>"off"]) }}
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
                                <a href='{{route("achievment.add")}}' class="btn btn-primary">
                                {{trans("messages.admin_common_Add_New")}} {{trans("messages.admin_common_Achievment")}}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                                <div id="collapseOne6" class="collapse <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
                                    <div>
                                        <div class="row mb-6">
                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.name")}}</label>
                                                <input type="text" class="form-control" name="name" placeholder="{{trans("messages.name")}}" value="{{$searchVariable['name'] ?? '' }}">
                                            </div>

                                            <div class="col-lg-3 mb-lg-5 mb-6">
                                                <label>{{trans("messages.description")}}</label>
                                                <input type="text" class="form-control" name="description" placeholder="{{trans("messages.description")}}" value="{{$searchVariable['description'] ?? '' }}">
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
                                                <a href='{{ route("achievment.index")}}' class="btn btn-secondary btn-secondary--icon">
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

                                                <th class="{{(($sortBy == 'name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route('achievment.index',array(	'sortBy' => 'name','order' => ($sortBy == 'name' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.admin_common_Icon")}}</a>
                                                </th>
                                                <th class="{{(($sortBy == 'name' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'name' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route('achievment.index',array(	'sortBy' => 'name','order' => ($sortBy == 'name' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.name")}}</a>
                                                </th>


                                                <th class="{{(($sortBy == 'id' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'id' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route('achievment.index',array(	'sortBy' => 'id',
                                                   'order' => ($sortBy == 'id' && $order == 'desc') ? 'asc' : 'desc',	
                                                   $query_string))}}">{{trans("messages.description")}}</a>
                                                </th>

                                                <th class="{{(($sortBy == 'created_at' && $order == 'desc') ? 'sorting_desc' : (($sortBy == 'created_at' && $order == 'asc') ? 'sorting_asc' : 'sorting'))}}">
                                                    <a href="{{route('achievment.index',array(	'sortBy' => 'created_at',
                                                    'order' => ($sortBy == 'created_at' && $order == 'desc') ? 'asc' : 'desc',	
                                                    $query_string))}}">{{trans("messages.admin_Created_On")}}</a>
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
                                                        <a class="fancybox-buttons" data-fancybox-group="button" href="{{url(Config('constants.ACHIEVMENT_IMAGE_PATH').$result->image)}}">
                                                            <img height="50" width="50" src="{{url(Config('constants.ACHIEVMENT_IMAGE_PATH').$result->image)}}" />
                                                        </a>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{ $result->name  ?? ''}}
                                                    </div>
                                                </td>


                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{ $result->description  ?? ''}}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="text-dark-75 mb-1 font-size-lg">
                                                        {{ date(config("Reading.date_format"),strtotime($result->created_at)) }}
                                                    </div>
                                                </td>

                                                <td class="text-right pr-2">

                                                    <a href="{{route('achievment.edit',array(base64_encode($result->id)))}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_Edit')}}">
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

                                                    <a href="{{route('achievment.delete',array(base64_encode($result->id)))}}" class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_Delete')}}">
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
@stop