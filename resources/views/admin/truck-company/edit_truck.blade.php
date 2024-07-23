<?php $i = 1; ?>
@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        {{trans("messages.admin_Edit_Truck")}}
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
                                <a href="{{ route('truck-company.edit', [request('tc_id'), 'tabs=truck_detail'])}}" class="text-muted"> {{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}</a>
                            </li> 
                         @elseif(request('from_page') != '' && request('from_page') == 'tc_view')
                            <li class="breadcrumb-item">
                                <a href="{{ route('truck-company.show', [request('tc_id'), 'tabs=truck_detail'])}}" class="text-muted"> {{trans('messages.admin_common_Edit')}} {{trans('messages.admin_Truck_Company')}}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <form action="{{route($model.'.update_truck',$entruckid)}}" method="POST" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="from_page" value="{{request('from_page') ?? ''}}"> 
                            <div class="col-xl-6">
                                <div class="form-group">
                                <label for="type_of_truck">{{trans("messages.Type_of_truck")}}</label><span class="text-danger"> * </span>
                                    <select name="truck_type" class="form-control select2init truck_types @error('company_type') is-invalid @enderror 1-form-fields" data-is-required="1" data-form-step="1" >
                                        <option value="">{{trans("messages.admin_common_select_truck_type")}}</option>
                                        @foreach($truckType as $row)
                                            <option value="{{$row->id}}" {{old('truck_type') ? (old('truck_type') == $row->id ? 'selected' : '') : ($truckDetails->type_of_truck == $row->id ? 'selected' : '') }} >{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class=" invalid-feedback error-company_type">
                                        @if ($errors->has('truck_type'))
                                            {{ $errors->first('truck_type') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="truck_number">{{trans("messages.Truck Number")}}</label>
                                    <input type="text" name="truck_system_number" class="form-control form-control-solid form-control-lg  @error('truck_system_number') is-invalid @enderror 1-form-fields" data-is-required="1" value="{{old('truck_system_number') ?? $truckDetails->truck_system_number}}">
                                    <div class="invalid-feedback error-truck_system_number">
                                        @if ($errors->has('truck_system_number'))
                                            {{ $errors->first('truck_system_number') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @foreach($truckType as $row)
                                <div class="col-xl-12 row truckTypeQuestionnaireClass{{$i}}" id="truckTypeQuestionnaire-{{$i}}-{{$row->id}}" style="display: {{ $row->id == $truckDetails->type_of_truck ? 'block' : 'none'}};">
                                    @foreach($row->TruckTypeQuestionsList as $questionsRow)
                                        @php
                                            $detailsValue = "";
                                            $detailsIsRequired = "0";
                                            if($row->id == $truckDetails->type_of_truck){
                                                if (!is_array($truckDetails->questionnaire)){
                                                    $questionnaire = (array)$truckDetails->questionnaire;
                                                    $detailsValue = $questionnaire[$questionsRow->id] ?? ""; 
                                                    if (is_array($detailsValue)){
                                                        $detailsValue = $detailsValue[0];
                                                    }
                                                    $detailsIsRequired = "0";
                                                }
                                            }
                                        @endphp

                                        <div class="for-question question-tag-{{$i}}-{{$questionsRow->id}} col-xl-6" data-question-row-id="{{$i}}" data-question-id="{{$questionsRow->id}}" @if($questionsRow->id == '23') style="display:none;" @endif >
                                            <label for="ans{{$i}}{{$row->id}}" class="form-label">{{$questionsRow->TruckTypeQuestionDiscription->name}}</label>
                                            @if($questionsRow->input_type == "number")
                                                <input type="number" class="form-control {{$i}}-form-fields remSpinnersIcon" data-is-required="{{$detailsIsRequired}}" id="ans-{{$i}}-{{$row->id}}-{{$questionsRow->id}}"
                                                value="{{$detailsValue}}"
                                                aria-describedby="ans{{$i}}{{$row->id}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}]">
                                                <small class="text-danger text-danger-text error-ans{{$i}}{{$row->id}}{{$questionsRow->id}}"></small>
                                            @elseif($questionsRow->input_type == "choice")
                                                @php
                                                    $input_description = explode(",",$questionsRow->TruckTypeQuestionDiscription->input_description);
                                                @endphp
                                                <div class="customRadio">
                                                    @foreach($input_description as $key => $option)
                                                    <span class="radioLabelrow">
                                                        <input type="checkbox" id="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}][]" class="{{$i}}-form-fields" data-is-required="{{$detailsIsRequired}}" value="{{$key}}" {{ $detailsValue == $key ? "checked" : "" }}>
                                                        <label for="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}">{{$option}}</label>
                                                    </span>
                                                    @endforeach
                                                </div>
                                                <small class="text-danger text-danger-text error-ans{{$i}}{{$row->id}}{{$questionsRow->id}}"></small>
                                            @elseif($questionsRow->input_type == "text")
                                                    <input type="text" class="form-control {{$i}}-form-fields" data-is-required="$detailsIsRequired" id="ans{{$i}}{{$row->id}}" value="{{$detailsValue}}" aria-describedby="ans{{$i}}{{$row->id}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}]">
                                                    <small class="text-danger text-danger-text error-ans{{$i}}{{$row->id}}{{$questionsRow->id}}"></small>
                                            @elseif($questionsRow->input_type == 'radio')
                                                @php
                                                    $input_description = explode(",",$questionsRow->TruckTypeQuestionDiscription->input_description);
                                                @endphp
                                                <div class="customRadio">
                                                    @foreach($input_description as $key => $option)
                                                    <span class="radioLabelrow">
                                                        <input type="radio" id="TruckTypeQuestions{{$i}}{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}][]" {{ $key == 0 ? 'checked' : ''}}  value="{{$key}}"  {{ $detailsValue == $key ? "checked" : "" }} >
                                                        <label for="TruckTypeQuestions{{$i}}{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}">{{$option}}</label>
                                                    </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <lable for="truck_insurance_picture">{{trans("messages.Upload_an_Insurance_certificate")}}</lable>
                                    <input type="file" name="truck_insurance_picture" class="form-control form-control-solid form-control-lg @error('truck_insurance_picture') is-invalid @enderror 1-form-fields" data-is-required="0" value="{{ old('truck_insurance_picture') }}">
                                    <a class="fancybox-buttons" data-fancybox-group="button" href="{{$truckDetails->truck_insurance_picture }}"><img height="50" width="50" src="{{$truckDetails->truck_insurance_picture }}" /></a>
                                    <div class=" invalid-feedback  error-truck_insurance_picture">
                                        @if ($errors->has('truck_insurance_picture'))
                                            {{ $errors->first('truck_insurance_picture') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <label>{{trans("messages.Expiry_date_of_insurance")}}</label>
                                <div class="input-group date" id="truck_insurance_expiration_date_id" data-target-input="nearest">
                                    {{ Form::text('truck_insurance_expiration_date',(
                                        
                                        old('truck_insurance_expiration_date') ?? ($truckDetails->truck_insurance_expiration_date ? \Carbon\Carbon::createFromFormat('Y-m-d', ($truckDetails->truck_insurance_expiration_date))->format('d/m/y') : '')

                                    ), ['class' => ' form-control datetimepicker-input','placeholder'=>'','data-target'=>'#truck_insurance_expiration_date_id','data-toggle'=>'datetimepicker']) }}
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="ki ki-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-group">
                                    <lable for="truck_licence_number">{{trans("messages.Upload_an_truck_license")}}</lable>
                                    <input type="file" name="truck_licence_number" class="form-control form-control-solid form-control-lg @error('truck_licence_number') is-invalid @enderror 1-form-fields" data-is-required="0" value="{{ old('truck_licence_number') }}">
                                    <a class="fancybox-buttons" data-fancybox-group="button" href="{{$truckDetails->truck_licence_number }}"><img height="50" width="50" src="{{$truckDetails->truck_licence_number }}" /></a>
                                    <div class=" invalid-feedback  error-truck_licence_number">
                                        @if ($errors->has('truck_licence_number'))
                                            {{ $errors->first('truck_licence_number') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <label>{{trans("messages.Expiry_date_of_license")}}</label>
                                <div class="input-group date" id="truck_licence_expiration_date" data-target-input="nearest">
                                    {{ Form::text('truck_licence_expiration_date',(
                                        
                                        old('truck_licence_expiration_date') ?? ($truckDetails->truck_licence_expiration_date ? \Carbon\Carbon::createFromFormat('Y-m-d', ($truckDetails->truck_licence_expiration_date))->format('d/m/y') : '')

                                    ), ['class' => ' form-control datetimepicker-input','placeholder'=>'','data-target'=>'#truck_licence_expiration_date','data-toggle'=>'datetimepicker']) }}
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="ki ki-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="driver_id">{{trans("messages.selecte_driver")}}</label><span class="text-danger"> * </span>
                                    <select name="driver_id" class="form-control select2init  @error('driver_id') is-invalid @enderror 1-form-fields" data-is-required="0" >
                                        <option value="">{{trans("messages.selecte_driver")}}</option>
                                        @foreach($free_driver as $row)
                                            <option value="{{$row->id}}" {{old() ? (old('driver_id') == $row->id ? 'selected' : '') : ($row->id == $truckDetails->driver_id ? 'selected' : '')}} >{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class=" invalid-feedback error-driver_id">
                                    @if ($errors->has('driver_id'))
                                        {{ $errors->first('driver_id') }}
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                            <div class="row">
                                <div class="col-6">
                                    <button button type="button" class="btn btn-success font-weight-bold text-uppercase px-9 py-4 next-step-btn" data-next-step="1">
                                        {{trans('messages.submit')}}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a type="button" href="{{ route($model.'.index_truck',[$entruckid])}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
                                        {{trans("messages.admin_cancel")}}
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $( document ).ready(function() {
        $('#truck_licence_expiration_date, #truck_insurance_expiration_date_id').datetimepicker({
            format: 'DD/MM/YY',
        });

$.fn.checkValidation = function(nextStepNumber) {
    // return false;
    var flag = false;
    $("." + nextStepNumber + "-form-fields").each(function() {
        if ($(this).data("type") == "same") {
            if ($(this).val() != $("input[name='" + $(this).data("same-with") + "']").val()) {
                flag = true;
                $(".error-" + $(this).attr("name")).html("Sa valeur doit être la même que la valeur de " + ($("input[name='" + $(this).data("same-with") + "']").data("name")));
                $(this).addClass("is-invalid");

            }
        }
        if (
            ($(this).val() == "" && $(this).attr("data-is-required") == "1") ||
            ($(this).attr('type') == "checkbox" && !$("input[name='"+$(this).attr('name')+"']").is(':checked') && $(this).attr("data-is-required") == "1") ||
            ($(this).attr('type') == "radio" && !$("input[name='"+$(this).attr('name')+"']").is(':checked') && $(this).attr("data-is-required") == "1")
        ) {

            flag = true;
            var str_name = $(this).attr("name");
            str_name = str_name.replace(/\[/g, '').replace(/\]/g, '');
            $(".error-" + str_name).html("{{ trans("messages.This field is required") }}");
            $(this).addClass("is-invalid");
        }
    });
    return flag;
}

$("body").on("click", ".next-step-btn", function() {
    $(".text-danger-text").html("");
    $(".is-invalid").removeClass("is-invalid");
    var nextStepNumber = $(this).data("next-step");
    var flag = $.fn.checkValidation(nextStepNumber);
    
    if (flag) {
        $("input.is-invalid:first").focus();
        return false;
    } else {
        if (nextStepNumber == 1) {
            $('form').submit();
            return false;
        }
    }
});


$("body").on("change", ".truck_types", function() {
    var dataformstep = $(this).attr('data-form-step');
    $(".truckTypeQuestionnaireClass"+dataformstep).hide();
    $(".truckTypeQuestionnaireClass"+dataformstep+" input[type=number], .truckTypeQuestionnaireClass"+dataformstep+" input[type=text], .truckTypeQuestionnaireClass"+dataformstep+" input[type=checkbox]").attr("data-is-required",0);

    $("#truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()).show();
    $("#truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()+" input[type=number], #truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()+" input[type=text], #truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()+" input[type=checkbox]").attr("data-is-required",0);
    
    if($(this).val() == 9){
        var changedQuestionValue = $('.question-tag-'+dataformstep+'-21 input:checked').val();
        if(changedQuestionValue == 0){
            $(".question-tag-"+dataformstep+"-22").show().find("input").attr("data-is-required",1);
            $(".question-tag-"+dataformstep+"-23").hide().find("input").attr("data-is-required",0);
        }else if(changedQuestionValue == 1){
            $(".question-tag-"+dataformstep+"-22").hide().find("input").attr("data-is-required",0);
            $(".question-tag-"+dataformstep+"-23").show().find("input").attr("data-is-required",1);
        }
    }else if($(this).val() == 7){
        var changedQuestionValue = $('.question-tag-'+dataformstep+'-14 input:checked').val();
        if(changedQuestionValue == 0){
            $(".question-tag-"+dataformstep+"-15").show().find("input").attr("data-is-required",1);
            $(".question-tag-"+dataformstep+"-16").hide().find("input").attr("data-is-required",0);
        }else if(changedQuestionValue == 1){
            $(".question-tag-"+dataformstep+"-15").hide().find("input").attr("data-is-required",0);
            $(".question-tag-"+dataformstep+"-16").show().find("input").attr("data-is-required",1);
        }
        var changedQuestionValue = $('.question-tag-'+dataformstep+'-17 input:checked').val();
        if(changedQuestionValue == 0){
            $(".question-tag-"+dataformstep+"-18").show().find("input").attr("data-is-required",1);
        }else if(changedQuestionValue == 1){
            $(".question-tag-"+dataformstep+"-18").hide().find("input").attr("data-is-required",0);
        }
    }
});

//////
    var dataformstep = $(".truck_types").attr('data-form-step');
    $(".truckTypeQuestionnaireClass"+dataformstep).hide();
    $(".truckTypeQuestionnaireClass"+dataformstep+" input[type=number], .truckTypeQuestionnaireClass"+dataformstep+" input[type=text], .truckTypeQuestionnaireClass"+dataformstep+" input[type=checkbox]").attr("data-is-required",0);

    $("#truckTypeQuestionnaire-"+dataformstep+"-"+$(".truck_types").val()).show();
    $("#truckTypeQuestionnaire-"+dataformstep+"-"+$(".truck_types").val()+" input[type=number], #truckTypeQuestionnaire-"+dataformstep+"-"+$(".truck_types").val()+" input[type=text], #truckTypeQuestionnaire-"+dataformstep+"-"+$(".truck_types").val()+" input[type=checkbox]").attr("data-is-required",0);
    
    if($(".truck_types").val() == 9){
        var changedQuestionValue = $('.question-tag-'+dataformstep+'-21 input:checked').val();
        if(changedQuestionValue == 0){
            $(".question-tag-"+dataformstep+"-22").show().find("input").attr("data-is-required",1);
            $(".question-tag-"+dataformstep+"-23").hide().find("input").attr("data-is-required",0);
        }else if(changedQuestionValue == 1){
            $(".question-tag-"+dataformstep+"-22").hide().find("input").attr("data-is-required",0);
            $(".question-tag-"+dataformstep+"-23").show().find("input").attr("data-is-required",1);
        }
    }else if($(".truck_types").val() == 7){
        var changedQuestionValue = $('.question-tag-'+dataformstep+'-14 input:checked').val();
        if(changedQuestionValue == 0){
            $(".question-tag-"+dataformstep+"-15").show().find("input").attr("data-is-required",1);
            $(".question-tag-"+dataformstep+"-16").hide().find("input").attr("data-is-required",0);
        }else if(changedQuestionValue == 1){
            $(".question-tag-"+dataformstep+"-15").hide().find("input").attr("data-is-required",0);
            $(".question-tag-"+dataformstep+"-16").show().find("input").attr("data-is-required",1);
        }
        var changedQuestionValue = $('.question-tag-'+dataformstep+'-17 input:checked').val();
        if(changedQuestionValue == 0){
            $(".question-tag-"+dataformstep+"-18").show().find("input").attr("data-is-required",1);
        }else if(changedQuestionValue == 1){
            $(".question-tag-"+dataformstep+"-18").hide().find("input").attr("data-is-required",0);
        }
    }

//////

$(".for-question input").change(function(){
    var changedQuestionId = $(this).closest(".for-question").attr("data-question-id");
    var changedQuestionRowId = $(this).closest(".for-question").attr("data-question-row-id");
    if(changedQuestionId == 21){
        if($(this).val() == 0){
            $(".question-tag-"+changedQuestionRowId+"-22").show().find("input").attr("data-is-required",1);
            $(".question-tag-"+changedQuestionRowId+"-23").hide().find("input").attr("data-is-required",0);
        }else if($(this).val() == 1){
            $(".question-tag-"+changedQuestionRowId+"-22").hide().find("input").attr("data-is-required",0);
            $(".question-tag-"+changedQuestionRowId+"-23").show().find("input").attr("data-is-required",1);
        }
    }else if(changedQuestionId == 14){
        if($(this).val() == 0){
            $(".question-tag-"+changedQuestionRowId+"-15").show().find("input").attr("data-is-required",1);
            $(".question-tag-"+changedQuestionRowId+"-16").hide().find("input").attr("data-is-required",0);
        }else if($(this).val() == 1){
            $(".question-tag-"+changedQuestionRowId+"-15").hide().find("input").attr("data-is-required",0);
            $(".question-tag-"+changedQuestionRowId+"-16").show().find("input").attr("data-is-required",1);
        }
    }else if(changedQuestionId == 17){
        if($(this).val() == 0){
            $(".question-tag-"+changedQuestionRowId+"-18").show().find("input").attr("data-is-required",1);
        }else if($(this).val() == 1){
            $(".question-tag-"+changedQuestionRowId+"-18").hide().find("input").attr("data-is-required",0);
        }
    }
});
});
</script>
@stop