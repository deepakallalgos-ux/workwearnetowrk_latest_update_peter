<!doctype html>
<html lang="en">
<head>
    <title>Install Wizard</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php
    foreach ($controller->getCss() as $css) {
        $href = $css['path'] . htmlspecialchars($css['file']);
        if (substr($href, -11) === 'install.css') {
            continue;
        }
        echo '<link type="text/css" rel="stylesheet" href="' . $href . '" />' . "\n";
    }
    $installCssBase = defined('PJ_INSTALL_URL') ? PJ_INSTALL_URL : '';
    if ($installCssBase === '') {
        $installCssBase = htmlspecialchars(dirname($_SERVER['SCRIPT_NAME']), ENT_QUOTES, 'UTF-8');
        $installCssBase = str_replace('\\', '/', $installCssBase);
        if ($installCssBase !== '' && $installCssBase !== '/') {
            $installCssBase .= '/';
        } elseif ($installCssBase === '/') {
            $installCssBase = '';
        }
    }
    echo '<link type="text/css" rel="stylesheet" href="' . $installCssBase . 'plugins/pjInstaller/web/css/install.css?v=5" />' . "\n";
    ?>
    <style id="pj-install-critical">
    /* Must load after custom.css — step label contrast */
    .pj-install-body .wizard > .steps a,
    .pj-install-body .wizard > .steps a:hover,
    .pj-install-body .wizard > .steps a:active,
    .pj-install-body .wizard > .steps .current a,
    .pj-install-body .wizard > .steps .current a:hover,
    .pj-install-body .wizard > .steps .current a:active,
    .pj-install-body .wizard > .steps .done a,
    .pj-install-body .wizard > .steps .disabled a {
        background: transparent !important;
        background-color: transparent !important;
        color: #636e72 !important;
    }
    .pj-install-body .wizard > .steps a .title {
        color: #636e72 !important;
    }
    .pj-install-body .wizard > .steps .current a .title {
        color: #0a5114 !important;
        font-weight: 700 !important;
    }
    .pj-install-body .wizard > .actions a[href="#next"],
    .pj-install-body .wizard > .actions a[href="#finish"] {
        width: auto !important;
        float: none !important;
    }
    </style>
    <?php
    foreach ($controller->getJs() as $js) {
        echo '<script src="' . $js['path'] . htmlspecialchars($js['file']) . '"></script>' . "\n";
    }
    ?>
</head>
<body class="pj-install-body">
    <div class="pj-install-wrap">
        <article class="pj-install-card">
            <header class="pj-install-card__header">
                <a href="https://www.phpjabbers.com/" class="pj-install-card__brand" target="_blank" rel="noopener">PHPJabbers</a>
                <h1 class="pj-install-card__title">Install Wizard</h1>
            </header>
            <div class="pj-install-card__body" id="middle">
                <?php require $content_tpl; ?>
            </div>
        </article>
        <footer class="pj-install-site-footer">
            Copyright <strong><a href="https://www.phpjabbers.com/" target="_blank" rel="noopener">PHPJabbers.com</a></strong>
            &copy; <?php date_default_timezone_set('Europe/London'); echo date('Y'); ?>
        </footer>
    </div>
</body>
</html>
