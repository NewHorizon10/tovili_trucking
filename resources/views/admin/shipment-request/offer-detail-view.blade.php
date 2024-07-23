@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
      <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
         <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
               <h5 class="text-dark font-weight-bold my-1 mr-5">
                  {{trans("messages.admin_shipment_request_offer_details")}}
               </h5>
               
               <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                  <li class="breadcrumb-item">
                     <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                  </li>
                  @if($viewType == 'offers')
                  <li class="breadcrumb-item">
                     <a href="{{ route('shipment-request-offers-list')}}" class="text-muted">    
                     {{trans("messages.shipment_offers")}}</a>
                  </li>
                  @else
                  <li class="breadcrumb-item">
                     <a href="{{ route($model.'.index')}}" class="text-muted">    
                     {{trans("messages.Shipment_Request")}}</a>
                  </li>
                  <li class="breadcrumb-item">
                     <a href="{{ route($model.'.show',$shipment->request_number)}}" class="text-muted">    
                     {{trans("messages.admin_View_Shipment_Request")}}</a>
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
                              {{trans("messages.admin_shipment_request_offer_details")}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_2">
                           <span class="nav-text">
                              {{trans("messages.Company Details")}}
                           </span>
                        </a>
                     </li>
                     
                     <li class="nav-item">
                        <a class="nav-link hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_3">
                           <span class="nav-text">
                              {{trans("messages.admin_contact_person_information")}}
                           </span>
                        </a>
                     </li>
                  </ul>
               </div>
            </div>
            
            <div class="card-body px-0">
               <div class="tab-content px-10">
                  <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.the_date_of_transport')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{ \Carbon\Carbon::createFromFormat('Y-m-d', ($shipment->request_date))->format(config("Reading.date_format"))  }}<br/>
                              {{$shipment->RequestTimeDescription->code ?? "" }} </span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.pickup_city")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{ $shipment->pickup_city }}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.original_address')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{$shipment->pickup_address}}</span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.admin_destination_city')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">
                                 @foreach($shipment->ShipmentStop as $ShipmentStop )
                                       {{$ShipmentStop->dropoff_city}}
                                       @break
                                 @endforeach 
                              </span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.destination_address')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">
                                 @foreach($shipment->ShipmentStop as $ShipmentStop )
                                       {{$ShipmentStop->dropoff_address}}
                                       @break
                                 @endforeach 
                              </span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.the_type_of_transport')}} :</label>
                           <div class="col-8">
                              @if($shipment->customer->customer_type == "private" )
                                 <span class="form-control-plaintext font-weight-bolder">{{ $shipment->TruckTypeDescriptionsPrivate->name ?? "" }}</span>
                              @else
                                 <span class="form-control-plaintext font-weight-bolder">{{ $shipment->TruckTypeDescriptions->name ?? "" }}</span>
                              @endif
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.admin_common_Price')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->price}}</span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.extra_time_price')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{Config('constants.CURRENCY_SIGN')}} {{$shipmentOffer->extra_time_price}}</span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.duration_in_hours')}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->duration }}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.shipment_note')}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->description}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.payment_condition')}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->payment_condition}}</span>
                        </div>
                     </div>
                     @if($shipmentOffer && $shipmentOffer->request_offer_date)
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.request_offer_date')}} :</label>
                           <div class="col-8">
                           <span class=" date_label form-control-plaintext font-weight-bolder">
                              {{ (\Carbon\Carbon::createFromFormat('Y-m-d', ($shipmentOffer->request_offer_date))->format(config("Reading.date_format")))  }} 
                              
                              </span>
                           </div>
                     </div>
                     @else
                        <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.request_offer_date')}} :</label>
                           <div class="col-8">
                              <span class=" date_label form-control-plaintext font-weight-bolder"> {{$shipmentOffer->TruckDetail->request_offer_date ?? ""}}</h3>
                           </div>
                        </div>
                     @endif

                     @php
                     $driverName = false ;
                     $driverNumber = false ;
                     if($shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->truckDriver){
                           $driverName = $shipment->shipmentDriverScheduleDetails->truckDriver->name;
                           $driverNumber = $shipment->shipmentDriverScheduleDetails->truckDriver->phone_number;
                     }else if($shipmentOffer->TruckDetail && $shipmentOffer->TruckDetail->truckDriver){
                           $driverName = $shipmentOffer->TruckDetail->truckDriver->name;
                           $driverNumber = $shipmentOffer->TruckDetail->truckDriver->phone_number;
                     }
                     @endphp
                     @if($driverName)
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.driver_name')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{ $driverName }}</span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.driver_number')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{ $driverNumber }}</span>
                           </div>
                     </div>
                     @endif

                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.Truck Number')}} :</label>
                           <div class="col-8"> 
                              <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->TruckDetail->truck_system_number ?? ""}}</span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.admin_common_Status')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{ trans("messages.$shipmentOffer->status") }}</span>
                           </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_2" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.Company Name')}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->company_name}}</span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.refueling_method')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->company_refueling}}</span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.company_number')}} (H.P.) :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->company_hp_number}}</span>
                           </div>
                     </div>
                     
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.admin_common_Company_Tidaluk')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->company_tidaluk}}</span>
                           </div>
                     </div>
                  
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.Company Location')}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->company_location}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.admin_Company_Terms_&_Conditions')}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->company_trms}}</span>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="kt_apps_contacts_view_tab_3" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans('messages.Contact Person Name')}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_name}}</span>
                        </div>
                     </div>
                      <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.Contact Person Email')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_email}}</span>
                           </div>
                     </div>
                     <div class="form-group row my-2">
                           <label class="col-4 col-form-label">{{trans('messages.Contact Person Phone Number')}} :</label>
                           <div class="col-8">
                              <span class="form-control-plaintext font-weight-bolder">{{$shipmentOffer->companyUser->userCompanyInformation->contact_person_phone_number}}</span>
                           </div>
                     </div>
                  
                    <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Contact_Person_Profile")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                              @if (!empty($shipmentOffer->companyUser->userCompanyInformation->contact_person_picture))
                                 <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo  $shipmentOffer->companyUser->userCompanyInformation->contact_person_picture; ?>">
                                       <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{  $shipmentOffer->companyUser->userCompanyInformation->contact_person_picture }}" />
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