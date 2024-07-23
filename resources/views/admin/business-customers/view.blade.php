@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
      <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
         <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
               <h5 class="text-dark font-weight-bold my-1 mr-5">
               {{trans("messages.admin_View_Business_Customer")}}
               </h5>
               <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                  <li class="breadcrumb-item">
                     <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                  </li>
                  <li class="breadcrumb-item">
                     <a href="{{ route($model.'.index')}}" class="text-muted">    
                     {{trans("messages.admin_Business_Customers")}}</a>
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
                           {{trans("messages.admin_Business_Customers_Information")}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_2">
                           <span class="nav-text">
                           {{trans("messages.admin_Company_Information")}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_3">
                           <span class="nav-text">
                           {{trans("messages.admin_customer_invoice")}}
                           </span>
                        </a>
                     </li>
               </div>
            </div>
            <div class="card-body px-0">
               <div class="tab-content px-10">
                  <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_profile_image")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              <a class="fancybox-buttons" data-fancybox-group="button" href="{{$userDetails->image ?? ''}}">
                                 <img width="100px" height="80" alt="Image" src="{{$userDetails->image ?? ''}}">
                             </a>
                           </span>
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
                           <span class="form-control-plaintext font-weight-bolder">   {{ date(config("Reading.date_format"),strtotime($userDetails->created_at)) }} </span>
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
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_2" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Company Name")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ucwords($userDetails->userCompanyInformation->company_name ?? '')}}</span>
                        </div>
                     </div>
                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.company_number')}} (H.P.) :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_hp_number ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Contact Person Name")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->contact_person_name ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Contact Person Email")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->contact_person_email ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Contact_Person_Number")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->contact_person_phone_number ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Contact_Person_Profile")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                           @if (!empty($userDetails->userCompanyInformation->contact_person_picture))
                              <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $userDetails->userCompanyInformation->contact_person_picture; ?>">
                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{ $userDetails->userCompanyInformation->contact_person_picture }}" />
                              </a>
                           @endif
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Company Location")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_location ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.company type")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              {{$userDetails->userCompanyInformation->company_type ?? ''}}
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Company Logo")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              
                              @if (!empty($userDetails->userCompanyInformation->company_logo))
                                 <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo  $userDetails->userCompanyInformation->company_logo; ?>">
                                       <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{  $userDetails->userCompanyInformation->company_logo }}" />
                                 </a>
                              @endif
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_3" role="tabpanel">
                     <div class="dataTables_wrapper ">
                        <div class="table-responsive table-responsive-new">
                           @if($ShipmentLists->count() > 0)
                            <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
                                <thead>
                                 <th>{{trans('messages.request_id')}}</th>
                                 <th>{{trans('messages.Company Name')}}</th>
                                 <th>{{trans('messages.type')}}</th>
                                 <th>{{trans('messages.send_date')}}</th>
                                 <th>{{trans('messages.action')}}</th>
                                </thead>
                                <tbody>
                                   @foreach($ShipmentLists as $key => $shipment)

                                   <tr>
                                    <td>{{$shipment->request_number ?? '' }}</td>
                                    <td>{{$shipment?->companyInformation?->company_name ?? ''}}</td>
                                    <td>{{$shipment->TruckTypeDescriptions->name ?? ''}}</td>
                                    <td>
                                        @if(!@empty($shipment->invoice_send_time))
                                      
                                         {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', ($shipment->invoice_send_time))->format(config("Reading.date_time_format"))  }}
                                
                                     @else

                                     @endif
                                    </td>
                                    <td>

                                       @php
                                       $filename = $shipment->invoice_file;
                                       $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                      if($extension == "png" || $extension == "jpg" || $extension == "jpeg"|| $extension == "svg"){
                                           @endphp 
                                               <a class="fancybox-buttons" data-fancybox-group="button" href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$shipment->invoice_file) }}" >
                                                   <div class="upload_img_item tabel_img">
                                                       <img  src="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$shipment->invoice_file) }}" alt="" width="50" height="50">
                                                       
                                                   </div>
                                                   </a>
                                                   <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
       
                                           @php
                                       }else if($extension == "docx" || $extension == "doc"){
                                           @endphp
                                           <a  href="{{asset( Config('constants.INVOICE_FILE_ROOT_PATH').$shipment->invoice_file) }}" target="_blank">
       
                                               <div class="upload_img_item tabel_img" >
                                                   <img src="{{url('/public/frontend/img/docx-icon.png')}}" alt="" width="50" height="50">
                                                  
                                               </div>
                                               </a>
                                               <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                           @php
                                       }else if($extension == "pdf"){
                                           @endphp
                                           <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" target="_blank">
                                               <div class="upload_img_item tabel_img"  >
                                                   <img src="{{url('/public/frontend/img/pdf-icon.png')}}" alt="" width="50" height="50">
                                                   
                                               </div>
                                               </a>
                                               <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                           @php
                                       }
                                       @endphp

                                    </td>
                                   </tr>
                                   @endforeach
                                 </tbody>
                                 @else 
                                 <h3 style="text-align: center;">{{trans('messages.invoice_not_found')}}</h3>
                                 @endif
                            </table>
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