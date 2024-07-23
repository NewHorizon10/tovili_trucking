<div class="modal fade rating_raview_box" tabindex="-1" id="viewReviewModal" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered clander_popup">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('messages.review') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          
                    </button>
            </div>
            <form method="post" id="review_submit" action="{{route('host.submit.review')}}">
                @csrf
                <div class="modal-body">
                    <div class="pay-dtls">
                        @php $user = auth()->user(); @endphp
                        <div class="row mb-3">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.professionality') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input readonly class="star star-5" id="star-5-21" type="radio" value="5" name="professionality" @if($shipment->shipmentRatingReviews->professionality == 5) checked @endif/>
                                        <label class="star star-5"></label>
                                        <input readonly class="star star-4" id="star-4-21" type="radio" value="4" name="professionality" @if($shipment->shipmentRatingReviews->professionality == 4) checked @endif/>
                                        <label class="star star-4"></label>
                                        <input readonly class="star star-3" id="star-3-21" type="radio" value="3" name="professionality" @if($shipment->shipmentRatingReviews->professionality == 3) checked @endif/>
                                        <label class="star star-3"></label>
                                        <input readonly class="star star-2" id="star-2-21" type="radio" value="2" name="professionality" @if($shipment->shipmentRatingReviews->professionality == 2) checked @endif/>
                                        <label class="star star-2"></label>
                                        <input readonly class="star star-1" id="star-1-21" type="radio" value="1" name="professionality" @if($shipment->shipmentRatingReviews->professionality == 1) checked @endif/>
                                        <label class="star star-1"></label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.meet_schedule') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input readonly class="star star-5" id="star-5-22" type="radio" value="5" name="meet_schedule" @if($shipment->shipmentRatingReviews->meet_schedule == 5) checked @endif/>
                                        <label class="star star-5"></label>
                                        <input readonly class="star star-4" id="star-4-22" type="radio" value="4" name="meet_schedule" @if($shipment->shipmentRatingReviews->meet_schedule == 4) checked @endif/>
                                        <label class="star star-4"></label>
                                        <input readonly class="star star-3" id="star-3-22" type="radio" value="3" name="meet_schedule" @if($shipment->shipmentRatingReviews->meet_schedule == 3) checked @endif/>
                                        <label class="star star-3"></label>
                                        <input readonly class="star star-2" id="star-2-22" type="radio" value="2" name="meet_schedule" @if($shipment->shipmentRatingReviews->meet_schedule == 2) checked @endif/>
                                        <label class="star star-2"></label>
                                        <input readonly class="star star-1" id="star-1-22" type="radio" value="1" name="meet_schedule" @if($shipment->shipmentRatingReviews->meet_schedule == 1) checked @endif/>
                                        <label class="star star-1"></label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.driver_rating') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input readonly class="star star-5" id="star-5-2-2" type="radio" value="5" name="driver_rating" @if($shipment->shipmentRatingReviews->driver_rating == 5) checked @endif/>
                                        <label class="star star-5"></label>
                                        <input readonly class="star star-4" id="star-4-2-2" type="radio" value="4" name="driver_rating" @if($shipment->shipmentRatingReviews->driver_rating == 4) checked @endif/>
                                        <label class="star star-4"></label>
                                        <input readonly class="star star-3" id="star-3-2-2" type="radio" value="3" name="driver_rating" @if($shipment->shipmentRatingReviews->driver_rating == 3) checked @endif/>
                                        <label class="star star-3"></label>
                                        <input readonly class="star star-2" id="star-2-2-2" type="radio" value="2" name="driver_rating" @if($shipment->shipmentRatingReviews->driver_rating == 2) checked @endif/>
                                        <label class="star star-2"></label>
                                        <input readonly class="star star-1" id="star-1-2-2" type="radio" value="1" name="driver_rating" @if($shipment->shipmentRatingReviews->driver_rating == 1) checked @endif/>
                                        <label class="star star-1"></label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="form_input_popup ratingRow">
                                <label class="payText">{{ trans('messages.overall_rating') }}:</label>
                                <div class="feedbackstars">
                                    <div class="form-inline feedbackstars_flex">
                                        <input readonly class="star star-5" id="star-5-2" type="radio" value="5" name="overall_rating" @if($shipment->shipmentRatingReviews->overall_rating == 5) checked @endif/>
                                        <label class="star star-5"></label>
                                        <input readonly class="star star-4" id="star-4-2" type="radio" value="4" name="overall_rating" @if($shipment->shipmentRatingReviews->overall_rating == 4) checked @endif/>
                                        <label class="star star-4"></label>
                                        <input readonly class="star star-3" id="star-3-2" type="radio" value="3" name="overall_rating" @if($shipment->shipmentRatingReviews->overall_rating == 3) checked @endif/>
                                        <label class="star star-3"></label>
                                        <input readonly class="star star-2" id="star-2-2" type="radio" value="2" name="overall_rating" @if($shipment->shipmentRatingReviews->overall_rating == 2) checked @endif/>
                                        <label class="star star-2"></label>
                                        <input readonly class="star star-1" id="star-1-2" type="radio" value="1" name="overall_rating" @if($shipment->shipmentRatingReviews->overall_rating == 1) checked @endif/>
                                        <label class="star star-1"></label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="pay-dtls  mt-3">
                            @if($shipment->shipmentRatingReviews->photos->count())
                            <div class="row mb-12"><h4 class="popup_inner_heading">{{trans("messages.photos")}}<h4></div>
                                
                            <div class="row mb-3">
                                    @foreach($shipment->shipmentRatingReviews->photos as $images)
                                        <div class="col-md-3">
                                            <a class="fancybox-buttons" data-fancybox-group="button" href="{{$images->photo}}" target="_blank">
                                                <img src="{{$images->photo}}">
                                            </a>
                                        </div>
                                    @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="pay-dtls mt-4">
                            <div class="row mb-3">
                                <h4 class="popup_inner_heading">{{ trans('messages.review') }}:</h4>

                                <div class="col-12">
                                    {{$shipment->shipmentRatingReviews->review}}
                                </div>
                                <span id="review_error" class="text-danger"></span>
                            </div>
                        </div>
                    </div>

            </form>

        </div>
    </div>
</div>
<style>
  
</style>

<script>
   document.addEventListener("DOMContentLoaded", function() {
    // Passing option
    var myModal = new bootstrap.Modal(document.getElementById("viewReviewModal"), {
        keyboard: false
    });
	// Show modal
	var btn = document.getElementById(".transportRequestBtn");
    btn.addEventListener("click", function() {        
        myModal.show();
    });
});


    $(document).ready(function () {
        $.fn.view_review_modal = function (shipment_id) {
            $("#viewReviewModal").modal('show');
        }
    });


</script>