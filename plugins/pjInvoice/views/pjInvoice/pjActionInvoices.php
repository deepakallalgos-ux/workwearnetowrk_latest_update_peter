<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], !isset($_GET['errTime']) ? @$bodies[$_GET['err']] : $_SESSION[$controller->invoiceErrors][$_GET['errTime']]);
	}
	
	$plugin_menu = PJ_VIEWS_PATH . sprintf('pjLayouts/elements/menu_%s.php', $controller->getConst('PLUGIN_NAME'));
	if (is_file($plugin_menu))
	{
		include $plugin_menu;
	}
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	// pjUtil::printNotice(@$titles['PIN12'], @$bodies['PIN12']);
	
	$statuses = __('plugin_invoice_statuses', true);
	?>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<h2><?php echo @$titles['PIN12']?></h2>
			<p class="m-b-none"><i class="fa fa-info-circle"></i> <?php echo @$bodies['PIN12'];?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionHelp#invoices"><?php echo htmlspecialchars(__('lblReadMore', true) ?: 'Lees meer in de handleiding', ENT_QUOTES); ?> &rarr;</a></p>
		</div><!-- /.col-md-12 -->
	</div>

	<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
		<div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row m-b-md">
                    <div class="col-md-3">
                        <a href="<?php echo $_SERVER['PHP_SELF'].'?controller=pjInvoice&action=pjActionIndex'?>" class="btn btn-default sc-page-help-link"><?php __('plugin_invoice_config'); ?></a>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <form action="" method="get" class="form-horizontal frm-filter sc-live-filter-form">
                            <div class="input-group sc-live-search-group">
                                <input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control" autocomplete="off">
                                <div class="input-group-btn">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseInvoiceFilters" class="btn btn-default btn-advance-search sc-btn-icon" title="<?php echo htmlspecialchars(__('btnAdvancedSearch', true) ?: 'Geavanceerd zoeken', ENT_QUOTES); ?>"><i class="fa fa-sliders"></i><i class="fa fa-times"></i></a>
                                    <button type="button" class="btn btn-default btn-clear-filters sc-btn-icon" data-controller="pjInvoice" data-action="pjActionGetInvoices" data-redirect="index.php?controller=pjInvoice&amp;action=pjActionInvoices" title="<?php echo htmlspecialchars(__('btnClearFilters', true) ?: 'Filters wissen', ENT_QUOTES); ?>" style="display:none;"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.row -->

                <div id="collapseInvoiceFilters" class="collapse">
                    <div class="m-b-lg">
                        <ul class="agile-list no-padding">
                            <li class="success-element b-r-sm">
                                <div class="panel-body">
                                    <form action="" method="get" class="frm-filter-advanced-invoices">
                                        <div class="row">
                                            <div class="col-md-<?php echo (isset($tpl['foreign_arr']) && !empty($tpl['foreign_arr'])) ? '6' : '12'; ?> col-sm-12">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo htmlspecialchars(__('plugin_invoice_status', true) ?: 'Factuurstatus', ENT_QUOTES); ?></label>
                                                    <select name="status_filter" id="adv_filter_invoice_status" class="form-control">
                                                        <option value="">— <?php echo htmlspecialchars(__('lblAll', true) ?: 'Alle statussen', ENT_QUOTES); ?> —</option>
                                                        <option value="paid"><?php echo pjSanitize::html($statuses['paid']); ?></option>
                                                        <option value="not_paid"><?php echo pjSanitize::html($statuses['not_paid']); ?></option>
                                                        <option value="cancelled"><?php echo pjSanitize::html($statuses['cancelled']); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if (isset($tpl['foreign_arr']) && !empty($tpl['foreign_arr'])): ?>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo htmlspecialchars(__('plugin_invoice_foreign', true) ?: 'Type', ENT_QUOTES); ?></label>
                                                    <select name="foreign_id" id="adv_filter_invoice_foreign" class="form-control">
                                                        <option value="">---</option>
                                                        <?php foreach ($tpl['foreign_arr'] as $item): ?>
                                                            <option value="<?php echo $item['id']; ?>"<?php echo !isset($_GET['foreign_id']) || $_GET['foreign_id'] != $item['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($item['title']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="grid"></div>
            </div>
        </div> 
	</div>
	</div>
	
	<!-- <div id="grid"></div> -->
	
	<div id="dialogSendInvoice" style="display: none" title="<?php __('plugin_invoice_send_invoice_title'); ?>"></div>
	
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	var myLabel = myLabel || {};
	myLabel.num = "<?php __('plugin_invoice_i_num'); ?>";
	myLabel.order_id = "<?php __('plugin_invoice_i_order_id'); ?>";
	myLabel.issue_date = "<?php __('plugin_invoice_i_issue_date'); ?>";
	myLabel.due_date = "<?php __('plugin_invoice_i_due_date'); ?>";
	myLabel.created = "<?php __('plugin_invoice_i_created'); ?>";
	myLabel.status = "<?php __('plugin_invoice_i_status'); ?>";
	myLabel.view_invoice = "<?php __('plugin_invoice_view_invoice'); ?>";
	myLabel.download_pdf_invoice = "<?php __('plugin_invoice_download_pdf_invoice'); ?>";
	myLabel.print_invoice = "<?php __('plugin_invoice_print_invoice'); ?>";
	myLabel.email_invoice = "<?php __('plugin_invoice_email_invoice'); ?>";
	myLabel.btn_send = "<?php __('btnSend'); ?>";
	myLabel.btn_cancel = "<?php __('btnCancel'); ?>";
	myLabel.total = "<?php __('plugin_invoice_i_total'); ?>";
	myLabel.delete_title = "<?php __('plugin_invoice_i_delete_title'); ?>";
	myLabel.delete_body = "<?php __('plugin_invoice_i_delete_body'); ?>";
	myLabel.download_pdfs_bulk = <?php $lbl = __('plugin_invoice_i_download_pdfs_bulk', true); echo json_encode(!empty($lbl) ? $lbl : 'Download PDFs'); ?>;
	myLabel.customer = "Klant";
	<?php
	// Strip HTML uit vertalingen — de inline edit-dropdown (select options) en badges willen plain text.
	// Oude vertalingen kunnen HTML bevatten (bv. "<strong style='color: red'>Niet betaald</strong>") die
	// via een double-quoted JS string literal een SyntaxError veroorzaakt.
	$status_paid_plain      = trim(strip_tags($statuses['paid']));
	$status_not_paid_plain  = trim(strip_tags($statuses['not_paid']));
	$status_cancelled_plain = trim(strip_tags($statuses['cancelled']));
	?>
	myLabel.status_paid_txt      = <?php echo json_encode($status_paid_plain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	myLabel.status_not_paid_txt  = <?php echo json_encode($status_not_paid_plain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	myLabel.status_cancelled_txt = <?php echo json_encode($status_cancelled_plain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	// Voor de inline edit-dropdown (plain labels)
	myLabel.paid      = <?php echo json_encode($status_paid_plain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	myLabel.not_paid  = <?php echo json_encode($status_not_paid_plain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	myLabel.cancelled = <?php echo json_encode($status_cancelled_plain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	myLabel.booking_url = "<?php echo defined('PJ_INVOICE_PLUGIN') ? PJ_INVOICE_PLUGIN : NULL; ?>";
	myLabel.empty_date = "<?php __('gridEmptyDate'); ?>";
	myLabel.invalid_date = "<?php __('gridInvalidDate'); ?>";
	myLabel.empty_datetime = "<?php __('gridEmptyDatetime'); ?>";
	myLabel.invalid_datetime = "<?php __('gridInvalidDatetime'); ?>";
	</script>
	<?php
}
?>