@extends('backend.layouts.admin')



@section('button')
    <div class="pull-left"><a href="{{ route('admin.bookings.create') }}"
            class=" bookride wqbookride">{{ trans('messages.BOOK_A_RIDE') }}</a></div>
@endsection



@section('content')

    <style type="text/css">
        .pac-container {
            z-index: 9999 !important;
        }

        .dz-size {
            display: none;
        }
    </style>



    <h1 class="page_title"><?php echo $isNewRecord ? trans('messages.add_new') : trans('messages.admin_update'); ?> {{ trans('messages.all_drivers') }}</h1>

    <form method="Post" action="{{ $action }}" enctype="multipart/form-data">

        @csrf

        {{ method_field($method_field) }}

        <div class="row">
            <div class="col-md-12">
                @include('backend.includes.messages')
                <div class="card card-shadow">
                    <div class="card-content">
                        <div class="card-body">
                            <div>
                                <input type="hidden" id="no_video" value="0" />
                                <input type="hidden" id="no_video_additional_1" value="0" />
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nw-tabs-head" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#general_information" aria-controls="general_information" role="tab"
                                            data-toggle="tab">{{ trans('messages.general_information') }}</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#personal_information" aria-controls="personal_information" role="tab"
                                            data-toggle="tab">{{ trans('messages.personal_information') }}</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#driver_information" aria-controls="driver_information" role="tab"
                                            data-toggle="tab">{{ trans('messages.payments') }}</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#others" aria-controls="others" role="tab"
                                            data-toggle="tab">{{ trans('messages.others') }}</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="general_information">
                                        <div class="nw-tab-content-inner">
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.display_name') }}<span
                                                        class="text-req">*</span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text"
                                                        placeholder="{{ trans('messages.display_name') }}"
                                                        class=" form-control form-control-solid" name="display_name"
                                                        value="{{ !empty(old('display_name')) ? old('display_name') : $model->display_name }}"
                                                        autofocus>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.Unique_ID') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.Unique_ID') }}"
                                                        class="form-control form-control-solid" name="unique_id"
                                                        value="{{ !empty(old('unique_id')) ? old('unique_id') : $model->unique_id }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.user_name') }}
                                                    <span class="text-req">*</span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.user_name') }} "
                                                        class=" form-control form-control-solid" name="username"
                                                        value="{{ !empty(old('username')) ? old('username') : $model->username }}"
                                                        <?php echo !$isNewRecord ? 'readonly' : ''; ?>>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.email') }}
                                                    <span class="text-req">*</span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="email" placeholder="{{ trans('messages.email') }} "
                                                        class="form-control form-control-solid" name="email"
                                                        value="{{ !empty(old('email')) ? old('email') : $model->email }}"
                                                        <?php echo !$isNewRecord ? 'readonly1' : ''; ?>>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <input type="hidden" name="mobile_no_prefix" id="mobile_no_prefix"
                                                    value="{{ !empty($model->mobile_no_prefix) ? $model->mobile_no_prefix : '+1' }}">
                                                <input type="hidden" name="mobile_no_prefix_country_code"
                                                    id="mobile_no_prefix_country_code"
                                                    value="{{ !empty($model->mobile_no_prefix_country_code) ? $model->mobile_no_prefix_country_code : 'us' }}">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.mobile_number') }}<span
                                                        class="text-req">*</span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text"
                                                        placeholder="{{ trans('messages.mobile_number') }}"
                                                        class="form-control form-control-solid" name="mobile_no"
                                                        value="{{ !empty(old('mobile_no')) ? old('mobile_no') : $model->mobile_no }}"
                                                        id="mobile_no">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.password') }}
                                                    <span class="text-req">*</span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="Password" placeholder="{{ trans('messages.password') }}"
                                                        autocomplete="new-password"
                                                        class=" form-control form-control-solid" name="password">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.confirm_password') }}<span
                                                        class="text-req">*</span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="password"
                                                        placeholder="{{ trans('messages.confirm_password') }}"
                                                        class=" form-control form-control-solid"
                                                        name="password_confirmation">

                                                </div>
                                            </div>
                                            @if ($model->photo)
                                                <div class="form-group row">
                                                    <label
                                                        class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.upload_photo') }}
                                                        <span class="text-req"></span></label>
                                                    <div class="col-lg-9 col-xl-4">
                                                        <div class="dropzone dropzone-default dropzone_init">
                                                            <div class="dropzone-msg dz-message needsclick">
                                                                <h3 class="dropzone-msg-title">
                                                                    {{ trans('messages.Drop_files_here_or_click_to_upload') }}
                                                                </h3>
                                                                <span
                                                                    class="dropzone-msg-desc">{{ trans('messages.Upload_photo_here') }}</span>
                                                            </div>
                                                        </div>
                                                        <input type="file" name="photo" style="display:none;">
                                                    </div>
                                                    <img src="{{ config('constants.DRIVER_IMAGE_PATH') . $model->photo }}"
                                                        alt="" style="max-width: 250px; max-height: 200px;">

                                                    {{-- <a href="{{route('drvier.deleteImage',$model->id)}}" >Delete</a> --}}
                                                    <a href="{{ route('admin.drivers.delete_file_record', $model->id) }}"
                                                        onclick="return confirm('Are you sure to delete this image ?')"
                                                        class="btn btn-danger btn-sm rounded-0" data-toggle="tooltip"
                                                        data-placement="top" title="Delete"><i
                                                            class="fa fa-trash"></i></a>
                                                </div>
                                            @else
                                                <div class="form-group row">
                                                    <label
                                                        class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.upload_photo') }}
                                                        <span class="text-req"></span></label>
                                                    <div class="col-lg-9 col-xl-4">
                                                        <div class="dropzone dropzone-default dropzone_init">
                                                            <div class="dropzone-msg dz-message needsclick">
                                                                <h3 class="dropzone-msg-title">
                                                                    {{ trans('messages.Drop_files_here_or_click_to_upload') }}
                                                                </h3>
                                                                <span
                                                                    class="dropzone-msg-desc">{{ trans('messages.Upload_photo_here') }}</span>
                                                            </div>
                                                        </div>

                                                        <input type="file" name="photos" style="display:none;">
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.status') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <select class="select2_init form-control form-control-solid"
                                                        name="status_id">

                                                        @foreach ($driverStatuses as $status)
                                                            @if (!empty(old('status_id')) && old('status_id') == $status->id)
                                                                @php $selected='selected'; @endphp
                                                            @elseif (!empty($model->status_id) && $model->status_id == $status->id)
                                                                @php $selected='selected'; @endphp
                                                            @else
                                                                @php $selected=''; @endphp
                                                            @endif

                                                            <option value="{{ $status->id }}" {{ $selected }}>
                                                                {{ $status->name }}</option>
                                                        @endforeach


                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nw-card-footer">
                                            <a href="javascript:void(0)"
                                                class="bookride wqbookride centered add-cust smambut btn-success btnNextTab">{{ trans('messages.next') }}</a>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade " id="personal_information">
                                        <div class="nw-tab-content-inner">
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.title') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.title') }}"
                                                        class=" form-control form-control-solid" name="title"
                                                        value="{{ !empty(old('title')) ? old('title') : $model->title }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.first_name') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text"
                                                        placeholder="{{ trans('messages.first_name') }}"
                                                        class=" form-control form-control-solid" name="first_name"
                                                        value="{{ !empty(old('first_name')) ? old('first_name') : $model->first_name }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.last_name') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.last_name') }}"
                                                        class=" form-control form-control-solid" name="last_name"
                                                        value="{{ !empty(old('last_name')) ? old('last_name') : $model->last_name }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.Date_of_birth') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="date" placeholder=""
                                                        class="form-control form-control-solid" name="dob"
                                                        value="{{ !empty(old('dob')) ? old('dob') : $model->dob }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.telephone_no') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="hidden" name="telephone_no_prefix"
                                                        id="telephone_no_prefix"
                                                        value="{{ !empty($model->telephone_no_prefix) ? $model->telephone_no_prefix : '+1' }}">
                                                    <input type="hidden" name="telephone_no_prefix_country_code"
                                                        id="telephone_no_prefix_country_code"
                                                        value="{{ !empty($model->telephone_no_prefix_country_code) ? $model->telephone_no_prefix_country_code : 'us' }}">
                                                    <input type="text"
                                                        placeholder="{{ trans('messages.telephone_no') }}"
                                                        class=" form-control form-control-solid " name="telephone_no"
                                                        value="{{ !empty(old('telephone_no')) ? old('telephone_no') : $model->telephone_no }}"
                                                        id="telephone_no">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.emergency_number') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="hidden" name="emergency_no_prefix"
                                                        id="emergency_no_prefix"
                                                        value="{{ !empty($model->emergency_no_prefix) ? $model->emergency_no_prefix : '+1' }}">
                                                    <input type="hidden" name="emergency_no_prefix_country_code"
                                                        id="emergency_no_prefix_country_code"
                                                        value="{{ !empty($model->emergency_no_prefix_country_code) ? $model->emergency_no_prefix_country_code : 'us' }}">
                                                    <input type="text"
                                                        placeholder="{{ trans('messages.emergency_number') }}"
                                                        class=" form-control form-control-solid " name="emergency_no"
                                                        value="{{ !empty(old('emergency_no')) ? old('emergency_no') : $model->emergency_no }}"
                                                        id="emergency_no">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.address') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.address') }}"
                                                        class="form-control form-control-solid" name="address"
                                                        id="address"
                                                        value="{{ !empty(old('address')) ? old('address') : $model->address }}">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.city') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.city') }}"
                                                        class="form-control form-control-solid" name="city"
                                                        id="city"
                                                        value="{{ !empty(old('city')) ? old('city') : $model->city }}">
                                                    <input type="hidden" name="lat" id="lat">
                                                    <input type="hidden" name="lng" id="lng">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.postcode') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.postcode') }}"
                                                        class="form-control form-control-solid" name="postcode"
                                                        id="postcode"
                                                        value="{{ !empty(old('postcode')) ? old('postcode') : $model->postcode }}">
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.country') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <input type="text" placeholder="{{ trans('messages.country') }}"
                                                        class=" form-control form-control-solid" name="county"
                                                        value="{{ !empty(old('county')) ? old('county') : $model->county }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.select_county') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <select class="select2_init form-control form-control-solid"
                                                        name="country_id">

                                                        @foreach ($countries as $country)
                                                            @if (!empty(old('country_id')) && old('country_id') == $country->id)
                                                                @php $selected='selected'; @endphp
                                                            @elseif (!empty($model->country_id) && $model->country_id == $country->id)
                                                                @php $selected='selected'; @endphp
                                                            @else
                                                                @php $selected=''; @endphp
                                                            @endif

                                                            <option value="{{ $country->id }}" {{ $selected }}>
                                                                {{ $country->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.profile_type') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <select class="select2_init form-control form-control-solid"
                                                        name="profile_type">

                                                        @foreach ($profileTypes as $key => $value)
                                                            @if (!empty(old('profile_type')) && old('profile_type') == $key)
                                                                @php $selected='selected'; @endphp
                                                            @elseif (!empty($model->profile_type) && $model->profile_type == $key)
                                                                @php $selected='selected'; @endphp
                                                            @else
                                                                @php $selected=''; @endphp
                                                            @endif

                                                            <option value="{{ $key }}" {{ $selected }}>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.note') }}</label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <textarea class=" form-control form-control-solid" cols="30" rows="10" name="note">{{ !empty(old('note')) ? old('note') : $model->note }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nw-card-footer">
                                            <a href="javascript:void(0)"
                                                class="bookride wqbookride centered add-cust smambut cancel-btn btnPreviousTab">{{ trans('messages.previous') }}</a>
                                            <a href="javascript:void(0)"
                                                class="bookride wqbookride centered add-cust smambut btn-success btnNextTab">{{ trans('messages.next') }}</a>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade " id="driver_information">
                                        <div class="nw-tab-content-inner">
                                            <div class="tabs-sub-heading">
                                                <h4>{{ trans('messages.driver_fees') }}</h4>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.weekly_fee') }}
                                                    <span><i class="fas fa-question-circle gray-que" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="To charge the driver a weekly fee."></i></span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <div class="quantity qyt rel-pos">
                                                        <!-- <span class="euro">£</span> -->
                                                        <input type="number" max="99999" step="1"
                                                            name="weekly_fee" id="weekly_fee" onchange="checkFee('fee');"
                                                            value="{{ !empty(old('weekly_fee')) ? old('weekly_fee') : $model->weekly_fee }}"
                                                            class="form-control form-control-solid">
                                                        <div class="quantity-nav">
                                                            <div class="quantity-button quantity-up"><i
                                                                    class="ion-ios-plus-empty"></i></div>
                                                            <div class="quantity-button quantity-down"><i
                                                                    class="ion-ios-minus-empty"></i></div>
                                                        </div>
                                                        <span class="gbp">{{ $setting->currency_code }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.per_job_fee') }}<span><i
                                                            class="fas fa-question-circle gray-que" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="To charge the driver a fixed fee for each job they complete."></i></span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <div class="quantity qyt rel-pos">
                                                        <!-- <span class="euro">£</span> -->
                                                        <input type="number" max="9999" step="1"
                                                            name="per_job_fee" id="per_job_fee"
                                                            onchange="checkFee('fee');"
                                                            value="{{ !empty(old('per_job_fee')) ? old('per_job_fee') : $model->per_job_fee }}"
                                                            class="form-control form-control-solid">
                                                        <div class="quantity-nav">
                                                            <div class="quantity-button quantity-up"><i
                                                                    class="ion-ios-plus-empty"></i></div>
                                                            <div class="quantity-button quantity-down"><i
                                                                    class="ion-ios-minus-empty"></i></div>
                                                        </div>
                                                        <span class="gbp">{{ $setting->currency_code }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.per_job_commission') }}<span><i
                                                            class="fas fa-question-circle gray-que" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="To charge the driver a fixed commission for each job they complete."></i></span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <div class="quantity qyt rel-pos">
                                                        <!-- <span class="euro">£</span> -->
                                                        <input type="number" max="100" step="1"
                                                            onchange="checkFee('fee');" name="per_job_fee_percentage"
                                                            id="per_job_fee_percentage"
                                                            value="{{ !empty(old('per_job_fee_percentage')) ? old('per_job_fee_percentage') : $model->per_job_fee_percentage }}"
                                                            class="form-control form-control-solid">
                                                        <div class="quantity-nav">
                                                            <div class="quantity-button quantity-up"><i
                                                                    class="ion-ios-plus-empty"></i></div>
                                                            <div class="quantity-button quantity-down"><i
                                                                    class="ion-ios-minus-empty"></i></div>
                                                        </div>
                                                        <span class="gbp">%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="tabs-sub-heading">
                                                <h4>{{ trans('messages.driver_earnings') }}</h4>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.weekly_pay') }}<span><i
                                                            class="fas fa-question-circle gray-que" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="Here you can set, how much the company will pay the driver each week."></i></span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <div class="quantity qyt rel-pos">
                                                        <!-- <span class="euro">£</span> -->
                                                        <input type="number" max="9999" step="1"
                                                            name="weekly_pay" id="weekly_pay"
                                                            onchange="checkFee('earning');"
                                                            value="{{ !empty(old('weekly_pay')) ? old('weekly_pay') : $model->weekly_pay }}"
                                                            class="form-control form-control-solid">
                                                        <div class="quantity-nav">
                                                            <div class="quantity-button quantity-up"><i
                                                                    class="ion-ios-plus-empty"></i></div>
                                                            <div class="quantity-button quantity-down"><i
                                                                    class="ion-ios-minus-empty"></i></div>
                                                        </div>
                                                        <span class="gbp">{{ $setting->currency_code }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.per_job_fee') }}
                                                    <span><i class="fas fa-question-circle gray-que" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="How much the driver will receive for each job."></i></span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <div class="quantity qyt rel-pos">
                                                        <!-- <span class="euro">£</span> -->
                                                        <input type="number" max="9999" step="1"
                                                            name="per_job_pay" id="per_job_pay"
                                                            onchange="checkFee('earning');"
                                                            value="{{ !empty(old('per_job_pay')) ? old('per_job_pay') : $model->per_job_pay }}"
                                                            class="form-control form-control-solid">
                                                        <div class="quantity-nav">
                                                            <div class="quantity-button quantity-up"><i
                                                                    class="ion-ios-plus-empty"></i></div>
                                                            <div class="quantity-button quantity-down"><i
                                                                    class="ion-ios-minus-empty"></i></div>
                                                        </div>
                                                        <span class="gbp">{{ $setting->currency_code }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label
                                                    class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.per_job_commission') }}
                                                    <span><i class="fas fa-question-circle gray-que" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="The commission amount the driver will receive per job."></i></span></label>
                                                <div class="col-lg-9 col-xl-4">
                                                    <div class="quantity qyt rel-pos">
                                                        <!-- <span class="euro">£</span> -->
                                                        <input type="number" max="100" step="1"
                                                            onchange="checkFee('earning');" name="per_job_pay_percentage"
                                                            id="per_job_pay_percentage" " value="{{ !empty(old('per_job_pay_percentage')) ? old('per_job_pay_percentage') : $model->per_job_pay_percentage }}" class="form-control form-control-solid">
                                                                                                                                                                                                            <div class="quantity-nav">
                                                                                                                                                                                                                <div class="quantity-button quantity-up"><i class="ion-ios-plus-empty"></i></div>
                                                                                                                                                                                                                <div class="quantity-button quantity-down"><i class="ion-ios-minus-empty"></i></div>
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                            <span class="gbp">%</span>
                                                                                                                                                                                                        </div>
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                 </div>
                                                                                                                                                                 <div class="nw-card-footer">
                                                                                                                                                                  <a href="javascript:void(0)" class="bookride wqbookride centered add-cust smambut cancel-btn btnPreviousTab">{{ trans('messages.previous') }}</a>
                                                                                                                                                                  <a href="javascript:void(0)" class="bookride wqbookride centered add-cust smambut btn-success btnNextTab">{{ trans('messages.next') }}</a>
                                                                                                                                                                 </div>
                                                                                                                                                                </div>
                                                                                                                                                                <div role="tabpanel" class="tab-pane fade " id="others">
                                                                                                                                                                 <div class="nw-tab-content-inner">
                                                                                                                                                                                               
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.national_insurance_number') }} <span><i class="fas fa-question-circle gray-que" data-toggle="tooltip" data-placement="top" title="" data-original-title="The National Insurance number is a number used in the United Kingdom in the administration of the National Insurance or social security system."></i></span></label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="text" placeholder="{{ trans('messages.national_insurance_number') }}" name="national_insurance_number" class=" form-control form-control-solid" value="{{ !empty(old('national_insurance_number')) ? old('national_insurance_number') : $model->national_insurance_number }}">

                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.bank_account_details') }}</label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="text" placeholder="{{ trans('messages.bank_account_details') }}" name="bank_account_details" class=" form-control form-control-solid" value="{{ !empty(old('bank_account_details')) ? old('bank_account_details') : $model->bank_account_details }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_insurance') }} <span><i class="fas fa-question-circle gray-que" data-toggle="tooltip" data-placement="top" title="" data-original-title="The insurance number for the vehicle registered to the driver."></i></span></label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="text" placeholder="{{ trans('messages.admin_insurance') }}" name="insurance" class=" form-control form-control-solid" value="{{ !empty(old('insurance')) ? old('insurance') : $model->insurance }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_insurance_expiry_date') }}</label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="date" placeholder="" class=" form-control form-control-solid" name="insurance_expiry_date" value="{{ !empty(old('insurance_expiry_date')) ? old('insurance_expiry_date') : $model->insurance_expiry_date }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_driving_licence') }}</label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="text" placeholder="{{ trans('messages.admin_driving_licence') }}" class=" form-control form-control-solid" name="driving_license" value="{{ !empty(old('driving_license')) ? old('driving_license') : $model->driving_license }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_driving_licence_expiry_date') }}</label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="date" placeholder="" class=" form-control form-control-solid" name="driving_licence_expiry_date" value="{{ !empty(old('driving_licence_expiry_date')) ? old('driving_licence_expiry_date') : $model->driving_licence_expiry_date }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_PCO_licence') }} <span><i class="fas fa-question-circle gray-que" data-toggle="tooltip" data-placement="top" title="" data-original-title="Your PCO licence is a plastic card the size of a standard credit card and includes its serial number and expiry date, as well as your basic personal information and your photo."></i></span></label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="text" placeholder="{{ trans('messages.admin_PCO_licence') }}" class=" form-control form-control-solid" name="pco_license" value="{{ !empty(old('pco_license')) ? old('pco_license') : $model->pco_license }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_PCO_licence_expiry_date') }}</label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="date" placeholder="" class=" form-control form-control-solid" name="pco_licence_expiry_date" value="{{ !empty(old('pco_licence_expiry_date')) ? old('pco_licence_expiry_date') : $model->pco_licence_expiry_date }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_PHV_licence') }}<span><i class="fas fa-question-circle gray-que" data-toggle="tooltip" data-placement="top" title="" data-original-title="Any vehicle that seats up to eight passengers and is available for hire with a driver requires a private hire vehicle (PHV) licence."></i></span></label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="text" placeholder="{{ trans('messages.admin_PHV_licence') }}" class="form-control form-control-solid" name="phv_license" value="{{ !empty(old('phv_license')) ? old('phv_license') : $model->phv_license }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_PHV_licence_expiry_date') }}</label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="date" placeholder="" class="form-control form-control-solid" name="phv_licence_expiry_date" value="{{ !empty(old('phv_licence_expiry_date')) ? old('phv_licence_expiry_date') : $model->phv_licence_expiry_date }}">
                                                                                                                                                                   </div>
                                                                                                                                                                  </div>
                                                                                                                                                                                                <div class="form-group row">
                                                                                                                                                                   <label class="col-lg-3 col-form-label text-lg-right">{{ trans('messages.admin_base_address') }}</label>
                                                                                                                                                                   <div class="col-lg-9 col-xl-4">
                                                                                                                                                                    <input type="text" placeholder="{{ trans('messages.admin_base_address') }}" class="form-control form-control-solid" name="base_address" value="{{ !empty(old('base_address')) ? old('base_address') : $model->base_address }}">
                                                                                                                                                                   </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                
                                                                                                                                                                  
                                                                                                                                                                  
                                                                                                                                                                                       
                                                                                                                                                          
                                                                                                                              
                                                                                                  
                                                                 @if (!$isNewRecord && count($model->files) > 0)
                                                        <hr>

                                                        <div class="tabs-sub-heading">
                                                            <h4>Uploaded Files</h4>
                                                        </div>

                                                        <div>

                                                            <table class="table table-striped table-hover">

                                                                <tbody>

                                                                    <tr>

                                                                        <th width="30%"><a
                                                                                href="#">{{ trans('messages.title') }}
                                                                            </a></th>

                                                                        <th width="40%"><a
                                                                                href="#">{{ trans('messages.admin_file') }}
                                                                            </a></th>

                                                                        <th width="10%">

                                                                            <a href="#"></a>

                                                                        </th>

                                                                    </tr>

                                                                    @foreach ($model->files as $file)
                                                                        <tr>

                                                                            <td>

                                                                                {{ $file->title }}

                                                                            </td>

                                                                            <td>

                                                                                <a href="{{ asset('public/backend/uploads/driver-images/' . $model->id . '/' . $file->file) }}"
                                                                                    download>{{ $file->file }}</a>

                                                                            </td>

                                                                            <td>

                                                                                <div class="btn-group">

                                                                                    <a data="{{ $file->id }}"
                                                                                        href="javascript:void(0)"
                                                                                        class="MuiIconButton-root delete-file-record"><i
                                                                                            class="fas fa-trash"></i>

                                                                                    </a>

                                                                                </div>

                                                                            </td>

                                                                        </tr>
                                                                    @endforeach

                                                                </tbody>

                                                            </table>



                                                        </div>
                                                        @endif

                                                        <hr>

                                                        <div class="tabs-sub-heading">
                                                            <h4>{{ trans('messages.admin_additional_files') }}</h4>
                                                        </div>

                                                        <div>
                                                            <table class="w-100" id="filesTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>
                                                                            {{ trans('messages.title') }}
                                                                        </th>
                                                                        <th>
                                                                            {{ trans('messages.admin_file') }}
                                                                        </th>
                                                                        <th>

                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                    <tr>

                                                                        <td>

                                                                            <input type="text" placeholder="Title"
                                                                                class="form-control form-control-solid"
                                                                                name="files_title[]">

                                                                        </td>

                                                                        <td>
                                                                            <div class='file-input'>
                                                                                <input type="file" placeholder=""
                                                                                    name="files[]">
                                                                                <span
                                                                                    class='button'>{{ trans('messages.admin_choose') }}</span>
                                                                                <span class='label'
                                                                                    data-js-label>{{ trans('messages.admin_no_file_selected') }}</label>
                                                                            </div>


                                                                        </td>

                                                                        <td>

                                                                            <a id="add_file" href="javascript:void(0)"
                                                                                class="MuiIconButton-root"><span
                                                                                    class="material-icons">{{ trans('messages.admin_add') }}</span></a>

                                                                        </td>

                                                                    </tr>


                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                    <div class="nw-card-footer">
                                                        <a href="javascript:void(0)"
                                                            class="bookride wqbookride centered add-cust smambut cancel-btn btnPreviousTab">{{ trans('messages.previous') }}</a>

                                                        <div class="d-inline-block">
                                                            <a href="{{ route('admin.drivers.index') }}"
                                                                class="bookride wqbookride centered add-cust smambut cancel-btn">{{ trans('messages.cancel') }}</a>
                                                            <a href="javascript:void(0)" id="create_btn"
                                                                class="bookride wqbookride centered add-cust smambut btn-success"><?php echo $isNewRecord ? trans('messages.admin_create') : trans('messages.admin_update'); ?></a>
                                                            <input type="submit" name="submit-btn" hidden=""
                                                                id="submit_btn">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    </form>

    {{-- @dd($model); --}}

@endsection



@section('scripts')
    @parent

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            initializeAutocomplete();
        });

        function initializeAutocomplete() {
            var options = {
                componentRestrictions: {
                    country: "fr"
                }
            };

            var addressInput = document.getElementById('address');
            var cityInput = document.getElementById('city');
            var postcodeInput = document.getElementById('postcode');
            var latInput = document.getElementById('lat');
            var lngInput = document.getElementById('lng');

            addressAutocomplete = new google.maps.places.Autocomplete(addressInput, options);

            addressAutocomplete.addListener('place_changed', function() {
                var place = addressAutocomplete.getPlace();

                if (!place.place_id) {
                    return;
                }

                addressInput.value = place.formatted_address;
                latInput.value = place.geometry.location.lat();
                lngInput.value = place.geometry.location.lng();

                var addressComponents = place.address_components;
                for (var i = 0; i < addressComponents.length; i++) {
                    var component = addressComponents[i];
                    if (component.types.includes('locality')) {
                        cityInput.value = component.long_name;
                    } else if (component.types.includes('postal_code')) {
                        postcodeInput.value = component.long_name;
                    }
                }
            });
        }
    </script>

    <script type="text/javascript">
        /***********Upload photo dropzone**********************************************/
        var filecount = 0;
        Dropzone.autoDiscover = false;
        Dropzone.options.myAwesomeDropzone = false;
        // $(".dropzone_init").dropzone({
        var myDropzone = new Dropzone(".dropzone_init", {
            acceptedFiles: 'image/*',
            url: "{{ URL::to('admin/drivers/upload-files') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 5,
            addRemoveLinks: !0,
            acceptedFiles: ".jpeg,.jpg,.png",
            removedfile: function(resources) {
                console.log(resources);
                if (resources.accepted) {
                    var Imagename = resources.new_name;
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        type: 'post',
                        url: "{{ route('admin.drivers.delete.upload.file') }}",
                        data: {
                            filename: Imagename,
                            id: "{{ $model->id }}"
                        },
                        success: function(data) {
                            resources.previewElement.remove();
                            $("#no_video").val(0);
                        },
                    });
                } else {
                    resources.previewElement.remove();
                }
            },
            accept: function(file, done) {
                //alert($("#no_video").val());
                if ($("#no_video").val() == 1) {
                    done("You cannot upload more video. Only one is allowed");
                } else {
                    done();
                }

            },
            init: function() {
                var myDropzone = this;
                if ("{{ $model->id }}") {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        url: "{{ route('admin.drivers.get.upload.file', ['$model->id']) }}",
                        type: 'get',
                        success: function(response) {
                            // console.log(response);
                            if (response.success == 1 && response.file_exist == 1) {
                                var mockFile = {
                                    name: response.filename,
                                    size: '0',
                                    id: response.id,
                                    new_name: response.filename,
                                    accepted: true
                                };
                                $("#no_video").val(1);

                                myDropzone.options.addedfile.call(myDropzone, mockFile);
                                myDropzone.options.thumbnail.call(myDropzone, mockFile, response
                                    .sourcePath);
                                mockFile.previewElement.classList.add('dz-success');
                                mockFile.previewElement.classList.add('dz-complete');
                            } else {
                                $("#no_video").val(0);
                            }
                        },
                    });
                }
            },
        });
        myDropzone.on("success", function(file, response) {
            // alert(response.file_name)
            // filecount++;
            $("#no_video").val(1);
            file.new_name = response.file_name;
            file.is_new = 'yes';
            // console.log(file);
        });
        myDropzone.on("error", function(file, response) {
            $("#no_video").val(1);
            // console.log(file);
        });



        var delete_file_record_url = '<?php echo URL::to('admin/drivers/delete-file-record/'); ?>';
    </script>




    <script type="text/javascript">
        $(document).ready(function()

            {

                $('#create_btn').click(function() {

                    $('#submit_btn').click();

                });



                $('#add_file').click(function() {

                    $("#filesTable").find('tbody')

                        .append(

                            '<tr>' +

                            '<td>' +

                            '<input type="text" placeholder="Title" class="input-class form-control form-control-solid" name="files_title[]">' +

                            '</td>' +

                            '<td>' +
                            '<div class="file-input">' +
                            '<input type="file" placeholder="" name="files[]">' +
                            '<span class="button">Choose</span>' +
                            '<span class="label" data-js-label>No File Selected</label>' +
                            '</div>' +
                            '</td>' +

                            '<td>' +

                            '<div class="btn-group"><a id="delete_file" href="javascript:void(0)" class="MuiIconButton-root"><i class="fas fa-trash"></i></a></div>' +

                            '</td>' +

                            '</tr>'

                        );

                });



                $('#filesTable').on('click', '#delete_file', function()

                    {

                        if (confirm('Are you sure you want to delete this record ?'))

                        {

                            $(this).parent().parent().parent().remove();

                        }

                    });



                $('.delete-file-record').click(function()

                    {

                        var element = $(this);

                        $.ajax({

                            type: "Get",

                            url: delete_file_record_url + '/' + $(this).attr('data'),

                            success: function(data, status)

                            {

                                element.parent().parent().parent().remove();

                            }



                        });

                    });



                $("#mobile_no").intlTelInput({
                    allowDropdown: true,
                    preferredCountries: [],
                    initialCountry: "{{ !empty($model->mobile_no_prefix_country_code) ? $model->mobile_no_prefix_country_code : 'us' }}",
                    separateDialCode: true
                });
                $("#mobile_no").on('countrychange', function(e, countryData) {
                    var data = $(".iti__selected-dial-code").html();
                    var data1 = $("#mobile_no").intlTelInput("getSelectedCountryData").iso2;
                    $("#mobile_no_prefix").val('+' + countryData.dialCode);
                    $("#mobile_no_prefix_country_code").val(data1);

                });
                $("#telephone_no").intlTelInput({
                    allowDropdown: true,
                    preferredCountries: [],
                    initialCountry: "{{ !empty($model->telephone_no_prefix_country_code) ? $model->telephone_no_prefix_country_code : 'us' }}",
                    separateDialCode: true
                });
                $("#telephone_no").on('countrychange', function(e, countryData) {
                    var data = $(".iti__selected-dial-code").html();
                    var data1 = $("#telephone_no").intlTelInput("getSelectedCountryData").iso2;
                    $("#telephone_no_prefix").val('+' + countryData.dialCode);
                    $("#telephone_no_prefix_country_code").val(data1);

                });

                $("#emergency_no").intlTelInput({
                    allowDropdown: true,
                    preferredCountries: [],
                    initialCountry: "{{ !empty($model->emergency_no_prefix_country_code) ? $model->emergency_no_prefix_country_code : 'us' }}",
                    separateDialCode: true
                });
                $("#emergency_no").on('countrychange', function(e, countryData) {
                    var data = $(".iti__selected-dial-code").html();
                    var data1 = $("#emergency_no").intlTelInput("getSelectedCountryData").iso2;
                    $("#emergency_no_prefix").val('+' + countryData.dialCode);
                    $("#emergency_no_prefix_country_code").val(data1);

                });


            });

        function checkFee(feetype) {

            if (feetype == 'fee') {

                //if($("#weekly_pay").val()!=''  || $("#per_job_pay").val()!=''   || $("#per_job_pay_percentage").val()!=''){
                $("#weekly_pay").val('');
                $("#per_job_pay").val('');
                $("#per_job_pay_percentage").val('');
                //}
            } else {
                //if($("#weekly_fee").val() !=''   || $("#per_job_fee").val()!=''   || $("#per_job_fee_percentage").val()!=''){
                $("#weekly_fee").val('');
                $("#per_job_fee").val('');
                $("#per_job_fee_percentage").val('');
                //}

            }

        }
    </script>
@endsection
