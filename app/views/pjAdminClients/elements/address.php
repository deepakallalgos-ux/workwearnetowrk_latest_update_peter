<div class="boxAddress">
	<div class="row">
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label class="control-label"><?php __('client_name'); ?></label>
		
				<input type="text" name="name[{INDEX}]" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
			</div>
		</div>
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label class="control-label"><?php __('client_country'); ?></label>
		
				<select name="country_id[{INDEX}]" class="form-control">
					<option value=""><?php __('client_choose'); ?></option>
					<?php
					foreach ($tpl['country_arr'] as $country)
					{
						?><option value="<?php echo $country['id']; ?>"><?php echo pjSanitize::html($country['country_title']); ?></option><?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label class="control-label"><?php __('client_state'); ?></label>
		
				<input type="text" name="state[{INDEX}]" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
			</div>
		</div>
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label class="control-label"><?php __('client_city'); ?></label>
		
				<input type="text" name="city[{INDEX}]" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
			</div>
		</div>
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label class="control-label"><?php __('client_zip'); ?></label>
		
				<input type="text" name="zip[{INDEX}]" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
			</div>
		</div>
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label class="control-label"><?php __('client_address_1'); ?></label>
		
				<input type="text" name="address_1[{INDEX}]" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
			</div>
		</div>
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label class="control-label"><?php __('client_address_2'); ?></label>
		
				<input type="text" name="address_2[{INDEX}]" class="form-control" data-msg-required="<?php __('fd_field_required', false, true);?>" maxlength="255">
			</div>
		</div>
		<div class="col-md-3 col-sm-6">
			<div class="form-group">
				<label><input type="radio" name="is_default_shipping" value="{INDEX}" /> <?php __('client_default_shipping'); ?></label>
				<label><input type="radio" name="is_default_billing" value="{INDEX}" /> <?php __('client_default_billing'); ?></label>
			</div>
		</div>		
	</div>
	<div class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-outline btn-sm text-capitalize btnRemoveAddress"><?php __('client_del_address'); ?></a></div>
</div>