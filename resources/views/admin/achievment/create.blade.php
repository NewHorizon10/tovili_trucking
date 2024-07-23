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
                    {{trans("messages.admin_common_Add_New")}}</h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('achievment.index')}}" class="text-muted">
                            {{trans("messages.admin_common_Achievment")}}
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
            <form action="{{route('achievment.add')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
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
                                                    <label for="{{$language->id}}.name">{{trans("messages.name")}}</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][name]" class="form-control form-control-solid form-control-lg  @error('name') is-invalid @enderror" value="{{old('name')}}">
                                                    @if ($errors->has('name'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('name') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.name">{{trans("messages.name")}}</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][name]" class="form-control form-control-solid form-control-lg" value="{{old('name')}}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="form-group">
                                                    @if($i == 1)
                                                    <label for="{{$language->id}}.description">{{trans("messages.description")}}</label><span class="text-danger"> * </span>
                                                    <input type="text" name="data[{{$language->id}}][description]" class="form-control form-control-solid form-control-lg  @error('description') is-invalid @enderror" value="{{old('description')}}">
                                                    @if ($errors->has('description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('description') }}
                                                    </div>
                                                    @endif
                                                    @else
                                                    <label for="{{$language->id}}.name">{{trans("messages.description")}}</label><span class="text-danger"> </span>
                                                    <input type="text" name="data[{{$language->id}}][description]" class="form-control form-control-solid form-control-lg" value="{{old('description')}}">
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
                                <div class="col-xl-6">
                                    <div class="form-group">
                                    <label for="image">{{trans("messages.admin_common_Icon")}}</label><span class="text-danger"> * </span>
                                        <input type="file" name="image" accept="image/*" class="form-control profile_img  form-control-solid form-control-lg  @error('image') is-invalid @enderror" value="{{old('image')}}">
                                        @if ($errors->has('image'))
                                        <div class=" invalid-feedback">
                                            {{ $errors->first('image') }}
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
                                        <a type="button" href="{{ route('achievment.index') }}" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4">
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
</div>

@stop
@section('css')

@stop
@section('script')

@stop