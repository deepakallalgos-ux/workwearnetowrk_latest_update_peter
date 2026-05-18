<form action="" method="post">
	<input type="hidden" name="stock_add" value="1" />
	<input type="hidden" name="order_id" value="<?php echo $controller->_get->toInt('order_id'); ?>" />
	<div class="row form-group">
		<label class="col-md-4 col-sm-5 col-xs-12 control-label"><?php __('order_select_product'); ?></label>
		<div class="col-md-8 col-sm-7 col-xs-12">
			<select name="product_id" class="form-control stock-product">
				<option value="">-- <?php __('order_p_name'); ?> --</option>
				<?php
				foreach ($tpl['product_arr'] as $product)
				{
					?><option value="<?php echo $product['id']; ?>"><?php echo pjSanitize::html($product['name'] . " (" . $product['sku'] . ")"); ?></option><?php
				}
				?>
			</select>
		</div>
	</div>
	
	<div class="p stock-products"></div>
</form>