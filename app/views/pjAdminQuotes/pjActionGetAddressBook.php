<p>
	<label class="title"><?php __('quote_address'); ?>:</label>
	<select name="address_id" id="address_id" class="pj-form-field w200">
		<option value=""><?php __('quote_choose'); ?></option>
		<?php
		$disabled = ' disabled="disabled"';
		foreach ($tpl['address_arr'] as $address)
		{
			$selected = NULL;
			if ($address['id'] == @$tpl['quote_arr']['address_id'])
			{
				$selected = ' selected="selected"';
				$disabled = NULL;
			}
			?><option value="<?php echo $address['id']; ?>"<?php echo $selected; ?>><?php echo pjSanitize::html($address['address_1']); ?></option><?php
		}
		?>
	</select>
	<input type="button" value="<?php __('quote_copy_b'); ?>" class="pj-button btnCopy btnCopyBilling"<?php echo $disabled; ?> />
	<input type="button" value="<?php __('quote_copy_s'); ?>" class="pj-button btnCopy btnCopyShipping"<?php echo $disabled; ?> />
</p>
<div id="boxAddress"></div>