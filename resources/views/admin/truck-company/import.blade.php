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
                    </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard')}}" class="text-muted">{{trans("messages.Dashboard")}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($model.'.index')}}" class="text-muted">{{trans('messages.admin_Truck_Company')}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{route($model.'.importList')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">

                    <div class="card-body " style="margin:20px 100px;">
                        <h3 class="mb-10 font-weight-bold text-dark">{{trans("messages.admin_Import_Truck_Company")}}</h3>
                        <div class="row">
                            <div class="col-md-12 d-flex">
                                <div class="form-group">
                                    <input type="file" name="file" class="form-control form-control-solid  form-control-lg  @error('file') is-invalid @enderror" accept=".xls,.csv,.ods,.xlsx" required>
                                    @if ($errors->has('file'))
                                    <div class=" invalid-feedback">
                                        {{ $errors->first('file') }}
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12 d-flex" >
                                <button button type="submit" onclick="submit_form();" class="btn btn-success ">{{trans('messages.submit')}}</button>
                                <a  href="{{ route($model.'.export-sample', 'company-type') }}" class="btn btn-primary" style="margin-left:7px;">
                                {{trans("messages.company type")}}<i class="fa fa-cloud-download" aria-hidden="true"></i>
                                    </a> 
                                <a  href="{{ route($model.'.export-sample', 'sample') }}" class="btn btn-primary mx-2">
                                {{trans("messages.admin_Sample_File")}}<i class="fa fa-cloud-download" aria-hidden="true"></i>
                                </a>                       
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@stop