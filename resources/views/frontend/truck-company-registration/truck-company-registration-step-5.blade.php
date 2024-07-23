@extends('frontend.layouts.truckCompanyLayout')
@section('content')

<?php
    $planData = $Plan->column_type == 0 ? 5 : 50; 
?>

<section class="form_section">
    <div class="container">
        <div class="outer_companyform_box">
            <div class="track_company_box track_company_page">
                <div class="white_form_theme">
                    <h1 class="form_page_title">
                        <span class="">{{trans('messages.Account Registration') }}</span>
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
                            <li id="step-5" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            {{-- <li id="step-6" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li> --}}
                        </ul>
                    </div>
                    <p class="company_page_subtitle">{{trans('messages.Payment') }}</p>
                    <div class="companyFormBox">
                        <div class="selectCompanyPlan">
                            <form id="submittruckCompany" action="{{ route('submittruckCompany') }}" method="post">
                                @csrf
                                <div class="plan-card">
                                    
                                    {{-- <p class="planTopTitle">{{ $Plan->column_type == 0 ? trans('messages.Up to 5 Trucks') : trans('messages.More then 5') }} </p> --}}
                                    <div class="plan-img-box">
                                        <img src="{{ config('constants.PLAN_IMAGE_PATH').$Plan->image}}" alt="">
                                    </div>
                                    <p class="plan-period">
                                        @if($Plan->type =='0')
                                            {{trans('messages.monthly') }}
                                        @elseif($Plan->type =='1') 
                                            {{trans('messages.quarterly')}}
                                        @elseif($Plan->type =='2')
                                            {{trans('messages.Half Yearly') }}
                                        @else
                                        {{trans('messages.Yearly') }}
                                        @endif
                                    </p>
                                    <h4 class="@if(!$Plan->is_free) plan-amount @else text-primary @endif">                                            
                                        @if($Plan->is_free)
                                            {{ trans('messages.Free') }}  
                                        @else
                                            {{ $price }} 
                                            @if($Plan->column_type == 1)<small style="font-size:16px">{{trans('messages.per_truck')}} /</small> @endif
                                        @endif
                                        
                                    </h4> 
                                    <div class="planFeatures">
                                        <ul class="planFeaturesList">
                                        @if(!empty($Plan->feature))
                                            <?php
                                            //    $featur = explode(",",$Plan->plan_features);
                                            //    $featur = array_map('trim', $featur);
                                            ?>
                                            @foreach($Plan->feature as $featur_data)
                                            <li>{{$featur_data->mlname}}</li>
                                            @endforeach
                                        @endif
                                        </ul>
                                    </div>
                                    <a href="{{ url('term-condition') }}" target="_blank" class="plansTCLink"> {{trans('messages.View')}} {{trans('messages.Terms & Conditions')}}</a>
                                    <a class=" btn plan-btn submit" href="javascript:void(0)"> {{trans('messages.Continue') }}</a>
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

    $('.submit').on('click',function(){
        $('#submittruckCompany').submit();
    });
   
</script>


@stop