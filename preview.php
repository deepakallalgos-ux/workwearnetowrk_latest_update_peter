<?php
if (!headers_sent())
{
	session_name('ShoppingCart50');
	@session_start();
}

if (!defined("ROOT_PATH"))
{
	define("ROOT_PATH", dirname(__FILE__) . '/');
}

require ROOT_PATH . 'app/config/options.inc.php';

require_once PJ_FRAMEWORK_PATH . 'pjAutoloader.class.php';
pjAutoloader::register();

pjUtil::bootstrapPreviewCompanyFromRequest($_REQUEST);
pjUtil::ensureCompanySession('admin_selected_company');

$previewCompanyId = pjUtil::resolvePreviewCompanyId();
$encodedCompanyId = pjUtil::encodePreviewCompanyId($previewCompanyId);

$previewParams = array('company_id' => $encodedCompanyId);
if (isset($_GET['locale']) && (int) $_GET['locale'] > 0)
{
	$previewParams['locale'] = (int) $_GET['locale'];
}
if (isset($_GET['hide']))
{
	$previewParams['hide'] = (int) $_GET['hide'];
}
if (isset($_GET['theme']) && $_GET['theme'] !== '')
{
	$previewParams['theme'] = $_GET['theme'];
}
if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
{
	$previewParams['category_id'] = (int) $_GET['category_id'];
}

$frontQuery = http_build_query(array_merge(array(
	'controller' => 'pjFront',
), $previewParams));
$loadCssUrl = PJ_INSTALL_URL . 'index.php?' . $frontQuery . '&action=pjActionLoadCss';
$loadJsUrl = PJ_INSTALL_URL . 'index.php?' . $frontQuery . '&action=pjActionLoad';
?>
<!DOCTYPE html>
<html>

<head>
	<title>Workwear Network</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="fragment" content="!">
	<meta name="viewport" content="width=device-width">

	<link href="<?php echo PJ_INSTALL_URL; ?>core/framework/libs/pj/css/pj.bootstrap.min.css" type="text/css" rel="stylesheet" />
	<link href="<?php echo htmlspecialchars($loadCssUrl, ENT_QUOTES, 'UTF-8'); ?>" type="text/css" rel="stylesheet" />
</head>

<body>
	<div style="max-width: 1024px; margin: 10px auto;">
		<script type="text/javascript" src="<?php echo htmlspecialchars($loadJsUrl, ENT_QUOTES, 'UTF-8'); ?>"></script>
	</div>
</body>

</html>
