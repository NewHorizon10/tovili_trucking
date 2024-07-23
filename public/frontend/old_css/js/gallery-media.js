
// play Video Popup

$(document).ready(function () {
    // Play Video
    var videoSrc;
    $('body').on("click",".video-btn",function () {
        videoSrc = $(this).data("src");
        $('#videoModal').modal("show");
        $("#video").attr('src', videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0");
    });

    // $('#videoModal').on('shown.bs.modal', function (e) {
    //     $("#video").attr('src', $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0");
    // })

    // $('#videoModal').on('hide.bs.modal', function (e) {
    //     $("#video").attr('src', $videoSrc);
    // })
});



// Drop video and Video
let dropAreaa = document.getElementById("drop-areaa")
;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
  dropAreaa.addEventListener(eventName, preventDefaults, false)   
  document.body.addEventListener(eventName, preventDefaults, false)
})

;['dragenter', 'dragover'].forEach(eventName => {
  dropAreaa.addEventListener(eventName, highlight, false)
})

;['dragleave', 'drop'].forEach(eventName => {
  dropAreaa.addEventListener(eventName, unhighlight, false)
})

dropAreaa.addEventListener('drop', handleDrops, false)

function preventDefaults (e) {
  e.preventDefault()
  e.stopPropagation()
}

function highlight(e) {
  dropAreaa.classList.add('highlight')
}

function unhighlight(e) {
  dropAreaa.classList.remove('active')
}

let uploadProgress = []
let progressBar = document.getElementById('progress-bar')

function handleDrops(e) {
  var dt = e.dataTransfer
  var files = dt.files
  const input = document.getElementById('ImageUploads')
    for (let i = 0; i < files.length; i++) {
        const file = files[i]
        dt.items.add(file);
    }
  input.files = dt.files
  GalleryImages(files)
}

function initializeProgress(numFiles) {
  progressBar.value = 0
  uploadProgress = []

  for(let i = numFiles; i > 0; i--) {
    uploadProgress.push(0)
  }
}


function GalleryImages(files){
    var fileName = document.getElementById("ImageUploads").value;
    var ext      = files[0].type.split('/');
    if (ext[0] == "image" || ext[0] == "video"){
        files = [...files]
        initializeProgress(files.length)
        var form_data = new FormData();
        var totalfiles = document.getElementById('ImageUploads').files.length;
        for (var index = 0; index < totalfiles; index++) {
            form_data.append("images[]", document.getElementById('ImageUploads').files[index]);                  
        }
        form_data.append("_token",_token);
        form_data.append("path",'GALLERY_MEDIA_IMAGE_ROOT_PATH');
        $('.loader-wrapper,.overlay').show();
        $.ajax({
            type:'POST',
            url: url+"/gallery-images-uploads",
            data: form_data,
            contentType: false,
            processData: false,
            success: function(data) {
                $.each(data,function(key,val) {
                    if (val.type){
                        if(val.type == 'video'){
                            var html = `<div class="gallery_item itemsappends video_block d-inline-flex">
                                        <input style="visibility: hidden;height:0px; position: absolute;" name="image[]" value="${val.image}">
                                        <input style="visibility: hidden;height:0px; position: absolute;" name="type[]" value="${val.type}">
                                        <div class="video-items">
                                            <video poster="${url+'/public/frontend/img/restaurantPlace.png'}">
                                                <source src="" type="video/mp4">
                                            </video>
                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-src="${url+'/public/uploads/gallery-image/'+val.image}" class="video-btn play_icon"> <i
                                                    class="fas fa-play"></i></a>

                                            <div class="gallery_action">
                                                <button type="button" class="dropdown-toggle action_btn ellipse-btn"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <svg width="5" height="30" viewBox="0 0 8 30" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="4" cy="4" r="4" fill="currentcolor" />
                                                        <circle cx="4" cy="15" r="4" fill="currentcolor" />
                                                        <circle cx="4" cy="26" r="4" fill="currentcolor" />
                                                    </svg>
                                                </button>
                                                <ul class="dropdown-menu action_droup dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item DeleteImage" data-image="${val.image}" href="javascript:void(0)">
                                                            <span>
                                                                <svg width="14" height="14" viewBox="0 0 10 10" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <g clip-path="url(#clip0_11_9049)">
                                                                        <path
                                                                            d="M8.78516 2.92969L8.26623 9.19404C8.22904 9.6458 7.84424 10 7.39076 10H2.60941C2.15594 10 1.77113 9.6458 1.73395 9.19404L1.21504 2.92969H8.78516ZM6.46521 8.82812C6.61857 8.82812 6.7476 8.70883 6.75732 8.55346L7.05029 3.82689C7.06031 3.66525 6.93758 3.52621 6.77621 3.51619C6.60885 3.50445 6.4758 3.62863 6.46551 3.79027L6.17254 8.51684C6.16217 8.68422 6.29461 8.82812 6.46521 8.82812ZM4.70713 8.53516C4.70713 8.69709 4.83816 8.82812 5.0001 8.82812C5.16203 8.82812 5.29307 8.69709 5.29307 8.53516V3.80859C5.29307 3.64666 5.16203 3.51562 5.0001 3.51562C4.83816 3.51562 4.70713 3.64666 4.70713 3.80859V8.53516ZM2.9499 3.82691L3.24287 8.55348C3.2525 8.70727 3.38068 8.83543 3.55357 8.82756C3.71494 8.81754 3.83768 8.6785 3.82766 8.51686L3.53469 3.79029C3.52467 3.62865 3.38305 3.51105 3.22398 3.51621C3.06262 3.52623 2.93988 3.66527 2.9499 3.82691Z"
                                                                            fill="#DA0200" />
                                                                        <path
                                                                            d="M1.19141 1.17188H2.94922V0.878906C2.94922 0.394258 3.34348 0 3.82812 0H6.17188C6.65652 0 7.05078 0.394258 7.05078 0.878906V1.17188H8.80859C9.13221 1.17188 9.39453 1.4342 9.39453 1.75781C9.39453 2.08139 9.13221 2.34375 8.80859 2.34375C6.11395 2.34375 3.88596 2.34375 1.19141 2.34375C0.867793 2.34375 0.605469 2.08139 0.605469 1.75781C0.605469 1.4342 0.867793 1.17188 1.19141 1.17188ZM3.53516 1.17188H6.46484V0.878906C6.46484 0.717266 6.33352 0.585938 6.17188 0.585938H3.82812C3.66648 0.585938 3.53516 0.717266 3.53516 0.878906V1.17188Z"
                                                                            fill="#DA0200" />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_11_9049">
                                                                            <rect width="14" height="14" fill="white"
                                                                                transform="matrix(-1 0 0 1 10 0)" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </span>
                                                            Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>`;
                    }else if(val.type == 'image'){
                            var html = `<div class="gallery_item itemsappends d-inline-flex">
                                            <input style="visibility: hidden;height:0px; position: absolute;" name="image[]" value="${val.image}">
                                            <input style="visibility: hidden;height:0px; position: absolute;" name="type[]" value="${val.type}">
                                            <figure>
                                                <img src="${url+'/public/uploads/gallery-image/'+val.image}" alt="">
                                            </figure>
                                            <div class="gallery_action">
                                                <button type="button" class="dropdown-toggle action_btn ellipse-btn"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <svg width="5" height="30" viewBox="0 0 8 30" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="4" cy="4" r="4" fill="currentcolor" />
                                                        <circle cx="4" cy="15" r="4" fill="currentcolor" />
                                                        <circle cx="4" cy="26" r="4" fill="currentcolor" />
                                                    </svg>
                                                </button>
                                                <ul class="dropdown-menu action_droup dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item DeleteImage" data-image="${val.image}" href="javascript:void(0)">
                                                            <span>
                                                                <svg width="14" height="14" viewBox="0 0 10 10" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <g clip-path="url(#clip0_11_9049)">
                                                                        <path
                                                                            d="M8.78516 2.92969L8.26623 9.19404C8.22904 9.6458 7.84424 10 7.39076 10H2.60941C2.15594 10 1.77113 9.6458 1.73395 9.19404L1.21504 2.92969H8.78516ZM6.46521 8.82812C6.61857 8.82812 6.7476 8.70883 6.75732 8.55346L7.05029 3.82689C7.06031 3.66525 6.93758 3.52621 6.77621 3.51619C6.60885 3.50445 6.4758 3.62863 6.46551 3.79027L6.17254 8.51684C6.16217 8.68422 6.29461 8.82812 6.46521 8.82812ZM4.70713 8.53516C4.70713 8.69709 4.83816 8.82812 5.0001 8.82812C5.16203 8.82812 5.29307 8.69709 5.29307 8.53516V3.80859C5.29307 3.64666 5.16203 3.51562 5.0001 3.51562C4.83816 3.51562 4.70713 3.64666 4.70713 3.80859V8.53516ZM2.9499 3.82691L3.24287 8.55348C3.2525 8.70727 3.38068 8.83543 3.55357 8.82756C3.71494 8.81754 3.83768 8.6785 3.82766 8.51686L3.53469 3.79029C3.52467 3.62865 3.38305 3.51105 3.22398 3.51621C3.06262 3.52623 2.93988 3.66527 2.9499 3.82691Z"
                                                                            fill="#DA0200" />
                                                                        <path
                                                                            d="M1.19141 1.17188H2.94922V0.878906C2.94922 0.394258 3.34348 0 3.82812 0H6.17188C6.65652 0 7.05078 0.394258 7.05078 0.878906V1.17188H8.80859C9.13221 1.17188 9.39453 1.4342 9.39453 1.75781C9.39453 2.08139 9.13221 2.34375 8.80859 2.34375C6.11395 2.34375 3.88596 2.34375 1.19141 2.34375C0.867793 2.34375 0.605469 2.08139 0.605469 1.75781C0.605469 1.4342 0.867793 1.17188 1.19141 1.17188ZM3.53516 1.17188H6.46484V0.878906C6.46484 0.717266 6.33352 0.585938 6.17188 0.585938H3.82812C3.66648 0.585938 3.53516 0.717266 3.53516 0.878906V1.17188Z"
                                                                            fill="#DA0200" />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_11_9049">
                                                                            <rect width="14" height="14" fill="white"
                                                                                transform="matrix(-1 0 0 1 10 0)" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </span>
                                                            Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>`;
                        }
                        $('.GalleryImagesAppends').append(html);
                        $('.loader-wrapper,.overlay').hide();
                    }else{
                        toastr.error("Browse to upload a valid File with images,video extension");
                    }
                });
            }
        });
    }else{
        toastr.error("Browse to upload a valid File with images,video extension");
    }   
    
}
// End Drop video and Video

$('body').on('click', '.DeleteImage', function() {
    var removeElement =  $(this).parents('.itemsappends');
    var ImageName     =  $(this).attr('data-image');
    var path          =  'GALLERY_MEDIA_IMAGE_ROOT_PATH';
    Swal.fire({
        title: "Are you sure?",
        text: "Want to delete this ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "No, cancel",
        reverseButtons: true
    }).then(function(result) {
        if (result.value) {  
            $('.loader-wrapper,.overlay').show();
            $.ajax({
                type: "POST",
                url: url + "/soft-delete-files",
                data: {image:ImageName,path:path,_token:_token},
                success: function(response) {
                    if(response == 'success'){
                        removeElement.remove();
                    }
                    $('.loader-wrapper,.overlay').hide();
                }
            }); 
        }
    });
});
//End Delete element 