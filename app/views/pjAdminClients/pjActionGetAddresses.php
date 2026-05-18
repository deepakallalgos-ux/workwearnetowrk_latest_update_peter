<?php
foreach ($tpl['address_arr'] as $address)
{
	?>
	<div class="boxAddress">
		<div class="row">
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label class="control-label"><?php __('client_name'); ?></label>
			
					<input type="text" name="name[<?php echo $address['id']; ?>]" value="<?php echo pjSanitize::html($address['name']); ?>" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label class="control-label"><?php __('client_country'); ?></label>
			
					<select name="country_id[<?php echo $address['id']; ?>]" class="form-control">
						<option value=""><?php __('client_choose'); ?></option>
						<?php
						foreach ($tpl['country_arr'] as $country)
						{
							?><option value="<?php echo $country['id']; ?>" <?php echo $country['id'] == $address['country_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['country_title']); ?></option><?php
						}
						?>
					</select>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label class="control-label"><?php __('client_state'); ?></label>
			
					<input type="text" name="state[<?php echo $address['id']; ?>]" value="<?php echo pjSanitize::html($address['state']); ?>" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label class="control-label"><?php __('client_city'); ?></label>
			
					<input type="text" name="city[<?php echo $address['id']; ?>]" value="<?php echo pjSanitize::html($address['city']); ?>" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label class="control-label"><?php __('client_zip'); ?></label>
			
					<input type="text" name="zip[<?php echo $address['id']; ?>]" value="<?php echo pjSanitize::html($address['zip']); ?>" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label class="control-label"><?php __('client_address_1'); ?></label>
			
					<input type="text" name="address_1[<?php echo $address['id']; ?>]" value="<?php echo pjSanitize::html($address['address_1']); ?>" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label class="control-label"><?php __('client_address_2'); ?></label>
			
					<input type="text" name="address_2[<?php echo $address['id']; ?>]" value="<?php echo pjSanitize::html($address['address_2']); ?>" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="form-group">
					<label><input type="radio" name="is_default_shipping" value="<?php echo $address['id']; ?>" <?php echo (int) $address['is_default_shipping'] === 1 ? ' checked="checked"' : NULL; ?> /> <?php __('client_default_shipping'); ?></label>
					<label><input type="radio" name="is_default_billing" value="<?php echo $address['id']; ?>" <?php echo (int) $address['is_default_billing'] === 1 ? ' checked="checked"' : NULL; ?> /> <?php __('client_default_billing'); ?></label>
				</div>
			</div>		
		</div>
		<div class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-outline btn-sm text-capitalize btnDeleteAddress" data-id="<?php echo $address['id']; ?>" data-client_id="<?php echo $address['client_id']; ?>"><?php __('client_del_address'); ?></a></div>
	</div>
	<?php
}
?>