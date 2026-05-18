<div class="table-responsive table-responsive-secondary">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th><?php __('product_name'); ?></th>
				<th><?php __('product_sku'); ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (count($tpl['arr']) > 0)
		{
			foreach ($tpl['arr'] as $k => $product)
			{
				?>
				<tr>
					<td><?php echo pjSanitize::html($product['name']); ?></td>
					<td><?php echo pjSanitize::html($product['sku']); ?></td>
					<td align="right"><button value="<?php echo $product['id']; ?>" class="btn btn-primary btn-sm btnCopy copy<?php echo $controller->_get->toString('copy'); ?>"><?php __('product_attr_copy_btn'); ?></button></td>
				</tr>
				<?php
			}
		}else{
			?>
			<tr>
				<td colspan="3"><?php __('product_empty');?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
</div>