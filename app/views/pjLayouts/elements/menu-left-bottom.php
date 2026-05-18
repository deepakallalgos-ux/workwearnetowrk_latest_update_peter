<?php

$controller_name = $controller->_get->toString('controller');

$action_name = $controller->_get->toString('action');



$isScriptPreview = $controller_name == 'pjAdminOptions' && $action_name == 'pjActionPreview';

$isScriptInstall = $controller_name == 'pjAdminOptions' && $action_name == 'pjActionInstall';

$isScriptSetup = $controller_name == 'pjAdminSetup' && $action_name == 'pjActionWelcome';



$hasAccessScriptPreview = pjAuth::factory('pjAdminOptions', 'pjActionPreview')->hasAccess();

$hasAccessScriptInstall = pjAuth::factory('pjAdminOptions', 'pjActionInstall')->hasAccess();

$hasAccessScriptSetup = pjAuth::factory('pjAdminSetup', 'pjActionWelcome')->hasAccess();

?>

<?php if ($hasAccessScriptSetup): ?>

    <li<?php echo $isScriptSetup ? ' class="active"' : NULL; ?>>

        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSetup&amp;action=pjActionWelcome"><i class="fa fa-magic"></i> <span class="nav-label"><?php __('menuSetupWizard'); ?></span></a>

    </li>

<?php endif; ?>

<?php if ($hasAccessScriptPreview): ?>

    <li<?php echo $isScriptPreview ? ' class="active"' : NULL; ?>>

        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview"><i class="fa fa-eye"></i> <span class="nav-label"><?php __('menuPreview');?></span></a>

    </li>

<?php endif; ?>



<?php if ($hasAccessScriptInstall): ?>

    <li<?php echo $isScriptInstall ? ' class="active"' : NULL; ?>>

        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionInstall"><i class="fa fa-code"></i> <span class="nav-label"><?php __('menuInstall');?></span></a>

    </li>

<?php endif; ?>

