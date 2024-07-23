<div class="mess_filter_block toggle_chat_media_child">
    @if($selectedUserSeshipmentOffer)
    <div>
        <h3 class="mess_filter_title"> {{trans("messages.company_offers")}}</h3>
    </div>
    <div class="messFilter_box">
        <div class="messFilt_tag">
            <a href="#!" class="messFilt_tagBtn">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_480_6539)">
                        <path
                            d="M20.6795 11.2808L20.6437 11.2449L10.7442 1.34547C10.5214 1.12304 10.2187 0.998545 9.90399 0.999765L9.90011 0.99978L2.20152 0.999772C1.53886 1.00254 1.00276 1.53865 1 2.20132V9.89913C1 10.2194 1.12753 10.5263 1.35438 10.7519L1.35631 10.7539L11.2558 20.6533L20.6795 11.2808ZM20.6795 11.2808C20.8844 11.5035 20.9996 11.7962 21.0008 12.1027L21.0008 12.1041C21.0024 12.4169 20.877 12.7163 20.6535 12.9342L20.6534 12.9341L20.6444 12.9431L12.9449 20.6427L12.9448 20.6427L12.9359 20.6518C12.7181 20.8752 12.4186 21.0006 12.1058 20.999V20.999L12.0968 20.999C11.7814 21.0002 11.4791 20.8759 11.2564 20.654L10.5493 21.3611M20.6795 11.2808L12.1006 21.999M10.5493 21.3611C10.9606 21.7716 11.519 22.0013 12.1006 21.999M10.5493 21.3611L0.6492 11.461L10.5493 21.3611ZM12.1006 21.999C12.6845 22.002 13.2444 21.7679 13.652 21.3498L12.1006 21.999ZM1.20053 3.84994C1.20053 5.31336 2.38675 6.49959 3.85017 6.49959C5.31358 6.49959 6.49981 5.31336 6.49981 3.84994C6.49981 2.38652 5.31358 1.2003 3.85017 1.2003C2.38675 1.2003 1.20053 2.38652 1.20053 3.84994Z"
                            stroke="white" stroke-width="2" />
                    </g>
                    <defs>
                        <clipPath id="clip0_480_6539">
                            <rect width="22" height="22" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
            </a>
            <div>
                <h3 class="messFilt_text">{{$selectedUserSeshipment->total_offers->count()}}</h3>
                <span class="messFilt_desc">{{trans("messages.transport_offer")}}</span>
            </div>
        </div>
        <ul class="mess_filt_list">
            <li class="mess_filRight_text">{{trans("messages.application")}}</li>
            <li class="mess_filLeft_text">{{$selectedUserSeshipmentOffer->system_id}}</li>
            <li class="mess_filRight_text">{{trans("messages.date")}}</li>
            <li class="mess_filLeft_text date_label">{{date(config("Reading.date_format"),strtotime($selectedUserSeshipmentOffer->created_at))}}</li>
            <li class="mess_filRight_text">{{trans("messages.type_of_transport")}}</li>
            <li class="mess_filLeft_text">{{$selectedUserSeshipmentOffer->truck_type_name}} </li>
            <li class="mess_filRight_text">{{trans("messages.admin_common_Status")}}</li>
            <li class="mess_filRight_text">
                <a href="#!" class="mess_filLeft_btn">{{trans("messages.".$selectedUserSeshipmentOffer->status)}}</a>
            </li>
            <li class="mess_filRight_text">
                <a href="#!" class="mess_fitbtnTExt">{{trans("messages.".$selectedUserSeshipmentOffer->status)}}</a>
            </li>
        </ul>
    </div>

    <div>
        <h3 class="mess_filter_title"> {{trans("messages.shipment_details")}}</h3>
    </div>
    <div class="messFilter_box">
        <div class="messFilt_tag">
            <a href="#!" class="messFilt_tagBtn">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M15.5521 1.7722C15.8337 1.63144 16.165 1.63144 16.4466 1.7722L29.7799 8.43887C30.1187 8.60826 30.3327 8.95452 30.3327 9.33329V22.6666C30.3327 23.0454 30.1187 23.3917 29.7799 23.5611L16.4466 30.2277C16.165 30.3685 15.8337 30.3685 15.5521 30.2277L2.2188 23.5611C1.88002 23.3917 1.66602 23.0454 1.66602 22.6666V9.33329C1.66602 8.95452 1.88002 8.60826 2.2188 8.43887L15.5521 1.7722ZM3.66602 9.95133V22.0486L15.9993 28.2153L28.3327 22.0486V9.95133L15.9993 3.78466L3.66602 9.95133Z"
                        fill="white" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M1.77181 8.88614C2.0188 8.39216 2.61947 8.19193 3.11345 8.43892L16.4468 15.1056C16.9408 15.3526 17.141 15.9533 16.894 16.4472C16.647 16.9412 16.0463 17.1414 15.5524 16.8944L2.21902 10.2278C1.72504 9.98079 1.52482 9.38011 1.77181 8.88614Z"
                        fill="white" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M16 15C16.5523 15 17 15.4477 17 16V29.3333C17 29.8856 16.5523 30.3333 16 30.3333C15.4477 30.3333 15 29.8856 15 29.3333V16C15 15.4477 15.4477 15 16 15Z"
                        fill="white" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M30.228 8.88614C30.475 9.38011 30.2747 9.98079 29.7808 10.2278L16.4474 16.8944C15.9535 17.1414 15.3528 16.9412 15.1058 16.4472C14.8588 15.9533 15.059 15.3526 15.553 15.1056L28.8863 8.43892C29.3803 8.19193 29.981 8.39216 30.228 8.88614Z"
                        fill="white" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M23.562 5.55276C23.809 6.04674 23.6087 6.64741 23.1147 6.8944L9.78142 13.5611C9.28744 13.8081 8.68677 13.6078 8.43978 13.1139C8.19279 12.6199 8.39301 12.0192 8.88699 11.7722L22.2203 5.10555C22.7143 4.85856 23.315 5.05878 23.562 5.55276Z"
                        fill="white" />
                </svg>
            </a>
            <div>
                <h3 class="messFilt_text">{{$selectedUserSeshipment->total_stops->count()}}</h3>
                <span class="messFilt_desc">{{trans("messages.Transportation")}}</span>
            </div>
        </div>
        <ul class="mess_filt_list">
            <li class="mess_filRight_text">{{trans("messages.application_number")}}</li>
            <li class="mess_filLeft_text">{{$selectedUserSeshipment->request_number}}</li>
            <li class="mess_filRight_text">{{trans("messages.date")}}</li>
            <li class="mess_filLeft_text date_label">{{date(config("Reading.date_format"),strtotime($selectedUserSeshipment->request_date))}}</li>
            <li class="mess_filRight_text">{{trans("messages.type_of_transport")}}</li>
            <li class="mess_filLeft_text">{{$selectedUserSeshipment->truck_type_name}} </li>
            <li class="mess_filRight_text">{{trans("messages.admin_common_Status")}}</li>
            <li class="mess_filLeft_text">
                <a href="#!" class="mess_filLeft_btn mess_unFil">{{trans("messages.".$selectedUserSeshipment->status)}}</a>
            </li>
            <li class="mess_filRight_text">
                <a href="#!" class="mess_fitbtnTExt">{{trans("messages.".$selectedUserSeshipment->status)}}</a>
            </li>
        </ul>
    </div>
    @endif

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