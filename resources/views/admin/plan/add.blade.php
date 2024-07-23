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
                    {{trans("messages.admin_common_Add_New")}} </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">
                            {{trans("messages.Plans")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route('plan.save')}}" method="post" class="mws-form" autocomplete="off"
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
                                        <div class="form-group " >
                                            <label for="plan_name">{{trans("messages.admin_plan_name")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="plan_name"
                                                class="form-control form-control-solid form-control-lg  @error('plan_name') is-invalid @enderror"
                                                value="{{old('plan_name')}}">
                                            @if ($errors->has('plan_name'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('plan_name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">                                        
                                            <label for="plan_duration">{{trans("messages.admin_common_Plan_Duration")}}</label><span class="text-danger"> * </span>
                                            <select name="plan_duration" id="plan_duration" class=" form-control select2init chosenselect_plan_duration @error('plan_duration') is-invalid @enderror">
                                                <option value="" selected disabled>{{trans("messages.admin_common_select_plan_duration")}}</option>
                                                <option value="0" {{ old('plan_duration') == '0' ? 'selected' : ''}}>{{trans("messages.monthly")}}</option>
                                                <option value="1" {{ old('plan_duration') == '1' ? 'selected' : ''}}>{{trans("messages.quarterly")}}</option>
                                                <option value="2" {{ old('plan_duration') == '2' ? 'selected' : ''}}>{{trans("messages.half_yearly")}}</option>
                                                <option value="3" {{ old('plan_duration') == '3' ? 'selected' : ''}}>{{trans("messages.Yearly")}}</option>
                                            </select> 
                                            @if ($errors->has('plan_duration'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('plan_duration') }}
                                            </div>
                                            @endif                
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">                                        
                                            <label for="is_free">{{trans("messages.admin_common_Is_Free")}}</label><span class="text-danger"> * </span>
                                            <select name="is_free" id="is_free" class=" form-control select2init chosenselect_is_free @error('designation_id') is-invalid @enderror">
                                                <option value="" selected disabled>{{trans("messages.admin_common_select_price_type")}}</option>
                                                <option value="0" {{ old('is_free') == '0' ? 'selected' : ''}}>{{trans("messages.admin_common_Paid")}}</option>
                                                <option value="1" {{ old('is_free') == '1' ? 'selected' : ''}}>{{trans("messages.Free")}}</option>
                                            </select> 
                                            @if ($errors->has('is_free'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('is_free') }}
                                            </div>
                                            @endif                
                                        </div>
                                    </div>

                                    
                                    <div class="col-xl-6 price_col {{ (old('is_free') == 1) ? 'd-none' : '' }}" >
                                        <div class="form-group price-tag" >
                                            <label for="price">{{trans("messages.admin_common_Price")}}</label><span class="text-danger"> * </span>
                                            <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').substring(0, 10);" name="price"
                                                class="form-control form-control-solid form-control-lg  @error('price') is-invalid @enderror price_inp"
                                                value="{{old('price')}}">
                                            @if ($errors->has('price'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('price') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <lable for="icon">{{trans("messages.admin_common_Image")}}</lable><span class="text-danger">*</span>
                                            <input type="file" name="image" accept=".jpg,.jpeg,.png"
                                                class="form-control form-control-solid form-control-lg @error('image') is-invalid @enderror">
                                            @if ($errors->has('image'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('image') }}
                                            </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div class="row">
                                        <div class="col-6">
                                            <button button type="submit" onclick="submit_form();"
                                                class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                                {{trans('messages.submit')}}
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a type="button" href="{{ route('plan.index') }}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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
@include('admin.plan.script')

<script>
    $('.chosenselect_is_free').on('change',function(){
        if($(this).val() == 0){
            $('.price_col').removeClass('d-none');
        }else{
            $('.price_col').addClass('d-none');
            $('.price_inp').val(0);
        }

    })
</script>
@stop