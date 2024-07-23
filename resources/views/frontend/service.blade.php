@extends('frontend.layouts.defaultpages')
@section('content')
@php $i = 1; @endphp
<section class="our-services-section">
    <div class="container">
        <div class="blur-title our-service-title">{{trans('messages.WHAT_WE_DO')}}</div>
        <h2 class="section-heading service-heading">{{trans('messages.Our_Services')}}</h2>
        @foreach($services as $key => $service)
        @php $i % 2 == 0 ? $class = 'order-1':$class =''; @endphp
        <div class="service-portion">
            <div class="row row_reverse{{$key}}">
                <div class="col-lg-6 {{$class}}">
                    <div class="right-logistics">
                        <img src="{{ config('constants.OURSERVICE_PATH').$service->image}}" alt="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="left-logistics">
                        <div class="text-body-services">
                            <h3 class="logistic-heading">{{$service->title}}</h3>
                            <p class="logistic-para">
                                {{$service->description}}
                            </p>
                        </div>
                        <a class="btn all-get-started" href="{{$service->button_link}}">{{$service->button_text}}</a>
                    </div>
                </div>
            </div>
        </div>
        @php $i++ @endphp
        @endforeach
</section>
@stop