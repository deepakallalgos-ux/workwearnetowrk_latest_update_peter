<?php
foreach ($tpl['address_arr'] as $address)
{
	?>
	<div class="scSelectorAddress scAddressHolder">
		<p class="scPaperChain">
			<label class="scTitle"><?php __('client_name'); ?></label>
			<input type="text" name="name[<?php echo $address['id']; ?>]" class="scText" value="<?php echo pjSanitize::html($address['name']); ?>" />
		</p>
		<p class="scPaperChain">
			<label class="scTitle"><?php __('client_country'); ?></label>
			<select name="country_id[<?php echo $address['id']; ?>]" class="scSelect">
				<option value=""><?php __('client_choose'); ?></option>
				<?php
				foreach ($tpl['country_arr'] as $country)
				{
					?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] == $address['country_id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
				}
				?>
			</select>
		</p>
		<p class="scPaperChain">
			<label class="scTitle"><?php __('client_state'); ?></label>
			<input type="text" name="state[<?php echo $address['id']; ?>]" class="scText" value="<?php echo pjSanitize::html($address['state']); ?>" />
		</p>
		<p class="scPaperChain">
			<label class="scTitle"><?php __('client_city'); ?></label>
			<input type="text" name="city[<?php echo $address['id']; ?>]" class="scText" value="<?php echo pjSanitize::html($address['city']); ?>" />
		</p>
		<p class="scPaperChain">
			<label class="scTitle"><?php __('client_zip'); ?></label>
			<input type="text" name="zip[<?php echo $address['id']; ?>]" class="scText" value="<?php echo pjSanitize::html($address['zip']); ?>" />
		</p>
		<p class="scPaperChain">
			<label class="scTitle"><?php __('client_address_1'); ?></label>
			<input type="text" name="address_1[<?php echo $address['id']; ?>]" class="scText" value="<?php echo pjSanitize::html($address['address_1']); ?>" />
		</p>
		<p class="scPaperChain">
			<label class="scTitle"><?php __('client_address_2'); ?></label>
			<input type="text" name="address_2[<?php echo $address['id']; ?>]" class="scText" value="<?php echo pjSanitize::html($address['address_2']); ?>" />
		</p>
		<p class="scPaperChain">
			<label class="scTitle">&nbsp;</label>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="scButton scButtonLight scSelectorDeleteAddress" data-id="<?php echo $address['id']; ?>" data-client_id="<?php echo $address['client_id']; ?>"><?php __('client_del_address'); ?></a>
		</p>
		<p class="scPaperChain">
			<label class="scUpperCase"><input type="radio" name="is_default_shipping" value="<?php echo $address['id']; ?>"<?php echo (int) $address['is_default_shipping'] === 1 ? ' checked="checked"' : NULL; ?> /> <?php __('client_default_shipping'); ?></label>
		</p>
		<p class="scPaperChain">
			<label class="scUpperCase"><input type="radio" name="is_default_billing" value="<?php echo $address['id']; ?>"<?php echo (int) $address['is_default_billing'] === 1 ? ' checked="checked"' : NULL; ?> /> <?php __('client_default_billing'); ?></label>
		</p>
	</div>
	<?php
}
?>