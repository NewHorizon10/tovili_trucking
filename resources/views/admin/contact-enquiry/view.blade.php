@extends('admin.layouts.layout')
@section('content')

    <!--begin::Content-->
    <div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
            <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin:: Heading-->
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <!--begin::Page Title-->
                        <h5 class="text-dark font-weight-bold my-1 mr-5">
                            {{ trans('messages.admin_common_View') }} {{ trans('messages.admin_common_contact_enquiry') }}
                        </h5>

                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}" class="text-muted">{{ trans('messages.Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route($modelName . '.index') }}" class="text-muted">
                                    {{ trans('messages.admin_common_contact_enquiry') }}
                                </a>
                            </li>
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->

                @include('admin.elements.quick_links')
            </div>
        </div>
        <!--end::Subheader-->

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class=" container ">
                <div class="card card-custom gutter-b">
                    <!--begin::Header-->
                    <div class="card-header card-header-tabs-line">
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-bold nav-tabs-line-3x"
                                role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ old('selected') ? '' : 'active' }}" data-toggle="tab"
                                        href="#kt_apps_contacts_view_tab_1">
                                        <span class="nav-text">
                                            {{ trans('messages.admin_common_contact_enquiry_details') }}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ old('selected') == 'reply' ? 'active' : '' }}" data-toggle="tab"
                                        href="#kt_apps_contacts_view_tab_2">
                                        <span class="nav-text">
                                            {{ trans('messages.admin_common_contact_enquiry_reply') }}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--end::Header-->

                    <!--begin::Body-->
                    <div class="card-body px-0">
                        <div class="tab-content px-10">
                            <!--begin::Tab Content-->
                            <div class="tab-pane {{ old('selected') ? '' : 'active' }}" id="kt_apps_contacts_view_tab_1"
                                role="tabpanel">
                                @if (!empty($modelDetails->image))
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">{{ trans('messages.admin_common_Image') }}
                                            :</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder">
                                                <img height="100" width="100"
                                                    src="{{ config('constants.CONTACT_US_IMAGE_ROOT_URL') . $modelDetails->image }}" />
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group row my-2">
                                    <label class="col-4 col-form-label">{{ trans('messages.name') }} :</label>
                                    <div class="col-8">
                                        <span class="form-control-plaintext font-weight-bolder">
                                            {{ isset($modelDetails->name) ? $modelDetails->name : '' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group row my-2">
                                    <label class="col-4 col-form-label">{{ trans('messages.Email Address') }} :</label>
                                    <div class="col-8">
                                        <span class="form-control-plaintext font-weight-bolder">
                                            {{ isset($modelDetails->email) ? $modelDetails->email : '' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group row my-2">
                                   <label class="col-4 col-form-label"> {{trans("messages.admin_phone_number")}}:</label>
                                   <div class="col-8">
                                    <span class="form-control-plaintext font-weight-bolder">
                                     {{ isset($modelDetails->phone_number) ? $modelDetails->phone_number : '' }}
                                    </span>
                                   </div>
                                </div> 

                                <div class="form-group row my-2">
                                    <label class="col-4 col-form-label">{{ trans('messages.message') }} :</label>
                                    <div class="col-8">
                                        <span class="form-control-plaintext font-weight-bolder">
                                            {{ $modelDetails->comments ?? '' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group row my-2">
                                    <label class="col-4 col-form-label">{{ trans('messages.admin_common_Status') }}
                                        :</label>
                                    <div class="col-8">
                                        <span class="form-control-plaintext font-weight-bolder">
                                            @if (isset($modelDetails->is_active) == 1)
                                                <span
                                                    class="label label-lg label-light-success label-inline">{{ trans('messages.admin_common_Activated') }}</span>
                                            @else
                                                <span
                                                    class="label label-lg label-light-danger label-inline">{{ trans('messages.admin_common_Deactivated') }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group row my-2">
                                    <label class="col-4 col-form-label">{{ trans('messages.admin_common_Added_On') }}
                                        :</label>
                                    <div class="col-8">
                                        <span class="form-control-plaintext font-weight-bolder">
                                            {{ date(config('Reading.date_format'), strtotime($modelDetails->created_at)) }}
                                        </span>
                                    </div>
                                </div>


                            </div>


                            <div class="tab-pane  {{ old('selected') == 'reply' ? 'active' : '' }}"
                                id="kt_apps_contacts_view_tab_2" role="tabpanel">

                                <div class="container">
                                    <form action="{{ route($modelName . '.reply', $modelDetails->id) }}" method="post"
                                        class="mws-form" autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="card card-custom gutter-b">

                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <label for="description">{{ trans('messages.description') }}</label>
                                                    <span class="text-danger"> * </span>
                                                    <textarea name="message" id="body_message" class="form-control form-control-solid form-control-lg">{{ old('message') }}</textarea>

                                                </div>
                                                <script src="{{ asset('/public/js/ckeditor/ckeditor.js') }}"></script>
                                                <script>
                                                    CKEDITOR.replace(<?php echo 'body_message'; ?>, {
                                                        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                        enterMode: CKEDITOR.ENTER_BR
                                                    });
                                                    CKEDITOR.config.allowedContent = true;
                                                </script>
                                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                                    <div>
                                                        <button button type="submit"
                                                            class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                                            {{ trans('messages.submit') }}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="row pad" style='margin-top:20px;'>
                                                    <div class="col-md-12 col-sm-12">

                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th width="30%">{{ trans('messages.message') }}</th>

                                                                    <th width="20%">
                                                                        {{ trans('messages.admin_common_replied_at') }}
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (count($contactReplies) > 0)
                                                                    @foreach ($contactReplies as $row)
                                                                        <tr>
                                                                            <td width="20%" data-th='Name'
                                                                                class="txtFntSze">
                                                                                {{ Str::limit(strip_tags($row->message), 50) }}
                                                                            </td>

                                                                            <td width="20%" data-th='Name'
                                                                                class="txtFntSze">
                                                                                {{ date('d-m-Y  H :i:s', strtotime($row->created_at)) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="4" style="text-align:center;">
                                                                            {{ trans('messages.admin_common_not_replied_yet') }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!--end::Tab Content-->
                        </div>
                    </div>
                    <!--end::Body-->
                </div>

            </div>
            <!--end::Container-->
        </div>
    </div>
    <!--end::Content-->
@stop
