
<div class="modal fade popupDesing" tabindex="-1" id="sendPproposalModal" data-bs-backdrop="static ">
  <div class="modal-dialog modal-dialog-centered clander_popup">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">{{ trans('messages.Send Proposal') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" id="proposal_form_submit" action="{{route('business-send-proposal')}}">
              @csrf
              <div class="modal-body">
                  <div class="pay-dtls">
                      <div class="pay-dtls mt-3">
                          @if(empty(auth()->user()->email))
                              <div class="row">
                                  <div class="col-md-6">
                                  <div class="form-group">
                                      <label class="form-label">{{ trans('messages.name') }}</label>
                                      <div class="col-12">
                                          <input type="text" name="proposalName" class="5-form-fields form-control " data-is-required="1" style="width: 100%;border-color: #D9D9D9;border-radius: 3px;" value="">
                                      </div>
                                      <span id="" class="error-proposalName text-danger"></span>
                                  </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-group">
                                      <label class="form-label">{{ trans('messages.email') }}</label>
                                      <div class="col-12">
                                          <input type="text" name="proposalEmail" class="5-form-fields form-control " data-is-required="1" data-type="email" style="width: 100%;border-color: #D9D9D9;border-radius: 3px;" value="">
                                      </div>
                                      <span id="" class="error-proposalEmail text-danger"></span>
                                  </div>
                                  </div>
                              
                          @endif
                          

                          <!-- <h4 class="popup_inner_heading">{{ trans('messages.write_a_message') }}:</h4> -->

                              <div class="col-12">
                                  <div class="form-group">
                                  <label class="form-label">{{ trans('messages.write_a_message') }}</label>
                                  <textarea class="5-form-fields  form-control"  name="proposalMessage" rows="4" cols="45" class="5-form-fields" data-is-required="1"></textarea>
                                  <span id="" class="error-proposalMessage text-danger"></span>
                              </div>
                              </div>
                           <div class="col-12">
                              <button type="button" class="btn popup_btn_theem transportRequestBtn send-proposal-submit" style="padding: 10px 20px;background: #FF7C03;border-radius: 6px;color: #fff;font-weight: 500;">{{ trans('messages.submit') }}</button>
                           </div>
                             
                          </div>
                      </div>
                  </div>
              </div>  
          </form>

      </div>
  </div>
</div>

<script>
  function openModal() {
      $('#sendPproposalModal').modal('show'); // Show the modal

  
      $('.modal-hide-button').click(function() {
          $('.modal').modal('hide');
      });
  }
  
  $.fn.checkProposalValidation = function(nextStepNumber) {
          // return false;
          $("." + nextStepNumber + "-form-fields").removeClass("is-invalid");
          var flag = false;
          $("." + nextStepNumber + "-form-fields").each(function() {
              var str_name = $(this).attr("name");
              str_name = str_name.replace('[', '').replace(']', '');
              str_name = str_name.replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '');
              $(".error-" + str_name).html("");
              if ($(this).data("type") == "same") {
                  if ($(this).val() != $("input[name='" + $(this).data("same-with") + "']").val()) {
                      flag = true;
                      $(".error-" + $(this).attr("name")).html("Sa valeur doit être la même que la valeur de " + ($("input[name='" + $(this).data("same-with") + "']").data("name")));
                      $(this).addClass("is-invalid");

                  }
              }
              if (($(this).val() == "" && $(this).attr("data-is-required") == "1") || ($(this).attr('type') == "checkbox" && $("input[name='"+$(this).attr('name')+"']:checked").length == 0 && $(this).attr("data-is-required") == "1") ) {
                  flag = true;
                  var str_name = $(this).attr("name");
                  str_name = str_name.replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '').replace('[', '').replace(']', '');
                  $(".error-" + str_name).html("{{ trans('messages.This field is required') }}");
                  $(this).addClass("is-invalid");
              }
              if($(this).val() != "" && $(this).data("type") == "email"){
                  var regEx = /^(\w+([-+.'][^\s]\w+)*)?@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
                  var val = $(this).val();
                  if (!val.match(regEx)) {
                      flag = true;
                      $(".error-"+$(this).attr("name")).html("Invalid email");
                  }
              }
          });
          return flag;
      }

      $(document).ready(function() {
          $("body").on("click", ".send-proposal-submit", function() {
              var flag = $.fn.checkProposalValidation(5);
              if (flag) {
                  // $(window).scrollTop(0);
                  $(".is-invalid:first").focus();
                  return false;
              }
              $("#proposal_form_submit").submit();
          });
      });
</script>