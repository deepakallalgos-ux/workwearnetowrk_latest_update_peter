<div class="scSelectorAddress scAddressHolder">
	<p class="scPaperChain">
		<label class="scTitle"><?php __('client_name'); ?></label>
		<input type="text" name="name[{INDEX}]" class="scText" />
	</p>
	<p class="scPaperChain">
		<label class="scTitle"><?php __('client_country'); ?></label>
		<select name="country_id[{INDEX}]" class="scSelect">
			<option value=""><?php __('client_choose'); ?></option>
			<?php
			foreach ($tpl['country_arr'] as $country)
			{
				?><option value="<?php echo $country['id']; ?>"><?php echo pjSanitize::html($country['name']); ?></option><?php
			}
			?>
		</select>
	</p>
	<p class="scPaperChain">
		<label class="scTitle"><?php __('client_state'); ?></label>
		<input type="text" name="state[{INDEX}]" class="scText" />
	</p>
	<p class="scPaperChain">
		<label class="scTitle"><?php __('client_city'); ?></label>
		<input type="text" name="city[{INDEX}]" class="scText" />
	</p>
	<p class="scPaperChain">
		<label class="scTitle"><?php __('client_zip'); ?></label>
		<input type="text" name="zip[{INDEX}]" class="scText" />
	</p>
	<p class="scPaperChain">
		<label class="scTitle"><?php __('client_address_1'); ?></label>
		<input type="text" name="address_1[{INDEX}]" class="scText" />
	</p>
	<p class="scPaperChain">
		<label class="scTitle"><?php __('client_address_2'); ?></label>
		<input type="text" name="address_2[{INDEX}]" class="scText" />
	</p>
	<p class="scPaperChain">
		<label class="scTitle">&nbsp;</label>
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="scButton scButtonDark scButtonDarkDel scSelectorRemoveAddress"><?php __('client_del_address'); ?></a>
	</p>
	<p class="scPaperChain">
		<label class="scUpperCase"><input type="radio" name="is_default_shipping" value="{INDEX}" /> <?php __('client_default_shipping'); ?></label>
	</p>
	<p class="scPaperChain">
		<label class="scUpperCase"><input type="radio" name="is_default_billing" value="{INDEX}" /> <?php __('client_default_billing'); ?></label>
	</p>
</div>