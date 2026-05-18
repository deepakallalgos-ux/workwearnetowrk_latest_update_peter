<?php
if (isset($tpl['status'])) {
	$status = __('status', true);
	switch ($tpl['status']) {
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h2><?php echo !empty($titles['PIN14']) ? $titles['PIN14'] : 'Factuur instellingen'; ?></h2>
			</div>
		</div>
		<p class="m-b-none"><i class="fa fa-info-circle"></i> <?php echo !empty($bodies['PIN14']) ? $bodies['PIN14'] : 'Configureer het factuurnummerformaat en overige opties.'; ?></p>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<?php
	$error_code = $controller->_get->toString('err');
	if (!empty($error_code) && in_array($error_code, array('PIN02'))):
	?>
	<div class="alert alert-success">
		<i class="fa fa-check m-r-xs"></i>
		<strong><?php echo @$titles[$error_code]; ?></strong>
		<?php echo @$bodies[$error_code]; ?>
	</div>
	<?php endif; ?>

	<div class="row">
		<div class="col-lg-12" style="max-width:1200px;">

			<!-- Info-balk: bedrijfsgegevens worden op Profiel beheerd -->
			<div class="alert alert-info" style="border-left:4px solid #8438df; background:#f5f0ff;">
				<i class="fa fa-info-circle" style="color:#8438df;"></i>
				<strong>Bedrijfsgegevens</strong> (bedrijfsnaam, adres, BTW-nummer, IBAN, logo) worden beheerd op de
				<a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProfile&amp;action=pjActionIndex" style="color:#8438df;font-weight:600;">Profiel-pagina &rarr;</a>
				&middot;
				<strong>Layout &amp; secties</strong> via <a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminOptions&amp;action=pjActionInvoiceTemplate" style="color:#8438df;font-weight:600;">Factuur sjabloon &rarr;</a>
			</div>

			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjInvoice&amp;action=pjActionIndex" method="post" class="form-horizontal" id="frmInvoiceConfig">
						<input type="hidden" name="invoice_post" value="1" />

						<!-- Voorvoegsel -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php __('plugin_invoice_i_invoice_number'); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="invoice_number" id="inv_prefix_input" value="<?php echo pjSanitize::html(@$tpl['arr']['invoice_number']); ?>" />
								<span class="help-block" style="margin-top:4px;font-size:12px;"><?php __('plugin_invoice_i_number_hint'); ?></span>
							</div>
						</div>

						<!-- Aantal opvolgnummers -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php __('plugin_invoice_i_number_digits'); ?></label>
							<div class="col-sm-9">
								<input type="number" min="2" max="10" class="form-control" style="max-width:120px;" name="invoice_number_digits" id="inv_digits_input" value="<?php echo (int) (@$tpl['arr']['invoice_number_digits'] ?: 5); ?>" />
							</div>
						</div>

						<!-- Reset per jaar -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php __('plugin_invoice_i_number_reset_yearly'); ?></label>
							<div class="col-sm-9" style="padding-top:7px;">
								<label style="font-weight:normal;cursor:pointer;">
									<input type="checkbox" name="invoice_number_reset_yearly" value="1"<?php echo (int) @$tpl['arr']['invoice_number_reset_yearly'] === 1 ? ' checked="checked"' : NULL; ?> />
									Volgnummer terugzetten naar 1 bij elk nieuw jaar
								</label>
							</div>
						</div>

						<!-- Voorbeeld -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php __('plugin_invoice_i_number_preview'); ?></label>
							<div class="col-sm-9" style="padding-top:7px;">
								<code id="inv_preview_output" style="font-size:14px;padding:6px 12px;background:#f1ebf8;color:#6b2cb8;border-radius:4px;display:inline-block;">&mdash;</code>
							</div>
						</div>

						<div class="hr-line-dashed"></div>

						<!-- Verzendgegevens gebruiken -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php __('plugin_invoice_i_use_shipping_details'); ?></label>
							<div class="col-sm-9" style="padding-top:7px;">
								<label style="font-weight:normal;cursor:pointer;">
									<input type="checkbox" name="si_include" value="1"<?php echo (int) @$tpl['arr']['si_include'] === 1 ? ' checked="checked"' : NULL; ?> />
									Toon verzendadres apart als dit afwijkt van factuuradres
								</label>
							</div>
						</div>

						<!-- Aantal × eenheidsprijs -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php __('plugin_invoice_i_use_qty_unit_price'); ?></label>
							<div class="col-sm-9" style="padding-top:7px;">
								<label style="font-weight:normal;cursor:pointer;">
									<input type="checkbox" name="o_use_qty_unit_price" value="1"<?php echo (int) @$tpl['arr']['o_use_qty_unit_price'] === 1 ? ' checked="checked"' : NULL; ?> />
									Toon kolommen Aantal en Eenheidsprijs apart in items-tabel
								</label>
							</div>
						</div>

						<div class="hr-line-dashed"></div>
						<?php
						$overviewUrl = null;
						include PJ_VIEWS_PATH . 'pjLayouts/elements/form-buttons.php';
						?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
// Live preview factuurnummer (Pijler 4)
(function() {
	var prefixInput = document.getElementById('inv_prefix_input');
	var digitsInput = document.getElementById('inv_digits_input');
	var preview     = document.getElementById('inv_preview_output');
	if (!prefixInput || !digitsInput || !preview) { return; }

	var now = new Date();
	var pad = function(n) { return n < 10 ? '0' + n : '' + n; };
	var tokens = {
		'{Year}':      String(now.getFullYear()),
		'{YearShort}': String(now.getFullYear()).slice(-2),
		'{Month}':     pad(now.getMonth() + 1),
		'{Day}':       pad(now.getDate())
	};

	function render() {
		var tpl = prefixInput.value || '';
		Object.keys(tokens).forEach(function(k) {
			tpl = tpl.split(k).join(tokens[k]);
		});
		var d = parseInt(digitsInput.value, 10);
		if (isNaN(d) || d < 2) { d = 2; }
		if (d > 10) { d = 10; }
		var seq = '1';
		while (seq.length < d) { seq = '0' + seq; }
		preview.textContent = tpl + seq;
	}

	prefixInput.addEventListener('input', render);
	digitsInput.addEventListener('input', render);
	render();
})();
</script>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.confirm_delete_msg = <?php x__encode('plugin_base_grid_confirmation_title'); ?>;
myLabel.choose = "<?php __('order_choose'); ?>";
myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;
</script>
<?php
}
?>
