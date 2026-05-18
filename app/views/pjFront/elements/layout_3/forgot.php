<div class="container-fluid pjScLogReg">
	<div class="panel panel-default">
		<div class="panel-heading"><strong class="text-primary text-uppercase pjScLogRegTitle"><?php __('front_forgot_password'); ?></strong></div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-5">
					<p><?php __('front_forgot_note'); ?></p>
				</div>
				<div class="col-sm-7">
					<form action="" method="post" class="scSelectorForgotForm">
						<input type="hidden" name="sc_forgot" value="1" />
						<div class="form-group required">
					    	<label class="control-label"><?php __('client_email'); ?></label>
							<div class="input-group">
							  	<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
							  	<input type="email" name="email" class="form-control" placeholder="<?php __('front_placeholder_email', false, true); ?>" data-err="<?php echo $validate['email'];?>" data-email="<?php echo $validate['email_invalid'];?>">
							</div>
				  		</div>
				  		<div class="alert scSelectorNoticeMsg" role="alert" style="display:none;"></div>
					  	<button type="submit" class="btn btn-default scSelectorButton pjScBtnPrimary"><?php __('front_send', false, true); ?></button>
					  	<a href="<?php echo $tpl['option_arr']['o_install_url']; ?>/<?php echo (!empty($tpl['option_arr']['o_page_prefix'])) ? $tpl['option_arr']['o_page_prefix'] . '-' : ''; ?>login" class="btn btn-link scSelectorLogin pjScBtnLink"><?php __('front_login'); ?></a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>