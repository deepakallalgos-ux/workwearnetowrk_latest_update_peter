<!doctype html>

<html>

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>PHPJabbers - Setup</title>

    <link rel="icon" href="<?php echo PJ_INSTALL_URL;?>app/web/img/favicons/favicon.svg" type="image/svg+xml">

    <link rel="icon" href="<?php echo PJ_INSTALL_URL;?>app/web/img/favicons/favicon-32x32.png" type="image/png" sizes="32x32">

    <link rel="shortcut icon" href="<?php echo PJ_INSTALL_URL;?>app/web/img/favicons/favicon.ico">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo PJ_INSTALL_URL . PJ_CSS_PATH; ?>admin-datagrid-listings.css">

    <?php

    foreach ($controller->getCss() as $css) {

        echo '<link rel="stylesheet" href="' . (isset($css['remote']) && $css['remote'] ? '' : PJ_INSTALL_URL) . $css['path'] . $css['file'] . '">' . "\n";

    }

    ?>

    <style>

    /* Theme from admin-datagrid-listings.css (--admin-primary, --wj-primary, …) */

    :root {

        --wj-bg: #f3f3f4;

        --wj-card-bg: #ffffff;

        --wj-radius: 16px;

        --wj-success: var(--admin-success, #22c55e);

        --wj-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);

        --wj-shadow-hover: 0 12px 32px rgba(10, 81, 20, 0.12);

    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {

        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

        background: var(--wj-bg);

        color: var(--wj-text);

        min-height: 100vh;

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 24px 16px;

    }

    .wj-setup { max-width: 920px; width: 100%; }

    .wj-setup-brand-wrap {
        text-align: center;
        padding: 20px 32px 0;
    }

    .wj-setup-brand {
        display: inline-block;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: var(--wj-text-muted, #636e72);
        text-decoration: none;
    }

    .wj-setup-brand:hover {
        color: var(--wj-primary, #0a5114);
    }

    .wj-setup-shell {

        background: var(--wj-card-bg);

        border-radius: 20px;

        box-shadow: var(--wj-shadow);

        border: 1px solid var(--wj-border, #e8e8e8);

        overflow: hidden;

    }



    /* Stepper */

    .wj-setup-stepper {

        display: flex;

        align-items: flex-start;

        justify-content: center;

        padding: 28px 32px 24px;

        background: linear-gradient(180deg, var(--admin-primary-lighter, var(--wj-primary-light)) 0%, var(--wj-card-bg) 100%);

        border-bottom: 1px solid var(--wj-border, #e8e8e8);

        gap: 0;

    }

    .wj-step {

        display: flex;

        flex-direction: column;

        align-items: center;

        gap: 8px;

        min-width: 72px;

        flex-shrink: 0;

    }

    .wj-step-circle {

        width: 40px;

        height: 40px;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 15px;

        font-weight: 700;

        background: var(--wj-card-bg);

        border: 2px solid var(--wj-border, #dde1e4);

        color: var(--wj-text-muted);

        transition: all 0.3s ease;

    }

    .wj-step-label {

        font-size: 12px;

        font-weight: 600;

        color: var(--wj-text-muted);

        text-align: center;

        max-width: 80px;

        line-height: 1.3;

        transition: color 0.3s ease;

    }

    .wj-step-label:empty,
    .wj-step-label[aria-hidden="true"] {
        display: none;
    }

    .wj-setup-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .wj-step-line {

        flex: 1;

        min-width: 24px;

        max-width: 64px;

        height: 2px;

        background: var(--wj-border, #dde1e4);

        margin-top: 19px;

        transition: background 0.3s ease;

    }

    .wj-step--active .wj-step-circle {

        background: var(--wj-primary);

        border-color: var(--wj-primary);

        color: #fff;

        box-shadow: 0 4px 12px rgba(10, 81, 20, 0.35);

    }

    .wj-step--active .wj-step-label { color: var(--wj-primary); }

    .wj-step--done .wj-step-circle {

        background: var(--wj-primary);

        border-color: var(--wj-primary);

        color: #fff;

    }

    .wj-step--done .wj-step-circle::after {

        content: '';

        width: 14px;

        height: 8px;

        border-left: 2.5px solid #fff;

        border-bottom: 2.5px solid #fff;

        transform: rotate(-45deg) translateY(-1px);

        margin-top: -2px;

    }

    .wj-step--done .wj-step-circle { font-size: 0; }

    .wj-step--done .wj-step-label { color: var(--wj-text); }

    .wj-step-line--done { background: var(--wj-primary); }



    /* Panel — single screen */

    .wj-setup-panel { padding: 36px 40px 40px; min-height: 420px; position: relative; }

    .wj-setup-main { transition: opacity 0.25s ease; }

    .wj-setup-main--busy { opacity: 0.35; pointer-events: none; }

    .wj-setup-overlay {
        position: absolute;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: rgba(255, 255, 255, 0.92);
        z-index: 10;
        text-align: center;
    }

    .wj-setup-overlay--visible { display: flex; }

    .wj-setup-overlay-view { width: 100%; max-width: 400px; }

    .wj-setup-overlay-view[hidden] { display: none !important; }

    @keyframes wj-fade-in {

        from { opacity: 0; transform: translateY(8px); }

        to { opacity: 1; transform: translateY(0); }

    }

    .wj-setup-step-head { text-align: center; margin-bottom: 28px; }

    .wj-setup-step-head--compact { margin-bottom: 20px; }

    .wj-setup-step-icon {

        width: 64px; height: 64px; border-radius: 16px;

        background: var(--admin-primary-lighter, var(--wj-primary-light));

        color: var(--wj-primary);

        display: flex; align-items: center; justify-content: center;

        margin: 0 auto 20px;

    }

    .wj-setup-title { font-size: 26px; font-weight: 800; margin-bottom: 10px; color: var(--wj-text); }

    .wj-setup-step-title { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: var(--wj-text); }

    .wj-setup-subtitle { font-size: 15px; color: var(--wj-text-muted); max-width: 520px; margin: 0 auto; line-height: 1.65; }

    .wj-setup-highlights {

        list-style: none; max-width: 480px; margin: 0 auto; padding: 0;

    }

    .wj-setup-highlights li {

        font-size: 14px; padding: 12px 16px; margin-bottom: 10px;

        background: var(--admin-primary-lighter, var(--wj-primary-light));

        border-radius: 12px; color: var(--wj-text);

        display: flex; align-items: flex-start; gap: 12px; line-height: 1.5;

    }

    .wj-setup-highlights li::before {

        content: ''; width: 8px; height: 8px; border-radius: 50%;

        background: var(--wj-primary); flex-shrink: 0; margin-top: 6px;

    }



    /* Choice cards */

    .wj-setup-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    @media (max-width: 700px) {

        .wj-setup-stepper { padding: 20px 16px; flex-wrap: wrap; gap: 8px; }

        .wj-step-line { display: none; }

        .wj-setup-panel { padding: 24px 20px; }

        .wj-setup-cards { grid-template-columns: 1fr; }

        .wj-setup-title { font-size: 22px; }

    }

    .wj-setup-card {

        background: var(--wj-card-bg);

        border: 2px solid var(--wj-border, #e8e8e8);

        border-radius: var(--wj-radius);

        padding: 24px 22px;

        transition: all 0.25s ease;

        position: relative;

    }

    .wj-setup-card:hover { border-color: var(--wj-primary); box-shadow: var(--wj-shadow-hover); }

    .wj-setup-card.wj-card--demo { border-color: var(--wj-primary); }

    .wj-card-icon {

        width: 52px; height: 52px; border-radius: 14px;

        display: flex; align-items: center; justify-content: center; margin-bottom: 16px;

    }

    .wj-card--demo .wj-card-icon { background: var(--admin-primary-lighter, var(--wj-primary-light)); color: var(--wj-primary); }

    .wj-card--empty .wj-card-icon { background: #f3f4f6; color: var(--wj-text-muted); }

    .wj-card-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }

    .wj-card-desc { font-size: 13px; color: var(--wj-text-muted); line-height: 1.55; margin-bottom: 12px; }

    .wj-card-features { list-style: none; padding: 0; margin: 0; }

    .wj-card-features li {

        font-size: 12px; padding: 3px 0; display: flex; align-items: center; gap: 8px;

    }

    .wj-card-features li::before {

        content: ''; width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0;

        background: var(--wj-success) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='9' height='9' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E") center no-repeat;

    }

    .wj-card-badge {

        display: inline-block; font-size: 10px; font-weight: 600; color: #fff;

        background: var(--wj-primary); padding: 3px 10px; border-radius: 20px; margin-bottom: 12px;

    }



    /* Footer nav */

    .wj-setup-nav {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 16px;

        padding: 20px 40px 28px;

        border-top: 1px solid var(--wj-border, #e8e8e8);

        background: #fafafa;

    }

    .wj-setup-nav.wj-setup-nav--hidden { display: none; }

    .wj-setup-nav-continue { margin-left: auto; min-width: 160px; }

    .wj-setup-btn {

        padding: 13px 28px; border: none; border-radius: 50px;

        font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit;

        transition: all 0.2s ease;

    }

    .wj-setup-btn--primary { background: var(--wj-primary); color: #fff; }

    .wj-setup-btn--primary:hover { background: var(--wj-primary-dark); }

    .wj-setup-btn--secondary {
        background: #fff;
        color: var(--wj-text);
        border: 2px solid var(--wj-border, #dde1e4);
    }

    .wj-setup-btn--secondary:hover {
        border-color: var(--wj-primary);
        color: var(--wj-primary);
    }

    .wj-setup-btn--block {
        display: block;
        width: 100%;
        margin-top: 16px;
        text-align: center;
    }

    .wj-setup-btn--ghost {

        background: transparent; color: var(--wj-text-muted);

        border: 2px solid var(--wj-border, #dde1e4);

    }

    .wj-setup-btn--ghost:hover { border-color: var(--wj-primary); color: var(--wj-primary); }

    .wj-setup-btn:disabled { opacity: 0.55; cursor: not-allowed; }



    /* Loading & success */

    .wj-spinner {

        width: 48px; height: 48px; border: 4px solid var(--admin-primary-lighter, var(--wj-primary-light));

        border-top-color: var(--wj-primary); border-radius: 50%;

        animation: wj-spin 0.8s linear infinite; margin: 0 auto 20px;

    }

    @keyframes wj-spin { to { transform: rotate(360deg); } }

    .wj-setup-loading-text { font-size: 17px; font-weight: 600; margin-bottom: 8px; color: var(--wj-text); }

    .wj-setup-loading-sub { font-size: 14px; color: var(--wj-text-muted); }

    .wj-progress-bar {

        width: 100%; max-width: 320px; height: 6px;

        background: var(--admin-primary-lighter, var(--wj-primary-light));

        border-radius: 3px; margin: 20px auto 0; overflow: hidden;

    }

    .wj-progress-bar-fill { height: 100%; background: var(--wj-primary); width: 0%; transition: width 0.5s ease; }

    .wj-success-icon {

        width: 72px; height: 72px; border-radius: 50%; background: var(--wj-success);

        display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;

    }

    .wj-success-icon svg { width: 36px; height: 36px; stroke: #fff; }

    .wj-setup-check-icon { color: var(--wj-primary); margin-right: 8px; }

    .wj-setup-admin-spinner { font-size: 24px; color: var(--wj-primary); margin-bottom: 15px; }

    </style>

</head>

<body>

    <?php require $content_tpl; ?>

    <?php

    foreach ($controller->getJs() as $js) {

        echo '<script src="' . (isset($js['remote']) && $js['remote'] ? '' : PJ_INSTALL_URL) . $js['path'] . $js['file'] . '"></script>' . "\n";

    }

    ?>

</body>

</html>

