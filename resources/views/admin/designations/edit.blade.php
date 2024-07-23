@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<h5 class="text-dark font-weight-bold my-1 mr-5">
					{{trans('messages.edit_role')}} </h5>
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard')}}" class="text-muted">{{trans('messages.Dashboard')}}</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{route('designations.index')}}" class="text-muted">{{trans('messages.roles')}}</a>
						</li>
					</ul>
				</div>
			</div>
			@include("admin.elements.quick_links")
		</div>
	</div>
	<div class="d-flex flex-column-fluid">
		<div class=" container ">
			<form action="" method="post" class=mws-form files=true autocomplete="off">
				@csrf
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-xl-1"></div>
							<div class="col-xl-10">
								<h3 class="mb-10 font-weight-bold text-dark">
									{{trans('messages.Role Information')}}
								</h3>
								<div class="row">
									<div class="col-xl-6">
										<div class="form-group">
											<label for="name">{{trans('messages.name')}}</label><span class="text-danger"> * </span>
											<input type="text" name="name" class="form-control form-control-solid form-control-lg   @error('name') is-invalid @enderror" value="{{$modell->name ?? ''}}">
											@if ($errors->has('name'))
											<div class=" invalid-feedback">
												{{ $errors->first('name') }}
											</div>
											@endif
										</div>
									</div>
								</div>
								@if (!empty($aclModules))
								<h3 class="mt-8 mb-8">{{trans('messages.Designation Permissions')}}</h3>
								<label class="font-size-lg font-weight-bold checkbox mb-5">
									<input type="checkbox" class="checkAll" />
									<span class="mr-2"></span>
									{{trans('messages.check_all')}}
								</label>
								<div id="accordion" role="tablist" class="accordion accordion-toggle-arrow">
									<?php $counter	=	0; ?>
									@foreach ($aclModules as $aclModule)
									<div class="card mb-4 border-bottom">
										<div class="card-header d-flex align-items-center" role="tab">
											<div class="ml-5">
												<label class="checkbox">
													<input type="checkbox" name="data[{{$counter}}][value]" value=1 class="parent parent_{{$aclModule->id}}" id="{{$aclModule->id}}" {{ ($aclModule->active == 1) ? 'checked' : '' }}>
													<input type="hidden" name="data[{{$counter}}][designation_id]" value="{{$aclModule->id}}">
													<span class="mr-2"></span>
												</label>
											</div>
											<a class="text-dark px-2 py-4 w-100" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$counter}}" aria-expanded="true" aria-controls="collapse{{$counter}}">
												<i class="more-less glyphicon glyphicon-plus"></i>
												{{strtoupper($aclModule->title ?? '')}}
											</a>
										</div>
										<div id="collapse{{$counter}}" class="collapse" data-parent="#accordion">
											@if (!empty($aclModule['sub_module']))
											<div class="card-body">
												<div class="">
													<?php $module_counter =	0;	?>
													@foreach ($aclModule['sub_module'] as $subModule)
													<div class="font-size-lg font-weight-bold mb-3">
														{{strtoupper($subModule->title ?? '')}}
													</div>
													<div class="row">
														@if (!$subModule['module']->isEmpty())
														<?php $count	=	0; 	?>
														@foreach ($subModule['module'] as $module)
														<?php $count++;	

														$newTitle = str_replace(' ', '_', $module->name);
														?>


														<div class="col-auto mb-5">
															<label class="checkbox">
																<input type="checkbox" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 id="{{$aclModule->id}}" class="childernAll childern_{{$aclModule->id}} children child_{{$aclModule->id}} @if($aclModule['is_third_level']==1) for_third_level_all_check @endif all_check_from_childern_{{$newTitle}}" data-parent-id="{{$aclModule->id}}" data-title="{{$newTitle}}" {{ ($module->active == 1) ? 'checked' : '' }}>
																<input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$module->id}}">
																<input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][designation_module_id]" value="{{$subModule->id}}">
																<span class="mr-2"></span>
																{{$module->name}}
															</label>
														</div>
														<?php $module_counter++; ?>
														@endforeach
														<td colspan="6-{{$count}}"></td>
														@else
														<td colspan="6"></td>
														@endif
													</div>
													


													<?php

													$module_counter2        =    0;
													if(!empty($subModule['third_level_module'])){
														foreach ($subModule['third_level_module'] as $third_level_module) {
															?>

															<div class="font-size-lg font-weight-bold mb-3">
																{{strtoupper($third_level_module->title ?? '')}}
															</div>


															<div class="row">
																<?php
																$count2    =    0;

																if (!empty($third_level_module['module'])) {

																	foreach ($third_level_module['module'] as $module) {
																		$count2++;
																		$newTitle = str_replace(' ', '_', $third_level_module->title);
																		$function_name = str_replace(' ', '_', $module->name);
																		?>
																		<div class="col-auto mb-5">
																			<label class="checkbox">
																				<input type="checkbox" id="{{$aclModule->id}}" class="children child{{$aclModule->id}} for_third_level_check edit_time_target_checkobx_{{$newTitle}}_{{$function_name}} target_all_check_checkobx_{{$newTitle}}" data-title="{{$newTitle}}" data-function-name="{{$function_name}}"  {{ ($module->active == 1) ?  'checked' : '' }}>

																				<span class="mr-2"></span>

																				{{$module->name}}
																			</div>
																			<?php
																			$module_counter2++;

																		}
																		?>
																		<td colspan="6-{{$count2}}"></td>
																		<?php
																	} else {
																		?>
																		<td colspan="6"></td>
																	<?php    }    ?>
																</div>


																<?php 
															}   
														}

														?>
														@endforeach




														@if (!empty($aclModule['extModule']))
														<?php $count	=	0;
														foreach ($aclModule['extModule'] as $subModule) {
															$count++;
															?>
															<div class="font-size-lg font-weight-bold mb-3 @if($aclModule['is_third_level']==1) d-none @endif">
																{{strtoupper($subModule->title ?? '')}}
															</div>
															<div class="row @if($aclModule['is_third_level']==1) d-none @endif">
																<?php
																$title = $subModule->title;
																$newTitle = str_replace(' ', '_', $title);
																?>
																@if (!$subModule['module']->isEmpty())
																@foreach ($subModule['module'] as $module)
																<?php
																$function_name = str_replace(' ', '_', $module->name);
																?>
																<div class="col-auto mb-5">
																	<label class="checkbox">
																		<input type="checkbox" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 id="{{$aclModule->id}}" class="children child{{$aclModule->id}} target_checkobx_{{$newTitle}}_{{$function_name}} already_selected_check_for_third_level target_all_check_checkobx_{{$newTitle}}" data-title="{{$newTitle}}" data-function-name="{{$function_name}}" {{ ($module->active == 1) ?  'checked' : '' }}>
																		<input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$module->id}}">
																		<input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][designation_module_id]" value="{{$subModule->id}}">
																		<span class="mr-2"></span>
																		{{$module->name}}
																	</label>
																</div>
																<?php $module_counter++; ?>
																@endforeach
																<td colspan="6-{{$count}}"></td>
																@else
																<td colspan="5"></td>
																@endif
															</div>
															<?php
														}
														?>
														@endif
													</div>
													@endif
													@if (isset($aclModule['parent_module_action']) && (!$aclModule['parent_module_action']->isEmpty()))
													<div class="font-size-lg font-weight-bold mb-3">
														{{$aclModule->title}}
													</div>
													<div class="row">
														@foreach ($aclModule['parent_module_action'] as $parentModule)
														
														<div class="card mb-5 border-0 col-auto">
															<label class="checkbox">
																<input id="{{$aclModule->id}}" type="checkbox" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 class="children child{{$aclModule->id}}" {{ ($parentModule->active == 1) ?  'checked' : '' }}>
																<input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$parentModule->id}}">
																<input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][designation_module_id]" value="{{$aclModule->id}}">
																<span class="mr-2"></span>

																{{$parentModule->name}}
															</label>
														</div>
														<?php
														$counter++;	?>
														@endforeach
													</div>
													@endif
												</div>
											</div>
										</div>
										<?php $counter++; ?>
										@endforeach
									</div>
									@endif
									<div class="d-flex justify-content-between border-top mt-5 pt-10">
										<div>
											<button button type="button" onclick="submit_form();" class="btn btn-success font-weight-bold text-uppercase px-9 py-4">
												Submit
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
		$(".checkAll").click(function() {
			$(".parent:input").not(this).prop('checked', this.checked);
			$(".children:input").not(this).prop('checked', this.checked);
		});

        $(".parent").on("change", function(){
            var allCheckSelected = $(".parent").filter(":checked").length;
            var notAllchecked    = $(".parent").not(":checked").length;
            
            if(notAllchecked === 0){
                $(".checkAll").prop("checked", true);
            }else if(notAllchecked > 0){
                $(".checkAll").prop("checked", false);
            }
        });

        $(".parent").on("change", function(){
            var parentId = $(this).attr("id");
            var isChecked = $(this).is(":checked");
            if(isChecked){
                $(".childern_" + parentId).prop("checked", true);
            }else{
                $(".childern_" + parentId).prop("checked", false);
            }
        });

        $(".childernAll").on("change", function(){
         
          var childParentId = $(this).data("parent-id");
          var childernAllCheck = $(this).is(":checked");

          var childrenCheckboxes = $(".childernAll[data-parent-id='" + childParentId + "']");
          var checkedChildren = childrenCheckboxes.filter(":checked").length;
          var uncheckedChildren = childrenCheckboxes.not(":checked").length;

          if(childParentId){
             $(".parent_" + childParentId).prop("checked", false);
          }
          if(uncheckedChildren === 0){
            $(".parent_" + childParentId).prop("checked", true);
          }

        });
	});


	</script>
	<script>
		function submit_form() {
			$(".mws-form").submit();
		}
	</script>


	@stop