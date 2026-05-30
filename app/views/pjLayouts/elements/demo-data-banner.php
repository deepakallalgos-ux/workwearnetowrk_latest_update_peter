<?php
if (empty($tpl['hasDemoData'])) {
    return;
}
$canRemove = pjAuth::factory('pjAdminSetup', 'pjActionRemoveDemo')->hasAccess()
    || pjAuth::factory('pjAdminSetup', 'pjActionWelcome')->hasAccess();
?>
<div class="wj-demo-banner alert alert-warning" role="status">
    <div class="wj-demo-banner__body">
        <strong><i class="fa fa-cube"></i> <?php __('setup_demo_active_title'); ?></strong>
        <p class="wj-demo-banner__text"><?php __('setup_demo_active_desc'); ?></p>
    </div>
    <?php if ($canRemove): ?>
    <div class="wj-demo-banner__actions">
        <button type="button" class="btn btn-danger" onclick="typeof wjSetup !== 'undefined' && wjSetup.removeDemo();">
            <i class="fa fa-trash"></i> <?php __('setup_btn_remove_demo'); ?>
        </button>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSetup&amp;action=pjActionWelcome" class="btn btn-default btn-outline">
            <?php __('menuSetupWizard'); ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.setup_remove_confirm_title = <?php x__encode('setup_remove_confirm_title'); ?>;
myLabel.setup_remove_confirm_text = <?php x__encode('setup_remove_confirm_text'); ?>;
myLabel.setup_btn_remove_confirm = <?php x__encode('setup_btn_remove_confirm'); ?>;
myLabel.setup_remove_confirm_fallback = <?php x__encode('setup_remove_confirm_fallback'); ?>;
myLabel.setup_removing_demo = <?php x__encode('setup_removing_demo'); ?>;
myLabel.setup_swal_removed_title = <?php x__encode('setup_swal_removed_title'); ?>;
myLabel.setup_summary_removed = <?php x__encode('setup_summary_removed'); ?>;
myLabel.setup_unit_products = <?php x__encode('setup_unit_products'); ?>;
myLabel.setup_unit_categories = <?php x__encode('setup_unit_categories'); ?>;
myLabel.setup_unit_images = <?php x__encode('setup_unit_images'); ?>;
myLabel.setup_unit_clients = <?php x__encode('setup_unit_clients'); ?>;
myLabel.setup_unit_quotes = <?php x__encode('setup_unit_quotes'); ?>;
myLabel.setup_error_unknown = <?php x__encode('setup_error_unknown'); ?>;
myLabel.setup_error_prefix = <?php x__encode('setup_error_prefix'); ?>;
myLabel.setup_error_short = <?php x__encode('setup_error_short'); ?>;
myLabel.setup_error_connection_short = <?php x__encode('setup_error_connection_short'); ?>;
myLabel.setup_btn_ok = <?php x__encode('setup_btn_ok'); ?>;
myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;
</script>
