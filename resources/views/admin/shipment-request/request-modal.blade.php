@if($shipmentRequestDetails->SelectedShipmentOffers)
   <div id="appruval_Modal" class="modal fade" aria-modal="true" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="themeBtn-close alert alert-danger" data-dismiss="modal"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"></path></svg>
               </button>
               <h4 class="modal-title"></h4>
            </div>
            <form action="{{route("$model.approve_shipment_offer",array($shipmentRequestDetails->SelectedShipmentOffers->id,$shipmentRequestDetails->id))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data" id="appruval_Modal_form">
            @csrf
               <div class="modal-body">
                  <div class="row">
                     <div class="col-xl-12">
                        <div class="form-group">
                           <label for="truck_id">{{trans('messages.admin_common_Truck')}}</label><span class="text-danger"> * </span>
                           <select name="truck_id" class="form-control form-control-solid form-control-lg truck_id-class ">
                              <option value="">{{trans('messages.Select')}}</option>
                              @foreach($truckList as $truck)
                                 <option value="{{$truck->id}}" data-truck-driver-id="{{$truck->driver_id}}" data-truck-driver-name="{{$truck->name}}">{{$truck->truck_system_number}}</option>
                              @endforeach
                           </select>
                           <span class="truck-alert text-danger"></span>
                        </div>
                     </div>
                     <div class="col-xl-12">
                        <div class="form-group">
                           <label for="driver_id">{{trans('messages.admin_Truck_Driver')}}</label><span class="text-danger"> * </span>
                           <select name="driver_id" class="form-control form-control-solid form-control-lg driver_id-class ">
                              <option value="">{{trans('messages.Select')}}</option>
                              @foreach($free_driver as $truck)
                                 <option value="{{$truck->id}}" >{{$truck->name}}</option>
                              @endforeach
                           </select>
                           <span class="driver-alert text-danger"></span>
                        </div>
                     </div>

                     <div class="col-xl-12">
                        <div class="form-group">
                           <label for="schedule_shipment_time">{{trans('messages.admin_schedule_shipment_time')}}</label><span class="text-danger"></span>
                           <div class="input-group date" id="timepickerscheduletime" data-target-input="nearest">
                              <input type="text" name="start_time" class="form-control datetimepicker-input schedule_time-class" value="" data-target="#timepickerscheduletime" data-toggle="datetimepicker" id="schedule_start_time_box">
                              <div class="input-group-append">
                                    <span class="input-group-text">
                                       <i class="ki ki-clock"></i>
                                    </span>
                              </div>
                           </div>
                           <span class="schedule_time-alert text-danger"></span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="text-center d-block" style="margin-bottom: 12px;">
                  <div class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                     <div class="row">
                        <div class="col-6">
                           <button type="submit" class="btn btn-success font-weight-bold text-uppercase px-9 py-4 appruval_Modal_submit">
                              {{trans("messages.submit")}}
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
@endif
@if($shipmentRequestDetails->SelectedShipmentOffers)
   <div id="schedule_Modal" class="modal fade" aria-modal="true" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="themeBtn-close alert alert-danger" data-dismiss="modal"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"></path></svg>
               </button>
               <h4 class="modal-title"></h4>
            </div>
            <form action="{{route("$model.shipment-schedule",array($shipmentRequestDetails->SelectedShipmentOffers->id,$shipmentRequestDetails->id))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data" id="shipment-schedule-form">
               @csrf
               <div class="modal-body">
                  <div class="row">
                     <div class="col-xl-12">
                        <div class="form-group">
                           <label for="duration">{{trans('messages.duration')}}</label><span class="text-danger"></span>
                           <input type="text" class="form-control form-control-solid form-control-lg" value="{{$shipmentRequestDetails->SelectedShipmentOffers->duration}}" readonly>
                        </div>
                     </div>
                     <div class="col-xl-12">
                        <div class="form-group">
                           <label for="start_date">{{trans('messages.start_date')}}</label><span class="text-danger"></span>
                           <input type="text" class="form-control form-control-solid form-control-lg" value="{{ \Carbon\Carbon::parse($shipmentRequestDetails->SelectedShipmentOffers->start_date)->format('d/m/Y') }}" readonly>
                        </div>
                     </div>
                     <div class="col-xl-12">
                        <div class="form-group">
                           <label for="start_time">{{trans('messages.start_time')}}</label><span class="text-danger"></span>
                           <div class="input-group date" id="timepickerfromto" data-target-input="nearest">
                              <input type="text" name="start_time" class="form-control datetimepicker-input" value="" data-target="#timepickerfromto" data-toggle="datetimepicker" id="start_time_box">
                              <div class="input-group-append">
                                    <span class="input-group-text">
                                       <i class="ki ki-clock"></i>
                                    </span>
                              </div>
                              <div class="invalid-feedback start_time-error"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="text-center d-block" style="margin-bottom: 12px;">
                  <div class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                     <div class="row">
                        <div class="col-6">
                           <button button="" type="submit"  class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                              {{trans("messages.submit")}}
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
@endif

@if($shipmentRequestDetails->shipmentDriverScheduleDetails);
   <div id="schedule_end_Modal" class="modal fade" aria-modal="true" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="themeBtn-close alert alert-danger" data-dismiss="modal"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"></path></svg>
               </button>
               <h4 class="modal-title"></h4>
            </div>
            <form action="{{route("$model.shipment-schedule-end",array($shipmentRequestDetails->shipmentDriverScheduleDetails->id))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
               @csrf
               <div class="modal-body">
                  <div class="row">
                     <div class="col-xl-12">
                        <div class="form-group">
                           <label for="comment">{{trans('messages.comment')}}</label><span class="text-danger"></span>
                           <input type="text" name="comment" class="form-control  " value="" >
                        </div>
                     </div>
                  
               

               <div class="col-xl-12">
                  <div class="form-group">
                     <label for="estimated_time">{{ trans('messages.estimated_hours') }}</label><span class="text-danger"></span>
                     <div class="input-group date" id="timepickertimeto" data-target-input="nearest">
                        <input type="text" name="estimated_time" class="form-control datetimepicker-input " value="" data-target="#timepickertimeto" data-toggle="datetimepicker">
                        <div class="input-group-append">
                              <span class="input-group-text">
                                 <i class="ki ki-clock"></i>
                              </span>
                        </div>
                     </div>
                  </div>
               </div>
               </div>
               </div>

               <div class="text-center d-block" style="margin-bottom: 12px;">
                  <div class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                     <div class="row">
                        <div class="col-6">
                           <button button="" type="submit"  class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                              {{trans("messages.submit")}}
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
@endif
<div id="upload_invoice_Modal" class="modal fade" aria-modal="true" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="themeBtn-close alert alert-danger" data-dismiss="modal"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"></path></svg>
            </button>
            <h4 class="modal-title"></h4>
         </div>
         <form action="{{route("$model.send-shipment-invoice",array($shipmentRequestDetails->id))}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
               <div class="row">
                  <div class="col-xl-12">
                     <div class="form-group">
                        <label for="invoice">{{trans('messages.invoice_file')}}</label><span class="text-danger"></span>
                        <input type="file" name="invoice" class="form-control form-control-solid form-control-lg" accept="image/png, image/jpeg, image/jpg, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                     </div>
                  </div>
                   <div class="col-xl-12">
                     <div class="form-group">
                        <label for="invoice">{{trans('messages.price')}}</label><span class="text-danger"></span>
                        <input type="number" name="invoice_price" class="form-control form-control-solid form-control-lg">
                     </div>
                  </div>
               </div>
            </div>
            <div class="text-center d-block" style="margin-bottom: 12px;">
               <div class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                  <div class="row">
                     <div class="col-6">
                        <button button="" type="submit"  class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                           {{trans("messages.submit")}}
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<div id="uploadFile_Modal" class="modal fade" aria-modal="true" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="themeBtn-close alert alert-danger" data-dismiss="modal"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6667 16.302L2.00004 2M16.6667 2L2 16.302" stroke="white" stroke-width="2.5" stroke-linecap="round"></path></svg>
            </button>
            <h4 class="modal-title"></h4>
         </div>
         <form action="{{route("$model.upload-files")}}" method="post" class="mws-form" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="stop_id" id="stop_id">
            <div class="modal-body">
               <div class="row">
                  <div class="col-xl-12">
                     <div class="form-group">
                        <label for="invoice">{{trans('messages.upload_certificate')}}</label><span class="text-danger"></span>
                        <input type="file" name="certificate" class="form-control form-control-solid form-control-lg" accept="image/png, image/jpeg, image/jpg">
                     </div>
                  </div>
                  <div class="col-xl-12">
                     <div class="form-group">
                        <label for="invoice">{{trans('messages.upload_signature')}}</label><span class="text-danger"></span>
                        <input type="file" name="signature" class="form-control form-control-solid form-control-lg" accept="image/png, image/jpeg, image/jpg">
                     </div>
                  </div>
               </div>
            </div>
            <div class="text-center d-block" style="margin-bottom: 12px;">
               <div class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                  <div class="row">
                     <div class="col-6">
                        <button button="" type="submit"  class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
                           {{trans("messages.submit")}}
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
   $('body').on("click",".submit_appruval",function () {
      $('#appruval_Modal').modal("show");
   });
   $('body').on("click",".submit_schedule",function () {
      $('#schedule_Modal').modal("show");
   });
   $('body').on("click",".submit_schedule_end",function () {
      $('#schedule_end_Modal').modal("show");
   });
   $('body').on("click",".submit_shipment_invoice",function () {
      $('#upload_invoice_Modal').modal("show");
   });
   $('body').on("click", "#uploadFile", function() {
      $('#uploadFile_Modal form').trigger("reset");
         var getId = $(this).data("id");
         $("#stop_id").val(getId);

      $('#uploadFile_Modal').modal("show");

   });

   $(".appruval_Modal_submit").on("click", function(e){
      e.preventDefault();

      var truck_id                = $(".truck_id-class").val();
      var driver_id               = $(".driver_id-class").val();
      var schedule_start_time_box = $(".schedule_time-class").val();

      if(truck_id == '' || driver_id == '' || schedule_start_time_box == ''){
         $(".truck-alert").html("{{trans('messages.This field is required')}}");
         $(".driver-alert").html("{{trans('messages.This field is required')}}");
         $(".schedule_time-alert").html("{{trans('messages.This field is required')}}");
      }
      else if(truck_id !== '' && driver_id !== '' && schedule_start_time_box != ''){
         $(".truck-alert").html('');
         $(".driver-alert").html('');
         $(".schedule_time-alert").html('');
         $("#appruval_Modal_form").submit();
      }

   });

    $('body').on("change",".truck_id-class",function () {
       var selectedOption = $(this).find(':selected');
       var truckDriverId = selectedOption.data('truck-driver-id');
       var truckDriverName = selectedOption.data('truck-driver-name');
       if(truckDriverId>0){
          $('.driver_id-class').append(`<option class="removable" value="${truckDriverId}">${truckDriverName}</option>`).val(truckDriverId);
       }else{
          $('.driver_id-class option.removable').remove();
       }
    });
    $('#timepickerfromto').datetimepicker({
      format: 'hh:mm A'
    });

    $('#timepickerscheduletime').datetimepicker({
      format: 'hh:mm A'
    });
    
     $('#timepickertimeto').datetimepicker({
      format: 'HH:mm'
    });

   $(document).ready(function(){
      $('#shipment-schedule-form').submit(function(event){
         $(".is-invalid").removeClass("is-invalid");
         if($("#start_time_box").val().trim() == ""){
               $("#start_time_box").addClass("is-invalid");
               $(".start_time-error").html('{{trans("messages.This field is required")}}');
               return false;
         }

      });
   });

 </script>