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
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" class="text-muted">{{trans("messages.admin_common_about_us_section")}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route('aboutus.index')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
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
                                        <h3 class="mb-10 font-weight-bold text-dark">
                                        {{trans("messages.admin_common_about_us_section")}}
                                        </h3>
                                        <div class="row">
                                            <div class="col-xl-12 pl-2">
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.heading">{{trans("messages.admin_common_Heading")}}</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][heading]" class="form-control form-control-solid form-control-lg @error('heading') is-invalid @enderror" value="{{@$multiLanguage[$language->id]['heading']}}">
                                                    @if ($errors->has('heading'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('heading') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.heading">{{trans("messages.admin_common_Heading")}}</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][heading]" class="form-control form-control-solid form-control-lg" value="{{@$multiLanguage[$language->id]['heading']}}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-12">
                                                <div class="form-group">
                                                    <div id="kt-ckeditor-1-toolbar"></div>
                                                    @if($i == 1)
                                                    <lable for="{{$language->id}}.goal_description_id">{{trans("messages.description")}}</lable><span class="text-danger"> * </span>
                                                    <textarea name="data[{{$language->id}}][description]" id="goal_description_id" class="form-control form-control-solid form-control-lg @error('description') is-invalid @enderror" rows="5">{{@$multiLanguage[$language->id]['description']}}</textarea>
                                                    <script src="{{asset('/public/js/ckeditor/ckeditor.js')}}"></script>
                                                    <script>
                                                        CKEDITOR.replace(<?php echo 'goal_description_id'; ?>, {
                                                            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                            enterMode: CKEDITOR.ENTER_BR
                                                        });
                                                        CKEDITOR.config.allowedContent = true;
                                                    </script>
                                                    @if ($errors->has('description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('description') }}
                                                    </div>
                                                    @endif

                                                    @else
                                                    <lable for="{{$language->id}}.goal_description_id1">{{trans("messages.description")}}</lable><span class="text-danger"> </span>
                                                    <textarea name="data[{{$language->id}}][description]" id="goal_description_id{{$language->id}}" class="form-control form-control-solid form-control-lg @error('description') is-invalid @enderror" rows="5">{{@$multiLanguage[$language->id]['description']}}</textarea>
                                                    <script src="{{asset('/public/js/ckeditor/ckeditor.js')}}"></script>
                                                    <script>
                                                        CKEDITOR.replace(<?php echo 'goal_description_id' . $language->id; ?>, {
                                                            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                            enterMode: CKEDITOR.ENTER_BR
                                                        });
                                                        CKEDITOR.config.allowedContent = true;
                                                    </script>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                        <h3 class="mb-10 mt-10 font-weight-bold text-dark">
                                            {{trans("messages.admin_common_about_target_section")}}
                                        </h3>
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="form-group">
                                                    <div id="kt-ckeditor-1-toolbar"></div>
                                                    @if($i == 1)
                                                    <lable for="{{$language->id}}.description_id">{{trans("messages.admin_common_goal_description")}}</lable><span class="text-danger"> * </span>
                                                    <textarea name="data[{{$language->id}}][goal_description]" id="description_id" class="form-control form-control-solid form-control-lg @error('goal_description') is-invalid @enderror" rows="5">{{@$multiLanguage[$language->id]['goal_description']}}</textarea>
                                                    <script src="{{asset('/public/js/ckeditor/ckeditor.js')}}"></script>
                                                    <script>
                                                        CKEDITOR.replace(<?php echo 'description_id'; ?>, {
                                                            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                            enterMode: CKEDITOR.ENTER_BR
                                                        });
                                                        CKEDITOR.config.allowedContent = true;
                                                    </script>
                                                    @if ($errors->has('description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('description') }}
                                                    </div>
                                                    @endif

                                                    @else
                                                    <lable for="{{$language->id}}.description_id1">{{trans("messages.admin_common_goal_description")}}</lable><span class="text-danger"> </span>
                                                    <textarea name="data[{{$language->id}}][goal_description]" id="description_id{{$language->id}}" class="form-control form-control-solid form-control-lg @error('goal_description') is-invalid @enderror" rows="5">{{@$multiLanguage[$language->id]['goal_description']}}</textarea>
                                                    <script src="{{asset('/public/js/ckeditor/ckeditor.js')}}"></script>
                                                    <script>
                                                        CKEDITOR.replace(<?php echo 'description_id' . $language->id; ?>, {
                                                            filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                            enterMode: CKEDITOR.ENTER_BR
                                                        });
                                                        CKEDITOR.config.allowedContent = true;
                                                    </script>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php $i++ ?>
                            @endforeach
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="image">{{trans("messages.admin_common_about_us_section_image")}}</label><span class="text-danger"> *</span>
                                    <input type="file" name="image" class="form-control form-control-solid form-control-lg  @error('image') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="title">{{trans("messages.admin_common_about_target_section_image")}}</label><span class="text-danger"> *</span>
                                    <input type="file" name="goal_image" class="form-control form-control-solid form-control-lg  @error('image') is-invalid @enderror">
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="d-flex col-xl-6">
                                @if(@$about->image)
                                <img width="70px" src="{{Config('constants.ABOUT_US_IMAGE_PATH').$about->image}}" class="profilePreview">
                                @endif
                            </div>
                            <div class="col-xl-6">
                                @if(@$about->goal_image)
                                
                                <img width="70px" src="{{Config('constants.ABOUT_US_GOAL_IMAGE_PATH').$about->goal_image}}" class="profilePreview">
                                @endif
                            </div>
                        </div>
                        <div class="d-flex mt-10">
                            <!-- <div>
                                <button button type="submit" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                    {{trans("messages.submit")}}
                                </button>
                            </div> -->
                            <div class="row">
                                <div class="col-6">
                                    <button button type="submit" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                        {{trans("messages.submit")}}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a type="button" href="{{ route('dashboard') }}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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