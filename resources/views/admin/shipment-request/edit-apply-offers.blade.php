@extends('admin.layouts.layout')
@section('content')
<style>
    .invalid-feedback {
        display: inline;
    }
</style>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans("messages.edit_apply_offer")}} </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                         </li>
                         <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">    
                            {{trans("messages.Shipment_Request")}}</a>
                         </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route("$model.edit_apply_offer",[$shipmentOffer->id,$shipmentOffer->shipment_id])}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                {{trans("messages.edit_apply_offer")}}
                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="truck_company">{{trans("messages.admin_Truck_Company")}}</label><span class="text-danger"> * </span>
                                            <select name="truck_company" class="form-control select2init  @error('truck_company') is-invalid @enderror">
                                                <option value="">{{trans("messages.Select")}}</option>
                                                @foreach($truckCompanyList as $truckCompany )
                                                    <option value="{{$truckCompany->id}}" {{ ((old('truck_company') ?? $shipmentOffer->truck_company_id) == $truckCompany->id) ? 'selected' : '' }} >{{$truckCompany->name}} ({{$truckCompany->company_name}})</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('truck_company'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('truck_company') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="price_nis">{{trans("messages.price_nis")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="price_nis" class="form-control form-control-solid form-control-lg  @error('price_nis') is-invalid @enderror" value="{{ old('price_nis') ?? $shipmentOffer->price}}">
                                            @if ($errors->has('price_nis'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('price_nis') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="duration">{{trans("messages.duration")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="duration" class="form-control form-control-solid form-control-lg  @error('duration') is-invalid @enderror" value="{{old('duration') ?? $shipmentOffer->duration}}">
                                            @if ($errors->has('duration'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('duration') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="addtional_hours_cost">{{trans("messages.addtional_hours_cost")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="addtional_hours_cost" class="form-control form-control-solid form-control-lg  @error('addtional_hours_cost') is-invalid @enderror" value="{{old('addtional_hours_cost') ?? $shipmentOffer->extra_time_price}}">
                                            
                                            @if ($errors->has('addtional_hours_cost'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('addtional_hours_cost') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="request_date">{{trans("messages.request_date")}}</label><span class="text-danger"> * </span>
                                            <div class="input-group date" id="datepickerfromto" data-target-input="nearest">
                                                <input type="text" name="request_date" class="form-control datetimepicker-input @error('request_date') is-invalid @enderror" value="{{old('request_date') ?? $shipmentOffer->request_offer_date}}" data-target="#datepickerfromto" data-toggle="datetimepicker" >
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="ki ki-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @if ($errors->has('request_date'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('request_date') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="shipmetnt_note_optional">{{trans("messages.shipmetnt_note_optional")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="shipmetnt_note_optional" class="form-control form-control-solid form-control-lg  @error('shipmetnt_note_optional') is-invalid @enderror" value="{{old('shipmetnt_note_optional') ?? $shipmentOffer->description}}">
                                            @if ($errors->has('shipmetnt_note_optional'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('shipmetnt_note_optional') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="term_of_payment">{{trans("messages.term_of_payment")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="term_of_payment" class="form-control form-control-solid form-control-lg  @error('term_of_payment') is-invalid @enderror" value="{{old('term_of_payment') ?? $shipmentOffer->payment_condition}}">
                                            @if ($errors->has('term_of_payment'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('term_of_payment') }}
                                            </div>
                                            @endif
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
                                            <a type="button" href="{{route($model.'.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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
    </div>
</div>

@stop
@section('css')
<style type="text/css">
    .profilePreview, .avatarPreview{display:none;height: 120px;margin-bottom: 15px;}
</style>
@stop
@section('script')
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

    $(".avatar_img").on('change',function(){
        var img = window.URL.createObjectURL(this.files[0]);
        $('.avatarPreview').attr('src',img);
        $('.avatarPreview').show();
    });
    $('#datepickerfromto').datetimepicker({
        format: 'DD-MM-YYYY',
        minDate: '{{$shipmentRequest->request_start_date}}',
        maxDate: '{{$shipmentRequest->request_end_date}}',
    });
</script>
@stop