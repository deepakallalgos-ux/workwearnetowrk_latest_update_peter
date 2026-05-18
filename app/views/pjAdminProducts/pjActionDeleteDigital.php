<div class="col-md-4 col-sm-6 col-xs-12">
	<div class="form-group">
		<label class="control-label block">&nbsp;</label>
		<div>
			<input type="radio" class="i-checks" name="digital_choose" value="1" checked="checked"  /> <?php __('product_file_1');?>
			<input type="radio" class="i-checks" name="digital_choose" value="2"  /> <?php __('product_file_2');?>
		</div>
	</div><!-- /.form-group -->
</div>
<div class="col-md-4 col-sm-6 col-xs-12 digitalFile">
	<div class="form-group">
		<label class="control-label"><?php __('product_file_1'); ?></label>
		<div>
			<input type="file" name="digital_file" class="form-control" />
		</div>
	</div><!-- /.form-group -->
</div>
<div class="col-md-4 col-sm-6 col-xs-12 digitalPath" style="display: none">
	<div class="form-group">
		<label class="control-label"><?php __('product_file_2'); ?></label>
		<div>
			<input type="text" name="digital_file" class="form-control" maxlength="255" />
		</div>
	</div><!-- /.form-group -->
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
	<div class="form-group">
		<label class="control-label block"><?php __('product_digital_expire'); ?></label>
		<?php
		$h = $m = NULL;
		if (isset($tpl['arr']) && !empty($tpl['arr']['digital_expire']))
		{
			list($h, $m,) = explode(":", $tpl['arr']['digital_expire']);
		}
		?>
		<span class="form-inline"><?php echo pjTime::factory()->prop('selected', $h)->attr('name', 'hour')->attr('id', 'hour')->attr('class', 'form-control')->hour(); ?></span>
		<span class="form-inline"><?php echo pjTime::factory()->prop('selected', $m)->attr('name', 'minute')->attr('id', 'minute')->attr('class', 'form-control')->prop('step', 5)->minute(); ?></span>
		<span>HH:MM</span>
	</div><!-- /.form-group -->
</div>