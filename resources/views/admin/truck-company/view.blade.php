@extends('admin.layouts.layout')
@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
      <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
         <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
               <h5 class="text-dark font-weight-bold my-1 mr-5">
               {{trans('messages.admin_common_View')}} {{trans('messages.admin_Truck_Company')}}
               </h5>
               <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                  <li class="breadcrumb-item">
                     <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                  </li>
                  <li class="breadcrumb-item">
                     <a href="{{ route($model.'.index')}}" class="text-muted">    
                     {{trans('messages.admin_Truck_Company')}}</a>
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
                        <a class="nav-link {{ ((request('tabs') != 'plan_details' && request('tabs') != 'truck_detail' && request('tabs') != 'driver_detail') ? 'active hide_me' : '') }}" data-toggle="tab" href="#kt_apps_contacts_view_tab_2">
                           <span class="nav-text">
                           {{trans("messages.admin_Company_Information")}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link  hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_1">
                           <span class="nav-text">
                           {{trans('messages.admin_contact_person_information')}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link  {{request('tabs') == 'truck_detail' ? 'active hide_me' : ''}}" href="#truckDetails" data-toggle="tab">
                           <span class="nav-text">
                           {{trans('messages.Truck Details')}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link  {{request('tabs') == 'driver_detail' ? 'active hide_me' : ''}}" href="#driverDetails" data-toggle="tab">
                           <span class="nav-text">
                           {{trans('messages.Truck Drivers')}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link  hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_4">
                           <span class="nav-text">
                           {{trans('messages.admin_company_invoice')}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link {{ (request('tabs') == 'plan_details' ? 'active' : '') }}  hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_5">
                           <span class="nav-text">
                           {{trans('messages.admin_plan_detail')}}
                           </span>
                        </a>
                     </li>
               </div>
            </div>
            <div class="card-body px-0">
               <div class="tab-content px-10">
                  <div class="tab-pane {{ ((request('tabs') != 'plan_details' && request('tabs') != 'truck_detail' && request('tabs') != 'driver_detail') ? 'active' : '') }}" id="kt_apps_contacts_view_tab_2" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_System_Id")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ucwords($userDetails->system_id ?? '')}}</span>
                        </div>
                     </div>
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
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_offers")}} :</label>
                        
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$total_offer}}</span>
                        </div>
                     </div>
                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Shipment")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$total_shipment}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_company_description")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_description ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Company_Terms_&_Conditions")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_trms ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.refueling_method")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{@$userDetails->userCompanyInformation->getCompanyRefuelingDescription->code ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Company_Tidaluk")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{@$userDetails->userCompanyInformation->getCompanyTidalukCompanyDescription->code ?? ''}}</span>
                        </div>
                     </div>

                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Company Location")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->userCompanyInformation->company_location ?? ''}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Company Logo")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              
                              @if (!empty($userDetails->userCompanyInformation->company_logo))
                                 <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $userDetails->userCompanyInformation->company_logo; ?>">
                                       <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{ $userDetails->userCompanyInformation->company_logo }}" />
                                 </a>
                              @endif
                           </span>
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
                           @if($userDetails->is_approved == 1)
                              @if($userDetails->is_active == 1)
                                 @php
                                    if($userDetails->last_active_date == null){
                                          $sDate = Carbon::parse($userDetails->updated_at);                                                                
                                    }else{
                                          $sDate = Carbon::parse($userDetails->last_active_date);                                                                
                                    }
                                    $currentDate = Carbon::now();
                                    $dayDifference = $sDate->diffInDays($currentDate);
                                 @endphp
                                 @if($dayDifference)
                                    <span class="label label-lg label-light-danger label-inline">{{trans("messages.inactive_over_30_days")}}</span>
                                 @else
                                    <span class="label label-lg label-light-success label-inline">{{trans("messages.admin_common_Activated")}}</span>
                                 @endif
                              @else
                              
                              <span class="label label-lg label-light-danger label-inline">{{trans("messages.admin_common_Deactivated")}}</span>
                              @endif
                           @elseif($userDetails->is_approved == 2)
                                 <div class="label label-lg label-light-danger label-inline">{{trans("messages.admin_common_rejected")}}</div>
                           @else
                                 <div class="label label-lg label-light-danger label-inline">{{trans("messages.admin_common_waiting_for_approval")}}</div>
                           @endif
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Contact Person Name")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ucwords($userDetails->name ?? '')}}</span>
                        </div>
                     </div>
                     
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Contact Person Email")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->email ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Contact_Person_Number")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->phone_number ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Contact_Person_Profile")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                           @if (!empty($userDetails->userCompanyInformation->contact_person_picture))
                              <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo  $userDetails->userCompanyInformation->contact_person_picture; ?>">
                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{  $userDetails->userCompanyInformation->contact_person_picture }}" />
                              </a>
                           @endif
                           </span>
                        </div>
                     </div>
                    

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.customer_as_driver")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$userDetails->truck_company_id > 0 ? trans("messages.yes") : trans("messages.no") }}</span>
                        </div>
                     </div>
                     
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_4" role="tabpanel">
                     <div class="dataTables_wrapper ">
                        <div class="table-responsive table-responsive-new">
                           @if($ShipmentInvoiceLists->count() > 0)
                            <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
                                <thead>
                                 <th>{{trans('messages.request_id')}}</th>
                                 <th>{{trans('messages.type')}}</th>
                                 <th>{{trans('messages.send_date')}}</th>
                                 <th>{{trans('messages.action')}}</th>
                                </thead>
                                <tbody>
                                   @foreach($ShipmentInvoiceLists as $key => $shipment)

                                   <tr>
                                    <td>
                                       <a href="{{ route('shipment-request.show', $shipment->request_number) }}" target="_blank">
                                       {{$shipment->request_number ?? '' }}
                                    </a>
                                    </td>
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
       
                                               <div class="upload_img_item tabel_img" style="padding: 15px;">
                                                   <img src="{{url('/public/frontend/img/docx-icon.png')}}" alt="" width="50" height="50">
                                                   
                                               </div>
                                               </a>
                                               <a href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" class="blue_btn dashboard_tableBtn" download>Download                                    </a>
                                           @php
                                       }else if($extension == "pdf"){
                                           @endphp
                                           
                                             <a  href="{{ asset(Config('constants.INVOICE_FILE_ROOT_PATH') . $shipment->invoice_file) }}" target="_blank">
                                               <div class="upload_img_item tabel_img"  style="padding: 15px;">
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
                                 <h3 style="text-align: center;">{{trans('messages.invoice_not_found')}}</h3>                                 @endif
                            </table>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_3" role="tabpanel">
                     @if($planDetails)
                        <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans("messages.admin_common_Plan_Duration")}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">
                                 @if($planDetails->type =='0')
                                    {{trans("messages.monthly")}}
                                 @elseif($planDetails->type =='1') 
                                    {{trans("messages.quarterly")}}
                                 @elseif($planDetails->type =='2')
                                    {{trans("messages.half_yearly")}}
                                 @else
                                    {{trans("messages.Yearly")}}
                                 @endif
                              </span>
                           </div>
                        </div>
                        <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans("messages.admin_common_plan_price")}} :</label>
                           <div class="col-8">
                              @if($planDetails->is_free)
                                 <span class="form-control-plaintext font-weight-bolder">{{ trans("messages.Free")}}</span>
                              @else
                                 <span class="form-control-plaintext font-weight-bolder"><img src="{{url('public/frontend/img/plan-icon.png')}}" style="width: 15px;"> {{  $planDetails->price}}</span>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans("messages.admin_plan_purchased_on")}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{ date(config("Reading.date_format"),strtotime($userTruckCompanySubscription->created_at)) }}</span>
                           </div>
                        </div>

                        

                        <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans("messages.admin_plan_renew_on")}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">   {{ date(config("Reading.date_format"),strtotime($userTruckCompanySubscription->end_time)) }} </span>
                           </div>
                        </div>
                     @else
                     <div class="form-group row my-2 text-center">
                           <div class="col-12">{{trans("messages.admin_common_No_Plan_Selected")}}</div>
                  </div>
                     @endif
                  </div>
                  <div class="tab-pane {{ (request('tabs') == 'plan_details' ? 'active' : '') }}" id="kt_apps_contacts_view_tab_5" role="tabpanel">

                     

                     @if($userTruckCompanySubscription != null)
                      <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_plan_name")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                             {{ $planDetails->plan_name ?? '' }}
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{ trans('messages.admin_common_Plan_Duration') }} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              @php 
                              $typeData = '';
                              if ($userTruckCompanySubscription->type == '0') {
                                 $typeData = trans('messages.monthly');
                              } elseif ($userTruckCompanySubscription->type == '1') {
                                 $typeData = trans('messages.quarterly');
                              } elseif ($userTruckCompanySubscription->type == '2') {
                                 $typeData = trans('messages.Half Yearly');
                              } elseif ($userTruckCompanySubscription->type == '3') {
                                 $typeData = trans('messages.Yearly');
                              }
                              @endphp
                              {{ $typeData ?? '' }}
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Is_Free")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                              @if($userTruckCompanySubscription->is_free == 0)
                                 {{trans("messages.paid")}}
                              @else
                                 {{trans("messages.Free")}}
                              @endif
                           </span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.price")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                              @if($userTruckCompanySubscription->is_free == 0)
                                 <img src="{{asset('public/img/plan-icon.png')}}" alt="" width="15px">  {{$userTruckCompanySubscription->price ?? ''}}
                              @else
                                 {{trans("messages.Free")}}
                              @endif
                           </span>
                        </div>
                     </div>

                     @if($userTruckCompanySubscription->discount > 0)
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.discount")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                              {{$userTruckCompanySubscription->discount ?? ''}} %
                           </span>
                        </div>
                     </div>
                     @endif

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.total_price")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                              <img src="{{asset('public/img/plan-icon.png')}}" alt="" width="15px">  {{$userTruckCompanySubscription->total_price ?? ''}}
                           </span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.start_date")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">   
                              {{date(Config('Reading.date_format'), strtotime($userTruckCompanySubscription->start_time ?? ''))}}
                           </span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_expiry_date")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">  
                              @php 
                                $currentDate = now();
                              @endphp
                              <p class="{{ ($userTruckCompanySubscription->end_time >= $currentDate) ? '' : 'text-danger' }}">
                                 {{date(Config('Reading.date_format'), strtotime($userTruckCompanySubscription->end_time ?? ''))}}
                              </p>
                           </span>
                        </div>
                     </div>


                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_common_Status")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">  
                              @if($userTruckCompanySubscription->status == 'activate')
                              <span class="label label-lg label-light-success label-inline">
                                 {{  trans('messages.admin_activate_status')  }}
                              </span>
                              @else 
                              <span class="label label-lg label-light-danger label-inline">
                                 {{ trans('messages.admin_common_Expired') }}
                              </span>
                              @endif
                           </span>
                        </div>
                     </div>


                     @else 
                     <h3 class="text-center"> {{ trans('messages.this_truck_company_has_no_active_plan_create_plan') }}</h3>
                     
                     @endif
                    </div>

                      <div class="tab-pane {{request('tabs') == 'truck_detail' ? 'active' : ''}}" id="truckDetails" role="tabpanel">
                        
                         <div class="dataTables_wrapper-fake-top-scroll">
                                    <div>&nbsp;</div>
                                </div>
                                <div class="dataTables_wrapper ">
                                    <div class="table-responsive table-responsive-new">
                                        <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
                                            <thead>
                                                <tr class="text-uppercase">
                                                    
                                                    <th> 
                                                        <a href="javascript:void(0);"> {{trans("messages.Type_of_truck")}} </a>
                                                    </th>
                                                    <th class="">
                                                     <a href="javascript:void(0);"> 
                                                         {{trans("messages.Truck Number")}}</a>
                                                    </th>
                                                    <th class="">  <a href="javascript:void(0);"> {{trans("messages.Company Name")}}</a>
                                                    </th>

                                                    <th
                                                        class=""> <a href="javascript:void(0);"> {{trans("messages.admin_Truck_Driver")}}</a>
                                                    </th>
                                                    <th
                                                        class=""> <a href="javascript:void(0);"> {{trans("messages.end_of_insurance_date")}}</a>
                                                    </th>
                                                    <th
                                                        class=""> <a href="javascript:void(0);"> {{trans("messages.end_of_license_date")}}</a>
                                                    </th>
                                                    <th class=""> <a href="javascript:void(0);"> {{trans("messages.admin_common_Status")}}</a>
                                                    </th>
                                                    <th class="text-right">{{trans("messages.admin_common_Action")}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!$truckDetalsList->isEmpty())
                                                @foreach($truckDetalsList as $result)
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
                                                        <a href="{{route($model.'.show_truck', [base64_encode($result->id), 'from_page' => 'tc_view', 'tc_id' => request()->segment(4)])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_View")}}">
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
                                </div>

                      </div>

                      <div class="tab-pane px-10 pt-5 pb-5 {{request('tabs') == 'driver_detail' ? 'active' : ''}}" id="driverDetails" role="tabpanel">


                      <div class="dataTables_wrapper-fake-top-scroll">
                            <div>&nbsp;</div>
                        </div>
                        <div class="dataTables_wrapper ">
                            <div class="table-responsive table-responsive-new">
                                <table class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center" id="taskTable">
                                    <thead>
                                        <tr class="text-uppercase">
                                           <th class="">
                                                <a href="javascript:void(0);">{{trans("messages.Company Name")}}</a>
                                            </th>
                                            <th class="">
                                                <a href="javascript:void(0);">{{trans("messages.name")}}
                                                </a>
                                            </th>
                                            <th class="">
                                                <a href="javascript:void(0);"> {{ trans("messages.Phone Number") }}</a>
                                            </th>
                                            <th class="">
                                                <a href="javascript:void(0);">{{trans("messages.admin_common_Last_Activity_Date")}}</a>
                                            </th>


                                            <th class="">
                                                <a href="javascript:void(0);">{{trans("messages.admin_Created_On")}}</a>
                                            </th>
                                            <th class="">
                                                <a href="javascript:void(0);">{{trans("messages.admin_common_Status")}}

                                                </a>
                                            </th>
                                            <th class="text-right">{{trans("messages.admin_common_Action")}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!$driverDetailsList->isEmpty())
                                        @foreach($driverDetailsList as $result)
                                        <tr>
                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                    {{ $result->company_name  ?? ''}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                    {{ $result->name  ?? ''}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                    {{ $result->phone_number ?? '' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark-75 mb-1 font-size-lg">
                                                    {{ $result->last_activity_date_time ? date(config("Reading.date_format"),strtotime($result->last_activity_date_time)) : trans('messages.not_login_yet') }}
                                                </div>
                                            </td>
                            <td>
                                <div class="text-dark-75 mb-1 font-size-lg">
                                    {{ date(config("Reading.date_format"),strtotime($result->created_at)) }}
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
                                <a href="{{route('truck-company-driver.show',[base64_encode($result->id),("truck_id=".$TCuserid), 'from_page' => 'tc_view', 'tc_id' => request()->segment(4)])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_View')}}">
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
                                <td colspan="6" style="text-align:center;"> {{trans("messages.admin_common_Record_not_found")}}</td>
                            </tr>
                            @endif
                            </tbody>
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

@include('admin.truck-company.plan_details_script')

@stop