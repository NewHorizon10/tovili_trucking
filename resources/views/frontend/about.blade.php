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
  <div class="page_height">
    <section class="about-us-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-5">
            <div class="about-right-box">
              <img src="{{Config('constants.ABOUT_US_IMAGE_PATH').$about->image}}" alt="">
            </div>
          </div>
          <div class="col-lg-7">
            <div class="about-left">
              <div class="about-us-top blur-title">{{trans("messages.admin_ABOUT_US")}}</div>
              <h2 class="section-heading">
                {!! $about->heading ? $about->heading : "" !!}
                <!-- <span class="blue-text">We Are the Biggest</span> Transporterium
                Platform -->
              </h2>
              <p class="banner-para">
                {!! $about->description ? $about->description : "" !!}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="forklift-section">
      <div class="container">
        <!-- <div class="main-forklift"> -->
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="forklift-data">
              <h2 class="our-goal">{{trans("messages.admin_Our_Goal")}}</h2>
              <div class="fork-para">
                {!! $about->goal_description ? $about->goal_description : "" !!}
                <div class="comma-img-box">
                  <img src="{{ asset('public/img/comma.png') }}" alt="">
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="forklift-img-box">
              <img src="{{Config('constants.ABOUT_US_GOAL_IMAGE_PATH').$about->goal_image}}" alt="">
            </div>
          </div>
        </div>
        <!-- </div> -->
      </div>
    </section>

    <section class="achievements-section counter-section">
      <div class="container">
        <div class="facts-title blur-title">{{trans("messages.admin_INTERESTING_FACTS")}}</div>
        <h2 class="high-heading">{{trans("messages.admin_High_Achievements")}}</h2>
        <div class="row">
          @foreach($achievments as $achievment)
          <div class="col-md-3 col-6 counter-count">
            <div class="clients-img-box">
              <img src="{{url(Config('constants.ACHIEVMENT_IMAGE_PATH').$achievment->image)}}" alt="">
            </div>
            <h3 class="clients-count"><span >{{$achievment->name}}</span></h3>
            <p class="client-text client-text-last">{{$achievment->description}}</p>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="creative-section">
      <div class="container">
        <div class="creative-title blur-title">{{trans("messages.admin_CREATIVE_PEOPLE")}}</div>
        <h2 class="high-heading meet-experts">{{trans("messages.admin_Meet_Experts")}}</h2>
        <div class="row">
          @foreach($teams as $team)
          <div class="col-lg-3 col-sm-6 mb-lg-4">
            <div class="experts-card">
              <div class="experts-img-box">
                <img src="{{url(Config('constants.TEAM_IMAGE_PATH').$team->image)}}" alt="">
              </div>
              <div class="experts-footer">
                <h4 class="experts-name">{{$team->name}}</h4>
                <p class="experts-post">{{$team->designation}}</p>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="trusted-section">
      <div class="container">
        <div class="blur-title our-clients">{{trans("messages.admin_OUR_CLIENTS")}}</div>
        <h2 class="high-heading trusted-by">{{trans("messages.admin_Trusted_By")}}</h2>
        <div class="row">
          <div class="swiper trusted_clients_swipersilder">
            <div class="swiper-wrapper">
              @foreach($clients as $client)
              <div class="swiper-slide client-block">
                <img src="{{url(Config('constants.CLIENT_IMAGE_PATH').$client->image)}}" alt="">
              </div>
              @endforeach
            </div>
            <div class="swiper-pagination"></div>
          </div>
        </div>
      </div>
    </section>
  </div>
  @endsection
  @section('extraJsLinks')
  <script>
    //COUNTER-SECTION IN-VIEW
    (function($) {
      $(function() {
        var section = document.querySelector('.counter-section');
        var hasEntered = false;
        if (!section)
          return;

        var initAnimate = (window.scrollY + window.innerHeight) >= section.offsetTop;
        if (initAnimate && !hasEntered) {
          hasEntered = true;
          counterActivate();
        }

        window.addEventListener('scroll', (e) => {
          var shouldAnimate = (window.scrollY + window.innerHeight) >= section.offsetTop;

          if (shouldAnimate && !hasEntered) {
            hasEntered = true;
            counterActivate();
          }
        });

        function counterActivate() {
          $('.counter-count .count').each(function() {
            $(this).prop('Counter', 0).animate({
              Counter: $(this).text()
            }, {
              duration: 4000,
              easing: 'swing',
              step: function(now) {
                $(this).text(Math.ceil(now), 3);
              }
            });
          });
        }
      }); // END OF DOCUMENT READY
    })(jQuery);

    var swiper = new Swiper(".trusted_clients_swipersilder", {
      spaceBetween: 30,
      //cssMode: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false
      },

      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      mousewheel: true,
      //keyboard: true,
      breakpoints: {
        0: {
          slidesPerView: 2,
        },
        575: {
          slidesPerView: 2,
        },

        767: {
          slidesPerView: 3,
        },

        991: {
          slidesPerView: 5,
        },

        1200: {
          slidesPerView: 5,
        },
      },
    });
  </script>
  @endsection