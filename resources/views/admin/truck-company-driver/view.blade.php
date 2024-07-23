@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
      <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
         <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
               <h5 class="text-dark font-weight-bold my-1 mr-5">
               {{trans('messages.admin_common_View')}} {{trans("messages.admin_common_Driver")}}
               </h5>
               <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                  <li class="breadcrumb-item">
                     <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                  </li>
                  <li class="breadcrumb-item">
                     <a href="{{ route($model.'.index',($TCuserid? ["truck_id=".$TCuserid] :[]))}}" class="text-muted">
                        {{trans("messages.admin_common_Driver")}}
                     </a>
                  </li>
                  @if(request('from_page') != '' && request('from_page') == 'tc_edit')
                   <li class="breadcrumb-item">
                     <a href="{{ route('truck-company.edit', [base64_encode(request('truck_id')), 'tabs=driver_detail'])}}" class="text-muted">
                       {{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}
                     </a>
                  </li>
                  @elseif(request('from_page') != '' && request('from_page') == 'tc_view')
                  <li class="breadcrumb-item">
                     <a href="{{ route('truck-company.show', [base64_encode(request('truck_id')), 'tabs=driver_detail'])}}" class="text-muted">
                       {{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}
                     </a>
                  </li>
                  @endif

               </ul>
            </div>
         </div>
         @include("admin.elements.quick_links")
      </div>
   </div>
   <div class="d-flex flex-column-fluid">
      <div class=" container ">
         <div class="card card-custom gutter-b">
            <div class="card-header card-header-tabs-line">
               <div class="card-toolbar">
                  <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-bold nav-tabs-line-3x" role="tablist">
                     <li class="nav-item">
                        <a class="nav-link active hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_1">
                           <span class="nav-text">
                              {{trans("messages.admin_common_driver_information")}}
                           </span>
                        </a>
                     </li>
               </div>
            </div>
            <div class="card-body px-0">
               <div class="tab-content px-10">

                  <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Truck_Company")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ucwords($userDetails->TruckCompanyInformation->company_name ?? '')}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.name")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ucwords($userDetails->name ?? '')}}</span>
                        </div>
                     </div>
                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Email Address")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->email ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Phone Number")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->phone_number ?? ''}}</span>
                        </div>
                     </div>

                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Registered_On")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                                {{ date(config("Reading.date_format"),strtotime($userDetails->created_at)) }} </span>
                        </div>
                     </div>
                    
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Status")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              @if($userDetails->is_active == 1)
                              <span class="label label-lg label-light-success label-inline">{{trans("messages.admin_common_Activated")}}</span>
                              @else
                              <span class="label label-lg label-light-danger label-inline">{{trans("messages.admin_common_Deactivated")}}</span>
                              @endif
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_System_Id")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                                {{ $userDetails->system_id }} 
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Driver_Picture")}} :</label>
                        <div class="col-8">

                        <span class="form-control-plaintext font-weight-bolder">
                           @if (!empty($userDetails->userDriverDetail->driver_picture))
                              <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo Config('constants.DRIVER_PICTURE_PATH') . $userDetails->userDriverDetail->driver_picture; ?>">
                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{Config('constants.DRIVER_PICTURE_PATH').$userDetails->userDriverDetail->driver_picture }}" />
                              </a>
                           @elseif($userDetails->id == $userDetails->TruckCompanyInformation->user_id)
                              <a class="fancybox-buttons" data-fancybox-group="button" href="{{ $userDetails->TruckCompanyInformation ?->contact_person_picture }}">
                                 <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{ $userDetails->TruckCompanyInformation ?->contact_person_picture }}" />
                              </a>
                           @endif
                        </span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

@stop