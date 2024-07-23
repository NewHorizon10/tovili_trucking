
<div class="mess_filter_block toggle_chat_media_child">

    <div class="mess_filter_block">
        <div>
            <h3 class="mess_filter_title">Details</h3>
        </div>
        <div class="messFilter_box">
            <h4 class="message_fil_head">
            Multimedia images
            </h4>
            @if($selectedUser != '')
            <div class="row g-3 sideimg">
                @foreach($selectedUser['mediaData'] as $mediaData)
                    @if($mediaData->types_image == 'image')
                        <div class="col-sm-4 col-6">
                            <figure class="filt_messageBox detail_img_box" >
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
            attached files
            </h4>
            @if($selectedUser != '')
            <span class="message_fil_desc sideimg1">Source File</span>
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