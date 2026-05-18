<div class="container-fluid pjScLogReg">
	<div class="panel panel-default">
		<div class="panel-heading"><strong class="text-primary text-uppercase pjScLogRegTitle"><?php __('front_login'); ?></strong></div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-5"><p><?php __('front_login_note'); ?></p></div>
				<div class="col-sm-7">
					<form action="" method="post" class="scSelectorLoginForm">
						<input type="hidden" name="sc_login" value="1" />
						<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
						<div class="form-group required">
					    	<label class="control-label"><?php __('client_email'); ?></label>
							<div class="input-group">
						  		<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
						  		<input type="email" name="email" class="form-control" placeholder="<?php __('front_placeholder_email', false, true); ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>">
							</div>
					  	</div>
					  	<div class="form-group required">
					    	<label class="control-label"><?php __('client_password'); ?></label>
					    	<div class="input-group">
							  	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
							  	<input type="password" name="password" class="form-control" placeholder="<?php __('front_placeholder_password', false, true); ?>" data-err="<?php echo $validate['password'];?>">
							  	<span class="input-group-addon pjScEyeIcon" title="<?php __('front_show_password');?>" data-show="<?php __('front_show_password');?>" data-hide="<?php __('front_hide_password');?>"><span class="glyphicon glyphicon-eye-open"></span></span>
							</div>
					  	</div>
					  	<button type="submit" class="btn btn-default scSelectorButton pjScBtnPrimary"><?php __('front_login', false, true); ?></button>
					  	<a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>forgot-password" class="btn btn-link scSelectorForgot pjScBtnLink"><?php __('front_forgot'); ?></a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>