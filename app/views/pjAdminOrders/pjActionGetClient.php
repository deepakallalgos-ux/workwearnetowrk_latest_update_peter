<div class="row">
	<div class="col-md-4 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_email'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['client_arr']['email']); ?></p>
		</div>
	</div>
	<div class="col-md-4 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_phone'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['client_arr']['phone']); ?></p>
		</div>
	</div>
	<div class="col-md-4 col-sm-12">
		<div class="form-group">
			<label class="control-label"><?php __('order_url'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['client_arr']['url']); ?></p>
		</div>
	</div>
</div>