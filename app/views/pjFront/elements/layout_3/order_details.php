<?php
// Status badge helper
function getOrderStatusClass($status) {
	switch ($status) {
		case 'completed': case 'processing': case 'delivered': case 'picked_up': return 'success';
		case 'pending': case 'new': return 'warning';
		case 'cancelled': case 'refunded': return 'danger';
		case 'processing': return 'info';
		default: return 'default';
	}
}
?>
<?php
$install_url = isset($tpl['option_arr']['o_install_url']) ? $tpl['option_arr']['o_install_url'] : '';
$page_prefix_segment = (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : '';
$sc_orders_list_href = rtrim($install_url, '/') . '/' . $page_prefix_segment . 'orders';
?>
<div class="container-fluid pjScCart pjScOrderDetails">

	<?php if ($tpl['status'] == 'OK') {
		$payment_methods_arr = __('payment_methods', true);
		$order_statuses_arr = __('order_statuses', true);
		$pay_link = false;
		$orderPaymentStatus = isset($tpl['arr']['payment_status']) ? $tpl['arr']['payment_status'] : '';
		if ($orderPaymentStatus !== 'paid' && $orderPaymentStatus !== 'refunded' && $tpl['arr']['status'] !== 'cancelled' && !in_array($tpl['arr']['payment_method'], array('creditcard', 'bank', 'cod', 'cash'))) {
			$pay_link = true;
		}
		$status_label = isset($order_statuses_arr[$tpl['arr']['status']]) ? $order_statuses_arr[$tpl['arr']['status']] : ucfirst(str_replace('_', ' ', $tpl['arr']['status']));
		$status_class = getOrderStatusClass($tpl['arr']['status']);
		$invoice_available = !empty($tpl['invoice_available']);
		$sc_invoice_href = PJ_INSTALL_URL . 'index.php?controller=pjFrontPublic&action=pjActionOrderInvoice&uuid=' . urlencode($tpl['arr']['uuid']);
	?>

	<!-- Terug knop -->
	<a href="<?php echo pjSanitize::html($sc_orders_list_href); ?>" class="sc-od-back scSelectorOrdersHistory">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
		<?php echo __('front_back_to_orders', true) ?: 'Back to orders'; ?>
	</a>

	<!-- Order Header Card -->
	<div class="sc-od-header">
		<div class="sc-od-header-main">
			<div class="sc-od-header-left">
				<div class="sc-od-order-number"><?php echo pjSanitize::html($tpl['arr']['uuid'] ?? ''); ?></div>
				<div class="sc-od-order-date">
					<span class="sc-od-date-label"><?php echo __('front_order_created', true) ?: 'Order date'; ?>:</span>
					<?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['created'])); ?>
				</div>
			</div>
			<div class="sc-od-header-right">
				<span class="sc-od-status sc-od-status--<?php echo $status_class; ?>"><?php echo pjSanitize::html($status_label); ?></span>
			</div>
		</div>
		<div class="sc-od-header-meta">
			<span class="sc-od-meta-item">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
				<?php
				$mollie_info = isset($tpl['mollie_info']) ? $tpl['mollie_info'] : array();
				$payment_label = @$payment_methods_arr[$tpl['arr']['payment_method']];
				// Mollie methode weergeven (bijv. "iDEAL")
				if (!empty($mollie_info['method'])) {
					$mollie_methods = array('ideal' => 'iDEAL', 'creditcard' => 'Creditcard', 'bancontact' => 'Bancontact', 'banktransfer' => 'Bankoverschrijving', 'paypal' => 'PayPal', 'applepay' => 'Apple Pay', 'klarna' => 'Klarna', 'in3' => 'in3', 'riverty' => 'Riverty', 'twint' => 'Twint');
					$method_name = isset($mollie_methods[$mollie_info['method']]) ? $mollie_methods[$mollie_info['method']] : ucfirst($mollie_info['method']);
					$payment_label = $method_name;
					// Bank details (bijv. "via Rabobank")
					if (!empty($mollie_info['details']['consumerName'])) {
						$payment_label .= ' (' . pjSanitize::html($mollie_info['details']['consumerName']) . ')';
					}
					if (!empty($mollie_info['details']['cardHolder'])) {
						$payment_label .= ' (' . pjSanitize::html($mollie_info['details']['cardHolder']) . ')';
					}
				}
				echo $payment_label;
				?>
			</span>
			<?php
			$payment_status_labels_od = array(
				'pending'   => __('front_payment_status_pending', true) ?: 'Pending',
				'pay_later' => __('front_payment_status_pay_later', true) ?: 'Pay later',
				'paid'      => __('front_payment_status_paid', true) ?: 'Paid',
				'failed'    => __('front_payment_status_failed', true) ?: 'Failed',
				'refunded'  => __('front_payment_status_refunded', true) ?: 'Refunded',
				'unknown'   => __('front_payment_status_unknown', true) ?: 'Unknown',
			);
			$ps_od = isset($tpl['arr']['payment_status']) ? $tpl['arr']['payment_status'] : 'pending';
			$ps_od_label = isset($payment_status_labels_od[$ps_od]) ? $payment_status_labels_od[$ps_od] : ucfirst(str_replace('_', ' ', $ps_od));
			?>
			<span class="sc-od-meta-item">
				<strong><?php echo __('front_order_payment_status', true) ?: 'Payment status'; ?>:</strong>
				<?php echo pjSanitize::html($ps_od_label); ?>
			</span>
			<?php if ($pay_link && class_exists('pjMollie') && method_exists('pjMollie', 'getRetryPaymentUrl')) {
				$retry_url = pjMollie::getRetryPaymentUrl($tpl['arr']);
			?>
			<a href="<?php echo pjSanitize::html($retry_url); ?>" class="sc-od-meta-item sc-od-pay-link">
				<?php echo __('front_order_pay_order', true) ?: 'Pay now'; ?>
			</a>
			<?php } ?>
			<?php if ($invoice_available) { ?>
			<a href="<?php echo pjSanitize::html($sc_invoice_href); ?>" class="sc-od-meta-item sc-od-invoice-link" target="_blank">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
				<?php echo __('front_print_invoice', true) ?: 'Download invoice'; ?>
			</a>
			<?php } ?>
		</div>
	</div>

	<?php if ($invoice_available) { ?>
	<div class="sc-od-actions sc-od-actions--top">
		<a href="<?php echo pjSanitize::html($sc_invoice_href); ?>" class="pjScBtnSecondary" target="_blank">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
			<?php echo __('front_print_invoice', true) ?: 'Download invoice'; ?>
		</a>
	</div>
	<?php } ?>

	<!-- Products (cart-style layout) -->
	<div class="sc-od-card pjScCart-page">
		<?php
		$total_products = 0;
		$total_extras = 0;
		?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th></th>
					<th class="hidden-xs"></th>
					<th width="100%">
						<div class="row">
							<div class="col-sm-5 mob-name"><?php echo __('front_product', true) ?: 'Product'; ?></div>
							<div class="col-sm-3 hidden-xs text-right"><?php echo __('front_price', true) ?: 'Price'; ?></div>
							<div class="col-sm-1 hidden-xs text-center"><?php echo __('front_quantity', true) ?: 'Quantity'; ?></div>
							<div class="col-sm-3 text-right"><?php echo __('front_total', true) ?: 'Total'; ?></div>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($tpl['os_arr'] as $i => $item) {
					// `order_stocks.price` is in incl-mode AL incl. BTW opgeslagen (zie admin pjActionUpdate.php
					// regel 820: "stocks.price is in incl-mode reeds incl. BTW"). Dus geen × (1 + vat/100) meer.
					$unit_price = (float) $item['price'];
					$line_total = $unit_price * (int) $item['qty'];
					$total_products += $line_total;

					$img_path = !empty($item['product_image']) && is_file(PJ_INSTALL_PATH . $item['product_image'])
						? PJ_INSTALL_URL . $item['product_image']
						: PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/80x106.png';
				?>
				<tr>
					<td class="hidden-xs sc-od-img-cell">
						<img class="product-img-larg" src="<?php echo $img_path; ?>" alt="<?php echo pjSanitize::html($item['name']); ?>">
					</td>
					<td class="hidden-xs" style="width:1px;"></td>
					<td>
						<div class="row top-pad1">
							<!-- Product naam -->
							<div class="col-sm-5 mob-one">
								<span class="product-name"><?php echo pjSanitize::html($item['name']); ?></span>
								<?php
								if (isset($item['attr']) && !empty($item['attr'])) {
									$at = array();
									$a = explode(",", $item['attr']);
									foreach ($a as $v) {
										$t = explode("_", $v);
										if (isset($t[0], $t[1])) $at[$t[1]] = $t[0];
									}
									foreach ($at as $attr_parent_id => $attr_id) {
										foreach ($tpl['attr_arr'] as $attr) {
											if ($attr['id'] == $attr_parent_id) {
												foreach ($attr['child'] as $child) {
													if ($child['id'] == $attr_id) {
														printf('<div class="sc-od-attr">%s: %s</div>', pjSanitize::html($attr['name']), pjSanitize::html($child['name']));
														break;
													}
												}
											}
										}
									}
								}
								?>
							</div>
							<!-- Eenheidsprijs -->
							<div class="col-sm-3 hidden-xs text-right sc-od-unit-price">
								<?php echo pjCurrency::formatPrice($unit_price); ?>
							</div>
							<!-- Aantal -->
							<div class="col-sm-1 hidden-xs text-center">
								<?php echo (int) $item['qty']; ?>
							</div>
							<!-- Regeltotaal -->
							<div class="col-sm-3 text-right mob-to">
								<span class="sc-current-price"><?php echo pjCurrency::formatPrice($line_total); ?></span>
							</div>
						</div>

						<?php
						// Extras weergeven
						if (isset($item['extra']) && !empty($item['extra'])) {
							$extras_display = array();
							$a = explode(",", $item['extra']);
							foreach ($a as $eid) {
								$_arr = explode('.', $eid);
								if (count($_arr) == 2) {
									$e_id = $_arr[0] ?? '';
									$ei_id = $_arr[1] ?? '';
									foreach ($tpl['extra_arr'] as $extra) {
										if ($extra['id'] == $e_id && isset($extra['extra_items'])) {
											foreach ($extra['extra_items'] as $extra_item) {
												if ($extra_item['id'] == $ei_id) {
													$ep = (float) $extra_item['price'];
													$extras_display[] = array('name' => $extra_item['name'], 'price' => $ep, 'total' => $ep * (int) $item['qty']);
													$total_extras += $ep * (int) $item['qty'];
													break;
												}
											}
											break;
										}
									}
								} else {
									$parts = explode("|", $eid);
									$e_id = $parts[0] ?? '';
									if (empty($e_id)) continue;
									foreach ($tpl['extra_arr'] as $extra) {
										if ($extra['id'] == $e_id) {
											$ep = (float) $extra['price'];
											$extras_display[] = array('name' => $extra['name'], 'price' => $ep, 'total' => $ep * (int) $item['qty']);
											$total_extras += $ep * (int) $item['qty'];
											break;
										}
									}
								}
							}
							if (!empty($extras_display)) {
						?>
						<div class="row">
							<div class="col-sm-12 row-pading">
								<?php foreach ($extras_display as $ed) { ?>
								<div class="row mb-1 sc-extra-row">
									<div class="col-sm-5 sc-od-extra-name"><?php echo pjSanitize::html($ed['name']); ?></div>
									<div class="col-sm-3 hidden-xs sc-od-extra-price text-right"><?php echo pjCurrency::formatPrice($ed['price']); ?></div>
									<div class="col-sm-1 hidden-xs"></div>
									<div class="col-sm-3 sc-od-extra-price text-right"><?php echo pjCurrency::formatPrice($ed['total']); ?></div>
								</div>
								<?php } ?>
							</div>
						</div>
						<?php } } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<!-- Totals (Workwear: price, discount, insurance, shipping, tax, total) -->
		<?php
		$_price       = (float) ($tpl['arr']['price'] ?? 0);
		$_discount    = (float) ($tpl['arr']['discount'] ?? 0);
		$_insurance   = (float) ($tpl['arr']['insurance'] ?? 0);
		$_shipping    = (float) ($tpl['arr']['shipping'] ?? 0);
		$_tax         = (float) ($tpl['arr']['tax'] ?? 0);
		$_grand_total = (float) ($tpl['arr']['total'] ?? 0);
		?>
		<div class="sc-od-totals">
			<div class="sc-od-total-row">
				<span><?php echo __('front_sub_total', true) ?: 'Subtotal'; ?></span>
				<span><?php echo pjCurrency::formatPrice($_price); ?></span>
			</div>
			<?php if ($_discount > 0) { ?>
			<div class="sc-od-total-row">
				<span><?php echo __('front_discount', true) ?: 'Discount'; ?><?php echo !empty($tpl['arr']['voucher']) ? ' <strong>' . pjSanitize::html($tpl['arr']['voucher']) . '</strong>' : ''; ?></span>
				<span class="sc-summary-discount">-<?php echo pjCurrency::formatPrice($_discount); ?></span>
			</div>
			<?php } ?>
			<?php if ($_insurance > 0) { ?>
			<div class="sc-od-total-row">
				<span><?php echo __('front_order_insurance', true) ?: 'Insurance'; ?></span>
				<span><?php echo pjCurrency::formatPrice($_insurance); ?></span>
			</div>
			<?php } ?>
			<?php if ($_shipping > 0) { ?>
			<div class="sc-od-total-row">
				<span><?php echo __('front_shipping', true) ?: 'Shipping'; ?></span>
				<span><?php echo pjCurrency::formatPrice($_shipping); ?></span>
			</div>
			<?php } ?>
			<?php if ($_tax > 0) { ?>
			<div class="sc-od-total-row">
				<span><?php echo __('front_tax', true) ?: 'Tax'; ?></span>
				<span><?php echo pjCurrency::formatPrice($_tax); ?></span>
			</div>
			<?php } ?>
			<div class="sc-od-total-row sc-od-total-row--grand">
				<span><?php echo __('front_order_total', true) ?: 'Total'; ?></span>
				<span><?php echo pjCurrency::formatPrice($_grand_total); ?></span>
			</div>
		</div>
	</div>

	<!-- Digital downloads (fase 4 + 6) — onder Producten geplaatst voor klant-flow -->
	<?php if (!empty($tpl['digital_downloads'])):
		$dlDateFmt = !empty($tpl['option_arr']['o_date_format']) ? $tpl['option_arr']['o_date_format'] : 'd-m-Y';
		$dlTimeFmt = !empty($tpl['option_arr']['o_time_format']) ? $tpl['option_arr']['o_time_format'] : 'H:i';
		$dlBaseUrl = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionDigitalGet&token=';
	?>
	<div id="digital-downloads" class="sc-od-card sc-od-downloads">
		<h3 class="sc-od-downloads-title"><?php __('front_digital_downloads_title'); ?></h3>
		<p class="sc-od-downloads-intro"><?php __('front_digital_downloads_intro'); ?></p>

		<div class="sc-od-downloads-list">
		<?php foreach ($tpl['digital_downloads'] as $dd):
			$ddCount = (int) $dd['download_count'];
			$ddMax   = ($dd['max_downloads'] !== null && $dd['max_downloads'] !== '') ? (int) $dd['max_downloads'] : null;
			$ddExpiresTs = !empty($dd['expires_at']) ? strtotime($dd['expires_at']) : null;
			$ddIsExpired = ($ddExpiresTs !== null && $ddExpiresTs < time());
			$ddIsMaxed   = ($ddMax !== null && $ddCount >= $ddMax);
			$ddDownloadable = (!$ddIsExpired && !$ddIsMaxed);
			$ddUrl = $dlBaseUrl . urlencode((string) $dd['token']);

			// Stat 1 — counter "remaining/max" of "∞"
			if ($ddMax !== null) {
				$ddCounterText = sprintf('%d/%d', max(0, $ddMax - $ddCount), $ddMax);
			} else {
				$ddCounterText = '∞';
			}
			// Stat 2 — geldig tot (label "Downloaden tot" → waarde alleen datum)
			$ddExpiryText = ($ddExpiresTs !== null)
				? date($dlDateFmt, $ddExpiresTs)
				: '—';
			// Stat 3 — laatst gedownload
			$ddLastText = '—';
			if (!empty($dd['last_downloaded_at'])) {
				$lastTs = strtotime($dd['last_downloaded_at']);
				if ($lastTs) {
					$ddLastText = date($dlDateFmt . ' ' . $dlTimeFmt, $lastTs);
				}
			}
		?>
			<div class="sc-od-download-item">
				<div class="sc-od-download-action">
					<?php if ($ddDownloadable): ?>
					<a href="<?php echo pjSanitize::html($ddUrl); ?>" class="pjScBtnPrimary sc-od-download-btn">
						<?php __('digital_download_btn'); ?>
					</a>
					<?php elseif ($ddIsMaxed): ?>
					<span class="sc-od-download-badge sc-od-download-badge--blocked">
						<?php __('front_digital_status_max'); ?>
					</span>
					<?php elseif ($ddIsExpired): ?>
					<span class="sc-od-download-badge sc-od-download-badge--blocked">
						<?php __('front_digital_status_expired'); ?>
					</span>
					<?php endif; ?>

					<?php if (!empty($dd['password'])): ?>
					<div class="sc-od-download-pw" data-pw="<?php echo pjSanitize::html($dd['password']); ?>">
						<span class="sc-od-download-pw-label"><?php __('digital_download_password_label'); ?>:</span>
						<code class="sc-od-download-pw-value"><?php echo pjSanitize::html($dd['password']); ?></code>
						<button type="button" class="sc-od-download-pw-copy" title="<?php echo __('front_digital_copied', true) ?: 'Copied!'; ?>" aria-label="<?php echo __('front_digital_copied', true) ?: 'Copied!'; ?>">
							<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
								<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
							</svg>
						</button>
					</div>
					<?php endif; ?>
				</div>

				<div class="sc-od-download-stats">
					<div class="sc-od-stat-card">
						<svg class="sc-od-stat-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
						<span class="sc-od-stat-label"><?php echo __('front_digital_label_count', true) ?: 'Download count'; ?></span>
						<span class="sc-od-stat-text"><?php echo pjSanitize::html($ddCounterText); ?></span>
					</div>
					<div class="sc-od-stat-card">
						<svg class="sc-od-stat-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
						<span class="sc-od-stat-label"><?php echo __('front_digital_label_until', true) ?: 'Download until'; ?></span>
						<span class="sc-od-stat-text"><?php echo pjSanitize::html($ddExpiryText); ?></span>
					</div>
					<div class="sc-od-stat-card">
						<svg class="sc-od-stat-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
						<span class="sc-od-stat-label"><?php echo __('front_digital_label_last', true) ?: 'Last download'; ?></span>
						<span class="sc-od-stat-text"><?php echo pjSanitize::html($ddLastText); ?></span>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>

	<script>
	(function() {
		var copiedLabel = <?php echo json_encode(__('front_digital_copied', true) ?: 'Copied!', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
		document.querySelectorAll('.sc-od-download-pw-copy').forEach(function(btn) {
			btn.addEventListener('click', function() {
				var wrap = btn.closest('.sc-od-download-pw');
				if (!wrap) return;
				var pw = wrap.getAttribute('data-pw') || '';
				if (!pw) return;
				var done = function() {
					btn.classList.add('is-copied');
					var oldTitle = btn.getAttribute('title');
					btn.setAttribute('title', copiedLabel);
					setTimeout(function() {
						btn.classList.remove('is-copied');
						if (oldTitle) btn.setAttribute('title', oldTitle);
					}, 1800);
				};
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(pw).then(done, function() {});
				} else {
					var ta = document.createElement('textarea');
					ta.value = pw;
					ta.style.position = 'absolute';
					ta.style.left = '-9999px';
					document.body.appendChild(ta);
					ta.select();
					try { document.execCommand('copy'); done(); } catch (e) {}
					document.body.removeChild(ta);
				}
			});
		});
	})();
	</script>
	<?php endif; ?>

	<?php if (!empty($tpl['arr']['voucher'])) { ?>
	<div class="sc-od-card sc-od-card--compact">
		<div class="sc-od-card-title"><?php echo __('front_order_voucher', true) ?: 'Discount code'; ?></div>
		<div class="sc-od-voucher-code"><?php echo pjSanitize::html($tpl['arr']['voucher']); ?></div>
	</div>
	<?php } ?>

	<?php if (!empty(trim($tpl['arr']['notes'] ?? ''))) { ?>
	<div class="sc-od-card sc-od-card--compact">
		<div class="sc-od-card-title"><?php echo __('front_order_notes', true) ?: 'Notes'; ?></div>
		<div class="sc-od-notes"><?php echo nl2br(pjSanitize::html($tpl['arr']['notes'])); ?></div>
	</div>
	<?php } ?>

	<?php
	// Custom fields
	if (!empty($tpl['arr']['custom_fields'])) {
		$cf_values = json_decode($tpl['arr']['custom_fields'], true);
		if (is_array($cf_values)) {
			$cf_defs = pjCheckoutCustomFieldModel::factory()->findAll()->getData();
			$cf_map = array();
			foreach ($cf_defs as $d) $cf_map[$d['field_key']] = $d;
			$has_cf = false;
			foreach ($cf_values as $ck => $cv) {
				if (!empty($cv) || $cv === '0') { $has_cf = true; break; }
			}
			if ($has_cf) {
	?>
	<div class="sc-od-card sc-od-card--compact">
		<div class="sc-od-card-title"><?php echo __('front_custom_fields', true) ?: 'Additional details'; ?></div>
		<div class="sc-od-field-grid">
			<?php foreach ($cf_values as $ck => $cv) {
				if (empty($cv) && $cv !== '0') continue;
				$cf_id = isset($cf_map[$ck]) ? $cf_map[$ck]['id'] : 0;
				$cf_label = $cf_id ? (__('cf_' . $cf_id . '_label', true) ?: $ck) : $ck;
				$cf_type = isset($cf_map[$ck]) ? $cf_map[$ck]['field_type'] : 'text';
				$display_val = ($cf_type === 'checkbox') ? ($cv ? (__('front_yes', true) ?: 'Yes') : (__('front_no', true) ?: 'No')) : pjSanitize::html($cv);
			?>
			<div class="sc-od-field">
				<div class="sc-od-field-label"><?php echo pjSanitize::html($cf_label); ?></div>
				<div class="sc-od-field-value"><?php echo $display_val; ?></div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } } } ?>

	<!-- Addresses -->
	<?php
	// Factuuradres altijd tonen
	$b_firstname = $tpl['arr']['b_firstname'] ?? '';
	$b_lastname = $tpl['arr']['b_lastname'] ?? '';
	$b_name = $tpl['arr']['b_name'] ?? '';
	$b_fullname = trim(trim($b_firstname . ' ' . ($b_lastname !== '' ? $b_lastname : $b_name)));
	if ($b_fullname === '' && $b_name !== '') {
		$b_fullname = trim($b_name);
	}
	$b_addr = trim(($tpl['arr']['b_address_1'] ?? '') . ' ' . (@$tpl['arr']['b_house_number'] ?: ''));
	$has_billing = (!empty($b_fullname) || !empty($b_addr) || !empty($tpl['arr']['b_city']));

	$s_firstname_chk = $tpl['arr']['s_firstname'] ?? '';
	$s_name_chk = $tpl['arr']['s_name'] ?? '';
	$s_addr_chk = $tpl['arr']['s_address_1'] ?? '';
	$s_city_chk = $tpl['arr']['s_city'] ?? '';
	$has_quote_address = ((int) ($tpl['arr']['same_as'] ?? 0) === 1 && $has_billing)
		|| !empty(trim($s_firstname_chk)) || !empty(trim($s_name_chk)) || !empty(trim($s_addr_chk)) || !empty(trim($s_city_chk));
	$quote_address_title = __('front_quote_address', true) ?: 'Quote address';
	?>
	<?php if ($has_billing || $has_quote_address) { ?>
	<div class="sc-od-address-grid<?php echo (!$has_quote_address) ? ' sc-od-address-grid--single' : ''; ?>">
		<?php if ($has_billing) { ?>
		<div class="sc-od-card sc-od-card--compact">
			<div class="sc-od-card-title"><?php echo __('front_order_tab_billing_details', true) ?: 'Billing address'; ?></div>
			<div class="sc-od-address">
				<?php if (!empty($b_fullname)) { ?><div class="sc-od-address-name"><?php echo pjSanitize::html($b_fullname); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_company'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['b_company']); ?></div><?php } ?>
				<?php if (!empty($b_addr)) { ?><div><?php echo pjSanitize::html($b_addr); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_address_2'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['b_address_2']); ?></div><?php } ?>
				<?php
				$b_location = array();
				if (!empty($tpl['arr']['b_zip'])) $b_location[] = pjSanitize::html($tpl['arr']['b_zip']);
				if (!empty($tpl['arr']['b_city'])) $b_location[] = pjSanitize::html($tpl['arr']['b_city']);
				if (!empty($b_location)) { ?><div><?php echo implode(' ', $b_location); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_state'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['b_state']); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_country'])) { ?><div class="sc-od-address-country"><?php echo pjSanitize::html($tpl['arr']['b_country']); ?></div><?php } ?>
			</div>
		</div>
		<?php } ?>

		<?php if ($has_quote_address) {
			$shipping_is_empty = (empty(trim($s_firstname_chk)) && empty(trim($s_name_chk)) && empty(trim($s_addr_chk)) && empty(trim($s_city_chk)));
			if ((int) ($tpl['arr']['same_as'] ?? 0) === 1 || $shipping_is_empty) { ?>
		<div class="sc-od-card sc-od-card--compact">
			<div class="sc-od-card-title"><?php echo pjSanitize::html($quote_address_title); ?></div>
			<div class="sc-od-address">
				<?php if (!empty($b_fullname)) { ?><div class="sc-od-address-name"><?php echo pjSanitize::html($b_fullname); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_company'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['b_company']); ?></div><?php } ?>
				<?php if (!empty($b_addr)) { ?><div><?php echo pjSanitize::html($b_addr); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_address_2'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['b_address_2']); ?></div><?php } ?>
				<?php if (!empty($b_location)) { ?><div><?php echo implode(' ', $b_location); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_state'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['b_state']); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['b_country'])) { ?><div class="sc-od-address-country"><?php echo pjSanitize::html($tpl['arr']['b_country']); ?></div><?php } ?>
			</div>
		</div>
		<?php } else {
				$s_firstname = $tpl['arr']['s_firstname'] ?? '';
				$s_lastname = $tpl['arr']['s_lastname'] ?? '';
				$s_name = $tpl['arr']['s_name'] ?? '';
				$s_fullname = trim(trim($s_firstname . ' ' . ($s_lastname !== '' ? $s_lastname : $s_name)));
				if ($s_fullname === '' && $s_name !== '') {
					$s_fullname = trim($s_name);
				}
				$s_addr = trim(($tpl['arr']['s_address_1'] ?? '') . ' ' . (@$tpl['arr']['s_house_number'] ?: ''));
		?>
		<div class="sc-od-card sc-od-card--compact">
			<div class="sc-od-card-title"><?php echo pjSanitize::html($quote_address_title); ?></div>
			<div class="sc-od-address">
				<?php if (!empty($s_fullname)) { ?><div class="sc-od-address-name"><?php echo pjSanitize::html($s_fullname); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['s_company'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['s_company']); ?></div><?php } ?>
				<?php if (!empty($s_addr)) { ?><div><?php echo pjSanitize::html($s_addr); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['s_address_2'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['s_address_2']); ?></div><?php } ?>
				<?php
				$s_location = array();
				if (!empty($tpl['arr']['s_zip'])) $s_location[] = pjSanitize::html($tpl['arr']['s_zip']);
				if (!empty($tpl['arr']['s_city'])) $s_location[] = pjSanitize::html($tpl['arr']['s_city']);
				if (!empty($s_location)) { ?><div><?php echo implode(' ', $s_location); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['s_state'])) { ?><div><?php echo pjSanitize::html($tpl['arr']['s_state']); ?></div><?php } ?>
				<?php if (!empty($tpl['arr']['s_country'])) { ?><div class="sc-od-address-country"><?php echo pjSanitize::html($tpl['arr']['s_country']); ?></div><?php } ?>
			</div>
		</div>
		<?php } ?>
		<?php } ?>
	</div>
	<?php } ?>

	<!-- Actions -->
	<div class="sc-od-actions">
		<?php if (!empty($invoice_available)) { ?>
		<a href="<?php echo pjSanitize::html($sc_invoice_href); ?>"
			class="pjScBtnSecondary" target="_blank">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
			<?php echo __('front_print_invoice', true) ?: 'Download invoice'; ?>
		</a>
		<?php } ?>
		<?php if ($pay_link && class_exists('pjMollie') && method_exists('pjMollie', 'getRetryPaymentUrl')) {
			$retry_url_action = pjMollie::getRetryPaymentUrl($tpl['arr']);
		?>
		<a href="<?php echo pjSanitize::html($retry_url_action); ?>" class="pjScBtnPrimary">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
			<?php echo __('front_order_pay_order', true) ?: 'Direct betalen'; ?>
		</a>
		<?php } ?>
		<?php /* Reorder requires pjFrontCart::pjActionReorder (Peter only) — enable when ported */ ?>
	</div>

	<?php } else {
		$isLoggedIn = $controller->isLoged();
	?>
	<div class="sc-od-empty">
		<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
		<?php if (!$isLoggedIn) : ?>
		<p><?php echo __('front_order_login_required', true) ?: 'Please log in to view your order.'; ?></p>
		<div class="sc-button-group" style="margin-top:12px;justify-content:center;">
			<a href="<?php echo pjSanitize::html($sc_orders_list_href); ?>" class="pjScBtnSecondary scSelectorOrdersHistory"><?php echo __('front_back_to_orders', true) ?: 'Back to orders'; ?></a>
			<a href="<?php echo pjSanitize::html(rtrim($install_url, '/') . '/' . $page_prefix_segment . 'login'); ?>" class="pjScBtnPrimary scSelectorLogin"><?php echo __('front_login', true) ?: 'Log in'; ?></a>
		</div>
		<?php else : ?>
		<p><?php echo __('front_order_not_found', true) ?: 'Order not found.'; ?></p>
		<div class="sc-button-group" style="margin-top:12px;justify-content:center;">
			<a href="<?php echo pjSanitize::html($sc_orders_list_href); ?>" class="pjScBtnSecondary scSelectorOrdersHistory"><?php echo __('front_back_to_orders', true) ?: 'Back to orders'; ?></a>
		</div>
		<?php endif; ?>
	</div>
	<?php } ?>
</div>
