<?php
if (!empty($aclModules)) {
?>
    <div class="card-body">

        <h3 class="mt-8 mb-8">{{trans('messages.admin_staff_permissions')}}</h3>
        <label class="font-size-lg font-weight-bold checkbox mb-5">
            <input type="checkbox" class="checkAll" />
            <span class="mr-2"></span>
            {{trans('messages.check_all')}}
        </label>
        <div id="accordion" role="tablist" class="accordion accordion-toggle-arrow">
            <?php
            $counter    =    0;
            foreach ($aclModules as $aclModule) {
            ?>
                <div class="card mb-5 border-bottom">
                    <div class="card-header d-flex align-items-center" role="tab">
                        <div class="ml-5">
                            <label class="checkbox">
                                <input type="checkbox" name="data[{{$counter}}][value]" value=1 class="parent parent_{{$aclModule->id}}" id="{{$aclModule->id}}" {{ ($aclModule->active == 1) ? 'checked' : '' }}>
                                <input type="hidden" name="data[{{$counter}}][department_id]" value="{{$aclModule->id}}">
                                <span class="mr-2"></span>
                            </label>
                        </div>
                        <a class="text-dark px-2 py-4 w-100" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$counter}}" aria-expanded="true" aria-controls="collapse{{$counter}}">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            @if($aclModule->title == 'Shipments')
                            {{trans('messages.shipments')}}
                            @else 
                            {{strtoupper($aclModule->title ?? '')}}
                            @endif
                        </a>
                    </div>
                    <div id="collapse{{$counter}}" class="collapse" data-parent="#accordion">
                        <?php if (!empty($aclModule['sub_module'])) { ?>
                            <div class="card-body ">
                                <div class="">
                                    <?php
                                    $module_counter        =    0;
                                    foreach ($aclModule['sub_module'] as $subModule) {
                                    ?>
                                        <div class="font-size-lg font-weight-bold mb-3">
                                            @if($subModule->title == 'Business Customers')
                                            {{trans("messages.BUSINESS CUSTOMERS")}}
                                            @else
                                            {{strtoupper($subModule->title ?? '')}}
                                            @endif
                                        </div>
                                        <div class="row">
                                            <?php
                                            $count    =    0;
                                            if (!$subModule['module']->isEmpty()) {
                                                foreach ($subModule['module'] as $module) {
                                                    $count++;
                                            ?>
                                                    <div class="col-auto mb-5">
                                                        <label class="checkbox">
                                                            <input type="checkbox" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1  class="childernAll childern_{{$aclModule->id}} children child_{{$aclModule->id}}" data-parent-id="{{$aclModule->id}}" {{ ($module->active == 1) ? 'checked' : '' }}>
                                                            <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$module->id}}">
                                                            <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][department_module_id]" value="{{$subModule->id}}">
                                                            <span class="mr-2"></span>
                                                            @if($module->name == 'Export')
                                                                {{trans('messages.export')}}
                                                                @else
                                                                {{trans('messages.'.$module->name)}}
                                                                @endif
                                                        </label>
                                                    </div>
                                                <?php
                                                    $module_counter++;
                                                }
                                                ?>
                                                <td colspan="6-{{$count}}"></td>
                                            <?php
                                            } else {
                                            ?>
                                                <td colspan="6"></td>
                                            <?php    }    ?>
                                        </div>
                                        <?php
                                    }
                                    if (!empty($aclModule['extModule'])) {
                                        $count    =    0;
                                        foreach ($aclModule['extModule'] as $subModule) {
                                            $count++;
                                        ?>
                                            <div class="font-size-lg font-weight-bold mb-3">
                                                {{strtoupper($subModule->title ?? '')}}
                                            </div>
                                            <div class="row">
                                                <?php
                                                if (!$subModule['module']->isEmpty()) {
                                                    foreach ($subModule['module'] as $module) {
                                                ?>
                                                        <div class="col-auto mb-5">
                                                            <label class="checkbox">
                                                                <input type="checkbox" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 class="children child{{$aclModule->id}}" {{ ($module->active == 1) ?  'checked' : '' }}>
                                                                <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$module->id}}">
                                                                <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][department_module_id]" value="{{$subModule->id}}">
                                                                <span class="mr-2"></span>

                                                                @if($module->name == 'Export')
                                                                {{trans('messages.export')}}
                                                                @else
                                                                {{trans('messages.'.$module->name)}}
                                                                @endif
                                                        </div>
                                                    <?php
                                                        $module_counter++;
                                                    }
                                                    ?>
                                                    <td colspan="6-{{$count}}"></td>
                                                <?php
                                                } else {
                                                ?>
                                                    <td colspan="5"></td>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            <?php
                        }
                            ?>
                            <?php
                            if (isset($aclModule['parent_module_action'])  && (!$aclModule['parent_module_action']->isEmpty())) {
                            ?>
                                <div class="font-size-lg font-weight-bold mb-3"> {{$aclModule->title}} </div>

                                <div class="row">
                                    <?php
                                    foreach ($aclModule['parent_module_action'] as $parentModule) {
                                    ?>
                                        <div class="col-auto mb-5">
                                            <label class="checkbox">
                                                <input type="checkbox" name="data[{{$counter}}][module][{{$module_counter}}][value]" value=1 class="children child{{$aclModule->id}}" {{ ($parentModule->active == 1) ?  'checked' : '' }}>
                                                <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][id]" value="{{$parentModule->id}}">
                                                <input type="hidden" name="data[{{$counter}}][module][{{$module_counter}}][department_module_id]" value="{{$aclModule->id}}">
                                                <span class="mr-2"></span>

                                                {{trans('messages.'.$parentModule->name)}}
                                            </label>
                                        </div>
                                    <?php
                                        $counter++;
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>
                            </div>

                    </div>
                </div>
            <?php
                $counter++;
            }
            ?>
        </div>
    </div>
<?php
}
?>

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