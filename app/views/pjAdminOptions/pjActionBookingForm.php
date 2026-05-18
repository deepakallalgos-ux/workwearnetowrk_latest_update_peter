<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true); 
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-lg-9 col-md-8 col-sm-6">
				<h2><?php __('infoBookingFormTitle');?></h2>
			</div>
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoBookingFormDesc');?></p>
	</div><!-- /.col-md-12 -->
</div>
<div class="wrapper wrapper-content animated fadeInRight">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" id="frmUpdateOptions">
		<input type="hidden" name="options_update" value="1" />
        <input type="hidden" name="tab" value="3" />
        <input type="hidden" name="next_action" value="pjActionBookingForm" />
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-content">
						<?php
						$error_code = $controller->_get->toString('err');
						if (!empty($error_code))
					    {
					    	switch ($error_code)
					    	{
					    		case in_array($error_code, array('AOP03')):
					    			?>
					    			<div class="alert alert-success">
					    				<i class="fa fa-check m-r-xs"></i>
					    				<strong><?php echo @$titles[$error_code]; ?></strong>
					    				<?php echo @$bodies[$error_code]?>
					    			</div>
					    			<?php
					    			break;
					    		case in_array($error_code, array('')):
					    			?>
					    			<div class="alert alert-danger">
					    				<i class="fa fa-exclamation-triangle m-r-xs"></i>
					    				<strong><?php echo @$titles[$error_code]; ?></strong>
					    				<?php echo @$bodies[$error_code]?>
					    			</div>
					    			<?php
					    			break;
					    	}
					    }
						?>
						<div class="row">
							<?php foreach ($tpl['arr'] as $option) { ?>
							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php __('opt_' . $option['key']); ?></label>

									<?php include dirname(__FILE__) . '/elements/enum.php'; ?>
								</div>
							</div><!-- /.col-md-3 -->
							<?php } ?>							
						</div>

						<div class="hr-line-dashed"></div>

						<button class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader" data-style="zoom-in">
							<span class="ladda-label"><?php __('plugin_base_btn_save'); ?></span>
							<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>