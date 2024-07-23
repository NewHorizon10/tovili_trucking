@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
      <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
         <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
               <h5 class="text-dark font-weight-bold my-1 mr-5">
                  View  Private  {{Config('constants.CUSTOMER.CUSTOMERS_TITLES')}}
               </h5>
               <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                  <li class="breadcrumb-item">
                     <a href="{{ route('dashboard')}}" class="text-muted">Dashboard</a>
                  </li>
                  <li class="breadcrumb-item">
                     <a href="{{ route($model.'.index')}}" class="text-muted">    
                     Private {{Config('constants.CUSTOMER.CUSTOMERS_TITLES')}}</a>
                  </li>
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
                           {{Config('constants.CUSTOMER.CUSTOMERS_TITLES')}} Information
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_2">
                           <span class="nav-text">
                           Company Information
                           </span>
                        </a>
                     </li>
               </div>
            </div>
            <div class="card-body px-0">
               <div class="tab-content px-10">

                  <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Name:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ucwords($userDetails->name ?? '')}}</span>
                        </div>
                     </div>
                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Email:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->email ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Phone Number:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->phone_number ?? ''}}</span>
                        </div>
                     </div>

                     

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Registered On:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">   {{ date(config("Reading.date_format"),strtotime($userDetails->created_at)) }} </span>
                        </div>
                     </div>
                    
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Status:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              @if($userDetails->is_active == 1)
                              <span class="label label-lg label-light-success label-inline">Activated</span>
                              @else
                              <span class="label label-lg label-light-danger label-inline">Deactivated</span>
                              @endif
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_2" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Company Name:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ucwords($userDetails->userCompanyInformation->company_name ?? '')}}</span>
                        </div>
                     </div>
                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Company Mobile Number:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_mobile_number ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Contact Person Name:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->contact_person_name ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Contact Person Email:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->contact_person_email ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Contact Person Number:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->contact_person_phone_number ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Contact Person Profile:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                           @if (!empty($userDetails->userCompanyInformation->contact_person_picture))
                              <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo Config('constants.CONTACT_PERSON_PROFILE_IMAGE_PATH') . $userDetails->userCompanyInformation->contact_person_picture; ?>">
                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{ Config('constants.CONTACT_PERSON_PROFILE_IMAGE_PATH') . $userDetails->userCompanyInformation->contact_person_picture }}" />
                              </a>
                           @endif
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Company Location:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_location ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Company Type:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_type ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">Company Logo:</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              
                              @if (!empty($userDetails->userCompanyInformation->company_logo))
                                 <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo Config('constants.COMPANY_LOGO_IMAGE_PATH') . $userDetails->userCompanyInformation->company_logo; ?>">
                                       <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{ Config('constants.COMPANY_LOGO_IMAGE_PATH') . $userDetails->userCompanyInformation->company_logo }}" />
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