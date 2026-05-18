<?php if (empty($tpl['wizardCompleted'])): ?>

<div class="wj-setup">

    <div class="wj-setup-shell">

        <div class="wj-setup-brand-wrap">
            <a href="https://www.phpjabbers.com/" class="wj-setup-brand" target="_blank" rel="noopener">PHPJabbers</a>
        </div>

        <div class="wj-setup-panel">

            <div id="wjSetupMain" class="wj-setup-main">

                <div class="wj-setup-header">

                    <h1 class="wj-setup-title">Welcome to PHPJabbers</h1>

                    <p class="wj-setup-subtitle">Let's finish setting up your application. You can load sample data to explore the system, or start with an empty catalogue. Demo data can be removed later.</p>

                </div>

                <div id="wjSetupCards" class="wj-setup-cards">

                    <div class="wj-setup-card wj-card--demo" data-choice="demo">

                        <div class="wj-card-icon">

                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>

                        </div>

                        <span class="wj-card-badge"><?php __('setup_badge_recommended'); ?></span>

                        <h2 class="wj-card-title"><?php __('setup_card_demo_title'); ?></h2>

                        <p class="wj-card-desc"><?php __('setup_card_demo_desc'); ?></p>

                        <ul class="wj-card-features">

                            <li><?php __('setup_demo_feat_categories'); ?></li>

                            <li><?php __('setup_demo_feat_products'); ?></li>

                            <li><?php __('setup_demo_feat_clients'); ?></li>

                            <li><?php __('setup_demo_feat_quotes'); ?></li>

                        </ul>

                        <button type="button" class="wj-setup-btn wj-setup-btn--primary wj-setup-btn--block" id="btnLoadDemo" onclick="wjSetup.loadDemo();"><?php __('setup_btn_start_demo'); ?></button>

                    </div>

                    <div class="wj-setup-card wj-card--empty" data-choice="empty">

                        <div class="wj-card-icon">

                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>

                        </div>

                        <h2 class="wj-card-title"><?php __('setup_card_empty_title'); ?></h2>

                        <p class="wj-card-desc"><?php __('setup_card_empty_desc'); ?></p>

                        <ul class="wj-card-features">

                            <li><?php __('setup_empty_feat_no_samples'); ?></li>

                            <li><?php __('setup_empty_feat_control'); ?></li>

                            <li><?php __('setup_empty_feat_migrate'); ?></li>

                        </ul>

                        <button type="button" class="wj-setup-btn wj-setup-btn--secondary wj-setup-btn--block" id="btnSkipDemo" onclick="wjSetup.skipDemo();"><?php __('setup_btn_start_empty'); ?></button>

                    </div>

                </div>

            </div>

            <div id="wjSetupOverlay" class="wj-setup-overlay" aria-hidden="true">

                <div id="wjSetupLoadingView" class="wj-setup-overlay-view">

                    <div class="wj-spinner"></div>

                    <div class="wj-setup-loading-text" id="wjLoadingText"><?php __('setup_loading_demo'); ?></div>

                    <div class="wj-setup-loading-sub" id="wjLoadingSub"><?php __('setup_loading_sub'); ?></div>

                    <div class="wj-progress-bar"><div class="wj-progress-bar-fill" id="wjProgressFill"></div></div>

                </div>

                <div id="wjSetupSuccessView" class="wj-setup-overlay-view" hidden>

                    <div class="wj-success-icon">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>

                    </div>

                    <div class="wj-setup-loading-text" id="wjSuccessText"><?php __('setup_success_title'); ?></div>

                    <div class="wj-setup-loading-sub" id="wjSuccessSub"><?php __('setup_success_redirect'); ?></div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php else: ?>

<?php $lblSetupWizard = __('menuSetupWizard', true); ?>

<?php $lblDashboard = __('pjAdmin_pjActionIndex', true); ?>

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-9">

        <h2><?php echo htmlspecialchars($lblSetupWizard); ?></h2>

        <ol class="breadcrumb">

            <li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex"><?php echo htmlspecialchars($lblDashboard); ?></a></li>

            <li class="active"><strong><?php echo htmlspecialchars($lblSetupWizard); ?></strong></li>

        </ol>

    </div>

</div>



<div class="wrapper wrapper-content">

    <div class="row">

        <div class="col-lg-8 col-lg-offset-2">

            <?php if (!empty($tpl['hasDemoProducts'])): ?>

            <div class="ibox">

                <div class="ibox-title"><h5><i class="fa fa-cube"></i> <?php __('setup_demo_active_title'); ?></h5></div>

                <div class="ibox-content">

                    <div class="text-center" style="padding: 30px 20px;">

                        <p style="color:#888;margin-bottom:25px;"><?php __('setup_demo_active_desc'); ?></p>

                        <button type="button" class="btn btn-danger btn-lg" id="btnRemoveDemoAdmin" onclick="wjSetup.removeDemo()">

                            <i class="fa fa-trash"></i> <?php __('setup_btn_remove_demo'); ?>

                        </button>

                    </div>

                </div>

            </div>

            <?php else: ?>

            <div class="ibox">

                <div class="ibox-title"><h5><i class="fa fa-magic"></i> <?php __('setup_load_demo_title'); ?></h5></div>

                <div class="ibox-content">

                    <div class="text-center" style="padding: 30px 20px;">

                        <p style="color:#888;margin-bottom:20px;"><?php __('setup_load_demo_desc'); ?></p>

                        <ul style="text-align:left;max-width:400px;margin:0 auto 25px;color:#666;list-style:none;padding:0;">

                            <li><i class="fa fa-check wj-setup-check-icon"></i> <?php __('setup_load_demo_feat_summary'); ?></li>

                            <li><i class="fa fa-check wj-setup-check-icon"></i> <?php __('setup_load_demo_feat_quotes'); ?></li>

                        </ul>

                        <button type="button" class="btn btn-primary btn-lg" id="btnLoadDemoAdmin" onclick="wjSetup.loadDemo()">

                            <i class="fa fa-magic"></i> <?php __('setup_btn_load_demo'); ?>

                        </button>

                    </div>

                </div>

            </div>

            <?php endif; ?>



            <div id="wjSetupLoading" class="text-center" style="display:none;padding:40px 20px;">

                <div class="wj-setup-admin-spinner"><i class="fa fa-spinner fa-spin"></i></div>

                <div id="wjLoadingText" style="font-size:16px;font-weight:600;"><?php __('setup_loading_demo'); ?></div>

                <div id="wjLoadingSub" style="color:#888;margin-top:5px;"><?php __('setup_loading_sub'); ?></div>

            </div>

        </div>

    </div>

</div>

<?php endif; ?>



<script type="text/javascript">

var myLabel = myLabel || {};

myLabel.setup_loading_demo = <?php x__encode('setup_loading_demo'); ?>;

myLabel.setup_loading_sub = <?php x__encode('setup_loading_sub'); ?>;

myLabel.setup_loading_demo_detail = <?php x__encode('setup_loading_demo_detail'); ?>;

myLabel.setup_success_title = <?php x__encode('setup_success_title'); ?>;

myLabel.setup_success_redirect = <?php x__encode('setup_success_redirect'); ?>;

myLabel.setup_swal_loaded_title = <?php x__encode('setup_swal_loaded_title'); ?>;

myLabel.setup_swal_removed_title = <?php x__encode('setup_swal_removed_title'); ?>;

myLabel.setup_summary_loaded = <?php x__encode('setup_summary_loaded'); ?>;

myLabel.setup_summary_removed = <?php x__encode('setup_summary_removed'); ?>;

myLabel.setup_unit_products = <?php x__encode('setup_unit_products'); ?>;

myLabel.setup_unit_categories = <?php x__encode('setup_unit_categories'); ?>;

myLabel.setup_unit_images = <?php x__encode('setup_unit_images'); ?>;

myLabel.setup_unit_clients = <?php x__encode('setup_unit_clients'); ?>;

myLabel.setup_unit_quotes = <?php x__encode('setup_unit_quotes'); ?>;

myLabel.setup_error_unknown = <?php x__encode('setup_error_unknown'); ?>;

myLabel.setup_error_generic = <?php x__encode('setup_error_generic'); ?>;

myLabel.setup_error_connection = <?php x__encode('setup_error_connection'); ?>;

myLabel.setup_error_connection_short = <?php x__encode('setup_error_connection_short'); ?>;

myLabel.setup_error_short = <?php x__encode('setup_error_short'); ?>;

myLabel.setup_error_prefix = <?php x__encode('setup_error_prefix'); ?>;

myLabel.setup_skip_loading = <?php x__encode('setup_skip_loading'); ?>;

myLabel.setup_skip_loading_sub = <?php x__encode('setup_skip_loading_sub'); ?>;

myLabel.setup_remove_confirm_title = <?php x__encode('setup_remove_confirm_title'); ?>;

myLabel.setup_remove_confirm_text = <?php x__encode('setup_remove_confirm_text'); ?>;

myLabel.setup_btn_remove_confirm = <?php x__encode('setup_btn_remove_confirm'); ?>;

myLabel.setup_remove_confirm_fallback = <?php x__encode('setup_remove_confirm_fallback'); ?>;

myLabel.setup_removing_demo = <?php x__encode('setup_removing_demo'); ?>;

myLabel.setup_btn_ok = <?php x__encode('setup_btn_ok'); ?>;

myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;

</script>
