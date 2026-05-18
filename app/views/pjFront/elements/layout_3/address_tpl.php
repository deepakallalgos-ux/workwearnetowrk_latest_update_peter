<div class="scAddressHolder">
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
	   			<label class="control-label"><?php __('client_name'); ?></label>
				<input name="name[{INDEX}]" class="form-control">
	  		</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
	   			<label class="control-label"><?php __('client_country'); ?></label>
				<select name="country_id[{INDEX}]" class="form-control">
					<option value=""><?php __('client_choose'); ?></option>
					<?php
					foreach ($tpl['country_arr'] as $country)
					{
						?><option value="<?php echo $country['id']; ?>"><?php echo pjSanitize::html($country['name']); ?></option><?php
					}
					?>
				</select>
	  		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
	   			<label class="control-label"><?php __('client_state'); ?></label>
				<input name="state[{INDEX}]" class="form-control">
	  		</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
	   			<label class="control-label"><?php __('client_city'); ?></label>
				<input name="city[{INDEX}]" class="form-control">
	  		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
	   			<label class="control-label"><?php __('client_zip'); ?></label>
				<input name="zip[{INDEX}]" class="form-control">
	  		</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
	   			<label class="control-label"><?php __('client_address_1'); ?></label>
				<input name="address_1[{INDEX}]" class="form-control">
	  		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
	   			<label class="control-label"><?php __('client_address_2'); ?></label>
				<input name="address_2[{INDEX}]" class="form-control">
	  		</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label">&nbsp;</label><br/>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-default text-uppercase scSelectorDeleteAddress" data-id="{INDEX}"><?php __('client_del_address'); ?></a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="radio">
				<label>
					<input type="radio" name="is_default_shipping" value="{INDEX}"/> <?php __('client_default_shipping'); ?>
				</label>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="radio">
				<label>
					<input type="radio" name="is_default_billing" value="{INDEX}"/> <?php __('client_default_billing'); ?>
				</label>
			</div>
		</div>
	</div>
</div>