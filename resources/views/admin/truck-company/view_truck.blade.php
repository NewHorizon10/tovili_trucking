@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
   <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
      <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
         <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex align-items-baseline flex-wrap mr-5">
               <h5 class="text-dark font-weight-bold my-1 mr-5">
               {{trans("messages.admin_common_View")}} {{trans("messages.admin_common_Truck")}} 
               </h5>
               <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route($model.'.index')}}" class="text-muted"> {{trans("messages.admin_Truck_Company")}}</a>
                </li>
                @if(request('from_page') != '' && request('from_page') == 'tc_edit')
                <li class="breadcrumb-item">
                    <a href="{{ route($model.'.edit',[base64_encode($truckDetails->truck_company_id), 'tabs=truck_detail'])}}" class="text-muted"> {{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}</a>
                </li>
                @elseif(request('from_page') != '' && request('from_page') == 'tc_view')
                  <li class="breadcrumb-item">
                    <a href="{{ route($model.'.show',[base64_encode($truckDetails->truck_company_id), 'tabs=truck_detail'])}}" class="text-muted"> {{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}</a>
                </li>
                @endif
                <!-- <li class="breadcrumb-item">
                    <a href="{{ route($model.'.index_truck',[base64_encode($truckDetails->truck_company_id)])}}" class="text-muted"> {{trans("messages.Truck Details")}}</a>
                </li> -->
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
                           {{trans("messages.admin_Basic_Information")}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link hide_me" data-toggle="tab" href="#kt_apps_contacts_view_tab_2">
                           <span class="nav-text">
                           {{trans("messages.admin_shipment_request_questionnaire")}}
                           </span>
                        </a>
                     </li>
               </div>
            </div>
            <div class="card-body px-0">
               <div class="tab-content px-10">

                  <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.truck_image")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                            
                                 <a class="fancybox-buttons" data-fancybox-group="button" href="{{$truckDetails->image}}">
                                       <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{$truckDetails->image}}" />
                                 </a>
                           </span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Type_of_truck")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$truckDetails->typeOfTruck->name ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.Truck Number")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$truckDetails->truck_system_number ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Tidaluk_Company")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">{{$truckDetails->company_tidaluk ?? ''}}</span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.refueling_method")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">   {{ $truckDetails->company_refueling }} </span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Insurance_certificate")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                           @if (!empty($truckDetails->truck_insurance_picture))
                              <a class="fancybox-buttons" data-fancybox-group="button" href="{{$truckDetails->truck_insurance_picture}}">
                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{$truckDetails->truck_insurance_picture}}" />
                              </a>
   
                           @endif
                           </span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.end_of_insurance_date")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">   {{ isset($truckDetails->truck_insurance_expiration_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', ($truckDetails->truck_insurance_expiration_date))->format(config("Reading.date_format")) : '' }} </span>
                        </div>
                     </div>


                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.end_of_license_date")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">   {{ isset($truckDetails->truck_licence_expiration_date) ? \Carbon\Carbon::createFromFormat('Y-m-d', ($truckDetails->truck_licence_expiration_date))->format(config("Reading.date_format")) : '' }} </span>
                        </div>
                     </div>

                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Truck_license")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder">
                           @if (!empty($truckDetails->truck_licence_number))
                              <a class="fancybox-buttons" data-fancybox-group="button" href="{{$truckDetails->truck_licence_number}}">
                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{$truckDetails->truck_licence_number}}" />
                              </a>   
                           @endif
                           </span>
                        </div>
                     </div>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_Truck_Driver")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> {!!$truckDetails->truckDriver ? $truckDetails->truckDriver->name." <br>( ".$truckDetails->truckDriver->phone_number." ) " : "----" !!} </span>
                        </div>
                     </div>
                    
                     
                  </div>

                  <div class="tab-pane" id="kt_apps_contacts_view_tab_2" role="tabpanel">
                     @if($truckDetails->questionnaire)
                     @php
                     if (!is_array($truckDetails->questionnaire) || isset($truckDetails->questionnaire)){
                        foreach($truckDetails->questionnaire as $key => $value){
                           $truck_type_question_descriptions =
                           DB::table('truck_type_question_descriptions')->where('parent_id',$key)->where('language_id',getAppLocaleId())->first();
                           if ($truck_type_question_descriptions && $value) {
                           @endphp
                           <div class="form-group row my-2">
                              <label class="col-4 col-form-label">{{$truck_type_question_descriptions->name}} :</label>
                              <div class="col-8">
                                 <span class="form-control-plaintext font-weight-bolder">
                                    @php
                                    if(is_array($value)){
                                       $opyionvalueStr = '';
                                       foreach($value as $valueKey => $opyionvalue){
                                          $input_description_array =
                                          explode(",",$truck_type_question_descriptions->input_description );
                                          if(isset($input_description_array[$opyionvalue])){
                                             echo ($opyionvalueStr == '' ? '' : ', ').$input_description_array[$opyionvalue];
                                          }
                                          $opyionvalueStr = 'in';
                                       }
                                    }else{
                                       echo $value;
                                    }
                                    @endphp
                                 </span>
                              </div>
                           </div>
                        @php
                           }
                        }
                     }
                     @endphp
                     @endif
                     
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

@stop