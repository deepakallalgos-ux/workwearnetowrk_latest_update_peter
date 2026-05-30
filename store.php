<?php
require_once 'app/config/config.inc.php';

$companySlug = isset($_GET['company']) ? trim($_GET['company']) : '';
if ($companySlug === '') {
    exit('Company missing');
}

$company = pjCompanyModel::factory()
    ->where('slug', $companySlug) // change field if your DB uses another name
    ->limit(1)
    ->findAll()
    ->getData();

if (empty($company) || empty($company[0]['id'])) {
    exit('Company not found');
}

$company_id = base64_encode((string) $company[0]['id']);
$theme = isset($_GET['theme']) ? $_GET['theme'] : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
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
    <link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss<?php echo $theme !== '' ? '&theme=' . urlencode($theme) : ''; ?>&company_id=<?php echo urlencode($company_id); ?>" type="text/css" rel="stylesheet" />
</head>
<body>
    <div style="max-width: 1024px; margin: 10px auto;">
        <script
            type="text/javascript"
            src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad<?php echo $theme !== '' ? '&theme=' . urlencode($theme) : ''; ?><?php echo $category_id !== '' ? '&category_id=' . urlencode($category_id) : ''; ?>&company_id=<?php echo urlencode($company_id); ?>">
        </script>
    </div>
</body>
</html>