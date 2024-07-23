
 <div id="extendModal" class="modal fade" aria-modal="true" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="themeBtn-close alert alert-danger" data-dismiss="modal"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"></path></svg>
            </button>
            <h4 class="modal-title"></h4>
         </div>
         <form action="{{route('truck-company-plan-expiry-extend')}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
            @csrf

            <div class="modal-body">
             <div class="row">
               <input type="hidden" name="truck_company_id" value="{{ucwords($userDetails->userCompanyInformation->user_id ?? '')}}">
                <div class="col-lg-12 ">
                    <label>{{trans("messages.admin_expiry_date")}}</label>
                    <div class="input-group date" id="datepicker" data-target-input="nearest">
                        {{ Form::text('expiry_date',((isset($searchVariable['expiry_date'])) ? $searchVariable['expiry_date'] : ''), ['class' => ' form-control datetimepicker-input expiry_date_picker','placeholder'=>trans("messages.admin_expiry_date"),'data-target'=>'#datepicker','data-toggle'=>'datetimepicker']) }}
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="ki ki-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <p class="dateError pt-3 text-danger"></p>
                </div>
             </div>
            </div>
            <div class="text-center d-block" style="margin-bottom: 12px;">
               <div class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                  <div class="row">
                     <div class="col-6">
                        <button button="" type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4 formSubmitButton">
                           Submit
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
   $(document).ready(function(){

      @if($userTruckCompanySubscription)
         $('#datepicker').datetimepicker({
            format: 'DD-MMM, YYYY',
            minDate: '{{$userTruckCompanySubscription->end_time}}',
         });
      @endif
      
      $(".formSubmitButton").on("click", function(event){
         var dateValue = $(".expiry_date_picker").val();
         if(dateValue === '' || dateValue === null){
            event.preventDefault();
            $(".dateError").html("{{trans('messages.This field is required')}}");
         }
      });

   });
</script>