<?php
if (!defined("ROOT_PATH")) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjAdminBranding extends pjAdmin
{
	public function __construct($requireLogin = null)
	{
		parent::__construct($requireLogin);

		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		if (in_array($action, array('pjActionUploadSidebarLogo', 'pjActionRemoveSidebarLogo'), true))
		{
			$this->setLayout('pjActionEmpty');
		}
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

		$redirect = $_SERVER['PHP_SELF'] . '?controller=pjBaseOptions&action=pjActionVisual';

		if (!self::isPost() || empty($_FILES['sidebar_logo']) || (int) $_FILES['sidebar_logo']['error'] !== UPLOAD_ERR_OK) {
			pjUtil::redirect($redirect . '&err=logo_upload');
		}

		$uploadDir = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'admin/';
		if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0755, true)) {
			pjUtil::redirect($redirect . '&err=logo_upload');
		}

		$pjUpload = new pjUpload();
		$pjUpload->setAllowedTypes(array('image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml'));
		$pjUpload->setAllowedExt(array('png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'));

		if (!$pjUpload->load($_FILES['sidebar_logo'])) {
			pjUtil::redirect($redirect . '&err=logo_upload');
		}

		$ext = strtolower($pjUpload->getExtension());
		$fileName = 'sidebar-logo.' . $ext;
		$absolutePath = $uploadDir . $fileName;

		foreach (glob($uploadDir . 'sidebar-logo.*') as $oldFile) {
			if (is_file($oldFile)) {
				@unlink($oldFile);
			}
		}

		if (!$pjUpload->save($absolutePath)) {
			pjUtil::redirect($redirect . '&err=logo_upload');
		}

		$relativePath = PJ_UPLOAD_PATH . 'admin/' . $fileName;

		pjBaseOptionModel::factory()
			->where('foreign_id', $this->getForeignId())
			->where('`key`', pjUtil::ADMIN_SIDEBAR_LOGO_OPTION)
			->limit(1)
			->modifyAll(array('value' => $relativePath));

		pjUtil::redirect($redirect . '&err=PBS02');
	}

	public function pjActionRemoveSidebarLogo()
	{
		$this->checkLogin();

		if (!self::isPost()) {
			pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjBaseOptions&action=pjActionVisual');
		}

		if (!pjAuth::factory('pjBaseOptions', 'pjActionVisual')->hasAccess()) {
			$this->sendForbidden();
			exit;
		}

		$redirect = $_SERVER['PHP_SELF'] . '?controller=pjBaseOptions&action=pjActionVisual';
		$uploadDir = PJ_INSTALL_PATH . PJ_UPLOAD_PATH . 'admin/';

		foreach (glob($uploadDir . 'sidebar-logo.*') as $oldFile) {
			if (is_file($oldFile)) {
				@unlink($oldFile);
			}
		}

		pjBaseOptionModel::factory()
			->where('foreign_id', $this->getForeignId())
			->where('`key`', pjUtil::ADMIN_SIDEBAR_LOGO_OPTION)
			->limit(1)
			->modifyAll(array('value' => ''));

		pjUtil::redirect($redirect . '&err=PBS02');
	}
}
