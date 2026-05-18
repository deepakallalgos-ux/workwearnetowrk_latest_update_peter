<?php
$isCheckoutReady = isset($tpl['price_arr']) && $tpl['price_arr']['status'] == 'OK';
?>

<?php if ((int) $tpl['option_arr']['o_disable_orders'] === 0) : ?>
<ul class="scCartMenu">
	<li class="scCartMenuIcon"><a href="<?php echo pjUtil::getReferer(); ?>#!/Cart"><span class="scColorOrange"><?php echo count($tpl['cart_arr']); ?></span> <?php count($tpl['cart_arr']) !== 1 ? __('front_items') : __('front_item'); ?></a></li>
	<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Cart"><span class="scColorOrange"><?php echo pjCurrency::formatPrice($isCheckoutReady ? $tpl['price_arr']['data']['total'] : '0.00'); ?></span></a></li>
	<li><a href="<?php echo pjUtil::getReferer(); ?>#!/Cart"><span class="scColorBlue"><?php __('front_cart'); ?></span></a></li>
</ul>
<?php endif; ?>