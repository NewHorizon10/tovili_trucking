

<link href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" rel="stylesheet"/>
<div class="modal fade singelChatModal allpopupsame" id="singelChatModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <div class="coordinator_name_box">
                        <img  class="chat_image" width="10px" src="" >
                        <h3 class="coordinator_name_title chat_name text-white"></h3>
                    </div>
                    <button type="button" class="btn-close chatCloseBtn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="0">
                    <div class="modalinnerblock">
                    <div class="form-group position-relative mb-0">
                        <textarea id="chatInput" class="form-control border-0 chat_input"
                            placeholder="{{trans("messages.write_somthing")}}" rows="1"
                            data-autosize="true"></textarea>
                    </div>
                    <div class="sendmessagebt">
                       
                        <div>
                            <input type="hidden" id="message_by_user_id" value="{{ $user->id ?? '' }}">
                            <div class="imojiblock_Outerbox chat_parent_box">
                            
                            <button type="button" data="" onclick="send_message_button()"  class="sendSmsButton btn btn-primary">
                                {{trans("messages.send")}}
                                <svg width="21" height="21" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0.923018 7.37004C0.413018 7.16504 0.419018 6.86004 0.957018 6.68104L20.043 0.31904C20.572 0.14304 20.875 0.43904 20.727 0.95704L15.273 20.043C15.123 20.572 14.798 20.596 14.556 20.113L10 11L0.923018 7.37004ZM5.81302 7.17004L11.449 9.42504L14.489 15.507L18.035 3.09704L5.81202 7.17004H5.81302Z"
                                    fill="Currentcolor" />
                                </svg>
                            </button>
                            <div class="imojiblock">
                                <div class="attach_files">
                                    <input type="file" id="chat_image_select" name="image[]" hidden="" multiple=''>
                                    <label  for="chat_image_select">
                                    <svg width="25" height="27" viewBox="0 0 25 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.7707 8.34382L9.22939 15.8865C9.10204 16.0095 9.00047 16.1566 8.93059 16.3193C8.86071 16.482 8.82393 16.6569 8.82239 16.834C8.82085 17.011 8.85459 17.1866 8.92163 17.3504C8.98867 17.5143 9.08767 17.6632 9.21286 17.7883C9.33805 17.9135 9.48692 18.0125 9.65079 18.0796C9.81465 18.1466 9.99022 18.1804 10.1673 18.1788C10.3443 18.1773 10.5193 18.1405 10.6819 18.0706C10.8446 18.0007 10.9917 17.8992 11.1147 17.7718L18.6574 10.2305C19.4076 9.48028 19.8291 8.46277 19.8291 7.40182C19.8291 6.34086 19.4076 5.32336 18.6574 4.57315C17.9072 3.82294 16.8897 3.40148 15.8287 3.40148C14.7678 3.40148 13.7503 3.82294 13.0001 4.57315L5.45739 12.1158C4.82549 12.732 4.32223 13.4675 3.9768 14.2797C3.63137 15.0919 3.45065 15.9646 3.44512 16.8471C3.43959 17.7297 3.60936 18.6046 3.94459 19.421C4.27981 20.2375 4.77382 20.9792 5.39794 21.6033C6.02207 22.2273 6.7639 22.7212 7.5804 23.0563C8.39689 23.3914 9.2718 23.5611 10.1544 23.5554C11.0369 23.5498 11.9096 23.3689 12.7217 23.0234C13.5339 22.6778 14.2693 22.1745 14.8854 21.5425L22.4281 14.0012L24.3134 15.8865L16.7707 23.4292C15.904 24.2959 14.875 24.9834 13.7426 25.4525C12.6102 25.9215 11.3965 26.163 10.1707 26.163C8.94499 26.163 7.73126 25.9215 6.59883 25.4525C5.4664 24.9834 4.43745 24.2959 3.57072 23.4292C2.704 22.5624 2.01648 21.5335 1.54741 20.401C1.07834 19.2686 0.836914 18.0549 0.836914 16.8292C0.836914 15.6034 1.07834 14.3897 1.54741 13.2573C2.01648 12.1248 2.704 11.0959 3.57072 10.2292L11.1147 2.68782C12.3721 1.47343 14.0561 0.801469 15.8041 0.816658C17.552 0.831848 19.2241 1.53297 20.4602 2.76903C21.6962 4.00508 22.3974 5.67717 22.4125 7.42514C22.4277 9.17312 21.7558 10.8571 20.5414 12.1145L13.0001 19.6598C12.6285 20.0313 12.1874 20.3259 11.702 20.5269C11.2166 20.7279 10.6963 20.8314 10.1709 20.8313C9.64553 20.8312 9.12529 20.7277 8.63991 20.5266C8.15453 20.3255 7.71352 20.0307 7.34206 19.6592C6.97059 19.2876 6.67595 18.8465 6.47494 18.3611C6.27394 17.8757 6.17052 17.3554 6.17058 16.83C6.17064 16.3046 6.27419 15.7844 6.4753 15.299C6.67642 14.8136 6.97117 14.3726 7.34272 14.0012L14.8854 6.45849L16.7707 8.34382Z" fill="#7684AD"/>
                                    </svg>
                                    </label>
                                </div>
                                <p class="error send_message_not_null"></p>
                                <div class="all_image_show"></div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <small class="text-danger message_not_null"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js"></script>
    <script>

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

        function singelChatModal(image,name,id){
            $('#singelChatModal').modal('show');
            $('.chat_name').text(name);
            $('.chat_image').attr('src',image);        
            $('.sendSmsButton').attr('data',id);
            $('.chat_input .emojionearea-editor').html('');
        }
        

        $("#chat_image_select").change(function() {
            let form = document.querySelector('form');
            let form_data = new FormData(form);

            var totalfilesLength = document.getElementById('chat_image_select').files.length;
            for (var index = 0; index < totalfilesLength; index++) {
            form_data.append("images[]", document.getElementById('chat_image_select').files[index]);                  
            }
            form_data.append("_token",'{{csrf_token()}}');
            form_data.append("path",'MESSAGE_IMAGES_ROOT_PATH');
            var fileName  = document.getElementById("chat_image_select").value;
            var idxDot    = fileName.lastIndexOf(".") + 1;
            var extFile   = fileName.substr(idxDot, fileName.length).toLowerCase();
            if (extFile=="jpg" || extFile=="jpeg" || extFile=="png" || extFile=="mp4" || extFile=="pdf" || extFile=="zip" || extFile=="odt"){
            $('.loader-wrapper').show();
            $('.overlay').show();
            $.ajax({
                type: "POST",
                url:  '{{ route('attachment_image') }}',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(response) {
                    $.each(response,function(key,val) {
                        var idxDot = val.image.lastIndexOf(".") + 1;
                        var extFile = val.image.substr(idxDot, val.image.length).toLowerCase();
                        if(extFile=="jpg" || extFile=="jpeg" || extFile=="png" || extFile=="mp4" || extFile=="pdf" || extFile=="zip" || extFile=="odt"){
                            var type =  val.type.split("/");
                            if(type[0] == 'image'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file mb-3 p-1 m-1">
                                    <input type="hidden" class="images" value="${val.image}">
                                    <input type="hidden" class="size" value="${val.size}">
                                    <input type="hidden" class="type" value="${val.type}">
                                    <input type="hidden" class="original_name" value="${val.original_name}">
                                    <img width="80%" height="50px" style="object-fit:cover;" src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}/${val.image}">
                                    <a class="close_file" data-image="${val.image}" href="javascript:void(0)"><i class="fal fa-times"></i></a>
                                </button>`;
                            }else if(type[0] == 'video'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file mb-3 p-1 m-1">
                                    <input type="hidden" class="images" value="${val.image}">
                                    <input type="hidden" class="size" value="${val.size}">
                                    <input type="hidden" class="type" value="${val.type}">
                                    <input type="hidden" class="original_name" value="${val.original_name}">
                                    <video width="130px" controls>
                                    <source src="{{Config('constants.MESSAGE_IMAGES_IMAGE_PATH')}}/${val.image}" type="video/mp4">
                                    </video>
                                    <a class="close_file" data-image="${val.image}" href="javascript:void(0)"><i class="fal fa-times"></i></a>
                                </button>`;
                            }else if(type[0] == 'application'){
                                var html = 
                                `<button type="download" class="btn  bg_none_dowload_file mb-3 p-1 m-1">
                                    <input type="hidden" class="images" value="${val.image}">
                                    <input type="hidden" class="size" value="${val.size}">
                                    <input type="hidden" class="type" value="${val.type}">
                                    <input type="hidden" class="original_name" value="${val.original_name}">
                                    <img width="80%" height="50px" style="object-fit:cover;" src="{{asset('public/frontend/img/fileatach.png')}}">
                                    <a class="close_file" data-image="${val.image}" href="javascript:void(0)"><i class="fal fa-times"></i></a>
                                </button>`;
                            }
                            $('.all_image_show').append(html);
                            $('.send_message_not_null').hide();
                            $('.loader-wrapper').hide();
                            $('.overlay').hide();
                        }else{
                            $('.send_message_not_null').show();
                            $('.send_message_not_null').html('{{trans("messages.browse_to_upload_a_valid_extension")}}');
                        }
                    });
                }
            });
            }else{
            $('.send_message_not_null').show();
            $('.send_message_not_null').html('{{trans("messages.browse_to_upload_a_valid_extension")}}');
            }
        });

        $('body').on('click', '.close_file', function() {

            var removeElement = $(this).parent('.bg_none_dowload_file');
            var image_delete  = $(this).data("image");
            var path          = 'MESSAGE_IMAGES_ROOT_PATH';
            var _token        = "{{csrf_token()}}";
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
                    url:  '{{ route('portfolio_image_add_delete') }}',
                    data: {image:image_delete,path:path,_token:_token},
                    success: function(response) {
                        if(response == 'success'){
                            removeElement.remove();
                            document.getElementById('chat_image_select').value = '';
                        }
                        $('.loader-wrapper').hide();
                        $('.overlay').hide();
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

        

    function send_message_button(){
        var form_data          = new FormData();
        var message            = $('#chatInput').val();
        var req                = $('.send_message_not_null');
        var host_id            = $('.sendSmsButton').attr('data');
        var property_id        = $('.propertyIdOfChat').val();
        var user_id            = $('#message_by_user_id').val();


        if(message == ''){
            $('.message_not_null').text('{{trans("messages.please_write_something")}}');
        }else{
            $('.message_not_null').text('');
        }


        form_data.append("_token",'{{csrf_token()}}');
        if(message != ''){
            req.hide();

            $('.images').map(function(){
            return form_data.append("images[]",this.value);
            }).get();

            $('.original_name').map(function(){
            return form_data.append("original_name[]",this.value);
            }).get();

            $('.size').map(function(){
            return form_data.append("size[]",this.value);
            }).get();

            form_data.append("message",message);
            form_data.append("receiver_id",host_id);
            form_data.append("user_id",user_id);
            form_data.append("property_id",0);

            $('.loader-wrapper').show();
            $('.overlay').show();
            $.ajax({
                type: "POST",
                url: '{{ route('host.sendSms') }}',
                data: form_data,
                contentType: false,
                processData: false,
                success: function(response) {

                    if(response.status == 'success'){
                        $('.loader-wrapper').hide();
                        $('.overlay').hide();
                        $("#singelChatModal").modal('hide');
                        show_message("{{trans("messages.message_send_to_host_successfully")}}", "success"); 
                    }
                }
            }); 

        }else{
            req.show();
            req.html('{{trans("messages.This field is required")}}')
        }
    }
    </script>