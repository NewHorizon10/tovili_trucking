@extends('admin.layouts.layout')
@section('css')
<style>
    .iti.iti--allow-dropdown.iti--separate-dial-code {
        width: 100%;
    }
</style>
@endsection
@section('content')
<?php $counter = 0; ?>
<style>
    .invalid-feedback {
        display: inline;
    }

    .AClass {
        right: 10px;
        position: absolute;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sumoselect/3.4.9/jquery.sumoselect.min.js" integrity="sha512-+Ea4TZ8vBWO588N7H6YOySCtkjerpyiLnV7bgqwrQF+vqR8+q/InGK9WDZx5d6VtdGRoV6uLd5Dwz2vE7EL3oQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<style>
    .feedbackstars {
        display: table;
        margin: 0 auto
    }

    .feedbackstars input.star {
        display: none
    }

    .feedbackstars label.star {
        padding: 0 8px;
        font-size: 26px;
        color: #444;
        transition: all .2s;
        cursor: pointer
    }

    .feedbackstars input.star:checked~label.star:before {
        color: orange;
        transition: all .25s;
        font-weight: 900
    }

    .feedbackstars input.star-5:checked~label.star:before {
        color: orange;
        text-shadow: 0 0 15px rgb(255 165 0 / 10%)
    }

    .feedbackstars input.star-1:checked~label.star:before {
        color: #f62
    }

    .feedbackstars label.star:hover {
        transform: rotate(-15deg) scale(1.3)
    }

    .feedbackstars label.star:before {
        content: "";
        font-family: "Font Awesome 5 Free";
    }

    .feedbackstars .rev-box {
        overflow: hidden;
        height: 0;
        width: 100%;
        transition: all .25s
    }

    input.star:checked~.rev-box {
        height: 125px;
        overflow: visible
    }

    .starRate-filter {
        border: 0;
        outline: 0 !important;
        background-color: transparent;
        padding: 0;
        font-size: 18px;
        color: #ff8800;
        margin: 0 0 2px;
    }

    .starRate-filter span {
        color: #000000;
        font-size: 16px;
    }

    .givefeedback_body h4 {
        text-align: center;
        font-size: 18px
    }

    .givefeedback_body.modal-body {
        padding: 40px 20px
    }

    .givefeedback_body p {
        text-align: center
    }
</style>
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        {{trans('messages.admin_common_Edit')}}  {{trans('messages.review')}} </h5>
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">{{trans('messages.Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route($modelName . '.index') }}" class="text-muted"> {{trans('messages.review_rating')}} </a>
                        </li>
                    </ul>
                </div>
            </div>
            @include('admin.elements.quick_links')
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class=" container ">
            <form action="{{ route($modelName . '.edit', [base64_encode($userDetails->id)]) }}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-1"></div>
                            <div class="col-xl-10">
                                <h3 class="mb-10 font-weight-bold text-dark">

                                </h3>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="name"> {{trans("messages.review_by")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="user_id" class="form-control form-control-solid form-control-lg  @error('user_id') is-invalid @enderror" value="{{ $userDetails->getUser->name ?? '' }}" readonly>
                                            @if ($errors->has('user_id'))
                                            <div class=" invalid-feedback">
                                                {{ $errors->first('user_id') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="review_to">{{trans("messages.reviewed_to")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="review_to" readonly class="form-control form-control-solid form-control-lg  @error('review_to') is-invalid @enderror" value="{{ $userDetails->getTruckCompany->name ?? ''}}">
                                            @if ($errors->has('review_to'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('review_to') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label for="review">{{trans("messages.reviews")}}</label><span class="text-danger"> * </span>
                                            <textarea type="text" name="review" class="form-control form-control-solid form-control-lg  @error('review') is-invalid @enderror" value="{{ $userDetails->review ?? '' }}" rows="5">{{ $userDetails->review ?? '' }}</textarea>
                                            @if ($errors->has('review'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('review') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                 

                                    <div class="col-xl-6">
                                        <label for="overall_rating">{{trans("messages.overall_rating")}}</label><span class="text-danger"> * </span>
                                        <div class="feedbackstars">
                                            <div class="form-inline flex-row-reverse align-items-center">
                                                <div class="form-inline flex-row-reverse align-items-center">
                                                    <input class="star star-5" id="star-5-1" type="radio" value="5" name="overalls_rating" @if($userDetails->overall_rating == 5) checked @endif/>
                                                    <label class="star star-5" for="star-5-1"></label>
                                                    <input class="star star-4" id="star-4-1" type="radio" value="4" name="overalls_rating" @if($userDetails->overall_rating == 4) checked @endif/>
                                                    <label class="star star-4" for="star-4-1"></label>
                                                    <input class="star star-3" id="star-3-1" type="radio" value="3" name="overalls_rating" @if($userDetails->overall_rating == 3) checked @endif/>
                                                    <label class="star star-3" for="star-3-1"></label>
                                                    <input class="star star-2" id="star-2-1" type="radio" value="2" name="overalls_rating" @if($userDetails->overall_rating == 2) checked @endif/>
                                                    <label class="star star-2" for="star-2-1"></label>
                                                    <input class="star star-1" id="star-1-1" type="radio" value="1" name="overalls_rating" @if($userDetails->overall_rating == 1) checked @endif/>
                                                    <label class="star star-1" for="star-1-1"></label>
                                                </div>
                                                <div class="invalid-feedback" style="display: {{$errors->first('rating') ? 'block' : 'none'}}"><?php echo $errors->first('rating'); ?></div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-6 mb-5">
                                        <label for="driver_rating">{{trans("messages.driver_rating")}}</label><span class="text-danger"> * </span>
                                        <div class="feedbackstars">
                                            <div class="form-inline flex-row-reverse align-items-center">
                                                <div class="form-inline flex-row-reverse align-items-center">
                                                    <input class="star star-5" id="star-5-1-2" type="radio" value="5" name="driver_rating" @if($userDetails->driver_rating == 5) checked @endif/>
                                                    <label class="star star-5" for="star-5-1-2"></label>
                                                    <input class="star star-4" id="star-4-1-2" type="radio" value="4" name="driver_rating" @if($userDetails->driver_rating == 4) checked @endif/>
                                                    <label class="star star-4" for="star-4-1-2"></label>
                                                    <input class="star star-3" id="star-3-1-2" type="radio" value="3" name="driver_rating" @if($userDetails->driver_rating == 3) checked @endif/>
                                                    <label class="star star-3" for="star-3-1-2"></label>
                                                    <input class="star star-2" id="star-2-1-2" type="radio" value="2" name="driver_rating" @if($userDetails->driver_rating == 2) checked @endif/>
                                                    <label class="star star-2" for="star-2-1-2"></label>
                                                    <input class="star star-1" id="star-1-1-2" type="radio" value="1" name="driver_rating" @if($userDetails->driver_rating == 1) checked @endif/>
                                                    <label class="star star-1" for="star-1-1-2"></label>
                                                </div>
                                                <div class="invalid-feedback" style="display: {{$errors->first('rating') ? 'block' : 'none'}}"><?php echo $errors->first('rating'); ?></div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <label for="professionality">{{trans("messages.professionality")}}</label><span class="text-danger"> * </span>
                                        <div class="feedbackstars">
                                            <div class="form-inline flex-row-reverse align-items-center">
                                                <div class="form-inline flex-row-reverse align-items-center">
                                                    <input class="star star-5" id="star-5-2" type="radio" value="5" name="professionality" @if($userDetails->professionality == 5) checked @endif/>
                                                    <label class="star star-5" for="star-5-2"></label>
                                                    <input class="star star-4" id="star-4-2" type="radio" value="4" name="professionality" @if($userDetails->professionality == 4) checked @endif/>
                                                    <label class="star star-4" for="star-4-2"></label>
                                                    <input class="star star-3" id="star-3-2" type="radio" value="3" name="professionality" @if($userDetails->professionality == 3) checked @endif/>
                                                    <label class="star star-3" for="star-3-2"></label>
                                                    <input class="star star-2" id="star-2-2" type="radio" value="2" name="professionality" @if($userDetails->professionality == 2) checked @endif/>
                                                    <label class="star star-2" for="star-2-2"></label>
                                                    <input class="star star-1" id="star-1-2" type="radio" value="1" name="professionality" @if($userDetails->professionality == 1) checked @endif/>
                                                    <label class="star star-1" for="star-1-2"></label>
                                                </div>
                                                <div class="invalid-feedback" style="display: {{$errors->first('rating') ? 'block' : 'none'}}"><?php echo $errors->first('rating'); ?></div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-xl-6">
                                        <label for="meet_schedule">{{trans("messages.meet_schedule")}}</label><span class="text-danger"> * </span>
                                        <div class="feedbackstars">
                                            <div class="form-inline flex-row-reverse align-items-center">
                                                <div class="form-inline flex-row-reverse align-items-center">
                                                    <input class="star star-5" id="star-5-3" type="radio" value="5" name="meet_schedule" @if($userDetails->meet_schedule == 5) checked @endif/>
                                                    <label class="star star-5" for="star-5-3"></label>
                                                    <input class="star star-4" id="star-4-3" type="radio" value="4" name="meet_schedule" @if($userDetails->meet_schedule == 4) checked @endif/>
                                                    <label class="star star-4" for="star-4-3"></label>
                                                    <input class="star star-3" id="star-3-3" type="radio" value="3" name="meet_schedule" @if($userDetails->meet_schedule == 3) checked @endif/>
                                                    <label class="star star-3" for="star-3-3"></label>
                                                    <input class="star star-2" id="star-2-3" type="radio" value="2" name="meet_schedule" @if($userDetails->meet_schedule == 2) checked @endif/>
                                                    <label class="star star-2" for="star-2-3"></label>
                                                    <input class="star star-1" id="star-1-3" type="radio" value="1" name="meet_schedule" @if($userDetails->meet_schedule == 1) checked @endif/>
                                                    <label class="star star-1" for="star-1-3"></label>
                                                </div>
                                                <div class="invalid-feedback" style="display: {{$errors->first('rating') ? 'block' : 'none'}}"><?php echo $errors->first('rating'); ?></div>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                 
                                </div>
                                <br>

                                <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                    <div>
                                        <button button type="submit" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                                            Submit
                                        </button>
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

<style>
    .form-control {
        width: 100%;
    }
</style>


@stop