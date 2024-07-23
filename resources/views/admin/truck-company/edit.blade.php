@extends('admin.layouts.layout')
@section('content')
<?php $counter = 0; ?>
<style>
    .invalid-feedback {
        display: inline;
    }
   
    .AClass{
    right:10px;
    position: absolute;
}
</style>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted"> 
                            {{trans('messages.admin_Truck_Company')}} 
                        </a>
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
                        <a class="nav-link {{ ((request('tabs') != 'plan_details' && request('tabs') != 'truck_detail' && request('tabs') != 'driver_detail') ? 'active hide_me' : '') }}" data-toggle="tab" href="#kt_apps_contacts_view_tab_1">
                           <span class="nav-text">
                           {{trans("messages.admin_Company_Information")}}
                           </span>
                        </a>
                     </li>
                      <li class="nav-item">
                        <a class="nav-link   {{request('tabs') == 'truck_detail' ? 'active hide_me' : ''}}" data-toggle="tab" href="#kt_apps_contacts_view_tab_2">
                           <span class="nav-text">
                           {{trans('messages.Truck Details')}}
                           </span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link  {{request('tabs') == 'driver_detail' ? 'active hide_me' : ''}}" data-toggle="tab" href="#kt_apps_contacts_view_tab_3">
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

            
            <div class="tab-content">
             <div class="tab-pane {{ ((request('tabs') != 'plan_details' && request('tabs') != 'truck_detail' && request('tabs') != 'driver_detail') ? 'active' : '') }}" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                <form action="{{route($model.'.update',array(base64_encode($userDetails->id)))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">

                                <!-- <h3 class="mb-10 font-weight-bold text-dark">
                                {{trans("messages.admin_Company_Information")}}
                                </h3> -->
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">{{trans("messages.Company Name")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_name" class="form-control form-control-solid form-control-lg  @error('company_name') is-invalid @enderror" value="{{old('company_name') ?? $userDetails->userCompanyInformation ?->company_name }}">
                                            @if ($errors->has('company_name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    {{--<div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_mobile_number">{{trans("messages.Company Mobile Number")}}</label><span class="text-danger"> * </span>
                                            <input type="number" name="company_mobile_number" class="form-control form-control-solid form-control-lg  @error('company_mobile_number') is-invalid @enderror" value="{{old('company_mobile_number') ?? $userDetails->userCompanyInformation ?->company_mobile_number }}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                                            @if ($errors->has('company_mobile_number'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_mobile_number') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div> --}} 

                                    {{--<div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_mobile_number">{{trans('messages.company_number')}} (H.P.)</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <!-- <div class="input-group-prepend">
                                                    <span class="input-group-text">+972</span>
                                                </div> -->
                                                <input type="text" name="company_mobile_number" class="form-control form-control-solid form-control-lg @error('company_mobile_number') is-invalid @enderror" value="{{old('company_mobile_number') ?? $userDetails->userCompanyInformation ?->company_hp_number }}">
                                                @if ($errors->has('company_mobile_number'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('company_mobile_number') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>--}}

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_number">{{trans('messages.company_number')}} (H.P.)</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <!-- <div class="input-group-prepend">
                                                    <span class="input-group-text">+972</span>
                                                </div> -->
                                                <input type="text" name="company_number" class="form-control form-control-solid form-control-lg @error('company_number') is-invalid @enderror" oninput="validateOnlyNumber(this);" onpaste="validateOnlyNumber(this);" value="{{old('company_number') ?? $userDetails->userCompanyInformation ?->company_hp_number }}">
                                                @if ($errors->has('company_number'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('company_number') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label for="company_description">{{trans("messages.admin_common_company_description")}}</label>
                                            <textarea rows="3" type="number" name="company_description" class="form-control form-control-solid form-control-lg  @error('company_description') is-invalid @enderror">{{ old('company_description') ?? $userDetails->userCompanyInformation ?->company_description }}</textarea>
                                            @if ($errors->has('company_description'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_description') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>



                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label for="company_terms">{{trans("messages.admin_Company_Terms_&_Conditions")}}</label>
                                            <textarea rows="3" type="number" name="company_terms" class="form-control form-control-solid form-control-lg  @error('company_terms') is-invalid @enderror">{{ old('company_terms') ?? $userDetails->userCompanyInformation ?->company_trms }}</textarea>
                                            @if ($errors->has('company_terms'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_terms') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    <!-- <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_refulling">{{trans("messages.refueling_method")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_refulling" class="form-control form-control-solid form-control-lg  @error('company_refulling') is-invalid @enderror" value="{{old('company_refulling') ?? $userDetails->userCompanyInformation ?->company_refueling }}">
                                            @if ($errors->has('company_refulling'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_refulling') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div> -->

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_refulling">{{trans("messages.refueling_method")}}</label><span class="text-danger"> * </span>
                                            <select name="company_refulling" class="form-control select2init  @error('company_refulling') is-invalid @enderror" >
                                                <option value="">{{trans("messages.select_refueling_method")}} </option>
                                                @foreach($fuelingType as $row)
                                                    <option value="{{$row->id}}" {{  old('company_refulling') ? (old('company_refulling') == $row->id ? 'selected' : '') : ($userDetails->userCompanyInformation ?->company_refueling == $row->id ? 'selected' : '') }} >{{$row->lookupDiscription->code}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('company_refulling'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_refulling') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                            
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_tidaluk">{{trans("messages.admin_common_Company_Tidaluk")}}</label><span class="text-danger"> * </span>
                                            <!-- <input type="text" name="company_tidaluk" class="form-control form-control-solid form-control-lg  @error('company_tidaluk') is-invalid @enderror" value="{{old('company_tidaluk') ?? $userDetails->userCompanyInformation ?->company_tidaluk }}"> -->
                                            <select name="company_tidaluk" class="form-control select2init  @error('company_tidaluk') is-invalid @enderror" >
                                                <option value="">{{trans("messages.select_company_tidaluk")}}</option>
                                                @foreach($tidalukCompanyType as $row)
                                                    <option value="{{$row->id}}" {{  old('company_tidaluk') ? (old('company_tidaluk') == $row->id ? 'selected' : '') : ($userDetails->userCompanyInformation ?->company_tidaluk == $row->id ? 'selected' : '') }} >{{$row->lookupDiscription->code}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('company_tidaluk'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_tidaluk') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    


                                    <!-- <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_name">Contact Person Name</label><span class="text-danger"> * </span>
                                            <input type="text" name="contact_person_name" class="form-control form-control-solid form-control-lg  @error('contact_person_name') is-invalid @enderror" value="{{old('contact_person_name') ?? $userDetails->userCompanyInformation ?->contact_person_name }}">
                                            @if ($errors->has('contact_person_name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">Contact Person Email</label><span class="text-danger"> * </span>
                                            <input type="email" name="contact_person_email" class="form-control form-control-solid form-control-lg  @error('contact_person_email') is-invalid @enderror" value="{{old('contact_person_email') ?? $userDetails->userCompanyInformation ?->contact_person_email }}">
                                            @if ($errors->has('contact_person_email'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_email') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_phone_number">Contact Person Number</label><span class="text-danger"> * </span>
                                            <input type="number" name="contact_person_phone_number" class="form-control form-control-solid form-control-lg  @error('contact_person_phone_number') is-invalid @enderror" value="{{old('contact_person_phone_number') ?? $userDetails->userCompanyInformation ?->contact_person_phone_number }}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                                            @if ($errors->has('contact_person_phone_number'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_phone_number') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div> -->

                              

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">{{trans("messages.Company Location")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_location" id="company_location" class="form-control form-control-solid form-control-lg  @error('company_location') is-invalid @enderror" value="{{old('company_location') ?? $userDetails->userCompanyInformation ?->company_location }}">
                                            @if ($errors->has('company_location'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_location') }}
                                            </div>
                                            @endif
                                        </div>
                                        <input type="hidden" name="current_lat" id="current_lat" value="{{old('current_lat') ?? $userDetails->userCompanyInformation ?->latitude }}">
                                        <input type="hidden" name="current_lng" id="current_lng" value="{{old('current_lng') ?? $userDetails->userCompanyInformation ?->longitude }}">
                                    </div>

                                    <!-- <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_email">Company Type</label><span class="text-danger"> * </span>
                                            <select name="company_type" class="form-control select2init  @error('company_type') is-invalid @enderror" >
                                                <option value="">Select Company Type </option>
                                                @foreach($companyType as $row)
                                                    <option value="{{$row->id}}" {{  old('company_type') ? (old('company_type') == $row->id ? 'selected' : '') : ($userDetails->userCompanyInformation ?->company_type == $row->id ? 'selected' : '') }} >{{$row->lookupDiscription->code}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('company_type'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_type') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div> -->

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="company_logo">{{trans("messages.Company Logo")}}</label>
                                            <input type="file" name="company_logo" class="form-control form-control-solid form-control-lg  @error('company_logo') is-invalid @enderror">
                                            @if ($errors->has('company_logo'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('company_logo') }}
                                            </div>
                                            @endif
                                            <!-- @if (!empty($userDetails->userCompanyInformation ->company_logo)) -->
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $userDetails->userCompanyInformation ?->company_logo; ?>">
                                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{ $userDetails->userCompanyInformation ?->company_logo }}" />
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <h3 class="mb-10 font-weight-bold text-dark">
                                {{trans("messages.Contact Person Info")}}
                                </h3>
                                <div class="row">

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name">{{trans("messages.Contact Person Name")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="name" class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror" value="{{ old('name') ?? $userDetails->name }}">
                                            @if ($errors->has('name'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="email">{{trans("messages.Contact Person Email")}}</label><span class="text-danger"> </span>
                                            <input type="text" name="email" class="form-control form-control-solid form-control-lg  @error('email') is-invalid @enderror" value="{{old('email') ?? $userDetails->email }}">
                                            @if ($errors->has('email'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('email') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    

                                    <!-- <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>{{trans("messages.admin_Contact_Person_Number")}}</label><span class="text-danger"> * </span>
                                            <input type="number" name="phone_number" class="form-control form-control-solid form-control-lg  @error('phone_number') is-invalid @enderror" value="{{old('phone_number') ?? $userDetails->phone_number}}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                                            @if ($errors->has('phone_number'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('phone_number') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div> -->

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="phone_number">{{trans("messages.admin_Contact_Person_Number")}}</label><span class="text-danger"> * </span>
                                            <div class="input-group mb-3">
                                                <!-- <div class="input-group-prepend">
                                                    <span class="input-group-text">+972</span>
                                                </div> -->
                                                <input type="text" name="phone_number" class="form-control form-control-solid form-control-lg @error('phone_number') is-invalid @enderror" value="{{old('phone_number') ?? $userDetails->phone_number}}" oninput="validatePhoneNumber(this);" onpaste="validatePhoneNumber(this);">
                                                @if ($errors->has('phone_number'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('phone_number') }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                          
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="contact_person_picture">{{trans("messages.admin_Contact_Person_Profile")}}</label>
                                            <input type="file" name="contact_person_picture" class="form-control form-control-solid form-control-lg  @error('contact_person_picture') is-invalid @enderror">
                                            @if ($errors->has('contact_person_picture'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('contact_person_picture') }}
                                            </div>
                                            @endif
                                            @if (!empty($userDetails->userCompanyInformation ?->contact_person_picture))
                                                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo  $userDetails->userCompanyInformation ?->contact_person_picture; ?>">
                                                    <img width="100px" height="80" alt="{{ trans('messages.admin_common_Image') }}" src="{{  $userDetails->userCompanyInformation ?->contact_person_picture }}" />
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="as_driver">{{trans("messages.customer_as_driver")}}</label>
                                            <input {{$userDetails->truck_company_id > 0 ? "checked" : ""}} name="as_driver" type="checkbox" id="as_driver">
                                        </div>
                                    </div>


                                    
                                </div>

                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div class="row">
                                        <div class="col-6">
                                            <button button type="submit" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                                {{trans('messages.submit')}}
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a type="button" href="{{route('truck-company.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
                                                {{trans("messages.admin_cancel")}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>

         <div class="tab-pane px-10 pt-5 pb-5 {{request('tabs') == 'truck_detail' ? 'active' : ''}}" id="kt_apps_contacts_view_tab_2" role="tabpanel">
            <div class="row justify-content-end">

                <a href='{{route("$model.truck_create",[ base64_encode($TCuserid),  $TCuserid ? ('tc_id=' . $TCuserid) : null, 'from_page=tc_edit'])}}' class="btn btn-primary mr-2 mb-2">
                    {{trans("messages.admin_Add_new_truck")}}
                </a>
            </div>
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
                                        @if($result->is_active == 1)
                                        <a title="{{trans("messages.admin_common_Click_To_Deactivate")}}" href='{{route($model.".status_truck",array($result->id,1))}}' class="btn btn-icon btn-light btn-hover-danger btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Deactivate">
                                            <span class="svg-icon svg-icon-md svg-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                                                            <rect x="0" y="7" width="16" height="2" rx="1" />
                                                            <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1" />
                                                        </g>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        @else
                                        <a title="{{trans("messages.admin_common_Click_To_Activate")}}" href='{{route($model.".status_truck",array($result->id,0))}}' class="btn btn-icon btn-light btn-hover-success btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Activate">
                                            <span class="svg-icon svg-icon-md svg-icon-success">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                        <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) " />
                                                        <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) " />
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                        @endif

                                         <a href="{{route($model.'.edit_truck', [base64_encode($result->id), 'from_page' => 'tc_edit', 'tc_id' => request()->segment(4)])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_Edit")}}">
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
                                        <a href="{{route($model.'.delete_truck', [base64_encode($result->id), 'from_page' => 'tc_edit', 'tc_id' => request()->segment(4)])}}" class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_Delete")}}">                            
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
                                        <a href="{{route($model.'.show_truck',[base64_encode($result->id), 'from_page=tc_edit', 'tc_id' =>base64_encode($TCuserid)])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans("messages.admin_common_View")}}">
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
         <div class="tab-pane px-10 pt-5 pb-5 {{request('tabs') == 'driver_detail' ? 'active' : ''}}" id="kt_apps_contacts_view_tab_3" role="tabpanel">
            <div class="row mb-3 justify-content-end">
                       <a href="{{ route('truck-company-driver.create', [$TCuserid ? ('truck_id=' . $TCuserid) : null, 'from_page=tc_edit']) }}" class="btn btn-primary mr-2">
                                    {{trans("messages.admin_common_Add_New")}} {{trans("messages.admin_common_Driver")}}
                                </a>
                            </div>
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
                                                <a href="javascript:void(0);">{{trans("messages.admin_app_status")}}</a>
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
                                            {{-- <td>
                                                        <div class="text-dark-75 mb-1 font-size-lg">
                                                            {{ $result->licence_exp_date ? date(config("Reading.date_format"),strtotime($result->licence_exp_date)) : "" }}
                            </div>
                            </td> --}}
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
                                @if($result->id != $result->truck_company_id)
                                @if($result->is_active == 1)
                                <a title="{{trans('messages.admin_common_Click_To_Deactivate')}}" href='{{route("$model.status",array($result->id,0))}}' class="btn btn-icon btn-light btn-hover-danger btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Deactivate">
                                    <span class="svg-icon svg-icon-md svg-icon-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                                    <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1" />
                                                </g>
                                            </g>
                                        </svg>
                                    </span>
                                </a>
                                @else
                                <a title="{{trans('messages.admin_common_Click_To_Activate')}}" href='{{route("$model.status",array($result->id,1))}}' class="btn btn-icon btn-light btn-hover-success btn-sm status_any_item" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" data-original-title="Activate">
                                    <span class="svg-icon svg-icon-md svg-icon-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <path d="M9.26193932,16.6476484 C8.90425297,17.0684559 8.27315905,17.1196257 7.85235158,16.7619393 C7.43154411,16.404253 7.38037434,15.773159 7.73806068,15.3523516 L16.2380607,5.35235158 C16.6013618,4.92493855 17.2451015,4.87991302 17.6643638,5.25259068 L22.1643638,9.25259068 C22.5771466,9.6195087 22.6143273,10.2515811 22.2474093,10.6643638 C21.8804913,11.0771466 21.2484189,11.1143273 20.8356362,10.7474093 L17.0997854,7.42665306 L9.26193932,16.6476484 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(14.999995, 11.000002) rotate(-180.000000) translate(-14.999995, -11.000002) " />
                                                <path d="M4.26193932,17.6476484 C3.90425297,18.0684559 3.27315905,18.1196257 2.85235158,17.7619393 C2.43154411,17.404253 2.38037434,16.773159 2.73806068,16.3523516 L11.2380607,6.35235158 C11.6013618,5.92493855 12.2451015,5.87991302 12.6643638,6.25259068 L17.1643638,10.2525907 C17.5771466,10.6195087 17.6143273,11.2515811 17.2474093,11.6643638 C16.8804913,12.0771466 16.2484189,12.1143273 15.8356362,11.7474093 L12.0997854,8.42665306 L4.26193932,17.6476484 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.999995, 12.000002) rotate(-180.000000) translate(-9.999995, -12.000002) " />
                                            </g>
                                        </svg>
                                    </span>
                                </a>
                                @endif
                                @endif

                                <a href="{{route('truck-company-driver.edit',[base64_encode($result->id),($TCuserid?  "truck_id=".$TCuserid : null), 'from_page' => 'tc_edit'])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_Edit')}}">
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
                                <a href="{{route('truck-company-driver.show',[base64_encode($result->id),($TCuserid? "truck_id=".$TCuserid : null), 'from_page=tc_edit'])}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_View')}}">
                                    <span class="svg-icon svg-icon-md svg-icon-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path d="M12.8434797,16 L11.1565203,16 L10.9852159,16.6393167 C10.3352654,19.064965 7.84199997,20.5044524 5.41635172,19.8545019 C2.99070348,19.2045514 1.55121603,16.711286 2.20116652,14.2856378 L3.92086709,7.86762789 C4.57081758,5.44197964 7.06408298,4.00249219 9.48973122,4.65244268 C10.5421727,4.93444352 11.4089671,5.56345262 12,6.38338695 C12.5910329,5.56345262 13.4578273,4.93444352 14.5102688,4.65244268 C16.935917,4.00249219 19.4291824,5.44197964 20.0791329,7.86762789 L21.7988335,14.2856378 C22.448784,16.711286 21.0092965,19.2045514 18.5836483,19.8545019 C16.158,20.5044524 13.6647346,19.064965 13.0147841,16.6393167 L12.8434797,16 Z M17.4563502,18.1051865 C18.9630797,18.1051865 20.1845253,16.8377967 20.1845253,15.2743923 C20.1845253,13.7109878 18.9630797,12.4435981 17.4563502,12.4435981 C15.9496207,12.4435981 14.7281751,13.7109878 14.7281751,15.2743923 C14.7281751,16.8377967 15.9496207,18.1051865 17.4563502,18.1051865 Z M6.54364977,18.1051865 C8.05037928,18.1051865 9.27182488,16.8377967 9.27182488,15.2743923 C9.27182488,13.7109878 8.05037928,12.4435981 6.54364977,12.4435981 C5.03692026,12.4435981 3.81547465,13.7109878 3.81547465,15.2743923 C3.81547465,16.8377967 5.03692026,18.1051865 6.54364977,18.1051865 Z" fill="#000000" />
                                            </g>
                                        </svg>
                                    </span>
                                </a>
                                @if($result->id != $result->truck_company_id)
                                <a href="{{route('truck-company-driver.delete',[base64_encode($result->id), 'from_page' => 'tc_edit', 'tc_id' => request()->segment(4)])}}" class="btn btn-icon btn-light btn-hover-danger btn-sm confirmDelete" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_Delete')}}">
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
                                @endif
                                {{-- <a href="{{route($model.'.changedPassword',array(base64_encode($result->id),($TCuserid? ["truck_id=".$TCuserid] : null)))}}" class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.Change Password')}}">
                                    <span class="svg-icon svg-icon-md svg-icon-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"></path>
                                        </svg>
                                    </span>
                                </a> --}}
                                {{-- <a href='{{URL::to("adminpnlx/customers/send-credentials","$result->id")}}' class="btn btn-icon btn-light btn-hover-primary btn-sm" data-toggle="tooltip" data-placement="top" data-container="body" data-boundary="window" title="" data-original-title="{{trans('messages.admin_common_Send_Credentials')}}">
                                    <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Map/Direction2.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path d="M14,13.381038 L14,3.47213595 L7.99460483,15.4829263 L14,13.381038 Z M4.88230018,17.2353996 L13.2844582,0.431083506 C13.4820496,0.0359007077 13.9625881,-0.12427877 14.3577709,0.0733126292 C14.5125928,0.15072359 14.6381308,0.276261584 14.7155418,0.431083506 L23.1176998,17.2353996 C23.3152912,17.6305824 23.1551117,18.1111209 22.7599289,18.3087123 C22.5664522,18.4054506 22.3420471,18.4197165 22.1378777,18.3482572 L14,15.5 L5.86212227,18.3482572 C5.44509941,18.4942152 4.98871325,18.2744737 4.84275525,17.8574509 C4.77129597,17.6532815 4.78556182,17.4288764 4.88230018,17.2353996 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.000087, 9.191034) rotate(-315.000000) translate(-14.000087, -9.191034) " />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </a> --}}

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


        <div class="tab-pane px-10 pt-5 pb-5" id="kt_apps_contacts_view_tab_4" role="tabpanel">
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
                  <div class="tab-pane px-10  pt-4 pb-4 {{ (request('tabs') == 'plan_details' ? 'active' : '') }}" id="kt_apps_contacts_view_tab_5" role="tabpanel">

                     

                     @if($userTruckCompanySubscription != null)

                      <div class="form-group row my-2">
                        <label class="col-4 col-form-label">{{trans("messages.admin_plan_name")}} :</label>
                        <div class="col-8">
                           <span class="form-control-plaintext font-weight-bolder"> 
                              {{$planDetails->plan_name ?? ''}}
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
                                 <button class="btn btn-primary mx-5" data-toggle="modal" data-target="#extendModal" id="extend_date_btn">{{trans('messages.admin_extend')}}</button>
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

                     <br>
                     <div class="form-group row my-2">
                        <label class="col-4 col-form-label">
                     <a href="{{route($model.'.companySubscriptionPlans',array(base64_encode($userDetails->id)))}}" class="btn btn-primary text-center">{{ trans('messages.admin_update_plan') }}</a>
                        </label>
                        </div>

                     @else 
                     <h3 class="text-center"> {{ trans('messages.this_truck_company_has_no_active_plan_create_plan') }}</h3>
                     <br>
                     <div class="row justify-content-center">
                     <a href="{{route($model.'.companySubscriptionPlans',array(base64_encode($userDetails->id)))}}" class="btn btn-primary text-center">{{ trans('messages.admin_create_plan') }}</a>
                     </div>
                     @endif
                    </div>

               </div>
    </div>
        </div>
    </div>
</div>

@include('admin.truck-company.plan_details_script')

@stop
@section('css')
<style type="text/css">
    .profilePreview, .avatarPreview{display:none;height: 120px;margin-bottom: 15px;}
</style>
@stop
@section('script')
@include('common.googleLocation') 
<script src="https://unpkg.com/@reactivex/rxjs@5.0.0-beta.7/dist/global/Rx.umd.js"></script>
<script>
    $(".profile_img").on('change',function(){
        
        if(jQuery.inArray(this.files[0].type, allowimagetypes) == -1){
            show_message('This file format is not allowed.','error');
            $('.profilePreview').attr('src','');
            $(this).val('');
            return false;
        }
        var img = window.URL.createObjectURL(this.files[0]);
        $('.profilePreview').attr('src',img);
        $('.profilePreview').show();
    })

    function doIt(e) {
        var e = e || event;
        if (e.keyCode == 32) return false;
    }
    window.onload = function() {
        var inp = document.getElementById("zip_code");

        inp.onkeydown = doIt;
    };

    function submit_form() {
        $(".mws-form").submit();
    }
    $('.chosenselect_country').select2({
        placeholder: "Select Country",
        allowClear: true
    });
    function initMap() {
            var ac = new google.maps.places.Autocomplete(document.getElementById('company_location'), {
                types: ['(regions)'] // Restrict results to regions (country, administrative_area_level_1, etc.)
            });
            ac.addListener('place_changed', () => {
                var place = ac.getPlace();
                console.log(place);

                // Extract the address components
                var addressComponents = place.address_components;
                var city, state, zipCode;

                // Loop through the address components and find the city, state, and zip code
                for (var i = 0; i < addressComponents.length; i++) {
                    var component = addressComponents[i];
                    var componentTypes = component.types;

                    // Check if the component is a city
                    if (componentTypes.includes('locality')) {
                        city = component.long_name;
                    }

                    // Check if the component is a state
                    if (componentTypes.includes('administrative_area_level_1')) {
                        state = component.long_name;
                    }

                    // Check if the component is a zip code
                    if (componentTypes.includes('postal_code')) {
                        zipCode = component.long_name;
                    }
                }

                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();

                $("#current_lat").val(lat);
                $("#current_lng").val(lng);

            
            });
        }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=initMap&libraries=places"> </script> 


@stop