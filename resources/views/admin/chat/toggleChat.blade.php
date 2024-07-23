@php
    $lastDate = null;
@endphp

@foreach($selectedUser['data'] as $selectedUserChat)
    @php
        $messageDate = $selectedUserChat->date; // Assuming $selectedUserChat->date contains the message date
        $formattedMessageDate = $messageDate;
    @endphp

    @if($lastDate !== $formattedMessageDate)
        <div class="datewise-row">
            @if($formattedMessageDate === now()->format('Y-m-d'))
                <span>Today</span>
            @elseif($formattedMessageDate === now()->subDay()->format('Y-m-d'))
                <span>Yesterday</span>
            @else
                <span>{{ $messageDate }}</span>
            @endif
        </div>
        @php
            $lastDate = $formattedMessageDate;
        @endphp
    @endif

    @if($selectedUserChat->sender_id != $auth->id)
        <div class="message message-right appendChatChild">
            <div class="message-body">
                <div class="message-row">
                    <div class="d-flex align-items-centers">
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
        <div class="message message-left appendChatChild">
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