@extends('admin.layouts.layout')
@section('content')
<script src="{{ asset('/public/js/ckeditor/ckeditor.js') }}"></script>

    <div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
            <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <h5 class="text-dark font-weight-bold my-1 mr-5">
                            {{ trans('messages.send_notification') }}
                        </h5>
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}" class="text-muted">{{ trans('messages.Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('truck.insurance.index') }}"
                                    class="text-muted">{{ trans('messages.Truck Insurance List') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                @include('admin.elements.quick_links')
            </div>
        </div>
        <div class="d-flex flex-column-fluid">
            <div class=" container ">
                <form action="{{ route('send-insurance-expire-notification') }}" id="notification-form" method="post" clas="kt-form kt-form--fit mb-0" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-custom card-stretch card-shadowless">
                                <div class="card-header">
                                    <div class="card-title">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input type="checkbox" class="notification-change"
                                                data-heading-type="notification" name="system_notification"
                                                id="system_notification" value="1">
                                            <label for="system_notification"
                                                class="font-size-sm mx-2 mb-0">{{ trans('messages.admin_system_notification') }}</label>

                                            <input type="checkbox" class="ml-3 notification-change"
                                                data-heading-type="notification" name="whatsapp_notification"
                                                id="whatsapp_notification" value="1">
                                            <label for="whatsapp_notification"
                                                class="font-size-sm mx-2 mb-0">{{ trans('messages.admin_whatsapp_notification') }}</label>

                                            <input type="checkbox" class="ml-3 notification-change"
                                                data-heading-type="email" name="email_notification" id="email_notification"
                                                value="1">
                                            <label for="email_notification"
                                                class="font-size-sm mx-2 mb-0">{{ trans('messages.admin_email_notification') }}</label>
                                        </div>
                                    </div>
                                    <div class="card-toolbar">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <button type="button" class="btn btn-primary mb-2 sendNotificationButton d-none">{{ trans('messages.send_notification') }}</button>
                                    <button type="submit" class="sendNotificationHiddenButton" value="send_notification" name="submit" style="display: none;"></button>
                                    <div class="dataTables_wrapper-fake-top-scroll">
                                        <div>&nbsp;</div>
                                    </div>
                                    <div class="dataTables_wrapper ">
                                        <div class="table-responsive table-responsive-new">
                                            <table
                                                class="table dataTable table-head-custom table-head-bg table-borderless table-vertical-center"
                                                id="taskTable">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <input type="checkbox" class="allCheckboxes notification-change" id="allCheckboxes">
                                                        </th>
                                                        <th>{{ trans('messages.name') }}</th>
                                                        <th>{{ trans('messages.action') }}</th>
                                                    </tr>

                                                </thead>
                                                <tbody>
                                                    @foreach ($NotificationTemplateDescription as $key => $template)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox"
                                                                    data-notification-id="{{ $template->NotificationAction?->id }}"
                                                                    data-notification-action="{{ $template->NotificationAction->action }}"
                                                                    class="childCheckboxes notification-template-list notification-change"
                                                                    name="notificationAction[]"
                                                                    value="{{ $template->NotificationAction?->action }}">
                                                            </td>
                                                            <td>
                                                                {{ $template->name ?? '' }}
                                                            </td>
                                                            <td>
                                                               
                                                            </td>
                                                        </tr>

                                                        <!-- Notification Modals -->

                                                        <div id="afterExpiryModel_{{$template->parent_id}}"
                                                            class="modal fade" aria-modal="true" role="dialog"  data-backdrop="static" 
                                                            data-keyboard="false">
                                                            <div class="modal-dialog modal-lg">
                                                                <!-- Modal content-->
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title"></h4>
                                                                        <button type="button"  class="btn-close close_button btn" data-dismiss="modal"> X </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        @foreach ($TemplateDescription as $keys => $desc)
                                                                            @if ($desc->parent_id == $template->parent_id)
                                                                                <h4 class="mt-3 mb-3">
                                                                                    {{ $desc->name ?? '' }}</h4>

                                                                                <div class="row">
                                                                                    <input type="hidden"
                                                                                        name="{{ $template->NotificationAction->action }}[notification][{{ $desc->language_id }}][language_id]"
                                                                                        value="{{ $desc->language_id }}">
                                                                                    <div class="col-xl-6 mb-3">
                                                                                        <label
                                                                                            for="">{{ trans('messages.admin_common_Subject') }}</label>
                                                                                        <input type="text" id="template-{{$template->parent_id}}_{{$desc->language_id}}-subject"
                                                                                            name="{{ $template->NotificationAction->action }}[notification][{{ $desc->language_id }}][subject]"
                                                                                            class="form-control form-control-solid form-control-lg subject"
                                                                                            value="{{ $template->subject ?? '' }}">
                                                                                    </div>
                                                                                    <div class="col-xl-6">
                                                                                        <div class="form-group">
                                                                                            <lable for="Constants">
                                                                                                {{ trans('messages.admin_common_Constants') }}
                                                                                            </lable><span
                                                                                                class="text-danger">
                                                                                            </span>
                                                                                            <div class="row">
                                                                                                <div class="col">
                                                                                                    <select
                                                                                                      
                                                                                                        class="form-control select2init"
                                                                                                        onchange="constants($keys)"
                                                                                                        id="constants_{{ $keys }}">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            {{ trans('messages.admin_common_select_one') }}
                                                                                                        </option>
                                                                                                        @foreach ($optionsvalue as $conKey => $Con)
                                                                                                            <option
                                                                                                                value="">
                                                                                                                {{ $Con }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-auto">
                                                                                                    <a onclick="return InsertHTML('<?php echo $keys; ?>')"
                                                                                                        href="javascript:void(0)"
                                                                                                        class="btn btn-lg btn-success no-ajax pull-right"><i
                                                                                                            class="icon-white "></i>{{ trans('messages.Insert Variable') }}
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-xl-12 ">
                                                                                        <lable for="">
                                                                                            {{ trans('messages.description') }}
                                                                                        </lable>
                                                                                        <textarea name="{{ $template->NotificationAction->action }}[notification][{{ $desc->language_id }}][description]"
                                                                                            id="body_{{ $keys }}"
                                                                                            class="template-{{$template->parent_id}}_{{$desc->language_id}}-body description form-control form-control-solid form-control-lg body_{{$keys}}  @error('description') is-invalid @enderror"
                                                                                            rows="5">
                                                                                            {{ $desc->body ?? '' }}
                                                                                        </textarea>
                                                                                    </div>
                                                                                </div>



                                                                                <script>
                                                                                    var editor{{ $keys }} = CKEDITOR.replace("body_{{ $keys }}", {
                                                                                        filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                                                        enterMode: CKEDITOR.ENTER_BR
                                                                                    });
                                                                                    CKEDITOR.config.allowedContent = true;

                                                                                    editor{{ $keys }}.on('change', function() {
                                                                                        // Update the corresponding textarea value
                                                                                        document.getElementById("body_{{ $keys }}").value = editor{{ $keys }}.getData();
                                                                                    });
                                                                                </script>
                                                                                <br>
                                                                            @endif
                                                                        @endforeach


                                                                    </div>
                                                                    <div class="text-center d-block"
                                                                        style="margin-bottom: 12px;">
                                                                        <div
                                                                            class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                                                                            <div class="row">
                                                                                <div class="col-6">
                                                                                    <button 
                                                                                    type="button"
                                                                                    value="edit_notification"
                                                                                    class="btn btn-danger closeModalBtn font-weight-bold text-uppercase px-9 py-4 close_button">
                                                                                    {{trans('messages.close')}}
                                                                                </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    @endforeach

                                                    @foreach ($Email_Template_Description as $emailkey => $emailTemplate)
                                                    <!-- Email Notification Modals -->
                                                    <div id="afterExpiryEmailModel_{{$emailTemplate->parent_id}}"
                                                        class="modal fade" aria-modal="true" role="dialog"  data-backdrop="static" 
                                                        data-keyboard="false">
                                                        <div class="modal-dialog modal-lg">
                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title"></h4>
                                                                    <button type="button"  class="btn-close close_button btn" data-dismiss="modal"> X </button>
                                                                </div>
                                                                <div class="modal-body">

                                                                    @foreach ($emailTemplateDescription as $emailkeys => $desc)
                                                                        @if ($desc->parent_id == $emailTemplate->parent_id)
                                                                            <h4 class="mt-3 mb-3">
                                                                                {{ $desc->name ?? '' }}</h4>

                                                                            <div class="row">
                                                                                <input type="hidden"
                                                                                    name="{{ $emailTemplate->EmailAction->action }}[email][{{ $desc->language_id }}][language_id]"
                                                                                    value="{{ $desc->language_id }}">
                                                                                <div class="col-xl-6 mb-3">
                                                                                    <label
                                                                                        for="">{{ trans('messages.admin_common_Subject') }}</label>
                                                                                    <input type="text"
                                                                                        name="{{ $emailTemplate->EmailAction->action }}[email][{{ $desc->language_id }}][subject]"
                                                                                        class="form-control form-control-solid form-control-lg"
                                                                                        value="{{ $emailTemplate->subject ?? '' }}">
                                                                                </div>
                                                                                <div class="col-xl-6">
                                                                                    <div class="form-group">
                                                                                        <lable for="Constants">
                                                                                            {{ trans('messages.admin_common_Constants') }}
                                                                                        </lable><span
                                                                                            class="text-danger">
                                                                                        </span>
                                                                                        <div class="row">
                                                                                            <div class="col">
                                                                                                <select
                                                                                                  
                                                                                                    class="form-control select2init"
                                                                                                    onchange="constants($desc->id)"
                                                                                                    id="emailconstants_{{ $desc->id }}">
                                                                                                    <option
                                                                                                        value="">
                                                                                                        {{ trans('messages.admin_common_select_one') }}
                                                                                                    </option>
                                                                                                    @foreach ($emailoptionsvalue as $conKey => $Con)
                                                                                                        <option
                                                                                                            value="">
                                                                                                            {{ $Con }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                            <div class="col-auto">
                                                                                                <a onclick="return EmailInsertHTML(<?php echo $desc->id; ?>)"
                                                                                                    href="javascript:void(0)"
                                                                                                    class="btn btn-lg btn-success no-ajax pull-right"><i
                                                                                                        class="icon-white "></i>{{ trans('messages.Insert Variable') }}
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-xl-12">
                                                                                    <lable for="">
                                                                                        {{ trans('messages.description') }}
                                                                                    </lable>
                                                                                    <textarea name="{{ $emailTemplate->EmailAction->action }}[email][{{ $desc->language_id }}][description]"
                                                                                        id="body_{{$desc->id}}_email"
                                                                                        class="form-control form-control-solid form-control-lg body_{{$desc->id}} @error('description') is-invalid @enderror"
                                                                                        rows="5">
                                                                                        {{ $desc->body }}
                                                                                    </textarea>
                                                                                </div>
                                                                            </div>



                                                                            <script>
                                                                                var editor{{$desc->id}} = CKEDITOR.replace('body_{{$desc->id}}_email', {
                                                                                    filebrowserUploadUrl: '<?php echo URL()->to('base/uploder'); ?>',
                                                                                    enterMode: CKEDITOR.ENTER_BR
                                                                                });
                                                                                CKEDITOR.config.allowedContent = true;

                                                                                editor{{$desc->id}}.on('change', function() {
                                                                                        document.getElementById("body_{{$desc->id}}_email").value = editor{{$desc->id}}.getData();
                                                                                    });
                                                                            </script>
                                                                            <br>
                                                                        @endif
                                                                    @endforeach




                                                                </div>
                                                                <div class="text-center d-block"
                                                                    style="margin-bottom: 12px;">
                                                                    <div
                                                                        class="d-flex justify-content-between border-top ml-5 mt-5 pt-10">
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <button type="button" value="edit_notification" class="btn btn-danger font-weight-bold text-uppercase px-9 py-4 close_button">
                                                                                    {{trans('messages.close')}}
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                


                    <div class="flex-column-fluid notificationList">
                        <div class="container p-4">
                            <div class="row mt-3 card card-custom card-stretch card-shadowless">
                                <div class="card-body">
                                    @foreach ($NotificationTemplateDescription as $key => $template)
                                        <div class="{{ $template->NotificationAction->action }}-notification for-hide" style="display:none;">
                                            <h4 class="sys_notification">{{ $template->name }}</h4>
                                            <div class="notifications-template for-hide" id="template-{{ $template->NotificationAction->action }}-notification" style="display: none;">
												<h6 class="sys_notification">{{ trans('messages.Notifications') }}:</h6>
                                                <div class="container">
                                                    <div class="row notificationShowList">
                                                        <div class="col-xl-4 subject" id="temp-{{$template->parent_id}}-subject">{{ $template->subject }}</div>
                                                        <div class="col-xl-7 description" id="temp-{{$template->parent_id}}-body">{!! $template->body !!}</div>
                                                        <div class="col-xl-1 button">
                                                            <a href="javascript:void(0);" onclick="updateCardContent({{ $template->parent_id }}, {{$template->language_id}})"
                                                                class="btn btn-icon btn-light btn-hover-primary btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#afterExpiryModel_{{ $template->parent_id }}"
                                                                data-toggle="tooltip" data-placement="top"
                                                                data-container="body" data-boundary="window"
                                                                title="{{ trans('messages.system_whatsapp_notification') }}"
                                                                data-original-title="{{ trans('messages.system_whatsapp_notification') }}">
                                                                <span class="svg-icon svg-icon-md svg-icon-primary">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24"
                                                                                height="24" />
                                                                            <path
                                                                                d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                                                fill="#000000" opacity="0.3" />
                                                                            <path
                                                                                d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                                                fill="#000000" />
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        	<!-- email template here -->
											@foreach ($template->NotificationAction->EmailActions->EmailActionsDescription as $emailkey => $email_template)
												@if ($template->language_id == $email_template->language_id)
													<div  class="notifications-template for-hide" id="template-{{ $template->NotificationAction->EmailActions->action }}-email" style="display: none;">
														<h6 class="sys_notification">{{ trans('messages.email') }}:</h6>
														<div class="container">
															<div class="row notificationShowList emailShowList">
																<div class="col-xl-4 subject">{{ $email_template->subject }}
																</div>
																<div class="col-xl-7 description">{!! $email_template->body !!}
																</div>
																<div class="col-xl-1 button">
																	<a href="javascript:void(0);"
																		class="btn btn-icon btn-light btn-hover-primary btn-sm"
																		data-toggle="modal"
																		data-target="#afterExpiryEmailModel_{{ $email_template->parent_id }}"
																		data-toggle="tooltip" data-placement="top"
																		data-container="body" data-boundary="window"
																		title="{{ trans('messages.system_whatsapp_notification') }}"
																		data-original-title="{{ trans('messages.system_whatsapp_notification') }}">
																		<span
																			class="svg-icon svg-icon-md svg-icon-primary">
																			<svg xmlns="http://www.w3.org/2000/svg"
																				xmlns:xlink="http://www.w3.org/1999/xlink"
																				width="24px" height="24px"
																				viewBox="0 0 24 24" version="1.1">
																				<g stroke="none" stroke-width="1"
																					fill="none" fill-rule="evenodd">
																					<rect x="0" y="0" width="24"
																						height="24" />
																					<path
																						d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
																						fill="#000000" opacity="0.3" />
																					<path
																						d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
																						fill="#000000" />
																				</g>
																			</svg>
																		</span>
																	</a>
																</div>
															</div>
														</div>
													</div>
												@endif
											@endforeach
											<hr>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
            </form>

            </div>
        </div>
    </div>

    <script type='text/javascript'>
        function InsertHTML(count) {
            var str = document.getElementById('constants_' + count);
            var strUser = str.options[str.selectedIndex].text;

            if (strUser != '') {
                var newStr = '{' + strUser + '}';
                var oEditor = CKEDITOR.instances['body_' + count];
                oEditor.insertHtml(newStr);
            }
        }

        function EmailInsertHTML(count) {
            var str = document.getElementById('emailconstants_' + count);
            var strUser = str.options[str.selectedIndex].text;
            
            if (strUser != '') {
                var newStr = '{' + strUser + '}';
                var oEditor = CKEDITOR.instances['body_' + count + '_email'] ;
                oEditor.insertHtml(newStr);
            }
        }

        $(document).ready(function() {
            $("#allCheckboxes").on("change", function() {
                if ($(this).is(":checked")) {
                    $(".childCheckboxes").prop("checked", true);
                    $(".sendNotificationButton").removeClass("d-none");
                } else {
                    $(".childCheckboxes").prop("checked", false);
                    $(".sendNotificationButton").addClass("d-none");
                }
            });

            $(".childCheckboxes").change(function() {
                var totalChildCheckboxes = $(".childCheckboxes").length;
                var checkedChildCheckboxes = $(".childCheckboxes:checked").length;

                if (checkedChildCheckboxes == totalChildCheckboxes) {
                    $("#allCheckboxes").prop("checked", true);
                } else if (checkedChildCheckboxes == 0) {
                    $("#allCheckboxes").prop("checked", false);
                } else if (checkedChildCheckboxes != totalChildCheckboxes) {
                    $("#allCheckboxes").prop("checked", false);

                }

                if ($(this).is(':checked')) {
                    $(".sendNotificationButton").removeClass("d-none");
                } else {
                    var checkedCheckboxes = $(".childCheckboxes:checked").length;
                    if (checkedCheckboxes === 0) {
                        $(".sendNotificationButton").addClass("d-none");
                    }
                }
            });

            $(".sendNotificationButton").on("click", function() {
                $(".sendNotificationHiddenButton").click();
            });





            $(".notification-change").on("change", function() {
                $(".for-hide").hide();

                var notificationInput = false;
                var emailInput = false;
                $("input[data-heading-type='notification']").each(function() {
                    if (notificationInput == false) {
                        notificationInput = $(this).prop('checked')
                    }
                });

                $("input[data-heading-type='email']").each(function() {
                    if (emailInput == false) {
                        emailInput = $(this).prop('checked')
                    }
                });

                $(".notification-template-list").each(function() {
                    if ($(this).prop('checked')) {
                        var notificationAction = $(this).attr('data-notification-action');

                        if (notificationInput || emailInput) {
                            $("." + notificationAction + "-notification").show();
                        }

                        if (notificationInput) {
                            $("#template-" + notificationAction + "-notification").show();
                        }

                        if (emailInput) {
                            $("#template-" + notificationAction + "-email").show();
                        }
                    }
                });
            });


            $(".close_button").on("click", function() {
                $("#notification-form").trigger('submit');
                var serializedData = $("#notification-form").serialize();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route('truck.insurance.notification-set') }}',
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: serializedData,
                    success: function(data) {
                        console.log(data.data);
                        $.each(data.data.notification,function(index,value) {
                            $("#template-"+index+"-notification .container .notificationShowList .subject").html(value.subject)
                            $("#template-"+index+"-notification .container .notificationShowList .description").html(value.description)
                        })
                        $.each(data.data.email,function(index1,value1) {
                            $("#template-"+index1+"-email .container .emailShowList .subject").html(value1.subject)
                            $("#template-"+index1+"-email .container .emailShowList .description").html(value1.description)
                        })
                        $('.modal').modal('hide');
                    },
                    error: function(response, status, error) {
                        console.error(response);
                        // Handle error here
                    }
                });
            });

});

    </script>
@stop
