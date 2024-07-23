<?php $i = 1; ?>
@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{ trans('messages.admin_edit_truck_type_questions') }}</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">
                                {{trans("messages.truck_types")}}
                             </a>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.questionnaire.index',$entruck_typeid)}}" class="text-muted">{{trans("messages.admin_truck_type_questions")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <form action="{{route($model.'.questionnaire.update',array($entruck_typeid,$enid))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-10">
                                
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="input_type"> {{trans("messages.admin_input_type")}}</label><span class="text-danger"> * </span>
                                            <select type="text" name="input_type" class="form-control alfa form-control-solid form-control-lg  @error('input_type') is-invalid @enderror" value="{{old('input_type')}}">
                                                <option value="">Select</option>
                                                <option {{old('input_type') == "number" ? "selected" : ($truck_typeFeatureDetails->input_type == "number" ? "selected" : "") }} value="number">{{trans('messages.admin_number')}}</option>
                                                <option {{old('input_type') == "choice" ? "selected" : ($truck_typeFeatureDetails->input_type == "choice" ? "selected" : "") }} value="choice">{{trans('messages.admin_choice')}}</option>
                                                <option {{old('input_type') == "text" ? "selected" : ($truck_typeFeatureDetails->input_type == "text" ? "selected" : "") }} value="text">{{trans('messages.admin_text')}}</option>
                                                <option {{old('input_type') == "radio" ? "selected" : ($truck_typeFeatureDetails->input_type == "radio" ? "selected" : "") }} value="radio">{{trans('messages.admin_radio')}}</option>
                                            </select>
                                            @if ($errors->has('input_type'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('input_type') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="tab-content">
                            @if(!empty($languages))
                            <?php $i = 1; ?>
                            @foreach($languages as $language)
                            <div class="{{($i==$language_code)?'show active':'' }}" id="{{$language->title}}" role="tabpanel" aria-labelledby="{{$language->title}}">
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
                                                <div class="form-group">
                                                    @if($i == $language_code)
                                                    <lable for="{{$language->id}}.question">{{trans("messages.admin_question")}}</lable><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][question]" class="form-control form-control-solid form-control-lg @error('question') is-invalid @enderror" value="{{ old('data.'.$language->id.'.question') ?? $multiLanguage[$language->id]['name']}}">
                                                    @if ($errors->has('question'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('question') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <lable for="{{$language->id}}.question">{{trans("messages.admin_question")}}</lable><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][question]" class="form-control form-control-solid form-control-lg" value="{{old('data.'.$language->id.'.question') ?? $multiLanguage[$language->id]['name']}}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    @if($i == $language_code)
                                                    <lable for="{{$language->id}}.question_descriptions">{{trans("messages.admin_question_descriptions")}}</lable><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][question_descriptions]" class="form-control form-control-solid form-control-lg @error('question_descriptions') is-invalid @enderror" value="{{ old('data.'.$language->id.'.question_descriptions') ?? $multiLanguage[$language->id]['question_descriptions']}}">
                                                    @if ($errors->has('question_descriptions'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('question_descriptions') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <lable for="{{$language->id}}.question_descriptions">{{trans("messages.admin_question_descriptions")}}</lable><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][question_descriptions]" class="form-control form-control-solid form-control-lg" value="{{old('data.'.$language->id.'.question_descriptions') ?? $multiLanguage[$language->id]['question_descriptions']}}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-6 input_description_div">
                                                <div class="form-group">
                                                    @if($i == $language_code)
                                                    <lable for="{{$language->id}}.input_description">{{trans("messages.input_description")}}</lable><span class="text-danger"> * </span>
                                                    <textarea type="text" name="data[{{$language->id}}][input_description]" class="form-control form-control-solid form-control-lg @error('input_description') is-invalid @enderror" placeholder="{{trans('messages.example_option_one_option_two')}}">{{old('data.'.$language->id.'.input_description') ?? $multiLanguage[$language->id]['input_description']}}</textarea>
                                                    @if ($errors->has('input_description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('input_description') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <lable for="{{$language->id}}.input_description">{{trans("messages.input_description")}}</lable><span class="text-danger"> </span>
                                                    <textarea type="text" name="data[{{$language->id}}][input_description]" class="form-control form-control-solid form-control-lg" placeholder="{{trans('messages.example_option_one_option_two')}}">{{old('data.'.$language->id.'.input_description') ?? $multiLanguage[$language->id]['input_description']}}</textarea>
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
                        </div>
                        <div class="d-flex justify-content-between border-top mt-5 pt-10">
                            <div class="row">
                                <div class="col-6">
                                    <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                        {{trans('messages.submit')}}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a type="button" href="{{ route($model.'.questionnaire.index',$entruck_typeid)}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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
    
    @php
        if(old()){
            $truck_typeFeatureDetails->input_type = old('input_type');
        }
    @endphp
    @if($truck_typeFeatureDetails->input_type == "choice" || $truck_typeFeatureDetails->input_type == "radio"  )
        $(".input_description_div").show();
    @else
        $(".input_description_div").hide();
    @endif
    
    $('select[name=input_type]').on('change',function(){
        if( $(this).val() == "choice" || $(this).val() == "radio" ){
            $(".input_description_div").show();
        }else{
            $(".input_description_div").hide();
        }
    })
</script>
@stop