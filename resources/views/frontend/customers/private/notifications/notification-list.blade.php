@extends('frontend.layouts.customers')
@section('extraCssLinks')
<!-- Custom Responsive CSS -->
<link rel="stylesheet" href="{{asset('public/frontend/css/responsive.css')}}">
<!-- Dashboard CSS-->
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard.css')}}">
<!-- Dashboard Responsive CSS-->
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard-responsive.css')}}">
@stop
@section('backgroundImage')

<body class="dashbord_page @if(Auth::user()->customer_type == 'private') privateCustomer_page @endif">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="img/logo.png" alt="">
        </div>
    </div>
    @stop
    @section('content')

    <div class="col-md-12 col-lg-9 col-sm-12">
        <div class="dashboardRight_block_wrapper all_requests_wrapper">
            
            <form class="pagination_form">

            <div class="dashboard_table">
                <div class="dashboard_notofication_main dahboard_whiteSpace">
                    <div class="table-responsive dashboard_notofication">
                        <div class="pageTopTitle">
                            <!-- <div class="row">
                                <div class="col-4">
                                    <h2 class="RightBlockTitle m-0">{{trans('messages.Notifications')}}</h2>
                                </div>
                                <div class="col-3">
                                    <a>{{trans('messages.everything')}}</a>
                                </div>
                                <div class="col-2">
                                    <a>{{trans('messages.new')}}</a>
                                </div>
                                <div class="col-2">
                                    <a>{{trans('messages.called')}}</a>
                                </div>
                            </div> -->
                            <div class="notification_head">
                                <div class="notification_left">
                                     <h2 class="RightBlockTitle m-0">{{trans('messages.Notifications')}}</h2>
                                </div>
                                <div class="notification_right">
                                    @if($results->count() > 0)
                                    <a href="{{route('clear-all-notification')}}" class="notification_action btn btn-warning confirmClear">{{trans('messages.clear_all_notification')}}</a>
                                    @endif
                                    <a href="#" class="notification_action active">{{trans('messages.everything')}}</a>

                                    <!-- <a href="#" class="notification_action">{{trans('messages.new')}}</a>
                                    <a href="#" class="notification_action">{{trans('messages.called')}}</a> -->
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <tbody>
                                
                            @if($results->count())
                                @foreach($results as $notification)
                                <tr class="table-tr"
                                    @if($notification->url != NULL)
                                    onclick="window.location = '{{ $notification->url ?? ''}}'"
                                    @elseif($notification->shipments_status == "shipment")
                                        onclick="window.location = '{{ route('private-shipment-details', [$notification->request_number]) }}'"
                                    @elseif($notification->shipments_status == "offers" || $notification->shipments_status == "new" || $notification->shipments_status == "offer_chosen")
                                        onclick="window.location = '{{ route('private-shipment-request-details', [$notification->request_number]) }}'"
                                    @endif
                                    data-status="{{ $notification->shipments_status }}" style="cursor: pointer;"
                                >
                                    <td>
                                    <a href="#!" class="notification_icon">
                                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 1.54054C14.5321 1.54054 17.3954 4.41438 17.3954 7.95946V17.4595H4.60465V7.95946C4.60465 4.41438 7.46794 1.54054 11 1.54054ZM18.9302 17.4595V7.95946C18.9302 3.56356 15.3798 0 11 0C6.62025 0 3.06977 3.56356 3.06977 7.95946V17.4595H0.767442C0.343595 17.4595 0 17.8043 0 18.2297C0 18.6551 0.343595 19 0.767442 19H21.2326C21.6564 19 22 18.6551 22 18.2297C22 17.8043 21.6564 17.4595 21.2326 17.4595H18.9302Z" fill="#FF7C03"/>
                                                <path d="M8 18.6667C8 18.2985 8.30996 18 8.69231 18H13.3077C13.69 18 14 18.2985 14 18.6667V19.1111C14 20.7066 12.6568 22 11 22C9.34316 22 8 20.7066 8 19.1111V18.6667ZM9.40097 19.3333C9.51295 20.0872 10.1862 20.6667 11 20.6667C11.8138 20.6667 12.487 20.0872 12.599 19.3333H9.40097Z" fill="#FF7C03"/>
                                            </svg>
                                                
                                            
                                        </a>
                                    </td>
                                    <td>
                                        <div class=col-6>
                                            <div>
                                                <p><b>{{$notification->title}}</b></b>
                                                <div>
                                                    {!!$notification->description!!}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                   
                                        {{  $notification->created_at  }}
                                     </td>
                                    <td>
                                        <a href="{{route('private.notification-destroy',$notification->map_id)}}" class="choose_trus_btn confirmDelete">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_872_51382)">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.8125 3.75C2.8125 3.43934 3.06434 3.1875 3.375 3.1875H14.625C14.9357 3.1875 15.1875 3.43934 15.1875 3.75V16.5C15.1875 16.8107 14.9357 17.0625 14.625 17.0625H3.375C3.06434 17.0625 2.8125 16.8107 2.8125 16.5V3.75ZM3.9375 4.3125V15.9375H14.0625V4.3125H3.9375Z"
                                                        fill="currentcolor " />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M7.5 6.9375C7.81066 6.9375 8.0625 7.18934 8.0625 7.5V12.375C8.0625 12.6857 7.81066 12.9375 7.5 12.9375C7.18934 12.9375 6.9375 12.6857 6.9375 12.375V7.5C6.9375 7.18934 7.18934 6.9375 7.5 6.9375Z"
                                                        fill="currentcolor " />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M10.5 6.9375C10.8107 6.9375 11.0625 7.18934 11.0625 7.5V12.375C11.0625 12.6857 10.8107 12.9375 10.5 12.9375C10.1893 12.9375 9.9375 12.6857 9.9375 12.375V7.5C9.9375 7.18934 10.1893 6.9375 10.5 6.9375Z"
                                                        fill="currentcolor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M0.9375 3.75C0.9375 3.43934 1.18934 3.1875 1.5 3.1875H16.5C16.8107 3.1875 17.0625 3.43934 17.0625 3.75C17.0625 4.06066 16.8107 4.3125 16.5 4.3125H1.5C1.18934 4.3125 0.9375 4.06066 0.9375 3.75Z"
                                                        fill="currentcolor " />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M6.74012 1.22962C6.83887 1.04948 7.02795 0.9375 7.23337 0.9375H10.7914C10.9986 0.9375 11.1889 1.05134 11.2869 1.23382L12.4955 3.48382C12.5892 3.65815 12.5843 3.86885 12.4828 4.03869C12.3812 4.20852 12.1979 4.3125 12 4.3125H6C5.80126 4.3125 5.61728 4.20763 5.51601 4.03663C5.41474 3.86563 5.41122 3.65389 5.50675 3.47962L6.74012 1.22962ZM7.5665 2.0625L6.94981 3.1875H11.0593L10.455 2.0625H7.5665Z"
                                                        fill="currentcolor " />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_872_51382">
                                                        <rect width="18" height="18" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </a>
                                    </td>
                                  
                                </tr>
                                
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="8">
                                        <h3 style="text-align: center;">{{trans('messages.notification_not_found')}}</h3>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        <!-- @include('pagination.business-default', ['results' => $results]) -->
                        @include('pagination.business-default', ['results' => $results])
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
    @stop
    @section('scriptCode')
    <script>
    
    </script>
    @stop