@extends('frontend.layouts.customers')
@section('extraCssLinks')
<!-- Custom Responsive CSS -->
<link rel="stylesheet" href="{{asset('public/frontend/css/responsive.css')}}">
<!-- Dashboard CSS-->
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard.css')}}">
<link rel="stylesheet" href="{{asset('public/frontend/css/style-messages.css')}}">
<!-- Dashboard Responsive CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" >
<link rel="stylesheet" href="{{asset('public/frontend/css/dashboard-responsive.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" rel="stylesheet"/>
@stop
@section('backgroundImage')

<body class="dashbord_page @if($user->customer_type == 'private') privateCustomer_page @endif">
    <!-- loader  -->
    <div class="loader-wrapper" style="display: none;">
        <div class="loader">
            <img src="img/logo.png" alt="">
        </div>
    </div>
@stop
@section('content')

<style>
    .emojionearea .emojionearea-editor{
        min-height : 4em;
        max-height : 5em;
    }
</style>

<div class="col-lg-9 col-xl-9 col-xxl-9 col-sm-12">
    <div class="dashboardRight_block_wrapper admin_right_page company_details_block other-chat @if($messagesDetails->count()==0) chat-is-blank @endif">
        <div class="pageTopTitle chats-h1">
            <h2 class="RightBlockTitle">{{trans("messages.Customer Service")}}</h2>
            <a href="#!" class="customer_supTitle">{{trans("messages.suggestions_will_benefit_the_system")}}</a>
        </div>

        <div class="row gy-lg-5 ">
            <div class="col-xxl-9 col-lg-12">
                <div class="dashChatInner chatInner_block" id="dashChatInner">
                
                    <div class="dashChatChatting customer_suppContent">

                        <div class="chat-header" id="chatHeader">
                            <div class="row g-0 align-items-center">
                                <!-- Chat photo -->
                                <div class="col-12 col-xl-12 pd-5">
                                    <div class="user_message_name">

                                        <div class="message_user_img">
                                            <div class="message_top_img">
                                                <a href="{{ $admin_data->image != null ? $admin_data->image : config('constants.NO_IMAGE_PATH')  }}" data-fancybox="userimage">
                                                    <img src="{{ $admin_data->image != null ? $admin_data->image : config('constants.NO_IMAGE_PATH')  }}" alt="" >
                                                </a>
                                            </div>
                                            <div>
                                                <h4>{{ $admin_data->name ?? ''}}</h4>
                                            </div>
                                        </div>
                                        <div class="call_info_box">

                                            <a href="tel:{{$admin_data->phone_number}}" class="chet_icon_color">
                                                <svg width="13" height="13" viewBox="0 0 13 13"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.0301 8.91685C11.224 8.91635 10.423 8.78917 9.65642 8.53996C9.53943 8.50029 9.41362 8.49442 9.29344 8.52302C9.17326 8.55161 9.06358 8.61351 8.97698 8.7016L7.92151 10.0259C5.91706 9.04294 4.29012 7.42997 3.2899 5.43406L4.60124 4.31809C4.6876 4.22909 4.74823 4.11835 4.77669 3.99765C4.80516 3.87695 4.80039 3.7508 4.76289 3.63259C4.51044 2.86677 4.38317 2.06523 4.386 1.25887C4.38442 1.08283 4.31379 0.914445 4.1893 0.78996C4.06482 0.665474 3.89643 0.594841 3.72039 0.593262H1.39421C1.03115 0.593262 0.59375 0.75491 0.59375 1.25887C0.618596 4.28431 1.83148 7.17878 3.97085 9.31816C6.11023 11.4575 9.00471 12.6704 12.0301 12.6953C12.1273 12.6906 12.2224 12.6658 12.3096 12.6226C12.3967 12.5793 12.4739 12.5185 12.5364 12.444C12.599 12.3695 12.6454 12.2828 12.6728 12.1895C12.7002 12.0962 12.708 11.9982 12.6958 11.9017V9.58332C12.6944 9.40713 12.6239 9.23852 12.4994 9.11385C12.3748 8.98918 12.2063 8.91843 12.0301 8.91685Z"
                                                        fill="#1535B9" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dashChatChattingInner appendChat" id="chatInner">
                            @if($selectedUser != '')
                                <span class="span-relative">
                                    <input type="hidden" value="{{ $selectedUser['receiver_data']->id  ?? '' }}" class="services_users_id">
                                    <input type="hidden" value="{{ @$selectedUser['auth_data']->image ? $selectedUser['auth_data']->image : config('constants.NO_IMAGE_PATH') }}" class="sender_image">
                                    <input type="hidden" value="{{ $auth->name }}" class="sender_name">
                                    <input type="hidden" value="{{ $messagesDetails[0]->channel_id ?? '' }}" class="property_id">
                                    <input type="hidden" value="{{ $auth->is_online }}" class="is_online">
                                    <input type="hidden" value="{{ $auth->id }}" class="sender_id">
                                </span>
                          
                                @php
                                    $lastDate = null;
                                @endphp

                                @foreach($selectedUser['data'] as $selectedUserChat)
                                    @php
                                        $messageDate = $selectedUserChat->date; // Assuming $selectedUserChat->date contains the message date
                                        $formattedMessageDate = \Carbon\Carbon::parse($messageDate)->format('Y-m-d');
                                    @endphp

                                    @if($lastDate !== $formattedMessageDate)
                                        <div class="datewise-row">
                                            @if($formattedMessageDate === now()->format('Y-m-d'))
                                                <span>{{trans("messages.today")}}</span>
                                            @elseif($formattedMessageDate === now()->subDay()->format('Y-m-d'))
                                                <span>{{trans("messages.yesterday")}}</span>
                                            @else
                                                <span>{{ \Carbon\Carbon::parse($messageDate)->format('M j, Y') }}</span>
                                            @endif
                                        </div>
                                        @php
                                            $lastDate = $formattedMessageDate;
                                        @endphp
                                    @endif

                                    @if($selectedUserChat->sender_id != $auth->id)
                                        <div class="message message-right">
                                            <div class="message-body">
                                                <div class="message-row">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <div class="message-content-block">
                                                            @if(!empty($selectedUserChat->message) || !empty($selectedUserChat->receiver_image_attachments))
                                                            <figure class="message_leftImg">
                                                                <img src="{{ @$selectedUser['receiver_data']->image ? $selectedUser['receiver_data']->image : config('constants.NO_IMAGE_PATH') }}" alt="">
                                                            </figure>
                                                            @endif
                                                            @if(!empty($selectedUserChat->message))
                                                            <div class="message-content">
                                                                <div>{{$selectedUserChat->message ?? ''}}</div>
                                                                <div class="text-left message_timeDate">
                                                                    <span>{{$selectedUserChat->date ?? ''}}</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <div class="mess_Send_box">
                                                                @if(!empty($selectedUserChat->receiver_image_attachments))
                                                                    @foreach($selectedUserChat->receiver_image_attachments as $sender_image_attachments_image )
                                                                    <div class="mess_Send_img">
                                                                        @if($sender_image_attachments_image['types_image'] == 'image')
                                                                            <a href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}{{ $sender_image_attachments_image['message'] }}" data-fancybox="messChet">
                                                                            <img src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}{{ $sender_image_attachments_image['message'] }}">
                                                                            </a>
                                                                      
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'video')

                                                                        <video id="video_undefined" width="100%" height="100%" preload="metadata" controls="controls" class="ratio ratio-21x9">
                                                                            <source src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$sender_image_attachments_image['message']}}" type="video/mp4">
                                                                                Your browser does not support the video tag.
                                                                        </video>
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'pdf')

                                                                        <a href="{{asset('public/frontend/img/pdf.png')}}" data-fancybox="messChet">    
                                                                            <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/pdf.png')}}">
                                                                        </a>
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'xlsx')
                                                                        <a href="{{asset('public/frontend/img/excel.png')}}" data-fancybox="messChet">    
                                                                            <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/excel.png')}}">
                                                                        </a>
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'doc' || $sender_image_attachments_image['types_image'] == 'docx')
                                                                        <a href="{{asset('public/frontend/img/doc.png')}}" data-fancybox="messChet">    
                                                                            <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/doc.png')}}">
                                                                        </a>
                                                                        @endif
                                                                        <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$sender_image_attachments_image['message']}}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/></svg>
                                                                        </a>
                                                                    </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="message message-left">
                                            <div class="message-body">
                                                <div class="message-row">
                                                    <div class="d-flex align-items-center">
                                                        <div class="message-content-block">
                                                            @if(!empty($selectedUserChat->message) || !empty($selectedUserChat->sender_image_attachments))
                                                            <figure class="message_leftImg">
                                                                <img src="{{ @$selectedUser['auth_data']->image ? $selectedUser['auth_data']->image : config('constants.NO_IMAGE_PATH') }}" alt="">
                                                            </figure>
                                                            @endif
                                                            @if(!empty($selectedUserChat->message))
                                                            <div class="message-content">
                                                                <div>{{$selectedUserChat->message ?? ''}}</div>
                                                                <div class="message_timeDate">
                                                                    <span>{{$selectedUserChat->date ?? ''}}</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <div class="mess_Send_box">
                                                                @if(!empty($selectedUserChat->sender_image_attachments))
                                                                    @foreach($selectedUserChat->sender_image_attachments as $sender_image_attachments_image )
                                                                    <div class="mess_Send_img">
                                                                        @if($sender_image_attachments_image['types_image'] == 'image')
                                                                            <a href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}{{ $sender_image_attachments_image['message'] }}" data-fancybox="messChet">
                                                                            <img src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}{{ $sender_image_attachments_image['message'] }}">
                                                                            </a>
                                                                      
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'video')

                                                                        <video id="video_undefined" width="100%" height="100%" preload="metadata" controls="controls" class="ratio ratio-21x9">
                                                                            <source src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$sender_image_attachments_image['message']}}" type="video/mp4">
                                                                                Your browser does not support the video tag.
                                                                        </video>
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'pdf')

                                                                        <a href="{{asset('public/frontend/img/pdf.png')}}" data-fancybox="messChet">    
                                                                            <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/pdf.png')}}">
                                                                        </a>
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'xlsx')
                                                                        <a href="{{asset('public/frontend/img/excel.png')}}" data-fancybox="messChet">    
                                                                            <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/excel.png')}}">
                                                                        </a>
                                                                        @elseif($sender_image_attachments_image['types_image'] == 'doc' || $sender_image_attachments_image['types_image'] == 'docx')
                                                                        <a href="{{asset('public/frontend/img/doc.png')}}" data-fancybox="messChet">    
                                                                            <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/doc.png')}}">
                                                                        </a>
                                                                        @endif
                                                                        <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$sender_image_attachments_image['message']}}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/></svg>
                                                                        </a>
                                                                    </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @else
                                <span class="span-relative">
                                    <input type="hidden" value="{{ $auth->image ? $auth->image : config('constants.NO_IMAGE_PATH') }}" class="sender_image">
                                    <input type="hidden" value="{{ $auth->name }}" class="sender_name">
                                    <input type="hidden" value="{{ $auth->is_online }}" class="is_online">
                                    <input type="hidden" value="{{ $auth->id }}" class="sender_id">
                                </span>
                                @endif
                            </div>

                        <div class="chat-footer" id="chatFooter">
                            <div class="container-xxl">
                            <div class="all_image_show"></div>

                                <form id="chat-id-1-form">
                                    <div class="form-row align-items-center">
                                        <div class="col">
                                            <div class="position-relative">
                                            <input type="hidden" id="message_by_user_id" value="{{ $auth->id ?? '' }}">
                                                <textarea id="chatInput" class="form-control border-0"
                                                    placeholder="{{trans("messages.write_somthing")}}" rows="1"
                                                    data-autosize="true"></textarea>
                                        
                                                    <div class="msg_SendInputBox">
                                                        <div class="btn-upload-img">


                                                            <label for="chat_image_select">
                                                                <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M16.548 1.99111C14.0642 -0.400684 10.0195 -0.400684 7.53831 1.99111L0.65647 8.61299C0.611646 8.65615 0.587915 8.71455 0.587915 8.77549C0.587915 8.83643 0.611646 8.89482 0.65647 8.93799L1.62942 9.8749C1.67389 9.91754 1.73409 9.94148 1.79685 9.94148C1.85961 9.94148 1.91981 9.91754 1.96428 9.8749L8.84612 3.25303C9.70041 2.43037 10.8368 1.97842 12.0445 1.97842C13.2521 1.97842 14.3885 2.43037 15.2402 3.25303C16.0945 4.07568 16.5638 5.17002 16.5638 6.33037C16.5638 7.49326 16.0945 8.58506 15.2402 9.40772L8.22649 16.1591L7.09006 17.2534C6.02747 18.2767 4.30041 18.2767 3.23782 17.2534C2.72366 16.7583 2.44153 16.1007 2.44153 15.3999C2.44153 14.6991 2.72366 14.0415 3.23782 13.5464L10.1961 6.84834C10.3728 6.68076 10.6048 6.58682 10.8527 6.58682H10.8553C11.1031 6.58682 11.3325 6.68076 11.5066 6.84834C11.6832 7.01846 11.7781 7.24189 11.7781 7.48057C11.7781 7.7167 11.6806 7.94014 11.5066 8.10772L5.81916 13.5794C5.77434 13.6226 5.75061 13.681 5.75061 13.7419C5.75061 13.8028 5.77434 13.8612 5.81916 13.9044L6.79211 14.8413C6.83659 14.8839 6.89679 14.9079 6.95955 14.9079C7.0223 14.9079 7.0825 14.8839 7.12698 14.8413L12.8117 9.36709C13.3364 8.86182 13.6239 8.1915 13.6239 7.47803C13.6239 6.76455 13.3338 6.0917 12.8117 5.58896C11.7281 4.54541 9.96672 4.54795 8.88303 5.58896L8.20803 6.2415L1.92737 12.287C1.50109 12.6951 1.16319 13.1806 0.93326 13.7154C0.70333 14.2502 0.585946 14.8236 0.587915 15.4024C0.587915 16.578 1.06516 17.6825 1.92737 18.5128C2.82122 19.371 3.99192 19.8001 5.16262 19.8001C6.33332 19.8001 7.50403 19.371 8.39524 18.5128L16.548 10.6671C17.7477 9.50928 18.4121 7.96807 18.4121 6.33037C18.4148 4.69014 17.7503 3.14893 16.548 1.99111Z" fill="#FF7C03"></path>
                                                                </svg>
                                                            </label>
                                                            <input id="chat_image_select" name="image[]" type="file" hidden="" multiple=''>
                                                
                                                        </div>
                               
                                                  
                                                        <button class="send-messsageBtn" onclick="send_message_button()" type="button">
                                                            <svg width="20" height="20" viewBox="0 0 17 17"
                                                                fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M8.59171 8.1139L14.3311 7.21936C14.3971 7.20906 14.459 7.18269 14.5106 7.1429C14.5623 7.10311 14.6018 7.05131 14.6252 6.9927L16.6041 2.0332C16.7931 1.57702 16.2833 1.14223 15.8155 1.36176L2.09947 7.77675C2.00467 7.8212 1.92496 7.88944 1.86925 7.97384C1.81354 8.05824 1.78404 8.15548 1.78404 8.25467C1.78404 8.35386 1.81354 8.4511 1.86925 8.5355C1.92496 8.6199 2.00467 8.68814 2.09947 8.73259L15.8155 15.1476C16.2833 15.3664 16.7931 14.9316 16.6041 14.4761L14.6245 9.51664C14.601 9.45804 14.5615 9.40623 14.5099 9.36644C14.4582 9.32665 14.3963 9.30028 14.3303 9.28998L8.59095 8.39545C8.55514 8.3901 8.52255 8.37299 8.49902 8.34719C8.47549 8.32139 8.46257 8.28859 8.46257 8.25467C8.46257 8.22076 8.47549 8.18796 8.49902 8.16215C8.52255 8.13635 8.55514 8.11924 8.59095 8.1139L8.59171 8.1139Z"
                                                                    fill="#FF7C03" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <small class="text-danger message_not_null"></small>
                                                    
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- sidebar -->

            <div class="col-xxl-3 col-lg-12">
                <div class="mess_filter_block">
                    <div>
                        <h3 class="mess_filter_title">{{trans("messages.admin_common_Details")}}</h3>
                    </div>
                    <div class="messFilter_box">
                        <h4 class="message_fil_head">
                        {{trans("messages.multimedia_images")}}
                        </h4>
                        @if($selectedUser != '')
                        <div class="row g-3 sideimg">
                            @foreach($selectedUser['mediaData'] as $mediaData)
                                @if($mediaData->types_image == 'image')
                                    <div class="col-sm-4 col-6">
                                        <figure class="filt_messageBox" >
                                            <a href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}{{ $mediaData->message }}" data-fancybox="gallery">
                                                <img src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}{{ $mediaData->message }}" alt="Image Gallery">
                                            </a>
                                        </figure>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="messFilter_box">
                        <h4 class="message_fil_head">
                        {{trans("messages.attached_files")}}
                        </h4>
                        @if($selectedUser != '')
                        <span class="message_fil_desc sideimg1">{{trans("messages.source_file")}}</span>
                            @foreach($selectedUser['mediaData'] as $mediaData)
                                @if($mediaData->types_image == 'pdf')
                                    <div class="mess_fil_xmlBox">
                                        <span><svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_357_7632)">
                                                    <path
                                                        d="M27.9918 0H0.00820313C0.00367266 0 0 0.00367266 0 0.00820313V27.9918C0 27.9963 0.00367266 28 0.00820313 28H27.9918C27.9963 28 28 27.9963 28 27.9918V0.00820313C28 0.00367266 27.9963 0 27.9918 0Z"
                                                        fill="#C80A0A" />
                                                    <path
                                                        d="M22.5862 16.5156C22.094 15.9688 21.0003 15.6953 19.5237 15.6953C18.6487 15.6953 17.719 15.8047 16.6253 15.9688C15.4672 14.884 14.5051 13.6075 13.7815 12.1953C14.3284 10.5547 14.7112 8.96875 14.7112 7.76562C14.7112 6.83594 14.3831 5.35938 13.0706 5.35938C12.6878 5.35938 12.3597 5.57812 12.1409 5.90625C11.594 6.89062 11.8128 9.07812 12.8518 11.375C12.0928 13.5698 11.1791 15.708 10.1175 17.7734C7.21903 18.9766 5.30497 20.2891 5.14091 21.3281C5.03153 21.8203 5.35966 22.6406 6.5081 22.6406C8.20341 22.6406 10.0628 20.1797 11.4847 17.6641C13.126 17.0997 14.808 16.6609 16.5159 16.3516C18.594 18.1562 20.3987 18.4297 21.2737 18.4297C23.0237 18.4297 23.1878 17.1719 22.5862 16.5156ZM12.4143 6.07031C12.8518 5.41406 13.8362 5.63281 13.8362 6.94531C13.8362 7.82031 13.5628 9.24219 13.0159 10.8828C12.0315 8.58594 12.0315 6.78125 12.4143 6.07031ZM5.46903 21.3828C5.6331 20.5078 7.27372 19.3047 9.84403 18.2656C8.42216 20.6719 7.00028 22.2031 6.12528 22.2031C5.57841 22.2031 5.41435 21.7109 5.46903 21.3828ZM16.2425 16.0234C14.7015 16.3106 13.1843 16.7128 11.7034 17.2266C12.5128 15.7656 13.1724 14.2265 13.6722 12.6328C14.3909 13.8613 15.2538 14.9995 16.2425 16.0234ZM16.9534 16.2422C18.7034 15.9688 20.18 16.0234 20.8362 16.1328C22.4222 16.4609 21.8753 18.375 20.1253 17.9375C18.8675 17.6641 17.8284 16.9531 16.9534 16.2422Z"
                                                        fill="#FCF6F5" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_357_7632">
                                                        <rect width="28" height="28" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg></span>
                                    {{ $mediaData->image_name ?? ''}}
                                    <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$mediaData->message}}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/></svg>
                                    </div>
                                @endif

                                @if($mediaData->types_image == 'doc' || $mediaData->types_image == 'docx')
                                    <div class="mess_fil_xmlBox">
                                        <span><svg width="34" height="32" viewBox="0 0 34 32" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_357_7635)">
                                                    <path
                                                        d="M32.5499 0H9.3556C8.55515 0 7.90625 0.65653 7.90625 1.4664V8L21.3481 12L33.9993 8V1.4664C33.9993 0.65653 33.3504 0 32.5499 0Z"
                                                        fill="#41A5EE" />
                                                    <path d="M33.9993 8H7.90625V16L21.3481 18.4L33.9993 16V8Z"
                                                        fill="#2B7CD3" />
                                                    <path d="M7.90625 16V24L20.5574 25.6L33.9993 24V16H7.90625Z"
                                                        fill="#185ABD" />
                                                    <path
                                                        d="M9.3556 32H32.5499C33.3504 32 33.9993 31.3435 33.9993 30.5336V24H7.90625V30.5336C7.90625 31.3435 8.55515 32 9.3556 32Z"
                                                        fill="#103F91" />
                                                    <path opacity="0.1"
                                                        d="M17.5267 6.40002H7.90625V26.4H17.5267C18.326 26.3974 18.9734 25.7424 18.976 24.9336V7.86643C18.9734 7.05765 18.326 6.40266 17.5267 6.40002Z"
                                                        fill="black" />
                                                    <path opacity="0.2"
                                                        d="M16.736 7.19995H7.90625V27.2H16.736C17.5353 27.1973 18.1827 26.5423 18.1853 25.7336V8.66636C18.1827 7.85758 17.5353 7.20258 16.736 7.19995Z"
                                                        fill="black" />
                                                    <path opacity="0.2"
                                                        d="M16.736 7.19995H7.90625V25.6H16.736C17.5353 25.5973 18.1827 24.9423 18.1853 24.1336V8.66636C18.1827 7.85758 17.5353 7.20258 16.736 7.19995Z"
                                                        fill="black" />
                                                    <path opacity="0.2"
                                                        d="M15.9453 7.19995H7.90625V25.6H15.9453C16.7446 25.5973 17.392 24.9423 17.3946 24.1336V8.66636C17.392 7.85758 16.7446 7.20258 15.9453 7.19995Z"
                                                        fill="black" />
                                                    <path
                                                        d="M1.44935 7.19995H15.946C16.7465 7.19995 17.3954 7.85648 17.3954 8.66636V23.3336C17.3954 24.1434 16.7465 24.8 15.946 24.8H1.44935C0.648896 24.8 0 24.1434 0 23.3336V8.66636C0 7.85648 0.648896 7.19995 1.44935 7.19995Z"
                                                        fill="url(#paint0_linear_357_7635)" />
                                                    <path
                                                        d="M5.94673 18.4465C5.97519 18.6729 5.99417 18.8697 6.00286 19.0385H6.03606C6.04871 18.8785 6.07506 18.6857 6.11513 18.4601C6.1552 18.2345 6.19131 18.0438 6.22346 17.8881L7.74792 11.2337H9.71913L11.3005 17.7881C11.3924 18.1956 11.4582 18.6087 11.4974 19.0249H11.5235C11.553 18.6217 11.6079 18.2209 11.688 17.8249L12.9491 11.2305H14.7432L12.5277 20.7665H10.4316L8.92923 14.4513C8.88575 14.2697 8.83594 14.0321 8.78138 13.7401C8.72682 13.448 8.69282 13.2345 8.67937 13.1001H8.65328C8.63589 13.2552 8.60188 13.4857 8.55127 13.7913C8.50067 14.0969 8.46007 14.323 8.42949 14.4697L7.0173 20.7649H4.88558L2.6582 11.2336H4.4847L5.85815 17.9016C5.889 18.0385 5.91826 18.2209 5.94673 18.4465Z"
                                                        fill="#FCF6F5" />
                                                </g>
                                                <defs>
                                                    <linearGradient id="paint0_linear_357_7635" x1="3.02193"
                                                        y1="6.05413" x2="14.5737" y2="25.8288"
                                                        gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#2368C4" />
                                                        <stop offset="0.5" stop-color="#1A5DBE" />
                                                        <stop offset="1" stop-color="#1146AC" />
                                                    </linearGradient>
                                                    <clipPath id="clip0_357_7635">
                                                        <rect width="34" height="32" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg></span>
                                            {{ $mediaData->image_name ?? ''}}
                                            <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$mediaData->message}}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/></svg>

                                    </div>
                                @endif

                                @if($mediaData->types_image == 'xlsx' )
                                    <div class="mess_fil_xmlBox">
                                        <span><svg width="36" height="38" viewBox="0 0 36 38" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M32.3786 6.23105H20.9824V3.39648L2.25 6.44836V33.1992L20.9824 36.6049V32.4035H32.3786C32.7238 32.422 33.0618 32.2954 33.3188 32.0516C33.5758 31.8077 33.7308 31.4663 33.75 31.102V7.53136C33.7305 7.16735 33.5754 6.82626 33.3184 6.58262C33.0614 6.33898 32.7236 6.21258 32.3786 6.23105ZM32.5586 31.3182H20.9441L20.925 29.075H23.7229V26.4625H20.9036L20.8901 24.9187H23.7229V22.3062H20.8687L20.8552 20.7625H23.7229V18.15H20.8463V16.6062H23.7229V13.9937H20.8463V12.45H23.7229V9.83748H20.8463V7.46248H32.5586V31.3182Z"
                                                    fill="#20744A" />
                                                <path d="M30.1602 9.83398H25.2969V12.4465H30.1602V9.83398Z"
                                                    fill="#20744A" />
                                                <path d="M30.1602 13.9912H25.2969V16.6037H30.1602V13.9912Z"
                                                    fill="#20744A" />
                                                <path d="M30.1602 18.1489H25.2969V20.7614H30.1602V18.1489Z"
                                                    fill="#20744A" />
                                                <path d="M30.1602 22.3062H25.2969V24.9187H30.1602V22.3062Z"
                                                    fill="#20744A" />
                                                <path d="M30.1602 26.4634H25.2969V29.0759H30.1602V26.4634Z"
                                                    fill="#20744A" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.14059 13.674L9.55484 13.5279L11.0725 17.9324L12.8657 13.3463L15.28 13.2002L12.3482 19.4536L15.28 25.7224L12.7273 25.5407L11.0038 20.7622L9.27922 25.359L6.93359 25.1405L9.65834 19.6032L7.14059 13.674Z"
                                                    fill="#FCF6F5" />
                                            </svg></span>
                                            {{ $mediaData->image_name ?? ''}}
                                            <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH').$mediaData->message}}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/></svg>
                                    </div>
                                @endif

                                
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

    

@stop
@section('scriptCode')
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.0/socket.io.js"></script>

<script>

    var socket = io.connect("https://ns.tovilli.co.il",{'transports': ['websocket']});
    socket.on( 'connect', function() {
        let userId = "user_"+{{ $auth->id }};
        console.log(userId,'userIduserId')
        socket.emit( 'loginChatRoom',  {"room":userId })
        console.log("connnected")
    });
    var ImageUrl  = "{{asset('public/img/noimage.png')}}";
    socket.on( 'sendEmitMessageResponce', function(data) {
        var messageStr =  data.message;
        if (messageStr.length > 50) {
            messageStr = messageStr.substring(0, 50) + '...';
        } else {
            messageStr = messageStr;
        }
        toastr.success(messageStr,data.sender_name);
            
        if(data.property_id == 1){
            $(".AdminChatAlertIcon").show();
        }else{
            $(".chatAlertIcon").addClass("show");
        }
        console.log(data,'datadata')
        var id      = $('.upendSearchList li.active').attr('data');
        var listid  = $('.upendSearchList li.active').attr('data1');

        if(data.property_id == 1 && data.sender_id == 1 ){
            console.log('fahlsdk')
            var chekside = 0;
            var sidehtml = '';
            var htmls = 
            `<div class="message message-right appendChatChild">
                <div class="message-body">
                    <div class="message-row">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="message-content-block">
                                <figure class="message_leftImg"><img src="${data.sender_image ? data.sender_image : ImageUrl}" alt="">
                            </figure>
                            <div class="mess_Send_box">`
                                if(data.message != ''){
                                    htmls += 
                                    `<div class="message-content">
                                        <div>${data.message}</div>
                                        <div class="text-left message_timeDate">
                                            <span>{{trans("messages.just_now")}}</span>
                                        </div>
                                    </div>
                                    <div class="mess_Send_box">`
                                }

                                if(data.image_data.length > 0){
                                    $.each(data.image_data,function(key,val) {   
                                        var exploded = val.split(".");
                                        var last = exploded[exploded.length - 1];
                                        var types_image = '';
                                        if(last == 'png' || last == 'jpg' || last == 'jpeg'){
                                            types_image = 'image';
                                        }else if(last == 'zip' || last == 'odt' || last == 'pdf' || last == 'doc' || last == 'docx' || last == 'xlsx' ){
                                            types_image = last;
                                        }else if(last == 'mp4'){
                                            types_image = 'video';
                                        }

                                        htmls += 
                                        `<div class="mess_Send_img">`
                                        if(types_image == 'image'){
                                            htmls += 
                                            `<a href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" data-fancybox="messChet">
                                                <img src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}">
                                             </a>`

                                            chekside = 1;
                                            sidehtml  +=  `
                                            <div class="col-sm-4 col-6">
                                                <figure class="filt_messageBox" >
                                                <a href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}${val}" data-fancybox="gallery">
                                                    <img src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}${val}" alt="Image Gallery">
                                                </a>
                                                </figure>
                                            </div>`
                                        }else if(types_image == 'video'){
                                            chekside = 0;
                                            htmls += 
                                            `<video id="video_undefined" width="100%" height="100%" preload="metadata" controls="controls" class="ratio ratio-21x9">
                                                <source src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" type="video/mp4">
                                            </video>`

                                        }else if(types_image == 'pdf'){
                                            htmls +=
                                            `<a href="{{asset('public/frontend/img/pdf.png')}}" data-fancybox="messChet">    
                                                <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/pdf.png')}}">
                                            </a>`
                                            chekside = 2;
                                            sidehtml  +=  
                                            `<div class="mess_fil_xmlBox">
                                                    <span>
                                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_357_7632)">
                                                                <path
                                                                d="M27.9918 0H0.00820313C0.00367266 0 0 0.00367266 0 0.00820313V27.9918C0 27.9963 0.00367266 28 0.00820313 28H27.9918C27.9963 28 28 27.9963 28 27.9918V0.00820313C28 0.00367266 27.9963 0 27.9918 0Z"
                                                                fill="#C80A0A" />
                                                                <path
                                                                d="M22.5862 16.5156C22.094 15.9688 21.0003 15.6953 19.5237 15.6953C18.6487 15.6953 17.719 15.8047 16.6253 15.9688C15.4672 14.884 14.5051 13.6075 13.7815 12.1953C14.3284 10.5547 14.7112 8.96875 14.7112 7.76562C14.7112 6.83594 14.3831 5.35938 13.0706 5.35938C12.6878 5.35938 12.3597 5.57812 12.1409 5.90625C11.594 6.89062 11.8128 9.07812 12.8518 11.375C12.0928 13.5698 11.1791 15.708 10.1175 17.7734C7.21903 18.9766 5.30497 20.2891 5.14091 21.3281C5.03153 21.8203 5.35966 22.6406 6.5081 22.6406C8.20341 22.6406 10.0628 20.1797 11.4847 17.6641C13.126 17.0997 14.808 16.6609 16.5159 16.3516C18.594 18.1562 20.3987 18.4297 21.2737 18.4297C23.0237 18.4297 23.1878 17.1719 22.5862 16.5156ZM12.4143 6.07031C12.8518 5.41406 13.8362 5.63281 13.8362 6.94531C13.8362 7.82031 13.5628 9.24219 13.0159 10.8828C12.0315 8.58594 12.0315 6.78125 12.4143 6.07031ZM5.46903 21.3828C5.6331 20.5078 7.27372 19.3047 9.84403 18.2656C8.42216 20.6719 7.00028 22.2031 6.12528 22.2031C5.57841 22.2031 5.41435 21.7109 5.46903 21.3828ZM16.2425 16.0234C14.7015 16.3106 13.1843 16.7128 11.7034 17.2266C12.5128 15.7656 13.1724 14.2265 13.6722 12.6328C14.3909 13.8613 15.2538 14.9995 16.2425 16.0234ZM16.9534 16.2422C18.7034 15.9688 20.18 16.0234 20.8362 16.1328C22.4222 16.4609 21.8753 18.375 20.1253 17.9375C18.8675 17.6641 17.8284 16.9531 16.9534 16.2422Z"
                                                                fill="#FCF6F5" />
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_357_7632">
                                                                <rect width="28" height="28" fill="white" />
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </span>
                                                    ${val}
                                                    <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"> 
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/>
                                                    </svg>
                                                </div>`
                                        }else if(types_image == 'xlsx'){
                                            htmls +=
                                            `<a href="{{asset('public/frontend/img/excel.png')}}" data-fancybox="messChet">    
                                                <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/excel.png')}}">
                                            </a>`
                                            chekside = 2;
                                            sidehtml  +=  
                                            `<div class="mess_fil_xmlBox">
                                                <span>
                                                    <svg width="36" height="38" viewBox="0 0 36 38" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M32.3786 6.23105H20.9824V3.39648L2.25 6.44836V33.1992L20.9824 36.6049V32.4035H32.3786C32.7238 32.422 33.0618 32.2954 33.3188 32.0516C33.5758 31.8077 33.7308 31.4663 33.75 31.102V7.53136C33.7305 7.16735 33.5754 6.82626 33.3184 6.58262C33.0614 6.33898 32.7236 6.21258 32.3786 6.23105ZM32.5586 31.3182H20.9441L20.925 29.075H23.7229V26.4625H20.9036L20.8901 24.9187H23.7229V22.3062H20.8687L20.8552 20.7625H23.7229V18.15H20.8463V16.6062H23.7229V13.9937H20.8463V12.45H23.7229V9.83748H20.8463V7.46248H32.5586V31.3182Z"
                                                            fill="#20744A" />
                                                        <path d="M30.1602 9.83398H25.2969V12.4465H30.1602V9.83398Z"
                                                            fill="#20744A" />
                                                        <path d="M30.1602 13.9912H25.2969V16.6037H30.1602V13.9912Z"
                                                            fill="#20744A" />
                                                        <path d="M30.1602 18.1489H25.2969V20.7614H30.1602V18.1489Z"
                                                            fill="#20744A" />
                                                        <path d="M30.1602 22.3062H25.2969V24.9187H30.1602V22.3062Z"
                                                            fill="#20744A" />
                                                        <path d="M30.1602 26.4634H25.2969V29.0759H30.1602V26.4634Z"
                                                            fill="#20744A" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M7.14059 13.674L9.55484 13.5279L11.0725 17.9324L12.8657 13.3463L15.28 13.2002L12.3482 19.4536L15.28 25.7224L12.7273 25.5407L11.0038 20.7622L9.27922 25.359L6.93359 25.1405L9.65834 19.6032L7.14059 13.674Z"
                                                            fill="#FCF6F5" />
                                                    </svg>
                                                </span>
                                                ${val}
                                                <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"> 
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/>
                                                </svg>
                                            </div>`
                                        }else if(types_image == 'doc' || types_image == 'docx'){
                                            htmls +=
                                            `<a href="{{asset('public/frontend/img/doc.png')}}" data-fancybox="messChet">    
                                                <img class="rounded" width="150px" height="80px" src="{{asset('public/frontend/img/doc.png')}}">
                                            </a>`
                                            chekside = 2;
                                            sidehtml  +=  
                                            `<div class="mess_fil_xmlBox">
                                                    <span>
                                                        <svg width="34" height="32" viewBox="0 0 34 32" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_357_7635)">
                                                                <path
                                                                d="M32.5499 0H9.3556C8.55515 0 7.90625 0.65653 7.90625 1.4664V8L21.3481 12L33.9993 8V1.4664C33.9993 0.65653 33.3504 0 32.5499 0Z"
                                                                fill="#41A5EE" />
                                                                <path d="M33.9993 8H7.90625V16L21.3481 18.4L33.9993 16V8Z"
                                                                fill="#2B7CD3" />
                                                                <path d="M7.90625 16V24L20.5574 25.6L33.9993 24V16H7.90625Z"
                                                                fill="#185ABD" />
                                                                <path
                                                                d="M9.3556 32H32.5499C33.3504 32 33.9993 31.3435 33.9993 30.5336V24H7.90625V30.5336C7.90625 31.3435 8.55515 32 9.3556 32Z"
                                                                fill="#103F91" />
                                                                <path opacity="0.1"
                                                                d="M17.5267 6.40002H7.90625V26.4H17.5267C18.326 26.3974 18.9734 25.7424 18.976 24.9336V7.86643C18.9734 7.05765 18.326 6.40266 17.5267 6.40002Z"
                                                                fill="black" />
                                                                <path opacity="0.2"
                                                                d="M16.736 7.19995H7.90625V27.2H16.736C17.5353 27.1973 18.1827 26.5423 18.1853 25.7336V8.66636C18.1827 7.85758 17.5353 7.20258 16.736 7.19995Z"
                                                                fill="black" />
                                                                <path opacity="0.2"
                                                                d="M16.736 7.19995H7.90625V25.6H16.736C17.5353 25.5973 18.1827 24.9423 18.1853 24.1336V8.66636C18.1827 7.85758 17.5353 7.20258 16.736 7.19995Z"
                                                                fill="black" />
                                                                <path opacity="0.2"
                                                                d="M15.9453 7.19995H7.90625V25.6H15.9453C16.7446 25.5973 17.392 24.9423 17.3946 24.1336V8.66636C17.392 7.85758 16.7446 7.20258 15.9453 7.19995Z"
                                                                fill="black" />
                                                                <path
                                                                d="M1.44935 7.19995H15.946C16.7465 7.19995 17.3954 7.85648 17.3954 8.66636V23.3336C17.3954 24.1434 16.7465 24.8 15.946 24.8H1.44935C0.648896 24.8 0 24.1434 0 23.3336V8.66636C0 7.85648 0.648896 7.19995 1.44935 7.19995Z"
                                                                fill="url(#paint0_linear_357_7635)" />
                                                                <path
                                                                d="M5.94673 18.4465C5.97519 18.6729 5.99417 18.8697 6.00286 19.0385H6.03606C6.04871 18.8785 6.07506 18.6857 6.11513 18.4601C6.1552 18.2345 6.19131 18.0438 6.22346 17.8881L7.74792 11.2337H9.71913L11.3005 17.7881C11.3924 18.1956 11.4582 18.6087 11.4974 19.0249H11.5235C11.553 18.6217 11.6079 18.2209 11.688 17.8249L12.9491 11.2305H14.7432L12.5277 20.7665H10.4316L8.92923 14.4513C8.88575 14.2697 8.83594 14.0321 8.78138 13.7401C8.72682 13.448 8.69282 13.2345 8.67937 13.1001H8.65328C8.63589 13.2552 8.60188 13.4857 8.55127 13.7913C8.50067 14.0969 8.46007 14.323 8.42949 14.4697L7.0173 20.7649H4.88558L2.6582 11.2336H4.4847L5.85815 17.9016C5.889 18.0385 5.91826 18.2209 5.94673 18.4465Z"
                                                                fill="#FCF6F5" />
                                                            </g>
                                                            <defs>
                                                                <linearGradient id="paint0_linear_357_7635" x1="3.02193"
                                                                y1="6.05413" x2="14.5737" y2="25.8288"
                                                                gradientUnits="userSpaceOnUse">
                                                                <stop stop-color="#2368C4" />
                                                                <stop offset="0.5" stop-color="#1A5DBE" />
                                                                <stop offset="1" stop-color="#1146AC" />
                                                                </linearGradient>
                                                                <clipPath id="clip0_357_7635">
                                                                <rect width="34" height="32" fill="white" />
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </span>
                                                    ${val}
                                                    <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"> 
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/>
                                                    </svg>
                                                </div>`
                                        }

                                        htmls +=
                                        `<a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/></svg>
                                            </a>
                                        </div>`;
                                    });
                                }
                                htmls += 
                                `</div>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

            $('.usermsgCount').hide();    
            $('.upendSearchList li.active .last_message_date_append').text('{{trans("messages.just_now")}}');
            $('.upendSearchList li.active .last_message_append').text(data.message);
            $('.appendChat').append(htmls); 
            if(chekside == 1){
                $('.sideimg').append(sidehtml);
            }else if(chekside == 2){
                $('.sideimg1').append(sidehtml);
            }
            $(".appendChat").animate({ scrollTop: $(".appendChat")[0].scrollHeight}, 1000);

        }
    

        setTimeout(() => {
            $("#chatInner").scrollTop($("#chatInner")[0].scrollHeight);
        }, 600);

    });

    
    $(document).ready(function() {
        const emojiPicker = $("#chatInput").emojioneArea({
            pickerPosition: "bottom",
            tonesStyle: "bullet",
            events: {
                keyup: function(editor, event) {
                }
            }
        });

        
    });
    
    $(".mediaChatdiscover, #stepBackwedMsg").click(function () {
        $(".dashChatChatting").toggleClass("active");
    })

    function send_message_button(){
        var htmls;
        var url = "<?php echo config('constants.WEBSITE_URL') ?>";

        var form_data          = new FormData();
        var message            = $('#chatInput').val();
        var property_id        = $('.property_id').val();
        var req                = $('.message_not_null');
        var seller_id          = $('.services_users_id').val();
        var sender_image       = $('.sender_image').val();
        var sender_name        = $('.sender_name').val();
        var sender_id          = $('.sender_id').val();
        var is_online          = $('.is_online').val();    
        var imageLengths       = $('.all_image_show').children('.bg_none_dowload_file').length;
        
        if(message == ''){
            $('.message_not_null').text('{{trans("messages.please_write_something")}}');
        }else{
            $('.message_not_null').text('');
        }

        form_data.append("_token",'{{csrf_token()}}');

        if(message != '' || imageLengths > 0){
            req.hide();
            $('.images').map(function(){
                message_type = 'attachment';
                return form_data.append("images[]",this.value);
            }).get();

            $('.original_name').map(function(){
                return form_data.append("original_name[]",this.value);
            }).get();
            $('.size').map(function(){
                return form_data.append("size[]",this.value);
            }).get();
            form_data.append("message",message);
            form_data.append("receiver_id",1);
            form_data.append("sender_image",sender_image);
            form_data.append("sender_name",sender_name);
            form_data.append("sender_id",sender_id);
            form_data.append("is_online",is_online);
            form_data.append("property_id",1);

            var formDataObj = Object.fromEntries(form_data.entries());
            formDataObj.image_data = form_data.getAll('images[]');
            var ImageUrl  = url+'public/img/noimage.png';

            socket.on( 'connect', function() {
                socket.emit( 'loginChatRoom',  {"room":{{ $auth->id }} })
                console.log("connnected")
            } )


            socket.emit( 'sendEmitMessage',  {"data":formDataObj});

            if(formDataObj.image_data.length > 0){
                var chekside = 0;
                var sidehtml = '';
                var htmls = 
                    `<div class="message message-left">
                        <div class="message-body">
                            <div class="message-row">
                                <div class="d-flex align-items-center">
                                    <div class="message-content-block">
                                        <figure class="message_leftImg">
                                            <img src="${sender_image ? sender_image : ImageUrl}" alt="">
                                        </figure>`
                                        if(formDataObj.message != ''){
                                            htmls += 
                                            `<div class="message-content">
                                                <div>${formDataObj.message}</div>
                                                <div class="message_timeDate">
                                                    <span>{{trans("messages.just_now")}}</span>
                                                </div>
                                            </div>
                                            `    
                                        }
                                        htmls += 
                                        `<div class="mess_Send_box">
                                            <div class="mess_Send_img">`
                                            $.each(formDataObj.image_data, function (key, val) {
                              
                                                var exploded = val.split(".");
                                                var last = exploded[exploded.length - 1];

                                                var types_image = '';
                                                if(last == 'png' || last == 'jpg' || last == 'jpeg'){
                                                    types_image = 'image';
                                                }else if(last == 'zip' || last == 'odt' || last == 'pdf' || last == 'doc' || last == 'docx' || last == 'xlsx'  ){
                                                    types_image = last;
                                                }else if(last == 'mp4'){
                                                    types_image = 'video';
                                                }

                                                if(types_image == 'image'){
                                                    chekside = 1;
                                                    htmls += 
                                                    `<a href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" data-fancybox="messChet"><img class="mb-3" src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}"></a>
                                                    `

                                                    sidehtml  +=  `
                                                    <div class="col-sm-4 col-6">
                                                        <figure class="filt_messageBox" >
                                                        <a href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}${val}" data-fancybox="gallery">
                                                            <img src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH') }}${val}" alt="Image Gallery">
                                                        </a>
                                                        </figure>
                                                    </div>`
                                                    
                                         

                                                }else if(types_image == 'video'){
                                                    chekside = 0;
                                                    htmls += 
                                                    `<video id="video_undefined" width="100%" height="100%" preload="metadata" controls="controls" class="ratio ratio-21x9">
                                                    <source src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                    </video>`

                                                }else if(types_image == 'pdf'){
                                                    chekside = 2;
                                                    htmls +=
                                                    `<a href="{{asset('public/frontend/img/pdf.png')}}" data-fancybox="messChet"><img src="{{asset('public/frontend/img/pdf.png')}}"></a>` 

                                                    sidehtml  +=  `<div class="mess_fil_xmlBox">
                                                                        <span>
                                                                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_357_7632)">
                                                                                    <path
                                                                                    d="M27.9918 0H0.00820313C0.00367266 0 0 0.00367266 0 0.00820313V27.9918C0 27.9963 0.00367266 28 0.00820313 28H27.9918C27.9963 28 28 27.9963 28 27.9918V0.00820313C28 0.00367266 27.9963 0 27.9918 0Z"
                                                                                    fill="#C80A0A" />
                                                                                    <path
                                                                                    d="M22.5862 16.5156C22.094 15.9688 21.0003 15.6953 19.5237 15.6953C18.6487 15.6953 17.719 15.8047 16.6253 15.9688C15.4672 14.884 14.5051 13.6075 13.7815 12.1953C14.3284 10.5547 14.7112 8.96875 14.7112 7.76562C14.7112 6.83594 14.3831 5.35938 13.0706 5.35938C12.6878 5.35938 12.3597 5.57812 12.1409 5.90625C11.594 6.89062 11.8128 9.07812 12.8518 11.375C12.0928 13.5698 11.1791 15.708 10.1175 17.7734C7.21903 18.9766 5.30497 20.2891 5.14091 21.3281C5.03153 21.8203 5.35966 22.6406 6.5081 22.6406C8.20341 22.6406 10.0628 20.1797 11.4847 17.6641C13.126 17.0997 14.808 16.6609 16.5159 16.3516C18.594 18.1562 20.3987 18.4297 21.2737 18.4297C23.0237 18.4297 23.1878 17.1719 22.5862 16.5156ZM12.4143 6.07031C12.8518 5.41406 13.8362 5.63281 13.8362 6.94531C13.8362 7.82031 13.5628 9.24219 13.0159 10.8828C12.0315 8.58594 12.0315 6.78125 12.4143 6.07031ZM5.46903 21.3828C5.6331 20.5078 7.27372 19.3047 9.84403 18.2656C8.42216 20.6719 7.00028 22.2031 6.12528 22.2031C5.57841 22.2031 5.41435 21.7109 5.46903 21.3828ZM16.2425 16.0234C14.7015 16.3106 13.1843 16.7128 11.7034 17.2266C12.5128 15.7656 13.1724 14.2265 13.6722 12.6328C14.3909 13.8613 15.2538 14.9995 16.2425 16.0234ZM16.9534 16.2422C18.7034 15.9688 20.18 16.0234 20.8362 16.1328C22.4222 16.4609 21.8753 18.375 20.1253 17.9375C18.8675 17.6641 17.8284 16.9531 16.9534 16.2422Z"
                                                                                    fill="#FCF6F5" />
                                                                                </g>
                                                                                <defs>
                                                                                    <clipPath id="clip0_357_7632">
                                                                                    <rect width="28" height="28" fill="white" />
                                                                                    </clipPath>
                                                                                </defs>
                                                                            </svg>
                                                                        </span>
                                                                        ${val}
                                                                        <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/>
                                                                        </svg>
                                                                    </div>`
                                                }else if(types_image == 'doc' || types_image == 'docx'){
                                                    chekside = 2;
                                                    htmls +=
                                                    `<a href="{{asset('public/frontend/img/doc.png')}}" data-fancybox="messChet"><img src="{{asset('public/frontend/img/doc.png')}}"></a>` 

                                                    sidehtml  +=  `<div class="mess_fil_xmlBox">
                                                                        <span>
                                                                            <svg width="34" height="32" viewBox="0 0 34 32" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_357_7635)">
                                                                                    <path
                                                                                    d="M32.5499 0H9.3556C8.55515 0 7.90625 0.65653 7.90625 1.4664V8L21.3481 12L33.9993 8V1.4664C33.9993 0.65653 33.3504 0 32.5499 0Z"
                                                                                    fill="#41A5EE" />
                                                                                    <path d="M33.9993 8H7.90625V16L21.3481 18.4L33.9993 16V8Z"
                                                                                    fill="#2B7CD3" />
                                                                                    <path d="M7.90625 16V24L20.5574 25.6L33.9993 24V16H7.90625Z"
                                                                                    fill="#185ABD" />
                                                                                    <path
                                                                                    d="M9.3556 32H32.5499C33.3504 32 33.9993 31.3435 33.9993 30.5336V24H7.90625V30.5336C7.90625 31.3435 8.55515 32 9.3556 32Z"
                                                                                    fill="#103F91" />
                                                                                    <path opacity="0.1"
                                                                                    d="M17.5267 6.40002H7.90625V26.4H17.5267C18.326 26.3974 18.9734 25.7424 18.976 24.9336V7.86643C18.9734 7.05765 18.326 6.40266 17.5267 6.40002Z"
                                                                                    fill="black" />
                                                                                    <path opacity="0.2"
                                                                                    d="M16.736 7.19995H7.90625V27.2H16.736C17.5353 27.1973 18.1827 26.5423 18.1853 25.7336V8.66636C18.1827 7.85758 17.5353 7.20258 16.736 7.19995Z"
                                                                                    fill="black" />
                                                                                    <path opacity="0.2"
                                                                                    d="M16.736 7.19995H7.90625V25.6H16.736C17.5353 25.5973 18.1827 24.9423 18.1853 24.1336V8.66636C18.1827 7.85758 17.5353 7.20258 16.736 7.19995Z"
                                                                                    fill="black" />
                                                                                    <path opacity="0.2"
                                                                                    d="M15.9453 7.19995H7.90625V25.6H15.9453C16.7446 25.5973 17.392 24.9423 17.3946 24.1336V8.66636C17.392 7.85758 16.7446 7.20258 15.9453 7.19995Z"
                                                                                    fill="black" />
                                                                                    <path
                                                                                    d="M1.44935 7.19995H15.946C16.7465 7.19995 17.3954 7.85648 17.3954 8.66636V23.3336C17.3954 24.1434 16.7465 24.8 15.946 24.8H1.44935C0.648896 24.8 0 24.1434 0 23.3336V8.66636C0 7.85648 0.648896 7.19995 1.44935 7.19995Z"
                                                                                    fill="url(#paint0_linear_357_7635)" />
                                                                                    <path
                                                                                    d="M5.94673 18.4465C5.97519 18.6729 5.99417 18.8697 6.00286 19.0385H6.03606C6.04871 18.8785 6.07506 18.6857 6.11513 18.4601C6.1552 18.2345 6.19131 18.0438 6.22346 17.8881L7.74792 11.2337H9.71913L11.3005 17.7881C11.3924 18.1956 11.4582 18.6087 11.4974 19.0249H11.5235C11.553 18.6217 11.6079 18.2209 11.688 17.8249L12.9491 11.2305H14.7432L12.5277 20.7665H10.4316L8.92923 14.4513C8.88575 14.2697 8.83594 14.0321 8.78138 13.7401C8.72682 13.448 8.69282 13.2345 8.67937 13.1001H8.65328C8.63589 13.2552 8.60188 13.4857 8.55127 13.7913C8.50067 14.0969 8.46007 14.323 8.42949 14.4697L7.0173 20.7649H4.88558L2.6582 11.2336H4.4847L5.85815 17.9016C5.889 18.0385 5.91826 18.2209 5.94673 18.4465Z"
                                                                                    fill="#FCF6F5" />
                                                                                </g>
                                                                                <defs>
                                                                                    <linearGradient id="paint0_linear_357_7635" x1="3.02193"
                                                                                    y1="6.05413" x2="14.5737" y2="25.8288"
                                                                                    gradientUnits="userSpaceOnUse">
                                                                                    <stop stop-color="#2368C4" />
                                                                                    <stop offset="0.5" stop-color="#1A5DBE" />
                                                                                    <stop offset="1" stop-color="#1146AC" />
                                                                                    </linearGradient>
                                                                                    <clipPath id="clip0_357_7635">
                                                                                    <rect width="34" height="32" fill="white" />
                                                                                    </clipPath>
                                                                                </defs>
                                                                            </svg>
                                                                        </span>
                                                                        ${val}
                                                                        <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/>
                                                                        </svg>
                                                                    </div>`
                                                }else if(types_image == 'xlsx'){
                                                    chekside = 2;
                                                    htmls +=
                                                    `<a href="{{asset('public/frontend/img/excel.png')}}" data-fancybox="messChet"><img src="{{asset('public/frontend/img/excel.png')}}"></a>` 

                                                    sidehtml  +=  `<div class="mess_fil_xmlBox">
                                                                        <span>
                                                                            <svg width="36" height="38" viewBox="0 0 36 38" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                                    d="M32.3786 6.23105H20.9824V3.39648L2.25 6.44836V33.1992L20.9824 36.6049V32.4035H32.3786C32.7238 32.422 33.0618 32.2954 33.3188 32.0516C33.5758 31.8077 33.7308 31.4663 33.75 31.102V7.53136C33.7305 7.16735 33.5754 6.82626 33.3184 6.58262C33.0614 6.33898 32.7236 6.21258 32.3786 6.23105ZM32.5586 31.3182H20.9441L20.925 29.075H23.7229V26.4625H20.9036L20.8901 24.9187H23.7229V22.3062H20.8687L20.8552 20.7625H23.7229V18.15H20.8463V16.6062H23.7229V13.9937H20.8463V12.45H23.7229V9.83748H20.8463V7.46248H32.5586V31.3182Z"
                                                                                    fill="#20744A" />
                                                                                <path d="M30.1602 9.83398H25.2969V12.4465H30.1602V9.83398Z"
                                                                                    fill="#20744A" />
                                                                                <path d="M30.1602 13.9912H25.2969V16.6037H30.1602V13.9912Z"
                                                                                    fill="#20744A" />
                                                                                <path d="M30.1602 18.1489H25.2969V20.7614H30.1602V18.1489Z"
                                                                                    fill="#20744A" />
                                                                                <path d="M30.1602 22.3062H25.2969V24.9187H30.1602V22.3062Z"
                                                                                    fill="#20744A" />
                                                                                <path d="M30.1602 26.4634H25.2969V29.0759H30.1602V26.4634Z"
                                                                                    fill="#20744A" />
                                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                                    d="M7.14059 13.674L9.55484 13.5279L11.0725 17.9324L12.8657 13.3463L15.28 13.2002L12.3482 19.4536L15.28 25.7224L12.7273 25.5407L11.0038 20.7622L9.27922 25.359L6.93359 25.1405L9.65834 19.6032L7.14059 13.674Z"
                                                                                    fill="#FCF6F5" />
                                                                            </svg>
                                                                        </span>
                                                                        ${val}
                                                                        <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"> 
                                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/>
                                                                        </svg>
                                                                    </div>`
                                                }

                                                htmls +=`
                                                    <a class="file-size"  download="" href="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}${val}" style="color:unset"> 
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8.6667 10.7807L12.2427 7.20468L13.1854 8.14735L8.00003 13.3327L2.8147 8.14735L3.75736 7.20468L7.33336 10.7807V2.66602H8.6667V10.7807Z" fill="currentcolor"/></svg>
                                                    </a>`
                                            
                                            });
                                htmls +=`   </div>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`

            }else{
                var htmls = 
                `<div class="message message-left">
                    <div class="message-body">
                        <div class="message-row">
                            <div class="d-flex align-items-center">
                                <div class="message-content-block">
                                    <figure class="message_leftImg">
                                        <img src="${sender_image ? sender_image : ImageUrl}" alt="">
                                    </figure>
                                    <div class="message-content">
                                        <div> ${formDataObj.message}</div>
                                        <div class="message_timeDate">
                                            <span>{{trans("messages.just_now")}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
            }
            console.log(sidehtml,'sidehtmlsidehtml')

            $('#chatInput').val('');
            $('.emojionearea-editor').empty('');
            $('.all_image_show').empty('');
            if(chekside == 1){
                $('.sideimg').append(sidehtml);
            }else if(chekside == 2){
                $('.sideimg1').append(sidehtml);
            }

            $('.appendChat').append(htmls); 
            $('.upendSearchList li.active .last_message_date_append').text('{{trans("messages.just_now")}}');
            $('.upendSearchList li.active .last_message_append').text(message);
            $(".appendChat").animate({ scrollTop: $(".appendChat")[0].scrollHeight}, 1000);

            if(seller_id != ''){
                $.ajax({
                    type: "POST",
                    url: '{{ route('host.sendSms') }}',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function(response) {

                    }
                }); 
            }else{
                req.show();
                req.html('{{trans("messages.server_issue_please_contact_your_admin")}}');
            }
        }else{
            req.show();
            req.html('{{trans("messages.This field is required")}}')
        }
    }

    $("#chat_image_select").change(function() {
        var url              = "<?php echo config('constants.WEBSITE_URL') ?>";
        var form_data        = new FormData();
        var totalfilesLength = document.getElementById('chat_image_select').files.length;
        for (var index = 0; index < totalfilesLength; index++) {
            form_data.append("images[]", document.getElementById('chat_image_select').files[index]);                  
        }
        form_data.append("_token",'{{csrf_token()}}');
        form_data.append("path",'MESSAGE_IMAGES_ROOT_PATH');
        var fileName  = document.getElementById("chat_image_select").value;
        var idxDot    = fileName.lastIndexOf(".") + 1;
        var extFile   = fileName.substr(idxDot, fileName.length).toLowerCase();
        if (extFile=="jpg" || extFile=="jpeg" || extFile=="png" || extFile=="mp4" || extFile=="pdf" || extFile=="zip" || extFile=="odt" || extFile=="docx" || extFile=="xlsx"){
            $('.loader-wrapper').show();
            $('.overlay').show();
            $.ajax({
                type: "POST",
                url:  url + "attachment-image",
                data: form_data,
                contentType: false,
                processData: false,
                success: function(response) {
                    $.each(response,function(key,val) {
                        var idxDot = val.image.lastIndexOf(".") + 1;
                        var extFile = val.image.substr(idxDot, val.image.length).toLowerCase();
                        if(extFile=="jpg" || extFile=="jpeg" || extFile=="png" || extFile=="mp4" || extFile=="pdf" || extFile=="zip" || extFile=="odt" || extFile=="docx" || extFile=="xlsx"){
                            var type =  val.type.split("/");
                            console.log(type,'typetype')
                            if(type[0] == 'image'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file img-select-box mb-3 p-1 m-1">
                                <input type="hidden" class="images" value="${val.image}">
                                <input type="hidden" class="size" value="${val.size}">
                                <input type="hidden" class="type" value="${val.type}">
                                <input type="hidden" class="original_name" value="${val.original_name}">
                                <img width="80%" height="50px" style="object-fit:cover;" src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}/${val.image}">
                                <a class="close_file" data-image="${val.image}" href="javascript:void(0)">  <svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_11_9049)">
                                                                    <path d="M8.78516 2.92969L8.26623 9.19404C8.22904 9.6458 7.84424 10 7.39076 10H2.60941C2.15594 10 1.77113 9.6458 1.73395 9.19404L1.21504 2.92969H8.78516ZM6.46521 8.82812C6.61857 8.82812 6.7476 8.70883 6.75732 8.55346L7.05029 3.82689C7.06031 3.66525 6.93758 3.52621 6.77621 3.51619C6.60885 3.50445 6.4758 3.62863 6.46551 3.79027L6.17254 8.51684C6.16217 8.68422 6.29461 8.82812 6.46521 8.82812ZM4.70713 8.53516C4.70713 8.69709 4.83816 8.82812 5.0001 8.82812C5.16203 8.82812 5.29307 8.69709 5.29307 8.53516V3.80859C5.29307 3.64666 5.16203 3.51562 5.0001 3.51562C4.83816 3.51562 4.70713 3.64666 4.70713 3.80859V8.53516ZM2.9499 3.82691L3.24287 8.55348C3.2525 8.70727 3.38068 8.83543 3.55357 8.82756C3.71494 8.81754 3.83768 8.6785 3.82766 8.51686L3.53469 3.79029C3.52467 3.62865 3.38305 3.51105 3.22398 3.51621C3.06262 3.52623 2.93988 3.66527 2.9499 3.82691Z" fill="#DA0200"></path>
                                                                    <path d="M1.19141 1.17188H2.94922V0.878906C2.94922 0.394258 3.34348 0 3.82812 0H6.17188C6.65652 0 7.05078 0.394258 7.05078 0.878906V1.17188H8.80859C9.13221 1.17188 9.39453 1.4342 9.39453 1.75781C9.39453 2.08139 9.13221 2.34375 8.80859 2.34375C6.11395 2.34375 3.88596 2.34375 1.19141 2.34375C0.867793 2.34375 0.605469 2.08139 0.605469 1.75781C0.605469 1.4342 0.867793 1.17188 1.19141 1.17188ZM3.53516 1.17188H6.46484V0.878906C6.46484 0.717266 6.33352 0.585938 6.17188 0.585938H3.82812C3.66648 0.585938 3.53516 0.717266 3.53516 0.878906V1.17188Z" fill="#DA0200"></path>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_11_9049">
                                                                        <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 10 0)"></rect>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg></a>
                                </button>`;

                            }else if(type[0] == 'video'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file img-select-box mb-3 p-1 m-1">
                                <input type="hidden" class="images" value="${val.image}">
                                <input type="hidden" class="size" value="${val.size}">
                                <input type="hidden" class="type" value="${val.type}">
                                <input type="hidden" class="original_name" value="${val.original_name}">
                                <video width="130px" controls>
                                <source src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}/${val.image}" type="video/mp4">
                                </video><a class="close_file" data-image="${val.image}" href="javascript:void(0)">  <svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_11_9049)">
                                                                    <path d="M8.78516 2.92969L8.26623 9.19404C8.22904 9.6458 7.84424 10 7.39076 10H2.60941C2.15594 10 1.77113 9.6458 1.73395 9.19404L1.21504 2.92969H8.78516ZM6.46521 8.82812C6.61857 8.82812 6.7476 8.70883 6.75732 8.55346L7.05029 3.82689C7.06031 3.66525 6.93758 3.52621 6.77621 3.51619C6.60885 3.50445 6.4758 3.62863 6.46551 3.79027L6.17254 8.51684C6.16217 8.68422 6.29461 8.82812 6.46521 8.82812ZM4.70713 8.53516C4.70713 8.69709 4.83816 8.82812 5.0001 8.82812C5.16203 8.82812 5.29307 8.69709 5.29307 8.53516V3.80859C5.29307 3.64666 5.16203 3.51562 5.0001 3.51562C4.83816 3.51562 4.70713 3.64666 4.70713 3.80859V8.53516ZM2.9499 3.82691L3.24287 8.55348C3.2525 8.70727 3.38068 8.83543 3.55357 8.82756C3.71494 8.81754 3.83768 8.6785 3.82766 8.51686L3.53469 3.79029C3.52467 3.62865 3.38305 3.51105 3.22398 3.51621C3.06262 3.52623 2.93988 3.66527 2.9499 3.82691Z" fill="#DA0200"></path>
                                                                    <path d="M1.19141 1.17188H2.94922V0.878906C2.94922 0.394258 3.34348 0 3.82812 0H6.17188C6.65652 0 7.05078 0.394258 7.05078 0.878906V1.17188H8.80859C9.13221 1.17188 9.39453 1.4342 9.39453 1.75781C9.39453 2.08139 9.13221 2.34375 8.80859 2.34375C6.11395 2.34375 3.88596 2.34375 1.19141 2.34375C0.867793 2.34375 0.605469 2.08139 0.605469 1.75781C0.605469 1.4342 0.867793 1.17188 1.19141 1.17188ZM3.53516 1.17188H6.46484V0.878906C6.46484 0.717266 6.33352 0.585938 6.17188 0.585938H3.82812C3.66648 0.585938 3.53516 0.717266 3.53516 0.878906V1.17188Z" fill="#DA0200"></path>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_11_9049">
                                                                        <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 10 0)"></rect>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg></a>
                                </button>`;
                            }else if(type[1] == 'pdf'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file img-select-box mb-3 p-1 m-1">
                                <input type="hidden" class="images" value="${val.image}">
                                <input type="hidden" class="size" value="${val.size}">
                                <input type="hidden" class="type" value="${val.type}">
                                <input type="hidden" class="original_name" value="${val.original_name}">
                                <img width="80%" height="50px" style="object-fit:cover;" src="{{asset('public/frontend/img/pdf.png')}}"><a class="close_file" data-image="${val.image}" href="javascript:void(0)">  <svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_11_9049)">
                                                                    <path d="M8.78516 2.92969L8.26623 9.19404C8.22904 9.6458 7.84424 10 7.39076 10H2.60941C2.15594 10 1.77113 9.6458 1.73395 9.19404L1.21504 2.92969H8.78516ZM6.46521 8.82812C6.61857 8.82812 6.7476 8.70883 6.75732 8.55346L7.05029 3.82689C7.06031 3.66525 6.93758 3.52621 6.77621 3.51619C6.60885 3.50445 6.4758 3.62863 6.46551 3.79027L6.17254 8.51684C6.16217 8.68422 6.29461 8.82812 6.46521 8.82812ZM4.70713 8.53516C4.70713 8.69709 4.83816 8.82812 5.0001 8.82812C5.16203 8.82812 5.29307 8.69709 5.29307 8.53516V3.80859C5.29307 3.64666 5.16203 3.51562 5.0001 3.51562C4.83816 3.51562 4.70713 3.64666 4.70713 3.80859V8.53516ZM2.9499 3.82691L3.24287 8.55348C3.2525 8.70727 3.38068 8.83543 3.55357 8.82756C3.71494 8.81754 3.83768 8.6785 3.82766 8.51686L3.53469 3.79029C3.52467 3.62865 3.38305 3.51105 3.22398 3.51621C3.06262 3.52623 2.93988 3.66527 2.9499 3.82691Z" fill="#DA0200"></path>
                                                                    <path d="M1.19141 1.17188H2.94922V0.878906C2.94922 0.394258 3.34348 0 3.82812 0H6.17188C6.65652 0 7.05078 0.394258 7.05078 0.878906V1.17188H8.80859C9.13221 1.17188 9.39453 1.4342 9.39453 1.75781C9.39453 2.08139 9.13221 2.34375 8.80859 2.34375C6.11395 2.34375 3.88596 2.34375 1.19141 2.34375C0.867793 2.34375 0.605469 2.08139 0.605469 1.75781C0.605469 1.4342 0.867793 1.17188 1.19141 1.17188ZM3.53516 1.17188H6.46484V0.878906C6.46484 0.717266 6.33352 0.585938 6.17188 0.585938H3.82812C3.66648 0.585938 3.53516 0.717266 3.53516 0.878906V1.17188Z" fill="#DA0200"></path>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_11_9049">
                                                                        <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 10 0)"></rect>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg></a>
                                </button>`;
                            }else if(type[1] == 'vnd.openxmlformats-officedocument.wordprocessingml.document'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file img-select-box mb-3 p-1 m-1">
                                <input type="hidden" class="images" value="${val.image}">
                                <input type="hidden" class="size" value="${val.size}">
                                <input type="hidden" class="type" value="${val.type}">
                                <input type="hidden" class="original_name" value="${val.original_name}">
                                <img width="80%" height="50px" style="object-fit:cover;" src="{{asset('public/frontend/img/doc.png')}}"><a class="close_file" data-image="${val.image}" href="javascript:void(0)">  <svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_11_9049)">
                                                                    <path d="M8.78516 2.92969L8.26623 9.19404C8.22904 9.6458 7.84424 10 7.39076 10H2.60941C2.15594 10 1.77113 9.6458 1.73395 9.19404L1.21504 2.92969H8.78516ZM6.46521 8.82812C6.61857 8.82812 6.7476 8.70883 6.75732 8.55346L7.05029 3.82689C7.06031 3.66525 6.93758 3.52621 6.77621 3.51619C6.60885 3.50445 6.4758 3.62863 6.46551 3.79027L6.17254 8.51684C6.16217 8.68422 6.29461 8.82812 6.46521 8.82812ZM4.70713 8.53516C4.70713 8.69709 4.83816 8.82812 5.0001 8.82812C5.16203 8.82812 5.29307 8.69709 5.29307 8.53516V3.80859C5.29307 3.64666 5.16203 3.51562 5.0001 3.51562C4.83816 3.51562 4.70713 3.64666 4.70713 3.80859V8.53516ZM2.9499 3.82691L3.24287 8.55348C3.2525 8.70727 3.38068 8.83543 3.55357 8.82756C3.71494 8.81754 3.83768 8.6785 3.82766 8.51686L3.53469 3.79029C3.52467 3.62865 3.38305 3.51105 3.22398 3.51621C3.06262 3.52623 2.93988 3.66527 2.9499 3.82691Z" fill="#DA0200"></path>
                                                                    <path d="M1.19141 1.17188H2.94922V0.878906C2.94922 0.394258 3.34348 0 3.82812 0H6.17188C6.65652 0 7.05078 0.394258 7.05078 0.878906V1.17188H8.80859C9.13221 1.17188 9.39453 1.4342 9.39453 1.75781C9.39453 2.08139 9.13221 2.34375 8.80859 2.34375C6.11395 2.34375 3.88596 2.34375 1.19141 2.34375C0.867793 2.34375 0.605469 2.08139 0.605469 1.75781C0.605469 1.4342 0.867793 1.17188 1.19141 1.17188ZM3.53516 1.17188H6.46484V0.878906C6.46484 0.717266 6.33352 0.585938 6.17188 0.585938H3.82812C3.66648 0.585938 3.53516 0.717266 3.53516 0.878906V1.17188Z" fill="#DA0200"></path>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_11_9049">
                                                                        <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 10 0)"></rect>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg></a>
                                </button>`;
                            }else if(type[1] == 'vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file img-select-box mb-3 p-1 m-1">
                                <input type="hidden" class="images" value="${val.image}">
                                <input type="hidden" class="size" value="${val.size}">
                                <input type="hidden" class="type" value="${val.type}">
                                <input type="hidden" class="original_name" value="${val.original_name}">
                                <img width="80%" height="50px" style="object-fit:cover;" src="{{asset('public/frontend/img/excel.png')}}"><a class="close_file" data-image="${val.image}" href="javascript:void(0)">  <svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_11_9049)">
                                                                    <path d="M8.78516 2.92969L8.26623 9.19404C8.22904 9.6458 7.84424 10 7.39076 10H2.60941C2.15594 10 1.77113 9.6458 1.73395 9.19404L1.21504 2.92969H8.78516ZM6.46521 8.82812C6.61857 8.82812 6.7476 8.70883 6.75732 8.55346L7.05029 3.82689C7.06031 3.66525 6.93758 3.52621 6.77621 3.51619C6.60885 3.50445 6.4758 3.62863 6.46551 3.79027L6.17254 8.51684C6.16217 8.68422 6.29461 8.82812 6.46521 8.82812ZM4.70713 8.53516C4.70713 8.69709 4.83816 8.82812 5.0001 8.82812C5.16203 8.82812 5.29307 8.69709 5.29307 8.53516V3.80859C5.29307 3.64666 5.16203 3.51562 5.0001 3.51562C4.83816 3.51562 4.70713 3.64666 4.70713 3.80859V8.53516ZM2.9499 3.82691L3.24287 8.55348C3.2525 8.70727 3.38068 8.83543 3.55357 8.82756C3.71494 8.81754 3.83768 8.6785 3.82766 8.51686L3.53469 3.79029C3.52467 3.62865 3.38305 3.51105 3.22398 3.51621C3.06262 3.52623 2.93988 3.66527 2.9499 3.82691Z" fill="#DA0200"></path>
                                                                    <path d="M1.19141 1.17188H2.94922V0.878906C2.94922 0.394258 3.34348 0 3.82812 0H6.17188C6.65652 0 7.05078 0.394258 7.05078 0.878906V1.17188H8.80859C9.13221 1.17188 9.39453 1.4342 9.39453 1.75781C9.39453 2.08139 9.13221 2.34375 8.80859 2.34375C6.11395 2.34375 3.88596 2.34375 1.19141 2.34375C0.867793 2.34375 0.605469 2.08139 0.605469 1.75781C0.605469 1.4342 0.867793 1.17188 1.19141 1.17188ZM3.53516 1.17188H6.46484V0.878906C6.46484 0.717266 6.33352 0.585938 6.17188 0.585938H3.82812C3.66648 0.585938 3.53516 0.717266 3.53516 0.878906V1.17188Z" fill="#DA0200"></path>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_11_9049">
                                                                        <rect width="14" height="14" fill="white" transform="matrix(-1 0 0 1 10 0)"></rect>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg></a>
                                </button>`;
                            }
                  
                            $('.all_image_show').append(html);
                            if($(".all_image_show button").length){
                                $(".dashChatChattingInner").addClass("dashChatChattingFiles");
                                setTimeout(() => {
                                    $("#chatInner").scrollTop($("#chatInner")[0].scrollHeight);
                                });
                            }else{
                                $(".dashChatChattingInner").removeClass("dashChatChattingFiles");
                            }
                            $('.message_not_null').hide();
                            $('.loader-wrapper').hide();
                            $('.overlay').hide();
                        }else{
                            $('.message_not_null').show();
                            $('.message_not_null').html('{{trans("messages.browse_to_upload_a_valid_extension")}}');
                        }
                    });
                }
            });
        }else{
            $('.message_not_null').show();
            $('.message_not_null').html('{{trans("messages.browse_to_upload_a_valid_extension")}}');
        }
    });

    $('body').on('click', '.close_file', function() {
        var url             = "<?php echo config('constants.WEBSITE_URL') ?>";
        var removeElement   = $(this).parent('.bg_none_dowload_file');
        var image_delete    = $(this).data("image");
        var path            = 'MESSAGE_IMAGES_ROOT_PATH';
        var _token          = "{{csrf_token()}}";

        Swal.fire({
            title: "{{trans("messages.admin_common_Are_you_sure")}}",
            text: "{{trans("messages.admin_Want_to_delete_this")}}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "{{trans("messages.admin_Yes_delete_it")}}",
            cancelButtonText: "{{trans("messages.admin_No_cancel")}}",
            reverseButtons: true
        }).then(function(result) {
            if (result.value) {  
                $('.loader-wrapper').show();
                $('.overlay').show();
                $.ajax({
                    type: "POST",
                    url: '{{ route('portfolio_image_add_delete') }}',
                    data: {image:image_delete,path:path,_token:_token},
                    success: function(response) {
                        if(response == 'success'){
                            removeElement.remove();
                            document.getElementById('chat_image_select').value = '';
                        }
                        $('.loader-wrapper').hide();
                        $('.overlay').hide();
                        if($(".all_image_show button").length){
                            $(".dashChatChattingInner").addClass("dashChatChattingFiles");
                        }else{
                            $(".dashChatChattingInner").removeClass("dashChatChattingFiles");
                        }
                    }
                }); 
            } else if (result.dismiss === "cancel") {
                Swal.fire(
                    "Cancelled",
                    "{{trans("messages.admin_Your_imaginary_file_is_safe")}}",
                    "error"
                    )
            }
        });
    });

    setTimeout(() => {
        $("#chatInner").scrollTop($("#chatInner")[0].scrollHeight);
    }, 600);

</script>


@stop