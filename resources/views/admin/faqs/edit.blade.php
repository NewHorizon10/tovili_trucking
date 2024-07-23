<?php $i = 1; ?>
@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans('messages.admin_common_Edit')}}  {{trans("messages.admin_common_FAQ")}}</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">{{trans("messages.admin_common_FAQs")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <form action="{{route($model.'.update',base64_encode($faqDetails->id))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <lable name="faq_order">{{trans("messages.admin_common_faq_order")}}</lable><span class="text-danger"> * </span>
                                    <input type="text" name="faq_order" class="form-control form-control-solid form-control-lg  @error('faq_order') is-invalid @enderror" value="{{$faqDetails->faq_order ?? old('faq_order')}}">
                                    @if ($errors->has('faq_order'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('faq_order') }}
                                    </div>
                                    @endif
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
                                                    @if($i == 1)
                                                    <lable for="{{$language->id}}.question">{{trans("messages.admin_common_Question")}}</lable><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][question]" class="form-control form-control-solid form-control-lg @error('question') is-invalid @enderror" value="{{$multiLanguage[$language->id]['question'] ?? old('question')}}">
                                                    @if ($errors->has('question'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('question') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <lable for="{{$language->id}}.question">{{trans("messages.admin_common_Question")}}</lable><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][question]" class="form-control form-control-solid form-control-lg" value="{{$multiLanguage[$language->id]['question'] ?? old('question')}}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                <div class="form-group">
                                                    <div id="kt-ckeditor-1-toolbar{{$language->id}}"></div>
                                                    @if($i == 1)
                                                    <lable>{{trans("messages.admin_common_Answer")}} </lable><span class="text-danger"> * </span>
                                                    <textarea id="body_{{$language->id}}" name="data[{{$language->id}}][answer]" class="form-control form-control-solid form-control-lg  @error('answer') is-invalid @enderror" value="{{old('answer')}}">
                                                    {{$multiLanguage[$language->id]['answer'] ?? old('answer')}} </textarea>
                                                    @if ($errors->has('answer'))
                                                    <div class="alert invalid-feedback admin_login_alert">
                                                        {{ $errors->first('answer') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <lable>{{trans("messages.admin_common_Answer")}}</lable>
                                                    <textarea name="data[{{$language->id}}][answer]" id="body_{{$language->id}}" class="form-control form-control-solid form-control-lg">
                                                    {{$multiLanguage[$language->id]['answer'] ?? old('answer')}}</textarea>
                                                    @endif
                                                </div>
                                                <script src="{{asset('/public/js/ckeditor/ckeditor.js')}}"></script>
                                                <script>
                                                    CKEDITOR.replace(<?php echo 'body_' . $language->id; ?>, {
                                                        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                        enterMode: CKEDITOR.ENTER_BR
                                                    });
                                                    CKEDITOR.config.allowedContent = true;
                                                </script>
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
                            <!-- <div>
                                <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                {{trans('messages.submit')}}
                                </button>
                            </div> -->
                            <div class="row">
                                <div class="col-6">
                                    <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                        {{trans('messages.submit')}}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a type="button" href="{{ route('faqs.index')}}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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
@stop