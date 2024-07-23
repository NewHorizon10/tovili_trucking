<!doctype html>
<!-- <html lang="en"> -->
<html dir="rtl" class="rtl" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    // $segment2    =    Request::segment(1);
    // $segment3    =    Request::segment(2);
    // $segment4    =    Request::segment(3);
    // $segment5    =    Request::segment(4);    

    $current_uri = request()->segments();
    $current_uri = implode("/", $current_uri) ?? "/";
    // if()
    $data_seo           = get_other_data($current_uri);
    // dd($data_seo);
    if ($data_seo) {
        $newUrl             = request()->path();
        // dd();
        $metatitle          = !empty($data_seo->title) ? $data_seo->title : '';
        $title              = !empty($metatitle) ? $metatitle : Config::get("Site.title");
        $metaDesc           = !empty($data_seo->meta_description) ? $data_seo->meta_description : '';
        $metaKey            = !empty($data_seo->meta_keywords) ? $data_seo->meta_keywords : '';
        $twitter_card       = !empty($data_seo->twitter_card) ? $data_seo->twitter_card : '';
        $twitter_site       = !empty($data_seo->twitter_site) ? $data_seo->twitter_site : '';
        $og_url             =  !empty($data_seo->og_url) ? $data_seo->og_url : '';
        $og_type            =  !empty($data_seo->og_type) ? $data_seo->og_type : '';
        $og_title           = !empty($data_seo->og_title) ? $data_seo->og_title : '';
        $og_description     = !empty($data_seo->og_description) ? $data_seo->og_description : '';
        $og_image           = !empty($data_seo->og_image) ? $data_seo->og_image    : '';
        $meta_chronicles    = !empty($data_seo->meta_chronicles) ? $data_seo->meta_chronicles    : '';
        if ($og_image) {
            $og_image = Config('constants.SEO_PAGE_IMAGE_IMAGE_PATH') . $og_image;
        }
    } else {
        $metatitle          = '';
        $title                = Config::get("Site.title");
        $metaDesc           = '';
        $metaKey            = '';
        $twitter_card       = '';
        $twitter_site       = '';
        $og_url             = '';
        $og_type            = '';
        $og_title           = '';
        $og_description     = '';
        $og_image           = '';
        $meta_chronicles    = '';
    }

    // dd($title);
    ?>
    <title>{{ Config('Site.title') }}</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{url("public/img/favicon/apple-icon-57x57.png")}}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{url("public/img/favicon/apple-icon-60x60.png")}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{url("public/img/favicon/apple-icon-72x72.png")}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{url("public/img/favicon/apple-icon-76x76.png")}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{url("public/img/favicon/apple-icon-114x114.png")}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{url("public/img/favicon/apple-icon-120x120.png")}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{url("public/img/favicon/apple-icon-144x144.png")}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{url("public/img/favicon/apple-icon-152x152.png")}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{url("public/img/favicon/apple-icon-180x180.png")}}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{url("public/img/favicon/android-icon-192x192.png")}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{url("public/img/favicon/favicon-32x32.png")}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{url("public/img/favicon/favicon-96x96.png")}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{url("public/img/favicon/favicon-16x16.png")}}">
    <link rel="shortcut icon" href="{{ asset('./public/img/favicon.ico')}}" />
    <link rel="manifest" href="{{url('public/img/favicon/manifest.json')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{url("public/img/favicon/ms-icon-144x144.png")}}">
    <meta name="theme-color" content="#ffffff">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.2.3 CSS -->
    <link rel="stylesheet" href="{{asset('public/frontend/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">
    <!--  Font-Awesome-5 CSS -->
    <link rel="stylesheet" href="{{asset('public/frontend/css/font-awesome.css')}}">
    <!-- Swiper 8.1.5 -->
    <link rel="stylesheet" href="{{asset('public/frontend/css/swiper-bundle.min.css')}}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset('public/frontend/css/style.css')}}">
    <!-- Custom Responsive CSS -->
    <link rel="stylesheet" href="{{asset('public/frontend/css/responsive.css')}}">
    <!-- RTL CSS -->
    <link rel="stylesheet" href="{{asset('public/frontend/css/rtl.css')}}">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Custom Responsive CSS -->
    <link rel="stylesheet" href="{{asset('public/frontend/css/responsive.css')}}">
    <!-- Dashboard CSS-->
    <link rel="stylesheet" href="{{asset('public/frontend/css/dashboard.css')}}">
    <!-- Dashboard Responsive CSS-->
    <link rel="stylesheet" href="{{asset('public/frontend/css/dashboard-responsive.css')}}">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
    @yield('extraCssLinks')
    <style>
            .attachment-elements{
                display: flex;
            }
            .attachment-elements a{
                margin: 10px;
                width: 50px;
                /* height: 100px; */
            }
            .attachment-elements a img{
                width: 50px;
                height: 100px;
            }
            .attachment-elements a video{
                width: 50px;
                /* height: 100px; */
            }
        </style>
</head>
@yield('backgroundImage')
@include('frontend.elements.customer-header')
<div class="dashboard-main">
    <div class="container">
        <div class="content-area">
            <div class="row">
                @include('frontend.elements.customer-side-menu')
                @yield('content')
            </div>
        </div>
    </div>
</div>

@include('frontend.elements.footer')
<a href="javascript:void(0)" id="top-button" class="back_top">
    <span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25" fill="none">
            <mask id="mask0_511_55" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="25" height="25">
                <rect x="0.5" y="0.25" width="24" height="24" fill="currentColor" />
            </mask>
            <g mask="url(#mask0_511_55)">
                <path d="M11.5 22.25V6.075L6.9 10.65L5.5 9.25L12.5 2.25L19.5 9.25L18.1 10.675L13.5 6.075V22.25H11.5Z" fill="currentColor" />
            </g>
        </svg>
    </span>
</a>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<!-- Bootstrap v5.2.3  -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<!-- Swiper 8.1.5 -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script src="{{asset('public/frontend/js/swiper-bundle.min.js')}}"></script>
<script src="{{asset('public/frontend/js/script.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.all.min.js"></script>
<script src="{{asset('public/frontend/js/theia-sticky-sidebar.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.0/socket.io.js"></script>
@yield('extraJsLinks')

<script>
    var url    = "{{url('/')}}";
    var _token = "{{csrf_token()}}"; 
</script>
@yield('scriptCode')
@yield('extraScriptCode')
<script src="{{asset('public/frontend/js/gallery-media.js')}}?v=1"></script>

<script type="text/javascript">
    @php
        $name = Route::currentRouteName();
        if(!($name == "business.chat" || $name == "private.chat" ||$name == "private.customerservice" ||  $name == "business.customerservice" || $name == "private.chat") ){
    @endphp
        var socket = io.connect("https://ns.tovilli.co.il",{'transports': ['websocket']});
        socket.on( 'connect', function() {
            let userId = "user_"+{{ Auth::user()->id }};
            console.log(userId,'userIduserId')
            socket.emit( 'loginChatRoom',  {"room":userId })
            console.log("connnected")
        });
        socket.on( 'sendEmitMessageResponce', function(data) {
            var messageStr =  data.message;
            if (messageStr.length > 50) {
                messageStr = messageStr.substring(0, 50) + '...';
            } else {
                messageStr = messageStr;
            }
            toastr.success(messageStr,data.sender_name);
            console.log(data.property_id);
            if(data.property_id == 1){
                $(".AdminChatAlertIcon").show();
            }else{
                $(".chatAlertIcon").addClass("show");
            }
        });

        // socket.on('sendEmitMessageResponce', function(data) {
        //     try {
        //         var messageStr = data.message;
        //         if (messageStr.length > 50) {
        //             messageStr = messageStr.substring(0, 50) + '...';
        //         } else {
        //             messageStr = messageStr;
        //         }
        //         toastr.success(messageStr, data.sender_name);
        //         $(".chatAlertIcon").addClass("show");
        //     } catch (error) {
        //         console.error("An error occurred:", error);
        //         toastr.error("An error occurred while processing the message.");
        //     }
        // });


    @php
        }
    @endphp
    



    // Set the options that I want
    toastr.options = {
        "closeButton": true,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    $(document).on('submit', 'form', function() {
        $(this).children('button').attr('disabled', true);
    })
    $(document).ready(function() {
        $(".private-customer-request-already-generated").click(function(e) {
            Swal.fire({
                title: "{{trans('messages.request_already_generated')}}",
                text: "{{trans('messages.please_complete_previous_shipment_request_first')}}",
                icon: "warning",
                confirmButtonText: "{{trans('messages.ok')}}",
            });
        });
        $(".cantDelete").click(function(e) {
            Swal.fire({
                title: "{{trans('messages.can_not_delete_this_request')}}",
                text: "{{trans('messages.you_cannot_delete_this_request_because_offers_have_been_generated_on_it')}}",
                icon: "warning",
                confirmButtonText: "{{trans('messages.ok')}}",
            });
        });

        $(".calendar-icon").click(function(){
            $(this).parent().find("input.form-control").focus();
        });

        $("body").on("input", 'input[type="number"]', function() {
            if ($(this).val().match(/[^+0-9]/g, '')) {
                $(this).val($(this).val().replace(/[^+0-9]/g, ''));
            }
        });
    });

    $(document).ready(function() {
        $('.magnific-image').magnificPopup({
            type: 'image'
        });
        $('.fancybox-buttons').magnificPopup({
            type: 'image'
        });
    
    });
</script>
@if (Session::has('success'))
<script type="text/javascript">
    toastr.success("{{ Session::get('success') }}");
</script>
@endif

@if (Session::has('error'))
<script type="text/javascript">
    toastr.error("{{ Session::get('error') }}");
</script>
@endif
<script>
        $(document).ready(function() {
            $('.dashboardSideBar .dash-nav-li a').click(function() {
                $('.dashboardSideBar .dash-nav-li a').removeClass("active");
                $(this).addClass("active");
            });

            var newWindowWidth = $(window).width();
            if (newWindowWidth >= 991) {
                $('.theia-sticky').theiaStickySidebar({
                    //'containerSelector': '',
                    'additionalMarginTop': 130,
                    // 'additionalMarginBottom': 0,
                    // 'updateSidebarHeight': true,
                    // 'minWidth': 0,
                    // 'disableOnResponsiveLayouts': true,
                    // 'sidebarBehavior': 'modern',
                    // 'defaultPosition': 'relative',
                    // 'namespace': 'TSS'
                });
            }


            $(".confirmDelete").click(function(e) {
                e.stopImmediatePropagation();
                url = $(this).attr('href');
                Swal.fire({
                    title: "{{trans('messages.admin_common_Are_you_sure')}}",
                    text: "{{trans('messages.admin_Want_to_delete_this')}}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{trans('messages.admin_Yes_delete_it')}}",
                    cancelButtonText: "{{trans('messages.admin_No_cancel')}}",
                    reverseButtons: true
                }).then(function(result) {
                    if (result.value) {
                        window.location.replace(url);
                    } else if (result.dismiss === "cancel") {
                        Swal.fire(
                            "{{trans('messages.admin_Want_to_delete_this')}} {{trans('messages.cancelled')}}",
                            "{{trans('messages.admin_Want_to_delete_this')}} {{trans('messages.admin_Your_imaginary_file_is_safe') }}",
                            "error"
                        )
                       
                    }
                });
                e.preventDefault();
            }); 

            $(".confirmClear").click(function(e) {
                e.stopImmediatePropagation();
                url = $(this).attr('href');
                Swal.fire({
                    title: "{{trans('messages.admin_common_Are_you_sure')}}",
                    text: "{{trans('messages.admin_Want_to_clear_all_notifications')}}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{trans('messages.admin_Yes_clear')}}",
                    cancelButtonText: "{{trans('messages.admin_No_cancel')}}",
                    reverseButtons: true
                }).then(function(result) {
                    if (result.value) {
                        window.location.replace(url);
                    } else if (result.dismiss === "cancel") {
                        Swal.fire(
                            "{{trans('messages.cancelled')}}",
                            "{{trans('messages.admin_Your_imaginary_file_is_safe') }}",
                            "error"
                        )
                       
                    }
                });
                e.preventDefault();
            }); 

            
        });
        // Custom Dropdown
        function showMe(evt) {
            console.log("evt.value ", evt.value);
        }

        function makeDd() {
            'use strict';
            let json = new Function(`return ${document.getElementById('json_data').innerHTML}`)();
            /*  new MsDropdown("#json_dropdown", {
                  byJson: {
                      data: json, selectedIndex:1
                  }
              })*/
            MsDropdown.make("#json_dropdown", {
                byJson: {
                    data: json,
                    selectedIndex: 0
                }
            });
        }

        // function validatePhoneNumber(input) {
        //     input.value = input.value.replace(/^0+|[^0-9]/g, '').substring(0, 10);
        // }

        function validatePhoneNumber(input) {
            var newValue = input.value.replace(/[^0-9]/g, ''); 
            if (newValue.length > 0 && newValue.charAt(0) === '0') {
                newValue = '0' + newValue.substring(1).replace(/^0+/g, '');
            }  
            // else if(newValue.length <= 12 && newValue.charAt(0) !== '0') {
            //     $(".error-" + input.name).html("{{trans('messages.phone_number_should_be_10_digits_and_should_be_start_with_0')}}");
            //     $(input).addClass("is-invalid");  
            //     return; // Exit the function early if there's an error
            // } else {
            //     // Clear any existing error message and 'is-invalid' class
            //     $(".error-" + input.name).html("");
            //     $(input).removeClass("is-invalid");
            // }
            input.value = newValue.substring(0, 10); 
        }

        function validateOnlyNumber(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        

        // function validatePassword(input) {
        //     input.value = input.value.replace(/\s+/|^(?=.[A-Z])(?=.[a-z])(?=.\d)(?=.[!@#$%^&*])[^\s]{1,8}$, '').substring(0, 8);
        // }

        function validatePassword(input) {
            input.value = input.value.replace(/\s+/g, '').substring(0, 8);
        }
</script>

<!--Video Modal -->
<div id="videoModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="themeBtn-close" data-bs-dismiss="modal" aria-label="Close"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"></path>
                </svg>
            </button>
          <h4 class="modal-title">Video</h4>
        </div>
        <div class="modal-body">
            <center>
            <video width="400" controls id="video">
                <source src="" type="video/mp4">
                <source src="mov_bbb.ogg" type="video/ogg">
                Your browser does not support HTML video.
              </video>
            </center>
        </div>
        <div class="text-center d-block" style="margin-bottom: 12px;">
            <button type="button" class="themeModalBtn" data-bs-dismiss="modal" aria-label="Close">Close</button>
        </div>
      </div>
  
    </div>
</div>
</body>

</html>