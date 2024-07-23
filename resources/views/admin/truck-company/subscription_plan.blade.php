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
                    {{trans("messages.admin_subscription_plans")}}
                 </h5>
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
            <form action="{{route('subscribe_plan.save', request()->id)}}" method="post" class="mws-form" autocomplete="off"
                enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">
                                    {{trans("messages.admin_common_plan_information")}}
                                </h3>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">                                        
                                            <label for="is_free">{{trans("messages.admin_common_Is_Free")}}</label><span class="text-danger"> * </span>
                                            <select name="is_free" id="is_free" class="set-plan-duration form-control select2init chosenselect_is_free 1-form-fields" data-is-required="1">
                                                <option value="0" {{ $subscribePlan ? (($subscribePlan->is_free ?? '') == 0 ? 'selected' : '') : 'selected' }}>{{trans("messages.admin_common_Paid")}}</option>
                                                <option value="1" {{ $subscribePlan ? (($subscribePlan->is_free ?? '') == 1 ? 'selected' : '') : ''  }}>{{trans("messages.Free")}}</option>
                                            </select> 
                                            <div class="invalid-feedback error-is_free"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6" style="display: none;">
                                        <div class="form-group">                                        
                                            <label for="column_type">{{trans("messages.type")}}</label><span class="text-danger"> * </span>
                                            <select name="column_type" id="column_type" class="set-plan-duration form-control select2init chosenselect_column_type 1-form-fields" data-is-required="1">
                                                <option value="1" {{ $subscribePlan ? (($subscribePlan->column_type ?? '') == 1 ? 'selected' : '') : '' }}>{{trans("messages.More then 5")}}</option>
                                            </select> 
                                            <div class="invalid-feedback error-column_type"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 plan-duration" style="display:none;">
                                        <div class="form-group">                                        
                                            <label for="plan_duration">{{trans("messages.admin_common_Plan_Duration")}}</label><span class="text-danger"> * </span>
                                            <select name="plan_duration" id="plan_duration" class=" form-control select2init chosenselect_plan_duration 1-form-fields" >
                                                <option value="" selected>{{trans("messages.admin_common_select_type")}}</option>
                                            </select> 
                                            <div class="invalid-feedback error-plan_duration"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 ">
                                        <div class="form-group">                                        
                                            <label for="plan_name">{{trans("messages.admin_plan_name")}}</label><span class="text-danger"> * </span>
                                            <select name="plan_name" id="plan_name" class=" form-control select2init chosenselect_plan_name 1-form-fields" data-is-required="1">
                                                <option value="" selected>{{trans('messages.Select')}} {{trans("messages.admin_plan_name")}}</option>
                                            </select> 
                                            <div class="invalid-feedback error-plan_name"></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="type" class="addType" value="{{$subscribePlan->type ?? '0'}}">
                                    <div class="col-xl-6 price_col" id="priceDivTag" @if( ( $subscribePlan->is_free ?? '' ) == 1 ) style="display:none" @endif>
                                        <div class="form-group price-tag" >
                                            <label for="price">{{trans("messages.admin_common_Price")}}</label><span class="text-danger"> * </span>
                                            <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').substring(0, 10);" name="price" class="form-control form-control-solid price_inp form-control-lg 1-form-fields" data-is-required="1" value="{{ old('price') ?? ($subscribePlan->price ?? "" ) }}">
                                            <div class="invalid-feedback error-price"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6" id="discountCheckDivTag" @if( ($subscribePlan->is_free ?? '') == 1 || $errors->has('discount_price')) style="display:none" @endif>
                                        <div class="form-group " >
                                            <label for="check">{{trans('messages.discount')}} </label> 
                                            <br>
                                            <input type="checkbox" name="discountCheck" id="discountCheck" {{old('discountCheck') || ($subscribePlan->discount ?? '' > 0) ? 'checked' : ''}}  class="form-control-lg">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 " id="discountPrice" {{ (old('discount_price') || ($subscribePlan->is_free ?? '') == 0 && ($subscribePlan->discount ?? 0) > 1) ? '' : 'style=display:none'}} >
                                        <div class="form-group " >
                                            <label for="check">{{trans('messages.discount')}} (%)</label> <span class="text-danger"> *</span>
                                            <br>
                                            <input type="text" name="discount_price" class="form-control form-control-solid form-control-lg form-control-lg 1-form-fields discount_price" data-is-required="1" value="{{ old('discount_price') ?? ($subscribePlan->discount ?? "" ) }}">
                                            @if($errors->has('discount_price'))
                                            <div class="text-danger d-block">{{$errors->first('discount_price')}}</div>
                                            @endif
                                        </div>
                                        <div class="invalid-feedback error-discount_price"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div class="row">
                                        <div class="col-6">
                                            <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4 next-step-btn" data-next-step="1">
                                                {{trans('messages.submit')}}
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a type="button" href="{{ route($model.'.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
                                                {{trans("messages.admin_cancel")}}
                                            </a>
                                        </div>
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
<script>

</script>
@stop
@section('script')
<script>
 $(document).ready(function() {
    $('#plan_name').on('change',function(){
        var planPrice = $(this).find(':selected').attr('data-plan-price');
        $("input[name=price]").val(planPrice);
    })
    $('#discountCheck').change(function() {
        if ($(this).is(':checked')) {
            $("#discountPrice").show().find('input').attr('data-is-required','1');
        }else{
            $("#discountPrice").hide().find('input').attr('data-is-required','0');;
        }
    });
    
    $.fn.checkValidation = function(nextStepNumber) {
        var flag = false;
        $("." + nextStepNumber + "-form-fields").each(function() {
            if (($(this).val() == "" && $(this).attr("data-is-required") == "1") || ($(this).attr('type') == "checkbox" && !$(this).is(':checked')) || ($(this).attr('type') == "radio" && !$("input[name='"+$(this).attr('name')+"']").is(':checked'))) {
                flag = true;
                var str_name = $(this).attr("name");
                str_name = str_name.replace('[', '').replace(']', '');
                $(".error-" + str_name).html("{{trans('messages.This field is required')}}");
                $(this).addClass("is-invalid");
            }
        });
        return flag;
    }

        
    $("body").on("click", ".next-step-btn", function() {
        $(".invalid-feedback").html("");
        $(".is-invalid").removeClass("is-invalid");
        var nextStepNumber = $(this).data("next-step");
        var flag = $.fn.checkValidation(nextStepNumber);
        
        if (flag) {
            $("input.is-invalid:first").focus();
            return false;
        } 
    });
    $(".set-plan-duration").click(function() {
        if($(this).attr('name') == "is_free"){
            if($(this).val() == "1"){
                $("#priceDivTag").hide().find('input').val('').attr('data-is-required','0');
                $("#discountCheckDivTag").hide();
                $("#discountCheck").prop("checked", false);
                $("#discountPrice").hide().find('input').val('').attr('data-is-required','0');
            }else{
                $("#priceDivTag").show().find('input').attr('data-is-required','1');
                $("#discountCheckDivTag").show();
            }
        }
        $.fn.setPlanDuration();
    }); 
    var selected_plan_duration = "{{$subscribePlan->plan_id ?? ''}}"
    var selected_plan_name     = "{{$subscribePlan->plan_id ?? ''}}"

    $.fn.setPlanDuration = function(nextStepNumber) {

        var typeValue = $("#column_type").val();
        var isFreeValue = $(".set-plan-duration").val();
        if(typeValue == ''){
            typeValue = 0;
        }
        if(isFreeValue == ''){
            isFreeValue = 0;
        }
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '{{ route('truck-company.getPlanDuration')}}/'+ isFreeValue + '/' + typeValue,
            success: function(response) {
                $.each(response, function(index, value) {
                    $("#plan_name").html(value);
                    $("#plan_name").val(selected_plan_name);
                   selected_plan_name = '';
                });
            }
        });
    }
    $.fn.setPlanDuration();
});


</script>
@stop

