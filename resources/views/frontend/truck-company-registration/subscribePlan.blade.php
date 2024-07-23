@extends('frontend.layouts.defaultpages')
@section('backgroundImage')
<body class="homePage">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="img/logo.png" alt="">
        </div>
    </div>
@stop
@section('content')

<section class="our-plans">
    <div class="container">
      <div class="blur-title our-plan-title">{{trans('messages.subscriptions')}}</div>
      <h3 class="section-heading plan-heading text-center">{{trans('messages.Our plans')}}</h3>
      <p class="plan-para">
      {{ trans('messages.plan_subscription_description') }}
      </p>
      {{-- <div class="plan-switch-box">
        <div class="form-check form-switch">
          <label class="form-check-label plan-label-2" for="flexSwitchCheckDefault">{{trans('messages.Up to 5 Trucks')}}</label>
          <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"  checked>
          <label class="form-check-label  plan-label-2" for="flexSwitchCheckDefault">{{trans('messages.More then 5')}}</label>

        </div>
      </div> --}}
      <div class="row gy-4">
        @if(!empty($plans))
        @foreach($plans as $plan)
          @if($plan->column_type == 1 )
          <div  class="col-lg-3 col-md-6 no_truck">
            <div class="plan-card">
              <div class="plan-img-box">
              <img src="{{ config('constants.PLAN_IMAGE_PATH').$plan->image}}" alt="" height="53" width="53">
              </div>
             
              <p class="plan-period">
              @if($plan->type =='0')
                    {{trans('messages.monthly') }}
                @elseif($plan->type =='1') 
                    {{trans('messages.quarterly')}}
                @elseif($plan->type =='2')
                    {{trans('messages.Half Yearly') }}
                @else
                   {{trans('messages.Yearly') }}
                @endif
              </p>
            
         
              <h4 class="@if(!$plan->is_free) plan-amount @else text-primary free-plan-amount @endif">
                @if($plan->is_free)
                <span style="font-size: 38px;color: #1535B9;">
                  {{ trans('messages.Free') }}  
                </span>
                @else
                  {{ $plan->price }} 
                @endif
            </h4>
              <div class="planFeatures planFeatures-tags">
                <ul class="planFeaturesList">
                  @if(!empty($plan->feature))
                  @foreach($plan->feature as $featur_data)
                    <li>{{$featur_data->mlname}}</li>
                  @endforeach
                  @endif
                </ul>
              </div>
              <a href="{{ url('term-condition') }}" target="_blank" class="plansCardTCLink">{{trans('messages.View')}} {{trans('messages.Terms & Conditions')}}</a>

              <a class=" btn plan-btn" href="{{ route('subscribe-plan',[$validate_string,base64_encode($plan->id)]) }}">{{trans('messages.Get Started') }}</a>
            </div>
          </div>
          @else
          <div class="col-lg-3 col-md-6 per_truck">
            <div class="plan-card">
              <div class="plan-img-box">
              <img src="{{ config('constants.PLAN_IMAGE_PATH').$plan->image}}" alt=""  height="53" width="53">
              </div>
        
              <p class="plan-period">
              @if($plan->type =='0')
                    {{trans('messages.monthly') }}
                @elseif($plan->type =='1') 
                    {{trans('messages.quarterly')}}
                @elseif($plan->type =='2')
                    {{trans('messages.Half Yearly') }}
                @else
                   {{trans('messages.Yearly') }}
                @endif
              </p>
            
              <h4 class="@if(!$plan->is_free) plan-amount @else text-primary free-plan-amount @endif">
                @if($plan->is_free)
                <span style="font-size: 38px;color: #1535B9;">
                  {{ trans('messages.Free') }}  
                </span>
                @else
                  {{ $plan->price }} 
                @endif
            </h4>
              <div class="planFeatures planFeatures-tags">
              <ul class="planFeaturesList">
                  @if(!empty($plan->feature))
                    @foreach($plan->feature as $featur_data)
                    <li>{{$featur_data->mlname}}</li>
                    @endforeach
                  @endif
                </ul>
              </div>
              <a href="{{ url('term-condition') }}" target="_blank" class="plansCardTCLink">{{trans('messages.View')}} {{trans('messages.Terms & Conditions')}}</a>

              <a class=" btn plan-btn" href="{{ route('subscribe-plan',[$validate_string,base64_encode($plan->id)]) }}">  {{trans('messages.Get Started') }}</a>
            </div>
          </div>
          @endif
       
        @endforeach
        @endif
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
</script>
@endsection
