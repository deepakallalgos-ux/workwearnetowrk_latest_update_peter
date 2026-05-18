<?php
$filter = __('filter_company', true);
$u_statarr = __('u_statarr', true);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCompanies&amp;action=pjActionCreate" method="post" id="frmCreateCompany">
    <input type="hidden" name="company_create" value="1" />
    <div class="panel-heading bg-completed">
        <p class="lead m-n"><?php __('infoAddCompanyTitle'); ?></p>
    </div><!-- /.panel-heading -->

    <div class="panel-body">
        <div class="form-group">

            <label class="control-label"><?php __('lblCompanyName'); ?></label>

            <input type="text" class="form-control required" name="name" data-msg-required="<?php __('ebc_field_required', false, true); ?>">

        </div>
        <div class="form-group">

            <label class="control-label"><?php __('lblCompanyWNumber'); ?></label>

            <input type="text" class="form-control required" name="w_number" data-msg-required="<?php __('ebc_field_required', false, true); ?>">

        </div>

        <div class="form-group">
            <label class="control-label"><?php __('lblStatus'); ?></label>

            <div class="clearfix">
                <div class="switch onoffswitch-data pull-left">
                    <div class="onoffswitch">
                        <input type="checkbox" class="onoffswitch-checkbox" id="status" name="status" checked>
                        <label class="onoffswitch-label" for="status">
                            <span class="onoffswitch-inner" data-on="<?php echo $u_statarr['T']; ?>" data-off="<?php echo $u_statarr['F']; ?>"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
            </div><!-- /.clearfix -->
        </div><!-- /.form-group -->

        <div class="m-t-lg">
            <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                <span class="ladda-label"><?php __('btnSave'); ?></span>
                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
            </button>
            <button type="button" class="btn btn-white btn-lg pull-right pjFdBtnCancel"><?php __('btnCancel'); ?></button>
        </div><!-- /.clearfix -->
    </div><!-- /.panel-body -->
</form>