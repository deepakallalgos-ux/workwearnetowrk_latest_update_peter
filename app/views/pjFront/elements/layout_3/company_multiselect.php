<?php
if (empty($tpl['company_arr']) || !is_array($tpl['company_arr'])) {
	return;
}
$selected_company_ids = isset($selected_company_ids) && is_array($selected_company_ids)
	? array_map('intval', $selected_company_ids)
	: array();
if (empty($selected_company_ids) && isset($tpl['selected_company_ids']) && is_array($tpl['selected_company_ids'])) {
	$selected_company_ids = array_map('intval', $tpl['selected_company_ids']);
}
$company_placeholder = __('front_select_companies', true, true);
if (empty($company_placeholder)) {
	$company_placeholder = 'Select companies';
}
$company_required_msg = __('front_company_selection_required', true, true);
if (empty($company_required_msg)) {
	$company_required_msg = 'Please select at least one company.';
}
?>
<div class="form-group required pjScCompanyField">
	<label class="control-label" for="sc_company_ids"><?php __('front_select_companies'); ?></label>
	<select
		id="sc_company_ids"
		name="company_ids[]"
		class="form-control scSelectorCompanyIds"
		multiple="multiple"
		data-placeholder="<?php echo pjSanitize::html($company_placeholder); ?>"
		data-msg-required="<?php echo pjSanitize::html($company_required_msg); ?>"
	>
		<option></option>
		<?php foreach ($tpl['company_arr'] as $company) : ?>
			<option value="<?php echo (int) $company['id']; ?>"<?php echo in_array((int) $company['id'], $selected_company_ids, true) ? ' selected="selected"' : ''; ?>>
				<?php echo pjSanitize::html($company['name']); ?>
			</option>
		<?php endforeach; ?>
	</select>
</div>
