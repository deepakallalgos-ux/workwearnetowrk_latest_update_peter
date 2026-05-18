<div class="row">
	<div class="col-md-3 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_country'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['address_arr']['country_name']); ?></p>
		</div>
	</div><!-- /.col-md-3 -->
	<div class="col-md-3 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_state'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['address_arr']['state']); ?></p>
		</div>
	</div><!-- /.col-md-3 -->
	<div class="col-md-3 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_city'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['address_arr']['city']); ?></p>
		</div>
	</div><!-- /.col-md-3 -->
	<div class="col-md-3 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_zip'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['address_arr']['zip']); ?></p>
		</div>
	</div><!-- /.col-md-3 -->
	<div class="col-md-3 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_name'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['address_arr']['name']); ?></p>
		</div>
	</div><!-- /.col-md-3 -->
	<div class="col-md-3 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_address_1'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['address_arr']['address_1']); ?></p>
		</div>
	</div><!-- /.col-md-3 -->
	<div class="col-md-3 col-sm-6">
		<div class="form-group">
			<label class="control-label"><?php __('order_address_2'); ?></label>
	
			<p class="form-control-static"><?php echo pjSanitize::html(@$tpl['address_arr']['address_2']); ?></p>
		</div>
	</div><!-- /.col-md-3 -->
</div>