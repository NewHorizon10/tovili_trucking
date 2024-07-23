@extends('frontend.layouts.defaultpages')
@section('backgroundImage')
<body class="homePage ">
@stop
@section('content')

    <!-- loader  -->
    <div class="loader-wrapper">
        <div style="position: relative;width: 100%;height: 100%;background-color: #fff;">
            Please wait
        </div>
    </div>
<section class="our-plans">
    <div class="container">
      <div class="blur-title our-plan-title">{{ trans('messages.subscriptions') }}</div>
      <h3 class="section-heading plan-heading text-center">{{ trans('messages.Our plans') }}</h3>
      <p class="plan-para">
      {{ trans('messages.plan_subscription_description') }}
      </p>


      <div class="service-portion">
      <div class="row">
                
                <div class="col-lg-6">
                    <div class="left-logistics">
                        <div class="text-body-services">
                        <div class="plan-img-box d-flex align-items-center">
                          <img src="{{ config('constants.PLAN_IMAGE_PATH').$planDetails->image}}" alt="" height="53" width="53">
                          <h3 class="logistic-heading mx-3 mb-0">
                            @if($plansubscriptionDetail->type =='0')
                            {{trans('messages.monthly') }}
                            @elseif($plansubscriptionDetail->type =='1') 
                            {{trans('messages.quarterly')}}
                            @elseif($plansubscriptionDetail->type =='2')
                            {{trans('messages.Half Yearly') }}
                            @else
                            {{trans('messages.Yearly') }}
                            @endif
                          </h3>
                          
                        </div>

                            <div class="row">
                              @if($plansubscriptionDetail->is_free == 1)
                                <div class="col-md-6"> <p>  {{trans("messages.Free")}} </p> </div>
                              @else
                                <div class="col-md-6"> <p>  {{ $plansubscriptionDetail->price ?? '' }}  <img src="{{asset('public/frontend/img/plan-icon.png')}}" alt=""></p> </div>
                              @endif
                              <div class="col-md-6"><p> {{trans('messages.price')}} </p></div>
                              @if($plansubscriptionDetail->discount != 0)
                                <div class="col-md-6"> <p> % {{ $plansubscriptionDetail->discount ?? '' }}  </p> </div>
                                <div class="col-md-6"><p> {{trans('messages.discount')}} </p></div>
                              @endif
                                @if($plansubscriptionDetail->is_free == 1)
                                  <div class="col-md-6"> <p>  {{trans("messages.Free")}} </p> </div>
                                @else
                                  <div class="col-md-6"> <p> {{ $plansubscriptionDetail->total_price ?? '' }} <img src="{{asset('public/frontend/img/plan-icon.png')}}" alt=""></p> </div>
                                @endif
                                <div class="col-md-6"><p> {{trans('messages.total_price')}} </p></div>
                              @php
                                  $currentDate = now(); 
                              @endphp
                              <div class="col-md-6">
                                <p>{{  date(Config('Reading.date_format'), strtotime($currentDate))  }}</p>
                              </div>
                              <div class="col-md-6">
                                <p>{{trans('messages.start_time')}}</p>
                              </div>
                              <div class="col-md-6">
                                @php
                                $startDate = $currentDate->copy();
                                
                                if ($plansubscriptionDetail->type == '0') {
                                    $endDate = $currentDate->copy()->addMonth();
                                } elseif ($plansubscriptionDetail->type == '1') {
                                    $endDate = $currentDate->copy()->addMonths(3);
                                } elseif ($plansubscriptionDetail->type == '2') {
                                    $endDate = $currentDate->copy()->addMonths(6);
                                } else {
                                    $endDate = $currentDate->copy()->addYear();
                                }
                                $dateAfter2day = $currentDate->addDay(2)->copy();
                                @endphp
                                 <p class="@if($endDate <= $dateAfter2day) text-danger @endif ">
                                 
                                
                                  
                                  {{date(Config('Reading.date_format'), strtotime($endDate))}}

                                  </p> 
                                </div>
                                
                              <div class="col-md-6"><p> {{trans('messages.end_time')}} </p></div>

                            </div>
                        </div>
                        <a class="btn all-get-started subscribe_now_cls" href="{{ route('payment', request()->segment(2)) }}">{{trans('messages.subscribe_now')}}</a>
                    </div>
                </div>
                <div class="col-lg-6 ">
                  <div class="right-logistics">
                   <h3> {{ trans('messages.admin_common_plan_features') }} </h3>

                   <div class="planFeatures planFeatures-tags" style="min-height: 331.84px;">
                <ul class="planFeaturesList">
                  <li>
                  @if($plansubscriptionDetail->column_type =='0')
                  {{trans('messages.Up to 5 Trucks')}}
                  @else 
                  {{trans('messages.More then 5')}}
                  @endif
                  </li>
                    @foreach($planFeatures as $value)
                        <li>{{$value->name ?? ''}}</li>
                    @endforeach
                </ul>

                
              </div>
                  </div>
                </div>
                
              </div>
            </div>




   </div>
  </section>
  


  <style>
  .free-plan-amount {
    font-weight: 800;
    font-size: 38px;
    color: #1535B9;
    margin-bottom: 43px;
    position: relative;
    margin-top: 11px;
    line-height: 1;
  }
  </style>


@stop
@section('scriptCode')
<script>
    $(document).ready(function() {
      $('.per_truck').hide();

      $('#flexSwitchCheckDefault').change(function() {
        if (!$(this).is(':checked')) {
          $('.per_truck').show();
          $('.no_truck').hide();
        } else {
          $('.per_truck').hide();
          $('.no_truck').show();
        }
      });
      // Cache the highest
      var highestBox = 0;
      // Select and loop the elements you want to equalise
      $('.planFeatures-tags', this).each(function(){
        // If this box is higher than the cached highest then store it
        if($(this).height() > highestBox) {
          highestBox = $(this).height(); 
        }
      });  
      // Set the height of all those children to whichever was highest 
      $('.planFeatures-tags').css("min-height",highestBox);

    });
    $(document).ready(function() {
        setTimeout(function() {
          $(".subscribe_now_cls").click();
        }, ); // 3000 milliseconds = 3 seconds
    })
    window.location.href = "{{ route('payment', request()->segment(2)) }}";
</script>
@endsection
