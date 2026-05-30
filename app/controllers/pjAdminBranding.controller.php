<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

/**
 * Backward-compatible endpoints; Visual branding uses pjBaseOptions actions.
 */
class pjAdminBranding extends pjAdmin
{
	public function __construct($requireLogin = null)
	{
		parent::__construct($requireLogin);
		$this->setLayout('pjActionEmpty');
	}

	public function beforeFilter()
	{
		parent::beforeFilter();
		pjUtil::ensureAdminSidebarLogoOption();
	}

	public function pjActionUploadSidebarLogo()
	{
		$this->checkLogin();
		if (!pjAuth::factory('pjBaseOptions', 'pjActionVisual')->hasAccess()) {
			$this->sendForbidden();
			exit;
		}
		pjUtil::handleAdminSidebarLogoUpload($this->getForeignId());
	}

	public function pjActionRemoveSidebarLogo()
	{
		$this->checkLogin();
		if (!pjAuth::factory('pjBaseOptions', 'pjActionVisual')->hasAccess()) {
			$this->sendForbidden();
			exit;
		}
		pjUtil::handleAdminSidebarLogoRemove($this->getForeignId());
	}
}
