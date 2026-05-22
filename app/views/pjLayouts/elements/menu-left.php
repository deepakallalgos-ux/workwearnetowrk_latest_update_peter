<?php
$controller_name = $controller->_get->toString('controller');
$action_name = $controller->_get->toString('action');

// Dashboard
$isScriptDashboard = in_array($controller_name, array('pjAdmin')) && in_array($action_name, array('pjActionIndex'));

// Bookings
$isScriptBookingsController = in_array($controller_name, array('pjAdminOrders'));
$isScriptBookings = $isScriptBookingsController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));

// Quotes
$isScriptQuotesController = in_array($controller_name, array('pjAdminQuotes'));
$isScriptQuotes = $isScriptQuotesController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));

// Clients
$isScriptClientsController       = in_array($controller_name, array('pjAdminClients'));

// Products
$isScriptProductsController       = in_array($controller_name, array('pjAdminProducts'));
$isScriptProducts = $isScriptProductsController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));
$isScriptProductsFlatFile = $isScriptProductsController && in_array($action_name, array('pjActionProductsFlatFileIndex', 'pjActionUpdateFlatFile'));
$isScriptProductStock = $isScriptProductsController && in_array($action_name, array('pjActionStock'));

// Categories
$isScriptCategoriesController     = in_array($controller_name, array('pjAdminCategories'));

// Brands
$isScriptBrandsController     = in_array($controller_name, array('pjAdminBrands'));

// Reports
$isScriptReportsController     = in_array($controller_name, array('pjAdminReports'));

// Vouchers
$isScriptVouchersController     = in_array($controller_name, array('pjAdminVouchers'));

// Payments
$isScriptPaymentsController = in_array($controller_name, array('pjPayments'));

// Settings
$isScriptOptionsController = in_array($controller_name, array('pjAdminOptions')) && !in_array($action_name, array('pjActionPreview', 'pjActionInstall'));

$isScriptOptionsBooking         = $isScriptOptionsController && in_array($action_name, array('pjActionBooking'));
$isScriptOptionsBookingForm     = $isScriptOptionsController && in_array($action_name, array('pjActionBookingForm'));
$isScriptOptionsShippingTax     = $isScriptOptionsController && in_array($action_name, array('pjActionShippingTax'));
$isScriptOptionsTerm            = $isScriptOptionsController && in_array($action_name, array('pjActionTerm'));
$isScriptOptionsNotifications   = $isScriptOptionsController && in_array($action_name, array('pjActionNotifications'));


// Permissions - Dashboard
$hasAccessScriptDashboard = pjAuth::factory('pjAdmin', 'pjActionIndex')->hasAccess();

// Permissions - Bookings
$hasAccessScriptBookings            = pjAuth::factory('pjAdminOrders')->hasAccess();
$hasAccessScriptBookingsIndex       = pjAuth::factory('pjAdminOrders', 'pjActionIndex')->hasAccess();

// Permissions - Bookings
$hasAccessScriptQuotes            = pjAuth::factory('pjAdminQuotes')->hasAccess();
$hasAccessScriptQuotesIndex       = pjAuth::factory('pjAdminQuotes', 'pjActionIndex')->hasAccess();

// Permissions - Clients
$hasAccessScriptClients        = pjAuth::factory('pjAdminClients')->hasAccess();
$hasAccessScriptClientsIndex   = pjAuth::factory('pjAdminClients', 'pjActionIndex')->hasAccess();

// Permissions - Products
$hasAccessScriptProducts  = pjAuth::factory('pjAdminProducts')->hasAccess();
$hasAccessScriptProductsIndex  = pjAuth::factory('pjAdminProducts', 'pjActionIndex')->hasAccess();
// Permissions - Products flat file (actions live on pjAdminProducts)
$hasAccessScriptProductsFlatFile  = pjAuth::factory('pjAdminProducts')->hasAccess();
$hasAccessScriptProductsFlatFileIndex  = pjAuth::factory('pjAdminProducts', 'pjActionProductsFlatFileIndex')->hasAccess();

// $hasAccessScriptProductsImportIndex  = pjAuth::factory( 'pjActionIndex')->hasAccess();
$hasAccessScriptProductsImportIndex  = pjAuth::factory('pjAdminProductImportHistory', 'pjActionIndex')->hasAccess();
$hasAccessScriptStockIndex  = pjAuth::factory('pjAdminProducts', 'pjActionStock')->hasAccess();

// Permissions - Categories
$hasAccessScriptCategories            = pjAuth::factory('pjAdminCategories', 'pjActionIndex')->hasAccess();

// Permissions - Brands
$hasAccessScriptBrands            = pjAuth::factory('pjAdminBrands', 'pjActionIndex')->hasAccess();

// Permissions - Reports
$hasAccessScriptReports                = pjAuth::factory('pjAdminReports', 'pjActionIndex')->hasAccess();

// Permissions - Vouchers
$hasAccessScriptVouchers                = pjAuth::factory('pjAdminVouchers', 'pjActionIndex')->hasAccess();

// Permissions - Settings
$hasAccessScriptOptions                 = pjAuth::factory('pjAdminOptions')->hasAccess();
$hasAccessScriptOptionsBooking          = pjAuth::factory('pjAdminOptions', 'pjActionBooking')->hasAccess();
$hasAccessScriptOptionsBookingForm      = pjAuth::factory('pjAdminOptions', 'pjActionBookingForm')->hasAccess();
$hasAccessScriptOptionsShippingTax      = pjAuth::factory('pjAdminOptions', 'pjActionShippingTax')->hasAccess();
$hasAccessScriptOptionsTerm             = pjAuth::factory('pjAdminOptions', 'pjActionTerm')->hasAccess();
$hasAccessScriptOptionsNotifications    = pjAuth::factory('pjAdminOptions', 'pjActionNotifications')->hasAccess();

// Permissions - Payments
$hasAccessScriptPayments = pjAuth::factory('pjPayments', 'pjActionIndex')->hasAccess();
$hasAccessScriptCompanies  = pjAuth::factory('pjAdminCompanies')->hasAccess();

$isScriptCompaniesController     = in_array($controller_name, array('pjAdminCompanies'));
$isScriptCompaniesIndex      = $isScriptCompaniesController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));

$isScriptImportProductsController     = in_array($controller_name, array('pjAdminProductImportHistory'));
// $isScriptImportProductsIndex      = $isScriptImportProductsController && in_array($action_name, array('pjActionIndex', 'pjActionCreate', 'pjActionUpdate'));
$isScriptImportProductsIndex = $isScriptImportProductsController && in_array($action_name, array('pjActionIndex','pjActionUpload'));
$companies_arr = pjCompanyModel::factory()->where('is_deleted', 0)->where('status', 'T')->findAll()->getData();
$default_company = $_SESSION['admin_selected_company'];
$role_id = pjAuth::factory()->getRoleId();
$id = $controller->_get->toInt('id');
$latest_import = pjProductImportHistoryModel::factory()
    ->reset()
    ->where('company_id', $default_company['id'])
    ->select('MAX(t1.id) as latest_id')
    ->findAll()
    ->getData();

$latest_id = !empty($latest_import) ? (int) $latest_import[0]['latest_id'] : 0;
// echo "<pre>"; print_r($latest_import); die;
?>
<style>
    .company-header {
        padding: 15px 20px;
        background: #06370d;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .company-icon { 
        font-size: 18px;
        margin-right: 8px;
        color: var(--admin-success, #22c55e);
    }

    .company-name {
        font-size: 15px;
        font-weight: 600;
        color: #ffffff;
        text-transform: capitalize;
        display: inline-block;
    }
</style>
<li class="nav-header company-header">
    <div class="dropdown profile-element">
        <span class="company-icon"><i class="fa fa-building"></i></span>
        <span class="company-name">
            <?php echo htmlspecialchars($default_company['name']); ?>
        </span>
    </div>
</li>
<?php if ($role_id == 1) { ?>

    <li <?php echo ($isScriptCompaniesController ? ' class="active"' : ''); ?>>
        <a href="#"><i class="fa fa-exchange"></i>
            <span class="nav-label"><?php __('menuCompany'); ?></span>
            <span class="fa arrow"></span>
        </a>

        <ul class="nav nav-second-level collapse">

            <?php
            $currentCompanyId = isset($_SESSION['admin_selected_company']['id'])
                ? $_SESSION['admin_selected_company']['id']
                : 1;

            $currentUrl = urlencode($_SERVER['REQUEST_URI']);
            foreach ($companies_arr as $value) {
                $isActive = ($currentCompanyId == $value['id']) ? ' class="active"' : '';
                $rowIcon = ($currentCompanyId == $value['id']) ? 'fa-check' : 'fa-circle-o';
            ?>
                <li<?php echo $isActive; ?>>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCompanies&amp;action=pjActionSetCompany&id=<?php echo $value['id']; ?>&return_url=<?php echo $currentUrl; ?>">
                        <i class="fa <?php echo $rowIcon; ?>"></i> <?php echo htmlspecialchars($value['name']); ?>
                    </a>
                </li>
                <?php } ?>

        </ul>
    </li>
<?php } ?>



<?php if ($hasAccessScriptDashboard): ?>
    <li<?php echo $isScriptDashboard ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex"><i class="fa fa-th-large"></i> <span class="nav-label"><?php __('plugin_base_menu_dashboard'); ?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptBookingsIndex): ?>
    <li<?php echo $isScriptBookings ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex"><i class="fa fa-list-ul"></i> <span class="nav-label"><?php __('menuQuotes'); ?></span></a>
    </li>
<?php endif; ?>
<!-- <?php if ($hasAccessScriptQuotesIndex): ?>
    <li<?php echo $isScriptQuotes ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminQuotes&amp;action=pjActionIndex"><i class="fa fa-file-text-o"></i> <span class="nav-label"><?php __('menuQuotes'); ?></span></a>
    </li>
<?php endif; ?> -->

<?php if ($hasAccessScriptClientsIndex): ?>
    <li<?php echo $isScriptClientsController ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex"><i class="fa fa-user"></i> <span class="nav-label"><?php __('menuClients'); ?></span></a>
    </li>
<?php endif; ?>
<?php if ($role_id == 1) : ?>

    <li <?php echo ($isScriptCompaniesIndex ? ' class="active"' : ''); ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCompanies&amp;action=pjActionIndex"><i class="fa fa-sitemap"></i> <span class="nav-label"><?php __('menuCompanies'); ?></span></a>
    </li>
<?php endif; ?>
<?php if ($role_id == 1) : ?>
<?php if ($hasAccessScriptProductsImportIndex ): ?>
<li <?php echo $isScriptImportProductsController ? ' class="active"' : NULL; ?>>
    <a href="#">
        <i class="fa fa-cloud-upload"></i>
        <span class="nav-label"><?php __('menuImportProduct'); ?></span>
        <span class="fa arrow"></span>
    </a>

    <ul class="nav nav-second-level collapse">

        <li<?php echo ($isScriptImportProductsController && empty($id)) ? ' class="active"' : NULL; ?>>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProductImportHistory&amp;action=pjActionIndex">
                <i class="fa fa-upload"></i> <?php __('menuPhase1Import'); ?>
            </a>
        </li>

        <li<?php echo ($isScriptImportProductsController && !empty($id)) ? ' class="active"' : NULL; ?>>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProductImportHistory&amp;action=pjActionIndex&sync=1&id=<?php echo $latest_id ?>">
                <i class="fa fa-check-square-o"></i> <?php __('menuPhase2ImportReview'); ?>
            </a>
        </li>

    </ul>
</li>
<?php endif; ?>
<?php endif; ?>
<?php if ($hasAccessScriptProductsIndex || $hasAccessScriptProductsFlatFileIndex || $hasAccessScriptStockIndex || $hasAccessScriptCategories || $hasAccessScriptBrands): ?>
    <li <?php echo $isScriptProducts || $isScriptProductStock || $isScriptCategoriesController || $isScriptBrandsController ||   $isScriptProductsFlatFile ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-cube"></i> <span class="nav-label"><?php __('menuProducts'); ?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">

            <?php if ($hasAccessScriptProductsIndex): ?>
                <li<?php echo $isScriptProducts ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionIndex"><i class="fa fa-cube"></i> <?php __('menuProductsList'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptProductsFlatFileIndex): ?>
                <li<?php echo $isScriptProductsFlatFile ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionProductsFlatFileIndex"><i class="fa fa-table"></i> <?php __('menuProductListFlatFileView'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptStockIndex): ?>
                <li<?php echo $isScriptProductStock ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionStock"><i class="fa fa-archive"></i> <?php __('menuProductStock'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptCategories): ?>
                <li<?php echo $isScriptCategoriesController ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex"><i class="fa fa-folder-o"></i> <?php __('menuCategories'); ?></a></li>
            <?php endif; ?>
            <?php if ($hasAccessScriptBrands): ?>
                <li<?php echo $isScriptBrandsController ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBrands&amp;action=pjActionIndex"><i class="fa fa-tags"></i> <?php __('menuBrands'); ?></a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptVouchers): ?>
    <li<?php echo $isScriptVouchersController ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex"><i class="fa fa-gift"></i> <span class="nav-label"><?php __('menuVouchers'); ?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptReports): ?>
    <li<?php echo $isScriptReportsController ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex"><i class="fa fa-files-o"></i> <span class="nav-label"><?php __('menuReports'); ?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptOptionsBooking || $hasAccessScriptPayments || $hasAccessScriptOptionsBookingForm || $hasAccessScriptOptionsTicket || $hasAccessScriptOptionsContent || $hasAccessScriptOptionsNotifications || $hasAccessScriptOptionsTerm): ?>
    <li<?php echo $isScriptOptionsController || $isScriptPaymentsController ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-cog"></i> <span class="nav-label"><?php __('menuSettings'); ?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            <?php if ($hasAccessScriptOptionsBooking): ?>
                <li<?php echo $isScriptOptionsBooking ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionBooking"><i class="fa fa-sliders"></i> <?php __('settingsTabOrders'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptPayments): ?>
                <li<?php echo $isScriptPaymentsController ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPayments&amp;action=pjActionIndex"><i class="fa fa-credit-card"></i> <?php __('settingsTabPayments'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptOptionsBookingForm): ?>
                <li<?php echo $isScriptOptionsBookingForm ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionBookingForm"><i class="fa fa-wpforms"></i> <?php __('settingsTabCheckoutForm'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptOptionsShippingTax): ?>
                <li<?php echo $isScriptOptionsShippingTax ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionShippingTax"><i class="fa fa-truck"></i> <?php __('settingsTabShippingTax'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptOptionsTerm): ?>
                <li<?php echo $isScriptOptionsTerm ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionTerm"><i class="fa fa-file-text-o"></i> <?php __('settingsTabTerms'); ?></a></li>
            <?php endif; ?>

            <?php if ($hasAccessScriptOptionsNotifications): ?>
                <li<?php echo $isScriptOptionsNotifications ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionNotifications"><i class="fa fa-envelope-o"></i> <?php __('settingsTabNotifications'); ?></a></li>
            <?php endif; ?>

        </ul>
    </li>
<?php endif; ?>