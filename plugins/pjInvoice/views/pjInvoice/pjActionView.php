<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php __('plugin_invoice_menu_invoices'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Latest Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <?php
    foreach ($controller->getCss() as $css) {
        echo '<link type="text/css" rel="stylesheet" href="'.$css['path'].$css['file'].'" />';
    }
    foreach ($controller->getJs() as $js) {
        echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].htmlspecialchars($js['file']).'"></script>';
    }
    ?>
</head>
<body class="bg-light">
    <div class="container py-5">
        <?php echo $tpl['template']; ?>

        <?php if ($tpl['arr']['status'] == 'not_paid' && (float) $tpl['arr']['total'] > 0 && !empty($tpl['retry_url'])): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-body text-center">
                <a href="<?php echo htmlspecialchars($tpl['retry_url'], ENT_QUOTES); ?>" class="btn btn-primary btn-lg">
                    <?php $lbl = __('plugin_invoice_pay_now', true); echo !empty($lbl) ? htmlspecialchars($lbl) : 'Betaal alsnog'; ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
