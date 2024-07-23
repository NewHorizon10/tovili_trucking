@extends('frontend.layouts.default')
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
<section class="hero-slider">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                @if(!empty($slider_data))
               @foreach($slider_data as $slider_data_loop)
                <div class="swiper-slide">
                    <figure>
                        <img src="{{ config('constants.SLIDER_IMAGE_PATH').$slider_data_loop->image}}" alt="">
                    </figure>
                    <div class="hero-content">
                        <div class="container">
                            <h1 class="banner-title">{{ $slider_data_loop->title ?? '' }}</h1>  
                            <h1 class="banner-title2">{{ $slider_data_loop->subtitle ?? '' }}</h1>
                            <h3 class="banner-title3">{!! $slider_data_loop->description ?? '' !!}<br></h3>
                            <a href="{{ route('contact') }}" class="secondary-btn support-btn">{{ $slider_data_loop->buttontext ?? '' }}<svg width="28" height="26" viewBox="0 0 28 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.33366 16.6665H15.367C15.7448 16.6665 16.0559 16.5385 16.3003 16.2825C16.5448 16.0265 16.667 15.7101 16.667 15.3332C16.667 14.9554 16.539 14.6385 16.283 14.3825C16.027 14.1265 15.7106 13.999 15.3337 13.9998H7.30033C6.92255 13.9998 6.61144 14.1278 6.36699 14.3838C6.12255 14.6398 6.00033 14.9563 6.00033 15.3332C6.00033 15.711 6.12833 16.0278 6.38433 16.2838C6.64033 16.5398 6.95677 16.6674 7.33366 16.6665ZM7.33366 12.6665H20.7003C21.0781 12.6665 21.3892 12.5385 21.6337 12.2825C21.8781 12.0265 22.0003 11.7101 22.0003 11.3332C22.0003 10.9554 21.8723 10.6385 21.6163 10.3825C21.3603 10.1265 21.0439 9.99895 20.667 9.99984H7.30033C6.92255 9.99984 6.61144 10.1278 6.36699 10.3838C6.12255 10.6398 6.00033 10.9563 6.00033 11.3332C6.00033 11.711 6.12833 12.0278 6.38433 12.2838C6.64033 12.5398 6.95677 12.6674 7.33366 12.6665ZM7.33366 8.66651H20.7003C21.0781 8.66651 21.3892 8.53851 21.6337 8.28251C21.8781 8.02651 22.0003 7.71006 22.0003 7.33317C22.0003 6.9554 21.8723 6.63851 21.6163 6.38251C21.3603 6.12651 21.0439 5.99895 20.667 5.99984H7.30033C6.92255 5.99984 6.61144 6.12784 6.36699 6.38384C6.12255 6.63984 6.00033 6.95628 6.00033 7.33317C6.00033 7.71095 6.12833 8.02784 6.38433 8.28384C6.64033 8.53984 6.95677 8.6674 7.33366 8.66651ZM0.666994 24.0998V3.33317C0.666994 2.59984 0.928328 1.97184 1.45099 1.44917C1.97366 0.926506 2.60122 0.665617 3.33366 0.666506H24.667C25.4003 0.666506 26.0283 0.92784 26.551 1.45051C27.0737 1.97317 27.3346 2.60073 27.3337 3.33317V19.3332C27.3337 20.0665 27.0723 20.6945 26.5497 21.2172C26.027 21.7398 25.3994 22.0007 24.667 21.9998H6.00033L2.93366 25.0665C2.51144 25.4887 2.02788 25.5834 1.48299 25.3505C0.938105 25.1176 0.666105 24.7007 0.666994 24.0998ZM3.33366 20.8998L4.90033 19.3332H24.667V3.33317H3.33366V20.8998Z" fill="white"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            <div class="swiper-pagination"></div>

            <div class="container hero-pagination">
                <div class="swiper-button-next">

                </div>
                <div class="swiper-button-prev">

                </div>
            </div>

        </div>
    </section>
@stop

@section('scriptCode')
    <script>
        // Banner Slider
        var swiper = new Swiper(".hero-swiper", {
            spaceBetween: 0,
            speed: 1000,
            //effect: 'fade',
            slidesPerView: 1,
            loop: true,
            navigation: {
                nextEl: ".hero-swiper .swiper-button-next",
                prevEl: ".hero-swiper .swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            // autoplay: {
            //   delay: 4000,
            //   disableOnInteraction: false,
            //   pauseOnMouseEnter: true,
            // },
        });
    </script>
@stop
