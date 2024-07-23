@extends('frontend.layouts.truckCompanyLayout')
@section('extraCssLinks')
@stop
@section('content')

<?php
    // $planData = $Plan->column_type == 0 ? 5 : 50; 
?>

<section class="form_section">
    <div class="container">
        <div class="outer_companyform_box middleHeight">
            <div class="track_company_box track_company_page">
                <div class="white_form_theme">
                    <h1 class="form_page_title">
                        <span class=""> {{trans('messages.Account Registration')}}</span>
                    </h1>
                    <div class="stepsProgressBar">
                        <ul class="list-unstyled multi-steps">
                            <li id="step-1" class="is-active">
                            <!--    <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div> -->
                            </li>
                            <li id="step-2" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            <li id="step-3" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            <li id="step-4" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            <li id="step-5">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            {{-- <li id="step-6">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li> --}}
                        </ul>
                    </div>
                    <p class="company_page_subtitle"> {{trans('messages.Truck Details')}}</p>
                    <div class="companyFormBox">                                
                        <div class="otp_verification">
                            <form method="post" action="{{ route('truck-company-registration') }}" >
                                @csrf
                                <div class="row">    
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class="form-label"><bdi> {{trans('messages.How many trucks are there in the company')}} ?</bdi></label><span class="text-danger"> * </span>
                                        <select id="exampleInputEmail1" name="number_of_trucks" class="form-select"
                                            aria-label="Default select example">
                                            <option value="" disabeled selected> {{trans('messages.Select number of trucks you have')}}</option>
                                            {{--
                                                @for ($i = 1; $i <= $planData; $i++)
                                                    <option   option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                             --}}
                                             @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @if ($errors->has('number_of_trucks'))
                                            <span class="text-danger text-center">
                                                {{ $errors->first('number_of_trucks') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <button type="submit" class="btn secondary-btn w-100 submit">{{trans('messages.next')}}</button>
                                    </div>
                                </div>
                                </div>
                            </form>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

    @section('extraJsLinks')
        <script src="{{ asset('public/frontend/js/drop-down.min.js') }}"></script>
    @stop

    @section('scriptCode')

<script>
    document.addEventListener('DOMContentLoaded', function() {
    var body = document.querySelector('body');
    body.classList.add('track_company');
    });
   
</script>


@stop