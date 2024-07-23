@extends('frontend.layouts.customers')
@section('extraCssLinks')
    <!-- Custom Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('public/frontend/css/responsive.css') }}">
    <!-- Dashboard CSS-->
    <link rel="stylesheet" href="{{ asset('public/frontend/css/dashboard.css') }}">
    <!-- Dashboard Responsive CSS-->
    <link rel="stylesheet" href="{{ asset('public/frontend/css/dashboard-responsive.css') }}">
@stop
@section('backgroundImage')

    <body class="dashbord_page @if ($user->customer_type == 'private') privateCustomer_page @endif">
        <!-- loader  -->
        <div class="loader-wrapper" style="display: none;">
            <div class="loader">
                <img src="img/logo.png" alt="">
            </div>
        </div>
    @stop
    @section('content')
        <div class="col-md-12 col-lg-9 col-sm-12">
            <div class="dashboardRight_block_wrapper">
                <div class="pageTopTitle">
                    <h2 class="RightBlockTitle">{{ trans('messages.Dashboard') }}</h2>
                    @if ($ShipmentRequestList->count() > 0)
                        <a href="javascript:void(0)" class="transportRequestBtn private-customer-request-already-generated">
                            {{ trans('messages.Create_New_Transport_Request') }}
                        </a>
                    @else
                        <a href="{{ route('private-shipment-request') }}" class="transportRequestBtn">
                            {{ trans('messages.Create_New_Transport_Request') }}
                        </a>
                    @endif
                </div>

                <div class="requestBoxes">
                    <div class="row gy-3">
                        <div class="col-md-6 col-lg-6">
                            <a href="{{ route('private-shipment.view-all') }}">
                                <div class="mothlyRequestBox">
                                    <div class="requestIcon-box">
                                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.41797 6.44475C2.41797 5.93296 2.83286 5.51807 3.34465 5.51807H28.0563C28.5681 5.51807 28.983 5.93296 28.983 6.44475V15.7116C28.983 16.2234 28.5681 16.6383 28.0563 16.6383C27.5445 16.6383 27.1296 16.2234 27.1296 15.7116V7.37144H4.27134V24.0518H15.7005C16.2123 24.0518 16.6272 24.4667 16.6272 24.9785C16.6272 25.4903 16.2123 25.9052 15.7005 25.9052H3.34465C2.83286 25.9052 2.41797 25.4903 2.41797 24.9785V6.44475Z"
                                                fill="#FF7C03" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M24.3564 17.4854C24.7236 17.1641 25.277 17.1826 25.6219 17.5275L28.0931 19.9987C28.2745 20.18 28.3725 20.4285 28.364 20.6848C28.3555 20.9411 28.2411 21.1825 28.0481 21.3514L23.1057 25.6759C22.9368 25.8237 22.72 25.9052 22.4955 25.9052H20.0243C19.5125 25.9052 19.0977 25.4903 19.0977 24.9785V22.5073C19.0977 22.2401 19.213 21.9859 19.4141 21.8099L24.3564 17.4854ZM20.951 22.9278V24.0518H22.1473L26.0822 20.6088L24.9244 19.4511L20.951 22.9278Z"
                                                fill="#FF7C03" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.60336 5.8888C2.91044 5.47936 3.49129 5.39638 3.90072 5.70346L15.7005 14.5533L27.5003 5.70346C27.9098 5.39638 28.4906 5.47936 28.7977 5.8888C29.1048 6.29823 29.0218 6.87908 28.6123 7.18616L16.2565 16.453C15.927 16.7001 15.474 16.7001 15.1445 16.453L2.7887 7.18616C2.37926 6.87908 2.29628 6.29823 2.60336 5.8888Z"
                                                fill="#FF7C03" />
                                        </svg>

                                    </div>
                                    <div class="mothlyRequest_content">
                                        <h3>{{ trans('messages.total_shipment_requests') }}</h3>
                                        <h2>{{ $totalRequest['Shipment'] ?? '' }}</h2>
                                        <span>{{ trans('messages.Road_transport_will_lead_me') }}</span>
                                    </div>
                                </div>
                            </a>

                        </div>
                        <div class="col-md-6 col-lg-6">
                            <a href="#" id="a_shipment_requests_list">
                                <div class="mothlyRequestBox">
                                    <div class="requestIcon-box">
                                        <svg width="33" height="33" viewBox="0 0 33 33" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M16.2699 2.64806C16.5482 2.50892 16.8757 2.50892 17.154 2.64806L30.3335 9.23782C30.6684 9.40526 30.8799 9.74753 30.8799 10.1219V23.3015C30.8799 23.6759 30.6684 24.0181 30.3335 24.1856L17.154 30.7753C16.8757 30.9145 16.5482 30.9145 16.2699 30.7753L3.09036 24.1856C2.75548 24.0181 2.54395 23.6759 2.54395 23.3015V10.1219C2.54395 9.74753 2.75548 9.40526 3.09036 9.23782L16.2699 2.64806ZM4.52088 10.7328V22.6906L16.7119 28.7861L28.903 22.6906V10.7328L16.7119 4.6373L4.52088 10.7328Z"
                                                fill="#FFA625" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.64754 9.67993C2.89168 9.19165 3.48542 8.99373 3.97371 9.23787L17.1532 15.8276C17.6415 16.0718 17.8394 16.6655 17.5953 17.1538C17.3512 17.6421 16.7574 17.84 16.2691 17.5959L3.0896 11.0061C2.60131 10.762 2.4034 10.1682 2.64754 9.67993Z"
                                                fill="#FFA625" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M16.7121 15.7233C17.258 15.7233 17.7006 16.1658 17.7006 16.7117V29.8913C17.7006 30.4372 17.258 30.8797 16.7121 30.8797C16.1662 30.8797 15.7236 30.4372 15.7236 29.8913V16.7117C15.7236 16.1658 16.1662 15.7233 16.7121 15.7233Z"
                                                fill="#FFA625" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M30.776 9.67993C31.0201 10.1682 30.8222 10.762 30.3339 11.0061L17.1544 17.5959C16.6661 17.84 16.0723 17.6421 15.8282 17.1538C15.5841 16.6655 15.782 16.0718 16.2703 15.8276L29.4498 9.23787C29.9381 8.99373 30.5318 9.19165 30.776 9.67993Z"
                                                fill="#FFA625" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M24.1861 6.38501C24.4303 6.87329 24.2323 7.46703 23.7441 7.71117L10.5645 14.3009C10.0762 14.5451 9.4825 14.3472 9.23836 13.8589C8.99422 13.3706 9.19214 12.7769 9.68042 12.5327L22.8599 5.94295C23.3482 5.69881 23.942 5.89673 24.1861 6.38501Z"
                                                fill="#FFA625" />
                                        </svg>

                                    </div>
                                    <div class="mothlyRequest_content">
                                        <h3>{{ trans('messages.total_new_requests') }}</h3>
                                        <h2>{{ $totalRequest['New'] ?? '' }}</h2>
                                        <span>{{ trans('messages.Uploaded_to_the_system') }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="dashboard_table">
                    <div class="tableTop-title">
                        <h3 class="tableBlockTitle">{{ trans('messages.Notifications') }}</h3>
                        <a href="{{ route('private.notification-list') }}"
                            class="tableTopLink">{{ trans('messages.View_All') }}</a>
                    </div>
                    <div class="dashboard_notofication_main">
                        <ul class="dashboard_notofication dashBord_list_block">
                            @if (count($notifications))
                                @foreach ($notifications as $notification)
                                    <li
                                    @if($notification->url != NULL)
                                       onclick="window.location = '{{ $notification->url ?? ''}}'" 
                                    @elseif ($notification->shipments_status == 'shipment' || $notification->shipments_status == 'end' ) onclick="window.location = '{{ route('private-shipment-details', [$notification->request_number]) }}'"
                                @elseif(
                                    $notification->shipments_status == 'offers' ||
                                        $notification->shipments_status == 'new' ||
                                        $notification->shipments_status == 'offer_chosen')
                                    onclick="window.location = '{{ route('private-shipment-request-details', [$notification->request_number]) }}'" @endif
                                        data-status="{{ $notification->shipments_status }}" style="cursor: pointer;">
                                        <div class="dashboard_notificationContent">
                                            <div>
                                                <p><b>{{ $notification->title }}</b></p>
                                                <div>
                                                    {!! $notification->description !!}
                                                </div>
                                            </div>
                                            <p>{{ $notification->created_at->format(config('Reading.date_format')) }}</p>
                                        </div>
                                        <div class="mailIcon_notification">
                                            <span class="table_mailIcon"><svg width="26" height="28"
                                                    viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M23.975 10.3969C24.2027 10.2488 24.4206 10.1069 24.6271 9.97225C24.659 9.95145 24.6887 9.9286 24.7163 9.904V26.1552C24.7163 26.7011 24.2738 27.1437 23.7279 27.1437H1.98163C1.43572 27.1437 0.993164 26.7011 0.993164 26.1552L0.993164 10.2432C1.22122 10.3961 1.46959 10.5632 1.73451 10.7417M23.975 10.3969V10.0926H21.7196M23.975 10.3969C22.1111 11.6092 19.5949 13.2327 17.4149 14.6113C16.1924 15.3844 15.0717 16.0829 14.23 16.5892C13.8101 16.8417 13.453 17.0505 13.1848 17.1976C13.052 17.2705 12.9311 17.3336 12.8307 17.3805C12.7813 17.4035 12.7255 17.428 12.6692 17.4482L12.6663 17.4493C12.6325 17.4615 12.5089 17.5061 12.3605 17.5061C12.2124 17.5061 12.0899 17.4636 12.0478 17.4488C11.9851 17.4269 11.9235 17.4 11.8688 17.3742C11.7581 17.3222 11.6276 17.2523 11.4865 17.1724C11.2018 17.0112 10.829 16.7837 10.3974 16.5115C9.5319 15.9657 8.39798 15.22 7.1923 14.4162C5.98552 13.6117 4.70192 12.7457 3.53586 11.958C3.39496 11.8628 3.25579 11.7688 3.11871 11.6762C2.62523 11.3428 2.15866 11.0276 1.73451 10.7417M23.975 10.3969V26.1552C23.975 26.2917 23.8643 26.4023 23.7279 26.4023H1.98163C1.84515 26.4023 1.73451 26.2917 1.73451 26.1552V10.7417M21.7196 10.0926C22.1175 9.83518 22.5008 9.58662 22.8631 9.35129H2.54969L2.42478 9.41895C2.73559 9.62829 3.07068 9.85447 3.42332 10.0926M21.7196 10.0926C20.1138 11.1317 18.271 12.3156 16.6224 13.3581C15.4026 14.1295 14.293 14.821 13.4657 15.3186C13.051 15.5681 12.7139 15.7648 12.4717 15.8976C12.4294 15.9208 12.3912 15.9414 12.3569 15.9596M21.7196 10.0926H3.42332M3.42332 10.0926H1.73451V10.7417M3.42332 10.0926C3.59461 10.2083 3.77004 10.3269 3.94885 10.4477C4.08597 10.5403 4.22507 10.6343 4.36581 10.7293C5.53191 11.517 6.81214 12.3808 8.01475 13.1825C9.21845 13.985 10.3395 14.722 11.1883 15.2574C11.6139 15.5257 11.9632 15.7384 12.2171 15.8822C12.2695 15.9119 12.316 15.9376 12.3569 15.9596M12.3569 15.9596C12.2921 15.9939 12.2413 16.0194 12.2038 16.0369C12.1782 16.0488 12.1671 16.0531 12.1671 16.0531C12.1671 16.0531 12.1674 16.053 12.1679 16.0528C12.1699 16.0521 12.1842 16.047 12.2072 16.0413C12.2238 16.0372 12.2814 16.0234 12.3605 16.0234C12.4303 16.0234 12.4812 16.0344 12.4995 16.0387C12.522 16.0439 12.5358 16.0487 12.5379 16.0494C12.5383 16.0496 12.5385 16.0497 12.5385 16.0497C12.5385 16.0496 12.5266 16.0451 12.4992 16.0322C12.4632 16.0153 12.416 15.9914 12.3569 15.9596Z"
                                                        stroke="white" stroke-width="1.4827" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M1.98131 10.3398L12.273 17.8247C12.6196 18.0768 13.0892 18.0768 13.4358 17.8247L23.7275 10.3398"
                                                        stroke="white" stroke-width="1.4827" />
                                                    <path
                                                        d="M12.8548 17.259L0.870347 9.1042L24.8394 9.1042L12.8548 17.259Z"
                                                        fill="#1535B9" />
                                                    <path
                                                        d="M24.7163 10.3398L13.4478 1.88841C13.0964 1.62482 12.6131 1.62482 12.2617 1.88841L0.993164 10.3398"
                                                        stroke="white" stroke-width="1.4827" />
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                    <!-- <li>
                                <div class="dashboard_notificationContent">
                                    <p>Request number 4956 received 8 new offers</p>
                                    <p>02-12-22</p>
                                </div>
                                <div class="mailIcon_notification">
                                    <span class="table_mailIcon"><svg width="26" height="28" viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M23.975 10.3969C24.2027 10.2488 24.4206 10.1069 24.6271 9.97225C24.659 9.95145 24.6887 9.9286 24.7163 9.904V26.1552C24.7163 26.7011 24.2738 27.1437 23.7279 27.1437H1.98163C1.43572 27.1437 0.993164 26.7011 0.993164 26.1552L0.993164 10.2432C1.22122 10.3961 1.46959 10.5632 1.73451 10.7417M23.975 10.3969V10.0926H21.7196M23.975 10.3969C22.1111 11.6092 19.5949 13.2327 17.4149 14.6113C16.1924 15.3844 15.0717 16.0829 14.23 16.5892C13.8101 16.8417 13.453 17.0505 13.1848 17.1976C13.052 17.2705 12.9311 17.3336 12.8307 17.3805C12.7813 17.4035 12.7255 17.428 12.6692 17.4482L12.6663 17.4493C12.6325 17.4615 12.5089 17.5061 12.3605 17.5061C12.2124 17.5061 12.0899 17.4636 12.0478 17.4488C11.9851 17.4269 11.9235 17.4 11.8688 17.3742C11.7581 17.3222 11.6276 17.2523 11.4865 17.1724C11.2018 17.0112 10.829 16.7837 10.3974 16.5115C9.5319 15.9657 8.39798 15.22 7.1923 14.4162C5.98552 13.6117 4.70192 12.7457 3.53586 11.958C3.39496 11.8628 3.25579 11.7688 3.11871 11.6762C2.62523 11.3428 2.15866 11.0276 1.73451 10.7417M23.975 10.3969V26.1552C23.975 26.2917 23.8643 26.4023 23.7279 26.4023H1.98163C1.84515 26.4023 1.73451 26.2917 1.73451 26.1552V10.7417M21.7196 10.0926C22.1175 9.83518 22.5008 9.58662 22.8631 9.35129H2.54969L2.42478 9.41895C2.73559 9.62829 3.07068 9.85447 3.42332 10.0926M21.7196 10.0926C20.1138 11.1317 18.271 12.3156 16.6224 13.3581C15.4026 14.1295 14.293 14.821 13.4657 15.3186C13.051 15.5681 12.7139 15.7648 12.4717 15.8976C12.4294 15.9208 12.3912 15.9414 12.3569 15.9596M21.7196 10.0926H3.42332M3.42332 10.0926H1.73451V10.7417M3.42332 10.0926C3.59461 10.2083 3.77004 10.3269 3.94885 10.4477C4.08597 10.5403 4.22507 10.6343 4.36581 10.7293C5.53191 11.517 6.81214 12.3808 8.01475 13.1825C9.21845 13.985 10.3395 14.722 11.1883 15.2574C11.6139 15.5257 11.9632 15.7384 12.2171 15.8822C12.2695 15.9119 12.316 15.9376 12.3569 15.9596M12.3569 15.9596C12.2921 15.9939 12.2413 16.0194 12.2038 16.0369C12.1782 16.0488 12.1671 16.0531 12.1671 16.0531C12.1671 16.0531 12.1674 16.053 12.1679 16.0528C12.1699 16.0521 12.1842 16.047 12.2072 16.0413C12.2238 16.0372 12.2814 16.0234 12.3605 16.0234C12.4303 16.0234 12.4812 16.0344 12.4995 16.0387C12.522 16.0439 12.5358 16.0487 12.5379 16.0494C12.5383 16.0496 12.5385 16.0497 12.5385 16.0497C12.5385 16.0496 12.5266 16.0451 12.4992 16.0322C12.4632 16.0153 12.416 15.9914 12.3569 15.9596Z" stroke="white" stroke-width="1.4827" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M1.98131 10.3398L12.273 17.8247C12.6196 18.0768 13.0892 18.0768 13.4358 17.8247L23.7275 10.3398" stroke="white" stroke-width="1.4827" />
                                            <path d="M12.8548 17.259L0.870347 9.1042L24.8394 9.1042L12.8548 17.259Z" fill="#1535B9" />
                                            <path d="M24.7163 10.3398L13.4478 1.88841C13.0964 1.62482 12.6131 1.62482 12.2617 1.88841L0.993164 10.3398" stroke="white" stroke-width="1.4827" />
                                        </svg>
                                    </span>
                                </div>
                            </li>
                            <li>
                                <div class="dashboard_notificationContent">
                                    <p>L.L. Transports have confirmed transport number 4911 Please add delivery certificate</p>
                                    <p>02-12-22</p>
                                </div>
                                <div class="mailIcon_notification">
                                    <span class="table_mailIcon"><svg width="26" height="28" viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M23.975 10.3969C24.2027 10.2488 24.4206 10.1069 24.6271 9.97225C24.659 9.95145 24.6887 9.9286 24.7163 9.904V26.1552C24.7163 26.7011 24.2738 27.1437 23.7279 27.1437H1.98163C1.43572 27.1437 0.993164 26.7011 0.993164 26.1552L0.993164 10.2432C1.22122 10.3961 1.46959 10.5632 1.73451 10.7417M23.975 10.3969V10.0926H21.7196M23.975 10.3969C22.1111 11.6092 19.5949 13.2327 17.4149 14.6113C16.1924 15.3844 15.0717 16.0829 14.23 16.5892C13.8101 16.8417 13.453 17.0505 13.1848 17.1976C13.052 17.2705 12.9311 17.3336 12.8307 17.3805C12.7813 17.4035 12.7255 17.428 12.6692 17.4482L12.6663 17.4493C12.6325 17.4615 12.5089 17.5061 12.3605 17.5061C12.2124 17.5061 12.0899 17.4636 12.0478 17.4488C11.9851 17.4269 11.9235 17.4 11.8688 17.3742C11.7581 17.3222 11.6276 17.2523 11.4865 17.1724C11.2018 17.0112 10.829 16.7837 10.3974 16.5115C9.5319 15.9657 8.39798 15.22 7.1923 14.4162C5.98552 13.6117 4.70192 12.7457 3.53586 11.958C3.39496 11.8628 3.25579 11.7688 3.11871 11.6762C2.62523 11.3428 2.15866 11.0276 1.73451 10.7417M23.975 10.3969V26.1552C23.975 26.2917 23.8643 26.4023 23.7279 26.4023H1.98163C1.84515 26.4023 1.73451 26.2917 1.73451 26.1552V10.7417M21.7196 10.0926C22.1175 9.83518 22.5008 9.58662 22.8631 9.35129H2.54969L2.42478 9.41895C2.73559 9.62829 3.07068 9.85447 3.42332 10.0926M21.7196 10.0926C20.1138 11.1317 18.271 12.3156 16.6224 13.3581C15.4026 14.1295 14.293 14.821 13.4657 15.3186C13.051 15.5681 12.7139 15.7648 12.4717 15.8976C12.4294 15.9208 12.3912 15.9414 12.3569 15.9596M21.7196 10.0926H3.42332M3.42332 10.0926H1.73451V10.7417M3.42332 10.0926C3.59461 10.2083 3.77004 10.3269 3.94885 10.4477C4.08597 10.5403 4.22507 10.6343 4.36581 10.7293C5.53191 11.517 6.81214 12.3808 8.01475 13.1825C9.21845 13.985 10.3395 14.722 11.1883 15.2574C11.6139 15.5257 11.9632 15.7384 12.2171 15.8822C12.2695 15.9119 12.316 15.9376 12.3569 15.9596M12.3569 15.9596C12.2921 15.9939 12.2413 16.0194 12.2038 16.0369C12.1782 16.0488 12.1671 16.0531 12.1671 16.0531C12.1671 16.0531 12.1674 16.053 12.1679 16.0528C12.1699 16.0521 12.1842 16.047 12.2072 16.0413C12.2238 16.0372 12.2814 16.0234 12.3605 16.0234C12.4303 16.0234 12.4812 16.0344 12.4995 16.0387C12.522 16.0439 12.5358 16.0487 12.5379 16.0494C12.5383 16.0496 12.5385 16.0497 12.5385 16.0497C12.5385 16.0496 12.5266 16.0451 12.4992 16.0322C12.4632 16.0153 12.416 15.9914 12.3569 15.9596Z" stroke="white" stroke-width="1.4827" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M1.98131 10.3398L12.273 17.8247C12.6196 18.0768 13.0892 18.0768 13.4358 17.8247L23.7275 10.3398" stroke="white" stroke-width="1.4827" />
                                            <path d="M12.8548 17.259L0.870347 9.1042L24.8394 9.1042L12.8548 17.259Z" fill="#1535B9" />
                                            <path d="M24.7163 10.3398L13.4478 1.88841C13.0964 1.62482 12.6131 1.62482 12.2617 1.88841L0.993164 10.3398" stroke="white" stroke-width="1.4827" />
                                        </svg>
                                    </span>
                                </div>
                            </li>
                            <li>
                                <div class="dashboard_notificationContent">
                                    <p>@Transport 7432 has ended. The delivery note can be viewed</p>
                                    <p>02-12-22</p>
                                </div>
                                <div class="mailIcon_notification">
                                    <span class="table_mailIcon"><svg width="26" height="28" viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M23.975 10.3969C24.2027 10.2488 24.4206 10.1069 24.6271 9.97225C24.659 9.95145 24.6887 9.9286 24.7163 9.904V26.1552C24.7163 26.7011 24.2738 27.1437 23.7279 27.1437H1.98163C1.43572 27.1437 0.993164 26.7011 0.993164 26.1552L0.993164 10.2432C1.22122 10.3961 1.46959 10.5632 1.73451 10.7417M23.975 10.3969V10.0926H21.7196M23.975 10.3969C22.1111 11.6092 19.5949 13.2327 17.4149 14.6113C16.1924 15.3844 15.0717 16.0829 14.23 16.5892C13.8101 16.8417 13.453 17.0505 13.1848 17.1976C13.052 17.2705 12.9311 17.3336 12.8307 17.3805C12.7813 17.4035 12.7255 17.428 12.6692 17.4482L12.6663 17.4493C12.6325 17.4615 12.5089 17.5061 12.3605 17.5061C12.2124 17.5061 12.0899 17.4636 12.0478 17.4488C11.9851 17.4269 11.9235 17.4 11.8688 17.3742C11.7581 17.3222 11.6276 17.2523 11.4865 17.1724C11.2018 17.0112 10.829 16.7837 10.3974 16.5115C9.5319 15.9657 8.39798 15.22 7.1923 14.4162C5.98552 13.6117 4.70192 12.7457 3.53586 11.958C3.39496 11.8628 3.25579 11.7688 3.11871 11.6762C2.62523 11.3428 2.15866 11.0276 1.73451 10.7417M23.975 10.3969V26.1552C23.975 26.2917 23.8643 26.4023 23.7279 26.4023H1.98163C1.84515 26.4023 1.73451 26.2917 1.73451 26.1552V10.7417M21.7196 10.0926C22.1175 9.83518 22.5008 9.58662 22.8631 9.35129H2.54969L2.42478 9.41895C2.73559 9.62829 3.07068 9.85447 3.42332 10.0926M21.7196 10.0926C20.1138 11.1317 18.271 12.3156 16.6224 13.3581C15.4026 14.1295 14.293 14.821 13.4657 15.3186C13.051 15.5681 12.7139 15.7648 12.4717 15.8976C12.4294 15.9208 12.3912 15.9414 12.3569 15.9596M21.7196 10.0926H3.42332M3.42332 10.0926H1.73451V10.7417M3.42332 10.0926C3.59461 10.2083 3.77004 10.3269 3.94885 10.4477C4.08597 10.5403 4.22507 10.6343 4.36581 10.7293C5.53191 11.517 6.81214 12.3808 8.01475 13.1825C9.21845 13.985 10.3395 14.722 11.1883 15.2574C11.6139 15.5257 11.9632 15.7384 12.2171 15.8822C12.2695 15.9119 12.316 15.9376 12.3569 15.9596M12.3569 15.9596C12.2921 15.9939 12.2413 16.0194 12.2038 16.0369C12.1782 16.0488 12.1671 16.0531 12.1671 16.0531C12.1671 16.0531 12.1674 16.053 12.1679 16.0528C12.1699 16.0521 12.1842 16.047 12.2072 16.0413C12.2238 16.0372 12.2814 16.0234 12.3605 16.0234C12.4303 16.0234 12.4812 16.0344 12.4995 16.0387C12.522 16.0439 12.5358 16.0487 12.5379 16.0494C12.5383 16.0496 12.5385 16.0497 12.5385 16.0497C12.5385 16.0496 12.5266 16.0451 12.4992 16.0322C12.4632 16.0153 12.416 15.9914 12.3569 15.9596Z" stroke="white" stroke-width="1.4827" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M1.98131 10.3398L12.273 17.8247C12.6196 18.0768 13.0892 18.0768 13.4358 17.8247L23.7275 10.3398" stroke="white" stroke-width="1.4827" />
                                            <path d="M12.8548 17.259L0.870347 9.1042L24.8394 9.1042L12.8548 17.259Z" fill="#1535B9" />
                                            <path d="M24.7163 10.3398L13.4478 1.88841C13.0964 1.62482 12.6131 1.62482 12.2617 1.88841L0.993164 10.3398" stroke="white" stroke-width="1.4827" />
                                        </svg>
                                    </span>
                                </div>
                            </li> -->
                                @endforeach
                            @else
                                <li style="text-align: center;">
                                    <h3>{{ trans('messages.notification_not_found') }}</h3>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="dashboard_table">
                    <div class="tableTop-title" id="shipment_requests_list">
                        <h3 class="tableBlockTitle">{{ trans('messages.Recent_Requests') }}</h3>
                        <!-- <a href="#!" class="tableTopLink">{{ trans('messages.View_All') }}</a> -->
                    </div>
                    <div class="dashboard_notofication_main dahboard_whiteSpace">
                        <div class="table-responsive dashboard_notofication">
                            <table class="table">
                                <thead>

                                    <tr>
                                        <th scope="col">{{ trans('messages.request_id') }}</th>
                                        <th scope="col">{{ trans('messages.type') }}</th>
                                        <th scope="col">{{ trans('messages.date') }}</th>
                                        {{-- <th scope="col">{{trans('messages.admin_creation_date')}}</th> --}}
                                        <th scope="col">{{ trans('messages.suggestions') }}</th>
                                        <th scope="col">{{ trans('messages.origin_address') }}</th>
                                        <th scope="col">{{ trans('messages.destination_address') }}</th>
                                        <th scope="col">{{ trans('messages.admin_common_Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($ShipmentRequestList->count())
                                        @foreach ($ShipmentRequestList as $key => $shipment)
                                            <tr class="table-tr"
                                                onclick="window.location = '{{ route('private-shipment-request-details', [$shipment->request_number]) }}'"
                                                style="cursor: pointer;">
                                                <td>
                                                    {{ $shipment->request_number }}
                                                </td>
                                                <td>
                                                    {{ $shipment->TruckTypeDescriptionsPrivate->name ?? '' }}
                                                </td>
                                                <td>
                                                    <a class="date_label" href="javascript:void(0)">
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $shipment->request_date)->format(config('Reading.date_format')) }}<br />
                                                        {{ $shipment->RequestTimeDescription->code ?? '' }}
                                                    </a>
                                                </td>
                                                {{-- <td class="">
                                            <a class="date_label" href="javascript:void(0)">
                                                {{ $shipment->created_at->format(config("Reading.date_format"))  }}
                                            </a>
                                        </td> --}}
                                                <td style="text-align: center;">
                                                    {{ $shipment->ShipmentOffers->count() }}

                                                </td>
                                                <td>
                                                    <div class="address-box">
                                                        {{ $shipment->pickup_city }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="address-box2">
                                                        @if ($shipment->ShipmentStop->count() > 1)
                                                            {{ trans('messages.multiple_destinations') }}
                                                        @else
                                                            @foreach ($shipment->ShipmentStop as $ShipmentStop)
                                                                {{ $ShipmentStop->dropoff_city }}
                                                            @break
                                                        @endforeach
                                                    @endif
                                                </div>
                                                {{-- <div class="address-box2">
                                                    {{ $shipment->ShipmentPrivateCustomer_ExtraInformation ? $shipment->ShipmentPrivateCustomer_ExtraInformation->drop_location : "" }}
                                            </div> --}}
                                            </td>
                                            @php
                                                $className = '';
                                                $shipmentStatus = '';
                                                if ($shipment->status == 'shipment') {
                                                    $className = 'orange_btn';
                                                    $shipmentStatus = 'in_process';
                                                } elseif ($shipment->status == 'offer_chosen') {
                                                    $shipmentStatus = 'offer_chosen';
                                                    $className = 'green_btn';
                                                } elseif ($shipment->status == 'new') {
                                                    $className = 'orange_btn';
                                                    $shipmentStatus = 'new';
                                                } elseif ($shipment->status == 'offers'  && $shipment->ShipmentOffers->isNotEmpty()){
                                                    $className = 'blue_btn';
                                                    $shipmentStatus = 'in_offer';
                                                }elseif($shipment->ShipmentOffers->isEmpty()){
                                                    $className = 'orange_btn';
                                                    $shipmentStatus = 'new';
                                                }
                                            @endphp
                                            <td>
                                                <a href="javascript:void(0)"
                                                    class="{{ $className }}  dashboard_tableBtn">
                                                    {{ trans('messages.' . $shipmentStatus) }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">
                                            <h3 style="text-align: center;">
                                                {{ trans('messages.shipment_not_found') }}</h3>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dashboard_table">
                <div class="tableTop-title">
                    <h3 class="tableBlockTitle">{{ trans('messages.common_recent_shipments') }}</h3>
                    <a href="{{ route('private-shipment.view-all') }}"
                        class="tableTopLink">{{ trans('messages.View_All') }}</a>
                </div>
                <div class="dashboard_notofication_main dahboard_whiteSpace">
                    <div class="table-responsive dashboard_notofication">
                        <table class="table">
                            <thead>

                                <tr>
                                    <th scope="col">{{ trans('messages.request_id') }}</th>
                                    <th scope="col">{{ trans('messages.type') }}</th>
                                    <th scope="col">{{ trans('messages.date') }}</th>
                                    <th scope="col">{{ trans('messages.admin_creation_date') }}</th>
                                    <th scope="col">{{ trans('messages.origin_address') }}</th>
                                    <th scope="col">{{ trans('messages.destination_address') }}</th>
                                    {{-- <th scope="col">{{trans('messages.suggestions')}}</th> --}}
                                    <th scope="col">{{ trans('messages.admin_common_Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($ShipmentList->count())
                                    @foreach ($ShipmentList as $key => $shipment)
                                        <tr class="table-tr"
                                            onclick="window.location = '{{ route('private-shipment-details', [$shipment->request_number]) }}'"
                                            style="cursor: pointer;">
                                            <td>
                                                {{ $shipment->request_number }}
                                            </td>
                                            <td>
                                                {{ $shipment->TruckTypeDescriptionsPrivate->name ?? '' }}
                                            </td>
                                            <td>
                                                <a class="date_label" href="javascript:void(0)">
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $shipment->request_date)->format(config('Reading.date_format')) }}<br />
                                                    {{ $shipment->RequestTimeDescription->code ?? '' }}
                                                </a>
                                            </td>
                                            <td>
                                                <a class="date_label" href="javascript:void(0)">
                                                    {{ $shipment->created_at->format(config('Reading.date_format')) }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="address-box">
                                                    {{ $shipment->pickup_city }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="address-box2">
                                                    @if ($shipment->ShipmentStop->count() > 1)
                                                        {{ trans('messages.multiple_destinations') }}
                                                    @else
                                                        @foreach ($shipment->ShipmentStop as $ShipmentStop)
                                                            {{ $ShipmentStop->dropoff_city }}
                                                        @break
                                                    @endforeach
                                                @endif
                                            </div>

                                            {{-- <div class="address-box2">
                                                {{ $shipment->ShipmentPrivateCustomer_ExtraInformation ? $shipment->ShipmentPrivateCustomer_ExtraInformation->drop_location : "" }}
                                            </div> --}}
                                        </td>
                                        {{-- <td>
                                            {{ $shipment->ShipmentOffers->count() }}
                                        </td> --}}

                                            @php
                                            // if($shipment->shipmentDriverScheduleDetails){
                                            //     dd($shipment->status == 'shipment' && $shipment->shipmentDriverScheduleDetails->shipment_status == 'start');
                                            // }

                                            $className = '';
                                            $shipmentStatus = '';
                                            if($shipment->status == 'shipment' && $shipment->shipmentDriverScheduleDetails && $shipment->shipmentDriverScheduleDetails->shipment_status == 'start'){
                                                $className = 'lightgray_btn';
                                                $shipmentStatus = 'active';
                                            }elseif ($shipment->status == 'shipment') {
                                                $className = 'orange_btn';
                                                $shipmentStatus = 'shipment';
                                             } elseif ($shipment->status == 'end') {
                                                $className = 'blue_btn';
                                                $shipmentStatus = 'end';
                                            }
                                            
                                        @endphp
                                        <td>
                                            <a href="javascript:void(0)"
                                                class="{{ $className }}  dashboard_tableBtn">
                                                {{ trans('messages.' . $shipmentStatus) }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8">
                                        <h3 style="text-align: center;">{{ trans('messages.shipment_not_found') }}
                                        </h3>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('scriptCode')
<script>
    //   $(function() {
    //         $('table.table').on("click", "tr.table-tr", function() {
    //             window.location = $(this).data("url");
    //             //alert($(this).data("url"));
    //         });
    //     }); 
</script>
<script>
    document.getElementById('a_shipment_requests_list').addEventListener('click', function(event) {
        event.preventDefault();
        const contentDiv = document.getElementById('shipment_requests_list');
        const offset = 110; // Set your desired offset value here
        window.scrollTo({
            top: contentDiv.offsetTop - offset,
            behavior: 'smooth'
        });
    });
</script>
@stop
