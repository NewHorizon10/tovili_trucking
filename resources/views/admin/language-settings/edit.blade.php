@csrf
<div class="mws-form-inline">
	<div class="mws-form-row">
		<div class="mws-form-item">
			<div class="controls" style="margin-left:0px;margin-bottom:0px;">
				<input type="hidden" class="ids" name="id" value="{{$result->id ?? ''}}">
				<input type="hidden" class="msgid" name="msgid" value="{{$result->msgid ?? ''}}">
				<input type="text" name="word" value="{{stripslashes($result->msgstr)}}" class="small" style="height:30px;width:200px;font-size:9pt" id="edit_msgstr">
				<?php echo $errors->first('word'); ?><br />
				<button type="button" value="Save" class="btn btn-primary formSubmitData">Save</button>
				<a id="cancel" class="btn btn-primary" href="javascript:void(0);">{{ trans('Reset') }}</a>
			</div>
		</div>
	</div>
</div>