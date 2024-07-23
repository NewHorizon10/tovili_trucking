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
                    {{trans("messages.add_new_truck_type")}} </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">
                            {{trans("messages.truck_types")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route($model.'.save')}}" method="post" class="mws-form" autocomplete="off"
                enctype="multipart/form-data">
                @csrf
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="tab-content">
                            @if(!empty($languages))
                                <?php $i = 1 ; ?>
                                @foreach($languages as $language)
                                    <div class="{{ ($i ==  $language_code )?'show active':'' }}" id="{{$language->title}}" role="tabpanel" aria-labelledby="{{$language->title}}">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <h3 class="mb-10 font-weight-bold text-dark">
                                                    <span class="symbol symbol-20 mr-3">
                                                        <img src="{{url (Config::get('constants.LANGUAGE_IMAGE_PATH').$language->image)}}" alt="">
                                                    </span>
                                                    <span class="nav-text">{{$language->title}}</span>
                                                </h3>
                                                <hr>
                                            </div>
                                            <div class="col-xl-12">	
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <!--begin::Input-->
                                                        <div class="form-group">
                                                            <div id="kt-ckeditor-1-toolbar{{$language->id}}"></div>
                                                            @if($i == $language_code)
                                                            <lable for="{{$language->id}}.name">{{trans("messages.name")}}</lable><span class="text-danger"> * </span>
                                                        <input type="text" name="data[{{$language->id}}][name]" class="form-control form-control-solid form-control-lg @error('name') is-invalid @enderror" value="{{old('name')}}">
                                                        @if ($errors->has('name'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('name') }}
                                                        </div>
                                                        @endif													
                                                            @else 
                                                            <lable for="{{$language->id}}.name">{{trans("messages.name")}}</lable><span class="text-danger">  </span>
                                                        <input type="text" name="data[{{$language->id}}][name]" class="form-control form-control-solid form-control-lg @error('name') is-invalid @enderror" value="{{old('name')}}">														
                                                            @endif
                                                        </div>
                                                    </div>												
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $i++; ?>
                                @endforeach
                            @endif
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="multiple_stop_allow" name="multiple_stop_allow" {{ empty(old()) ? '' : (old('multiple_stop_allow') ? "checked" : "")  }}>
                                            <label class="form-check-label" for="multiple_stop_allow">{{trans("messages.admin_master_truck_type_multiple_stop_allow")}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                            <div class="row">
                                <div class="col-6">
                                    <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                        {{trans('messages.submit')}}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a type="button" href="{{route('truck-types.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
                                        {{trans("messages.admin_cancel")}}
                                    </a>
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
    $('.chosenselect_is_free')on('change',function(){
        if($(this).val() == 0){
            $('.price_col').show();
        }else{
            $('.price_col').hide();
            $('.price_inp').val(0);
        }

    })
</script>
@stop