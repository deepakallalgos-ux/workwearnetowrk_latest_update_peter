<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PHPJabbers - Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php
    $cnt = count($controller->getCss());
    foreach ($controller->getCss() as $i => $css) {
        echo '<link rel="stylesheet" href="' . (isset($css['remote']) && $css['remote'] ? '' : PJ_INSTALL_URL) . $css['path'] . $css['file'] . '">' . "\n";
    }
    ?>
    <link rel="stylesheet" href="<?php echo PJ_INSTALL_URL; ?>app/web/css/admin-login.css?v=1">
</head>
<body class="pj-admin-login-body light-grey-bg">

<div id="wrapper">
    <div class="main">
        <div class="container">
            <div class="pj-admin-login-wrap">
                <article class="pj-admin-login-card">
                    <header class="pj-admin-login-card__header">
                        <a href="https://www.phpjabbers.com/" class="pj-admin-login-card__brand" target="_blank" rel="noopener">PHPJabbers</a>
                        <h1 class="pj-admin-login-card__title">Administration</h1>
                    </header>
                    <div class="pj-admin-login-card__body">
                        <div class="middle-box login-box animated fadeInDown">
                            <?php require $content_tpl; ?>
                        </div>
                    </div>
                </article>

                <footer class="pj-admin-login-site-footer">
                    <?php
                    if ($tpl['option_arr']['o_hide_footer'] == 'No') {
                        if (!empty($tpl['option_arr']['o_footer_text'])) {
                            echo pjSanitize::html($tpl['option_arr']['o_footer_text']);
                        } else {
                            ?>
                            Copyright <strong><a href="https://www.phpjabbers.com" target="_blank" rel="noopener">PHPJabbers.com</a></strong>
                            &copy; <?php echo date('Y'); ?>
                            <?php
                        }
                    }
                    ?>
                </footer>
            </div>
        </div>
    </div>
</div>

<?php
foreach ($controller->getJs() as $js) {
    echo '<script src="' . (isset($js['remote']) && $js['remote'] ? '' : PJ_INSTALL_URL) . $js['path'] . $js['file'] . '"></script>' . "\n";
}
?>
</body>
</html>
