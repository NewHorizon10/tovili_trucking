<link href="http://ezify.dev21.obtech.inet/css/admin/button.css" rel="stylesheet">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">
			{{trans("messages.admin_common_Email_Logs")}}
			</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<i aria-hidden="true" class="ki ki-close"></i>
			</button>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group row my-2">
						<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder">{{trans("messages.admin_common_Email_To")}}</label>
						<div class="col-12">
							<span class="font-size-sm text-muted font-weight-bold mt-1r">{{$result->email_to}}</span>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group row my-2">
						<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder">{{trans("messages.admin_common_Email_From")}}</label>
						<div class="col-12">
							<span class="font-size-sm text-muted font-weight-bold mt-1r">{{$result->email_from}}</span>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="form-group row my-2">
				<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder">{{trans("messages.admin_common_Subject")}}</label>
				<div class="col-12">
					<span class="font-size-sm text-muted font-weight-bold mt-1r">{{$result->subject}}</span>
				</div>
			</div>
			<hr>
			<div class="form-group row my-2">
				<label class="col-12 font-size-h6 text-dark-75 font-weight-bolder">{{trans("messages.admin_common_Messages")}}</label>
				<div class="col-12">
					<span class="font-size-sm text-muted font-weight-bold mt-1r"><?php echo  $result->message; ?></span>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
		</div>
	</div>
</div>