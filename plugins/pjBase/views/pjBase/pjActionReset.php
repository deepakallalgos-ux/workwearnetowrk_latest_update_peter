<div class="pj-login-grid">
    <div class="pj-login-grid__form">
        <?php
        if ($code = $controller->_get->toInt('err'))
        {
            ?><h2><?php __('plugin_base_admin_reset'); ?></h2><?php
            $login_err = __('plugin_base_login_err', true);
            if(isset($login_err[$code]))
            {
                ?><div class="alert alert-danger" role="alert"><?php echo $login_err[$code]; ?></div><?php
            }
        } elseif (isset($tpl['new_password'])) {
            ?>
            <h2><?php __('plugin_base_admin_reset_success'); ?></h2>
            <div class="well"><?php echo pjSanitize::html($tpl['new_password']); ?></div>
            <?php
        }
        ?>
        <div class="m-t-sm">
            <a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjBase&action=pjActionLogin"><small><?php __('plugin_base_link_login'); ?></small></a>
        </div>
    </div>

    <aside class="pj-login-grid__welcome">
        <h3>Welcome to PHPJabbers</h3>
        <p>Reset your admin password to regain access to your panel.</p>
        <p class="pj-login-grid__version">Version <?php echo PJ_SCRIPT_VERSION; ?></p>
    </aside>
</div>
