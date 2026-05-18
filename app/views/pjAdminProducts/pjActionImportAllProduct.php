<div class="row">
	<div class="col-lg-12">
		<div class="tabs-container">
			<div class="tab-content">
				<div class="tab-pane active">
					<div class="panel-body">
						<div class="ibox-content no-margins no-padding no-top-border">
							<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionImportAllProduct" method="post" class="form-horizontal" enctype="multipart/form-data">
								<input type="hidden" name="import" value="1" />
								<div class="m-t-sm m-b-sm">
									<h2><?php __('plugin_base_locale_lbl_import');?></h2>
								</div>
								<div class="hr-line-dashed"></div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php __('plugin_base_locale_browse_csv_file');?></label>
									<div class="col-sm-6">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<span class="btn btn-primary btn-outline btn-file">
											<input name="file" type="file" class="fileinput required" data-msg-required="<?php __('plugin_base_validate_select_file', false, true);?>"/></span>
											<span class="fileinput-filename"></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">&nbsp;</label>
									<div class="col-sm-6">
										<button type="submit" class="ladda-button btn btn-primary btn-lg pull-left btn-phpjabbers-loader" data-style="zoom-in">
											<span class="ladda-label"><?php __('plugin_base_btn_import'); ?></span>
										</button>
									</div>
								</div>
							</form>
							<br/><br/>
						</div><!-- /.ibox-content no-margins no-padding no-top-border -->
					</div><!-- /.panel-body -->
				</div><!-- /.tab-pane active -->
			</div><!-- /.tab-content -->
		</div><!-- /.tabs-container -->
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->