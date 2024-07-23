@extends('frontend.layouts.truckCompanyLayout')
@section('extraCssLinks')
@stop
@section('content')
<style>
    .custom_check .checkbox:checked ~ .knobs:before {
    content: "{{trans('messages.yes')}}";
}
.custom_check .knobs:after {
    content: "{{trans('messages.no')}}";
}

</style>

<section class="form_section">
    <div class="container">
        <div class="outer_companyform_box">
            <div class="track_company_box track_company_page">
                <div class="white_form_theme">
                    <h1 class="form_page_title">
                        <span class="">{{trans('messages.Account Registration') }}</span>
                    </h1>
                    <div class="stepsProgressBar">
                        <ul class="list-unstyled multi-steps">
                            <li id="step-1" class="is-active">
                            <!--    <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div> -->
                            </li>
                            <li id="step-2" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            <li id="step-3" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            <li id="step-4" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            <li id="step-5" class="is-active">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li>
                            {{-- <li id="step-6">
                                <div class="progress-bar progress-bar--success">
                                <div class="progress-bar__bar"></div>
                            </li> --}}
                        </ul>
                    </div>
                    <p class="company_page_subtitle">{{trans("messages.Truck_details_of")}} <span class="Details-of">1</span> {{trans("messages.of")}}
                        {{$number_of_trucks}}</p>
                    <div class="companyFormBox">
                        <form method="post" id="myForm" action="{{ route('truck-company-registration-step-5') }}" enctype="multipart/form-data">
                                @csrf
                                @for($i = 1 ; $i<= $number_of_trucks ;$i++ )
                            <div class="step-{{$i}}" @if($i==1) style="display: block" @else style="display: none" @endif>
                                <div class="row">


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{trans("messages.Truck Number")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="truck_system_number[{{$i}}]" id class="form-control {{$i}}-form-fields" data-is-required="1" value="" >
                                                <small class="text-danger text-danger-text error-truck_system_number{{$i}}"></small>
                                        </div>
                                    </div>
                               

                                    {{-- <!--
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('messages.Type_of_truck')}}</label><span class="text-danger"> * </span>
                                            <select class="form-select {{$i}}-form-fields" name="type_of_truck[{{$i}}]" 
                                             data-is-required="1" id="truckType" >
                                                @foreach($truckType as $row)
                                                    <option value="{{$row->id}}">{{$row->lookupDiscription->code}}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger text-danger-text error-type_of_truck{{$i}}"></small>
                                        </div>
                                    </div>        --> --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="truck_types" class="form-label"> {{trans('messages.choose_shipment')}}</label><span class="text-danger"> * </span>
                                            <select id="truck_types" class="form-select {{$i}}-form-fields truck_types" data-is-required="1" data-form-step="{{$i}}" aria-label="Default select example" name="type_of_truck[{{$i}}]">
                                                <option selected value="" data-multiple-stop-allow="0">{{trans("messages.Select")}}</option>
                                                    @foreach($truckTypeQuestionnaire as $row)
                                                        <option value="{{$row->id}}" data-multiple-stop-allow="{{$row->multiple_stop_allow}}">{{$row->truckTypeDiscription->name}}</option>
                                                    @endforeach
                                            </select>
                                            <small class="text-danger text-danger-text error-type_of_truck{{$i}}"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12"></div>
                                    @foreach($truckTypeQuestionnaire as $row)
                                        <div class="col-md-12 col-sm-12 row truckTypeQuestionnaireClass{{$i}}" id="truckTypeQuestionnaire-{{$i}}-{{$row->id}}" style="display: none;">
                                            @foreach($row->TruckTypeQuestionsList as $questionsRow)
                                                <div class="col-sm-6 for-question question-tag-{{$i}}-{{$questionsRow->id}}" data-question-row-id="{{$i}}" data-question-id="{{$questionsRow->id}}" @if($questionsRow->id == '23') style="display:none;" @endif >
                                                    <div class="form-group">
                                                        <label for="ans{{$i}}{{$row->id}}" class="form-label">{{$questionsRow->TruckTypeQuestionDiscription->name}}</label>
                                                        @if($questionsRow->input_type == "number")
                                                            <input type="number" class="form-control {{$i}}-form-fields remSpinnersIcon" data-is-required="0" id="ans-{{$i}}-{{$row->id}}-{{$questionsRow->id}}" value="" aria-describedby="ans{{$i}}{{$row->id}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}]">
                                                            <small class="text-danger text-danger-text error-ans{{$i}}{{$row->id}}{{$questionsRow->id}}"></small>
                                                        @elseif($questionsRow->input_type == "choice")
                                                            @php
                                                                $input_description = explode(",",$questionsRow->TruckTypeQuestionDiscription->input_description);
                                                            @endphp
                                                            <div class="customRadio">
                                                                @foreach($input_description as $key => $option)
                                                                <span class="radioLabelrow">
                                                                    <input type="checkbox" id="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}][]" class="{{$i}}-form-fields" data-is-required="0" value="{{$key}}">
                                                                    <label for="TruckTypeQuestions{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}">{{$option}}</label>
                                                                </span>
                                                                @endforeach
                                                            </div>
                                                            <small class="text-danger text-danger-text error-ans{{$i}}{{$row->id}}{{$questionsRow->id}}"></small>
                                                        @elseif($questionsRow->input_type == "text")
                                                                <input type="text" class="form-control {{$i}}-form-fields" data-is-required="0" id="ans{{$i}}{{$row->id}}" value="" aria-describedby="ans{{$i}}{{$row->id}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}]">
                                                                <small class="text-danger text-danger-text error-ans{{$i}}{{$row->id}}{{$questionsRow->id}}"></small>
                                                        @elseif($questionsRow->input_type == 'radio')
                                                            @php
                                                                $input_description = explode(",",$questionsRow->TruckTypeQuestionDiscription->input_description);
                                                            @endphp
                                                            <div class="customRadio">
                                                                @foreach($input_description as $key => $option)
                                                                <span class="radioLabelrow">
                                                                    <input type="radio" id="TruckTypeQuestions{{$i}}{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}" name="ans[{{$i}}][{{$row->id}}][{{$questionsRow->id}}][]" {{ $key == 0 ? 'checked' : ''}}  value="{{$key}}">
                                                                    <label for="TruckTypeQuestions{{$i}}{{$questionsRow->id.'-'.$questionsRow->input_type.'-'.$key}}">{{$option}}</label>
                                                                </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                    <div class="col-md-12 col-sm-12"></div>

                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company_refueling_id_{{$i}}" class="form-label">{{trans('messages.refueling_method')}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_refueling[{{$i}}]" class="form-control {{$i}}-form-fields"  data-is-required="1" id="company_refueling_id_{{$i}}">
                                                <small class="text-danger text-danger-text error-company_refueling{{$i}}"></small>
                                        </div>
                                    </div>                          
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company_tidaluk_id_{{$i}}" class="form-label"> {{trans('messages.tidaluk')}} {{trans('messages.company')}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="company_tidaluk[{{$i}}]" class="form-control {{$i}}-form-fields"  data-is-required="1" id="company_tidaluk_id_{{$i}}">
                                                <small class="text-danger text-danger-text error-company_tidaluk{{$i}}"></small>
                                        </div>
                                    </div>      -->
                                    {{-- 
                                        <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{trans("messages.The_size_of_the_crane")}}</label><span class="text-danger"> * </span>
                                            <input type="text" name="the_size_of_the_crane[{{$i}}]" class="form-control {{$i}}-form-fields" data-is-required="1" id="" value="">
                                                <small class="text-danger text-danger-text error-the_size_of_the_crane{{$i}}"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{trans("messages.Basketman")}}</label>
                                            <div class="Swtchrow" >
                                                <div class="button b2 custom_check" >
                                                    <input type="checkbox" name="basketman[{{$i}}]" class="checkbox" />
                                                    <div class="knobs">
                                                        <span></span>
                                                    </div>
                                                    <div class="layer"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --> --}}
                                    <hr class="devideLIne">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{trans("messages.Expiry_date_of_insurance")}}
                                            <br>
                                            <small class="text-warning p-0 m-0">{{trans("messages.update_truck_details_in_profile_later")}}</small>
                                            </label>
                                            <div class="withIconInput extraPadding">
                                                <svg width="20" height="23" class="useIcon" viewBox="0 0 20 23"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.3921 12.6498H9.89456V18.4007H15.3921V12.6498ZM14.2929 0V2.29966H5.49785V0H3.29949V2.29966H2.20031C0.99089 2.29966 0.0116804 3.32569 0.0133016 4.59084C0.0133016 4.59423 0.0133016 4.59763 0.0133016 4.59932L0.00195312 20.7003C0.00195312 21.9706 0.986026 23 2.20031 23H17.5904C18.8031 22.9966 19.7856 21.9689 19.7888 20.7003V4.59932C19.7856 3.33078 18.8031 2.30305 17.5904 2.29966H16.4913V0H14.2929ZM17.5904 20.7003H2.19869V8.05051H17.5904V20.7003Z"
                                                        fill="#2A3EEC" />
                                                </svg>
                                                <input type="text" name="truck_insurance_expiration_date[{{$i}}]" class="form-control select_date {{$i}}-form-fields" data-is-required="0" id="" value="">
                                            </div>
                                            <small class="text-danger text-danger-text error-truck_insurance_expiration_date{{$i}}"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{trans("messages.Upload_an_Insurance_certificate")}}
                                            <br>
                                            <small class="text-warning p-0 m-0">{{trans("messages.update_truck_details_in_profile_later")}}</small>
                                            </label>
                                            <label class="picture" for="uploadInsurance1{{$i}}">
                                           
                                            
                                                    <img src="{{url('public/img/upload_file.svg')}}" id="uploadPreview1{{$i}}">
                                            </label>
                                            <input type="file" accept="image/png, image/gif, image/jpeg" name="truck_insurance_picture[{{$i}}]" class="picture__input {{$i}}-form-fields" data-is-required="0" id="uploadInsurance1{{$i}}" onchange="PreviewImage('uploadInsurance1{{$i}}','uploadPreview1{{$i}}');">
                                        </div>
                                        <small class="text-danger text-danger-text error-truck_insurance_picture{{$i}}"></small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{trans("messages.Expiry_date_of_license")}}
                                            <br>
                                            <small class="text-warning p-0 m-0">{{trans("messages.update_truck_details_in_profile_later")}}</small>
                                            </label>
                                            <div class="withIconInput extraPadding">
                                                <svg width="20" height="23" class="useIcon" viewBox="0 0 20 23"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15.3921 12.6498H9.89456V18.4007H15.3921V12.6498ZM14.2929 0V2.29966H5.49785V0H3.29949V2.29966H2.20031C0.99089 2.29966 0.0116804 3.32569 0.0133016 4.59084C0.0133016 4.59423 0.0133016 4.59763 0.0133016 4.59932L0.00195312 20.7003C0.00195312 21.9706 0.986026 23 2.20031 23H17.5904C18.8031 22.9966 19.7856 21.9689 19.7888 20.7003V4.59932C19.7856 3.33078 18.8031 2.30305 17.5904 2.29966H16.4913V0H14.2929ZM17.5904 20.7003H2.19869V8.05051H17.5904V20.7003Z"
                                                        fill="#2A3EEC" />
                                                </svg>
                                                <input type="text" name="truck_licence_expiration_date[{{$i}}]" class="form-control select_date {{$i}}-form-fields" data-is-required="0" id="" value="">
                                            </div>
                                            <small class="text-danger text-danger-text error-truck_licence_expiration_date{{$i}}"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="uploadInsurance2{{$i}}" class="form-label">{{trans("messages.Upload_an_truck_license")}}
                                            <br>
                                            <small class="text-warning p-0 m-0">{{trans("messages.update_truck_details_in_profile_later")}}</small>
                                            </label>

                                            <label class="picture" for="uploadInsurance2{{$i}}">
                                                    <img src="{{url('public/img/upload_file.svg')}}" id="uploadPreview2{{$i}}">
                                            </label>

                                            <input type="file" style="display:none" accept="image/png, image/gif, image/jpeg" name="truck_licence_number[{{$i}}]" class="picture__input {{$i}}-form-fields" data-is-required="0" id="uploadInsurance2{{$i}}" onchange="PreviewImage('uploadInsurance2{{$i}}','uploadPreview2{{$i}}');">
                                                <small class="text-danger text-danger-text error-truck_licence_number{{$i}}"></small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <button  type="button" class="btn secondary-btn w-100 submit next-step-btn" data-next-step="{{$i}}">{{trans("messages.next")}}</button>
                                            <div  type="button" class="my-1 text-white skip_form" >{{trans("messages.skip")}}</div>
                                            <!-- <a href="javascript:void(0)" class="backLink back-step-btn" data-back-step="{{$i}}">{{trans("messages.skip")}}</a> -->
                                        </div>
                                    </div>
                                </div>
                    </div>
                    @endfor
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>
@stop

@section('scriptCode')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<!-- <img id="uploadPreview" style="width: 100px; height: 100px;" /> -->
<!-- <input id="uploadImage" type="file" name="myPhoto" onchange="PreviewImage();" /> -->
<script type="text/javascript">

    function PreviewImage(uploadImage,uploadPreview) {
        var oFReader = new FileReader();
        if(typeof document.getElementById(uploadImage).files[0] == 'undefined'){
            document.getElementById(uploadPreview).src = '{{url('public/img/upload_file.svg')}}';
        }else{
            oFReader.readAsDataURL(document.getElementById(uploadImage).files[0]);
            oFReader.onload = function (oFREvent) {
                document.getElementById(uploadPreview).src = oFREvent.target.result;
            };
        }
    };
    

</script>
<style>

span.radioLabelrow label {
    color: #fff;
}
</style>
<script>
    $(document).ready(function(){
        $(".for-question input").change(function(){
            var changedQuestionId = $(this).closest(".for-question").attr("data-question-id");
            var changedQuestionRowId = $(this).closest(".for-question").attr("data-question-row-id");
            if(changedQuestionId == 21){
                if($(this).val() == 0){
                    $(".question-tag-"+changedQuestionRowId+"-22").show().find("input").attr("data-is-required",0);
                    $(".question-tag-"+changedQuestionRowId+"-23").hide().find("input").attr("data-is-required",0);
                }else if($(this).val() == 1){
                    $(".question-tag-"+changedQuestionRowId+"-22").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-"+changedQuestionRowId+"-23").show().find("input").attr("data-is-required",0);
                }
            }else if(changedQuestionId == 14){
                if($(this).val() == 0){
                    $(".question-tag-"+changedQuestionRowId+"-15").show().find("input").attr("data-is-required",0);
                    $(".question-tag-"+changedQuestionRowId+"-16").hide().find("input").attr("data-is-required",0);
                }else if($(this).val() == 1){
                    $(".question-tag-"+changedQuestionRowId+"-15").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-"+changedQuestionRowId+"-16").show().find("input").attr("data-is-required",0);
                }
            }else if(changedQuestionId == 17){
                if($(this).val() == 0){
                    $(".question-tag-"+changedQuestionRowId+"-18").show().find("input").attr("data-is-required",0);
                }else if($(this).val() == 1){
                    $(".question-tag-"+changedQuestionRowId+"-18").hide().find("input").attr("data-is-required",0);
                }
            }
        });
        $(".select_date").prop('readonly', true).datepicker({
            minDate: new Date(),
            format: "dd/mm/YY",
        });

        $(".number_only").on("input", function() {
            if ($(this).val().match(/[^+0-9]/g, '')) {
                $(this).val($(this).val().replace(/[^+0-9]/g, ''));
            }
        });

        $.fn.checkValidation = function(nextStepNumber) {
            // return false;
            var flag = false;
            $("." + nextStepNumber + "-form-fields").each(function() {
                if ($(this).data("type") == "same") {
                    if ($(this).val() != $("input[name='" + $(this).data("same-with") + "']").val()) {
                        flag = true;
                        $(".error-" + $(this).attr("name")).html("Sa valeur doit être la même que la valeur de " + ($("input[name='" + $(this).data("same-with") + "']").data("name")));
                        $(this).addClass("is-invalid");

                    }
                }
                if (
                    ($(this).val() == "" && $(this).attr("data-is-required") == "1") ||
                    ($(this).attr('type') == "checkbox" && !$("input[name='"+$(this).attr('name')+"']").is(':checked') && $(this).attr("data-is-required") == "1") ||
                    ($(this).attr('type') == "radio" && !$("input[name='"+$(this).attr('name')+"']").is(':checked') && $(this).attr("data-is-required") == "1")
                ) {

                    flag = true;
                    var str_name = $(this).attr("name");
                    str_name = str_name.replace(/\[/g, '').replace(/\]/g, '');
                    $(".error-" + str_name).html("{{ trans("messages.This field is required") }}");
                    $(this).addClass("is-invalid");
                }
            });
            return flag;
        }

        $("body").on("click", ".next-step-btn", function() {
            // $(".next-step-btn").click(function() {
            $(".text-danger-text").html("");
            $(".is-invalid").removeClass("is-invalid");
            var nextStepNumber = $(this).data("next-step");
            var flag = $.fn.checkValidation(nextStepNumber);
            
            if (flag) {
                $("input.is-invalid:first").focus();
                return false;
            } else {
                if (nextStepNumber == {{$number_of_trucks}}) {
                    $('form').submit();
                    return false;
                }
                toastr.success('{{trans('messages.truck_added')}}', '{{trans('messages.Success')}}');

                $(".step-" + nextStepNumber).hide();
                $(".step-" + (nextStepNumber + 1)).show();
                $(".Details-of").html(nextStepNumber + 1);
                $(window).scrollTop(0);
            }
        });

        $('.skip_form').on('click',function(){

            $('form').submit();
            return false;
        });

        $(".back-step-btn").click(function() {
            var backStepNumber = $(this).data("back-step");
            if(backStepNumber > 1){
                $(".step-" + backStepNumber).hide();
                $(".step-" + (backStepNumber - 1)).show();
                $(".Details-of").html(backStepNumber - 1);
            }else{
                window.location.href = '{{route("truck-company-registration-step-4")}}';
                return false ;
            }
            $(window).scrollTop(0);    
        });



        $("body").on("change", "#sports", function(e) {
            const inputTarget = e.target;
            const file = inputTarget.files[0];

            if (file) {
                const reader = new FileReader();

                reader.addEventListener("load", function (e) {
                const readerTarget = e.target;

                const img = document.createElement("img");
                img.src = readerTarget.result;
                img.classList.add("picture__img");

                pictureImage.innerHTML = "";
                pictureImage.appendChild(img);
                });

                reader.readAsDataURL(file);
            } else {
                pictureImage.innerHTML = pictureImageTxt;
            }
        });

        $("body").on("change", ".truck_types", function() {
            var dataformstep = $(this).attr('data-form-step');
            $(".truckTypeQuestionnaireClass"+dataformstep).hide();
            $(".truckTypeQuestionnaireClass"+dataformstep+" input[type=number], .truckTypeQuestionnaireClass"+dataformstep+" input[type=text], .truckTypeQuestionnaireClass"+dataformstep+" input[type=checkbox]").attr("data-is-required",0);

            $("#truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()).show();
            $("#truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()+" input[type=number], #truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()+" input[type=text], #truckTypeQuestionnaire-"+dataformstep+"-"+$(this).val()+" input[type=checkbox]").attr("data-is-required",0);
            
            // $(".question-tag-"+dataformstep+"-23 input").attr("data-is-required",0);
            if($(this).val() == 9){
                var changedQuestionValue = $('.question-tag-'+dataformstep+'-21 input:checked').val();
                if(changedQuestionValue == 0){
                    $(".question-tag-"+dataformstep+"-22").show().find("input").attr("data-is-required",0);
                    $(".question-tag-"+dataformstep+"-23").hide().find("input").attr("data-is-required",0);
                }else if(changedQuestionValue == 1){
                    $(".question-tag-"+dataformstep+"-22").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-"+dataformstep+"-23").show().find("input").attr("data-is-required",0);
                }
            }else if($(this).val() == 7){
                var changedQuestionValue = $('.question-tag-'+dataformstep+'-14 input:checked').val();
                if(changedQuestionValue == 0){
                    $(".question-tag-"+dataformstep+"-15").show().find("input").attr("data-is-required",0);
                    $(".question-tag-"+dataformstep+"-16").hide().find("input").attr("data-is-required",0);
                }else if(changedQuestionValue == 1){
                    $(".question-tag-"+dataformstep+"-15").hide().find("input").attr("data-is-required",0);
                    $(".question-tag-"+dataformstep+"-16").show().find("input").attr("data-is-required",0);
                }
                var changedQuestionValue = $('.question-tag-'+dataformstep+'-17 input:checked').val();
                if(changedQuestionValue == 0){
                    $(".question-tag-"+dataformstep+"-18").show().find("input").attr("data-is-required",0);
                }else if(changedQuestionValue == 1){
                    $(".question-tag-"+dataformstep+"-18").hide().find("input").attr("data-is-required",0);
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        var body = document.querySelector('body');
        body.classList.add('track_company');
    });
    // Custom Dropdown
    function showMe(evt) {
        console.log("evt.value ", evt.value);
    }

    function makeDd() {
        'use strict';
        let json = new Function(`return ${document.getElementById('json_data').innerHTML}`)();
        MsDropdown.make("#json_dropdown", {
            byJson: {
                data: json,
                selectedIndex: 0
            }
        });
    }

</script>


@stop