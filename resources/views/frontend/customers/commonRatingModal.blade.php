<div class="modal fade rating_raview_box" tabindex="-1" id="myModal" data-bs-backdrop="static ">
    <div class="modal-dialog modal-dialog-centered clander_popup">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('messages.review') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="review_submit" action="{{route('host.submit.review')}}">
                @csrf
                <div class="modal-body">
                    <div class="pay-dtls">
                        @php $user = auth()->user(); @endphp
                        <div class="row">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.professionality') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input class="star star-5" id="star-5-21" type="radio" value="5" name="professionality"/>
                                        <label class="star star-5" for="star-5-21"></label>
                                        <input class="star star-4" id="star-4-21" type="radio" value="4" name="professionality"/>
                                        <label class="star star-4" for="star-4-21"></label>
                                        <input class="star star-3" id="star-3-21" type="radio" value="3" name="professionality"/>
                                        <label class="star star-3" for="star-3-21"></label>
                                        <input class="star star-2" id="star-2-21" type="radio" value="2" name="professionality"/>
                                        <label class="star star-2" for="star-2-21"></label>
                                        <input class="star star-1" id="star-1-21" type="radio" value="1" name="professionality"/>
                                        <label class="star star-1" for="star-1-21"></label>
                                    </div>
                                </div>
                            </div>
                            <p class="text-danger text-danger-text error-professionality"></p>
                        </div>
                        <div class="row">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.meet_schedule') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input class="star star-5" id="star-5-22" type="radio" value="5" name="meet_schedule"/>
                                        <label class="star star-5" for="star-5-22"></label>
                                        <input class="star star-4" id="star-4-22" type="radio" value="4" name="meet_schedule"/>
                                        <label class="star star-4" for="star-4-22"></label>
                                        <input class="star star-3" id="star-3-22" type="radio" value="3" name="meet_schedule"/>
                                        <label class="star star-3" for="star-3-22"></label>
                                        <input class="star star-2" id="star-2-22" type="radio" value="2" name="meet_schedule"/>
                                        <label class="star star-2" for="star-2-22"></label>
                                        <input class="star star-1" id="star-1-22" type="radio" value="1" name="meet_schedule"/>
                                        <label class="star star-1" for="star-1-22"></label>
                                    </div>
                                </div>
                            </div>
                            <p class="text-danger text-danger-text error-meet_schedule"></p>
                        </div>

                        <div class="row mb-3">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.driver_rating') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input class="star star-5" id="star-5-2-2" type="radio" value="5" name="driver_rating"/>
                                        <label class="star star-5" for="star-5-2-2"></label>
                                        <input class="star star-4" id="star-4-2-2" type="radio" value="4" name="driver_rating"/>
                                        <label class="star star-4" for="star-4-2-2"></label>
                                        <input class="star star-3" id="star-3-2-2" type="radio" value="3" name="driver_rating"/>
                                        <label class="star star-3" for="star-3-2-2"></label>
                                        <input class="star star-2" id="star-2-2-2" type="radio" value="2" name="driver_rating"/>
                                        <label class="star star-2" for="star-2-2-2"></label>
                                        <input class="star star-1" id="star-1-2-2" type="radio" value="1" name="driver_rating"/>
                                        <label class="star star-1" for="star-1-2-2"></label>
                                    </div>
                                </div>
                            </div>
                            <p class="text-danger text-danger-text error-driver_rating"></p>
                        </div>

                        <div class="row mb-3">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.overall_rating') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input class="star star-5" id="star-5-2" type="radio" value="5" name="overall_rating"/>
                                        <label class="star star-5" for="star-5-2"></label>
                                        <input class="star star-4" id="star-4-2" type="radio" value="4" name="overall_rating"/>
                                        <label class="star star-4" for="star-4-2"></label>
                                        <input class="star star-3" id="star-3-2" type="radio" value="3" name="overall_rating"/>
                                        <label class="star star-3" for="star-3-2"></label>
                                        <input class="star star-2" id="star-2-2" type="radio" value="2" name="overall_rating"/>
                                        <label class="star star-2" for="star-2-2"></label>
                                        <input class="star star-1" id="star-1-2" type="radio" value="1" name="overall_rating"/>
                                        <label class="star star-1" for="star-1-2"></label>
                                    </div>
                                </div>
                            </div>
                            <p class="text-danger text-danger-text error-overall_rating"></p>
                        </div>
                        <div class="pay-dtls">
                            <div class="row mb-3 p-2">
                                <div class="dropzone" id="drop-areaa" data-max-file="5" data-max-file-messages="You can upload up to 5 files">
                                    <div class="GalleryImagesAppends ">
                                        <div class="gallery_item itemsappends d-inline-flex add_img_upload">
                                            <label class="text-center dz-message needsclick" for="ImageUploads"
                                                style="color: #fff;">
                                                <div class="dz-message needsclick">
                                                    <span class="dropzoneText">
                                                        <span class="svg-files">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="32"
                                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-6 h-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                            </svg>

                                                        </span>
                                                    </span>
                                                </div> <span
                                                    style="color:#0f30b6;">{{trans("messages.upload_here")}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <input style="visibility: hidden;height:0px; position: absolute;" type="file" accept="image/jpg, image/jpeg, image/png" onchange="GalleryImages(this.files)" name="" multiple id="ImageUploads">
                                    <progress hidden id="progress-bar" max="100" value="0"></progress>
                                </div>
                            </div>
                        </div>

                        <div class="pay-dtls mt-3">
                            <div class="row mb-3">
                                <h4 class="popup_inner_heading">{{ trans('messages.write_a_review') }}:</h4>

                                <div class="col-12">
                                    <textarea id="review" name="review" rows="4" cols="45"></textarea>
                                </div>
                                <span id="review_error" class="text-danger"></span>
                            </div>
                        </div>
                        <input type="hidden" name="shipment_id" id="shipment_id" value="" />
                        <button type="submit" class="btn popup_btn_theem transportRequestBtn">{{ trans('messages.submit') }}</button>
                    </div>

            </form>

        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    // Passing option
    var myModal = new bootstrap.Modal(document.getElementById("myModal"), {
        keyboard: false
    });
	// Show modal
	var btn = document.getElementById(".transportRequestBtn");
    btn.addEventListener("click", function() {        
        myModal.show();
    });
});


    var previewsContainer = document.getElementById('previewsContainer');


    function clearPreviews() {
        // Remove all file previews
        while (previewsContainer.firstChild) {
            previewsContainer.removeChild(previewsContainer.firstChild);
        }
    }
    function handleFileSelect(event) {
        event.stopPropagation();
        event.preventDefault();

        var files = event.target.files || event.dataTransfer.files;

        // Remove existing previews before adding new ones
        clearPreviews();

        // Process each file
        for (var i = 0, file; file = files[i]; i++) {
            // Perform necessary actions with the file (e.g., upload, display preview, etc.)
            displayFilePreview(file);
        }
    }
    var dropArea = document.getElementById('drop-areaa');
    document.getElementById('ImageUploads').addEventListener('change', handleFileSelect, false);
    $(document).ready(function () {
        $.fn.review_modal = function (shipment_id) {
            $("#review_submit").trigger("reset");
            $('#shipment_id').val(shipment_id);
            // $('#bookings_id').val(booking_id);
            $("#review_error").text('');
            $("#myModal").modal('show');
        }
    });
    $("#review_submit").submit(function(){
        var professionality = $("input[name=professionality]:checked").val();
        var meet_schedule = $("input[name=meet_schedule]:checked").val();
        var overall_rating = $("input[name=overall_rating]:checked").val();
        var driver_rating = $("input[name=driver_rating]:checked").val();
        var flag = false;
        
        if (typeof professionality === "undefined"){
            $(".error-professionality").html("{{trans('messages.at_least_one_star_is_required')}}")
            flag = true;
        }else{
            $(".error-professionality").html("");
        }
        if (typeof meet_schedule === "undefined"){
            $(".error-meet_schedule").html("{{trans('messages.at_least_one_star_is_required')}}")
            flag = true;
        }else{
            $(".error-meet_schedule").html("");
        }
        if (typeof overall_rating === "undefined"){
            $(".error-overall_rating").html("{{trans('messages.at_least_one_star_is_required')}}")
            flag = true;
        }else{
            $(".error-overall_rating").html("");
        }
        if (typeof driver_rating === "undefined"){
            $(".error-driver_rating").html("{{trans('messages.at_least_one_star_is_required')}}")
            flag = true;
        }else{
            $(".error-driver_rating").html("");
        }

        if(flag){
            return false;
        }
    });


    function displayFilePreview(file) {
        var reader = new FileReader();

        reader.onload = function(event) {
            var filePreview = document.createElement('div');
            filePreview.className = 'file-preview';

            // Check the file type and create appropriate preview elements
            if (file.type.startsWith('image/')) {
                var imagePreview = document.createElement('img');
                imagePreview.src = event.target.result;
                filePreview.appendChild(imagePreview);
            } else if (file.type.startsWith('video/')) {
                var videoPreview = document.createElement('video');
                videoPreview.src = event.target.result;
                videoPreview.controls = true;
                filePreview.appendChild(videoPreview);
            } else if (file.type === 'application/pdf') {
                var pdfPreview = document.createElement('embed');
                pdfPreview.src = event.target.result;
                pdfPreview.type = 'application/pdf';
                filePreview.appendChild(pdfPreview);
            }

            // Append the file preview to the previews container
            previewsContainer.appendChild(filePreview);
        };

        // Read the file as data URL
        reader.readAsDataURL(file);
    }
</script>