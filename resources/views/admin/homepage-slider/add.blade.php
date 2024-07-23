@extends('admin.layouts.layout')
@section('content')

<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                    {{trans("messages.admin_common_Add_New")}} {{trans("messages.admin_common_Homepage_Slider")}}</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted"> 
                            {{trans("messages.admin_common_Homepage_Slider")}}
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
            <form action="{{route($model.'.store')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <div class="tab-content">
                            @if(!empty($languages))
                            <?php $i = 1; ?>
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
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.title">{{trans("messages.admin_common_Title")}}</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][title]" class="form-control form-control-solid form-control-lg @error('title') is-invalid @enderror" value="{{old('title')}}">
                                                    @if ($errors->has('title'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('title') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.title">{{trans("messages.admin_common_Title")}}</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][title]" class="form-control form-control-solid form-control-lg" value="{{old('title')}}">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.subtitle">{{trans("messages.admin_common_SubTitle")}}</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][subtitle]" class="form-control form-control-solid form-control-lg @error('subtitle') is-invalid @enderror" value="{{old('subtitle')}}">
                                                    @if ($errors->has('subtitle'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('subtitle') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.subtitle">{{trans("messages.admin_common_SubTitle")}}</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][subtitle]" class="form-control form-control-solid form-control-lg" value="{{old('subtitle')}}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.buttontext">{{trans("messages.admin_common_Button")}} {{trans("messages.admin_common_Text")}}</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][buttontext]" class="form-control form-control-solid form-control-lg @error('buttontext') is-invalid @enderror" value="{{old('buttontext')}}">
                                                    @if ($errors->has('buttontext'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('buttontext') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.buttontext">{{trans("messages.admin_common_Button")}} {{trans("messages.admin_common_Text")}}</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][buttontext]" class="form-control form-control-solid form-control-lg" value="{{old('buttontext')}}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.buttonlink">{{trans("messages.admin_common_Button")}} {{trans("messages.admin_common_Link")}}</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][buttonlink]" class="form-control form-control-solid form-control-lg @error('buttonlink') is-invalid @enderror" value="{{old('buttonlink')}}">
                                                    @if ($errors->has('buttonlink'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('buttonlink') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.buttonlink">{{trans("messages.admin_common_Button")}} {{trans("messages.admin_common_Link")}}</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][buttonlink]" class="form-control form-control-solid form-control-lg" value="{{old('buttonlink')}}">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-xl-12">
												<div class="form-group">
													@if($i == 1)
													<lable for="{{$language->id}}.description">{{trans("messages.description")}}</lable><span class="text-danger"> * </span>
													<textarea name="data[{{$language->id}}][description]" id="description_{{$language->id}}" class="form-control form-control-solid form-control-lg @error('description') is-invalid @enderror" rows="5"></textarea>
													@if ($errors->has('description'))
													<div class="invalid-feedback">
														{{ $errors->first('description') }}
													</div>
													@endif

													@else
													<lable for="{{$language->id}}.description">{{trans("messages.description")}}</lable><span class="text-danger"> </span>
													<textarea name="data[{{$language->id}}][description]" id="description_{{$language->id}}" class="form-control form-control-solid form-control-lg @error('description') is-invalid @enderror" rows="5"></textarea>
													@endif
												</div>
                                                <script src="{{asset('/public/js/ckeditor/ckeditor.js')}}"></script>
                                                <script>
                                                    CKEDITOR.replace(<?php echo 'description_' . $language->id; ?>, {
                                                        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                        enterMode: CKEDITOR.ENTER_BR
                                                    });
                                                    CKEDITOR.config.allowedContent = true;
                                                </script>
											</div>

                                            <!-- <div class="col-xl-12">
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.description">Description</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][description]" class="form-control form-control-solid form-control-lg @error('description') is-invalid @enderror" value="{{old('description')}}">
                                                    @if ($errors->has('description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('description') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.description">Description</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][description]" class="form-control form-control-solid form-control-lg" value="{{old('description')}}">
                                                    @endif
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="image">{{trans("messages.admin_common_Image")}}</label><span class="text-danger"> * </span>
                                    <input type="file" name="image" class="form-control form-control-solid form-control-lg @error('image') is-invalid @enderror" accept="image/png, image/jpg, image/jpeg">
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
                                    <button button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                        {{trans('messages.submit')}}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a type="button" href="{{ route('homepage-slider.index') }}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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
