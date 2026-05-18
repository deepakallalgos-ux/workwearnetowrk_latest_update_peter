/**
 * Admin setup wizard and demo data management (Workwear).
 */
var wjSetup = (function() {
    'use strict';

    var ajaxUrl = 'index.php';
    var isAdminMode = !document.querySelector('.wj-setup-shell');
    var L = typeof myLabel !== 'undefined' ? myLabel : {};

    function label(key, fallback) {
        return L[key] || fallback;
    }

    function getEl(id) {
        return document.getElementById(id);
    }

    function showOverlay() {
        var overlay = getEl('wjSetupOverlay');
        var main = getEl('wjSetupMain');
        if (overlay) {
            overlay.classList.add('wj-setup-overlay--visible');
            overlay.setAttribute('aria-hidden', 'false');
        }
        if (main) {
            main.classList.add('wj-setup-main--busy');
        }
    }

    function hideOverlay() {
        var overlay = getEl('wjSetupOverlay');
        var main = getEl('wjSetupMain');
        if (overlay) {
            overlay.classList.remove('wj-setup-overlay--visible');
            overlay.setAttribute('aria-hidden', 'true');
        }
        if (main) {
            main.classList.remove('wj-setup-main--busy');
        }
    }

    function showLoadingView(text, sub) {
        showOverlay();
        var loadingView = getEl('wjSetupLoadingView');
        var successView = getEl('wjSetupSuccessView');
        if (loadingView) loadingView.hidden = false;
        if (successView) successView.hidden = true;
        if (text && getEl('wjLoadingText')) {
            getEl('wjLoadingText').textContent = text;
        }
        if (sub && getEl('wjLoadingSub')) {
            getEl('wjLoadingSub').textContent = sub;
        }
        updateProgress(0);
    }

    function showSuccessView(text, sub) {
        showOverlay();
        var loadingView = getEl('wjSetupLoadingView');
        var successView = getEl('wjSetupSuccessView');
        if (loadingView) loadingView.hidden = true;
        if (successView) successView.hidden = false;
        if (text && getEl('wjSuccessText')) {
            getEl('wjSuccessText').textContent = text;
        }
        if (sub && getEl('wjSuccessSub')) {
            getEl('wjSuccessSub').textContent = sub;
        }
    }

    function updateProgress(percent) {
        var fill = getEl('wjProgressFill');
        if (fill) fill.style.width = percent + '%';
    }

    function redirectToDashboard() {
        window.location.href = ajaxUrl + '?controller=pjAdmin&action=pjActionIndex';
    }

    function formatSummary(data, removed) {
        var parts = [];
        if (data.products) parts.push(data.products + ' ' + label('setup_unit_products', 'products'));
        if (data.stocks) parts.push(data.stocks + ' ' + label('setup_unit_variants', 'variants'));
        if (data.categories) parts.push(data.categories + ' ' + label('setup_unit_categories', 'categories'));
        if (data.images) parts.push(data.images + ' ' + label('setup_unit_images', 'images'));
        if (data.clients) parts.push(data.clients + ' ' + label('setup_unit_clients', 'clients'));
        if (data.orders) parts.push(data.orders + ' ' + label('setup_unit_quotes', 'quotes'));
        if (parts.length > 0) {
            return parts.join(', ') + '.';
        }
        return removed
            ? label('setup_summary_removed', 'Demo data removed.')
            : label('setup_summary_loaded', 'Demo data loaded.');
    }

    function handleLoadDemoSuccess(resp) {
        var msg = formatSummary(resp.data || {}, false);
        if (isAdminMode) {
            hideOverlay();
            var loadingEl = getEl('wjSetupLoading');
            if (loadingEl) {
                loadingEl.style.display = 'none';
            }
            if (typeof swal === 'function') {
                swal({
                    title: label('setup_swal_loaded_title', 'Demo data loaded'),
                    text: msg,
                    type: 'success',
                    confirmButtonText: label('setup_btn_ok', 'OK')
                }, function() {
                    window.location.reload();
                });
            } else {
                alert(msg);
                window.location.reload();
            }
        } else {
            updateProgress(100);
            setTimeout(function() {
                showSuccessView(
                    label('setup_success_title', 'Your platform is ready'),
                    msg
                );
                setTimeout(redirectToDashboard, 2000);
            }, 400);
        }
    }

    function loadDemoBatch(offset) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxUrl + '?controller=pjAdminSetup&action=pjActionLoadDemo', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function() {
            try {
                var resp = JSON.parse(xhr.responseText);

                if (resp.status === 'CONTINUE') {
                    var total = parseInt(resp.total, 10) || 1;
                    var done = parseInt(resp.offset, 10) || 0;
                    if (!isAdminMode) {
                        updateProgress(Math.min(92, 10 + (done / total) * 82));
                        var sub = getEl('wjLoadingSub');
                        if (sub) {
                            sub.textContent = label('setup_loading_demo_progress', 'Importing catalogue') + ' (' + done + ' / ' + total + ')';
                        }
                    }
                    loadDemoBatch(done);
                    return;
                }

                if (resp.status === 'OK') {
                    handleLoadDemoSuccess(resp);
                    return;
                }

                hideOverlay();
                alert(label('setup_error_prefix', 'Error:') + ' ' + (resp.text || label('setup_error_unknown', 'Unknown error')));
                window.location.reload();
            } catch (e) {
                hideOverlay();
                alert(label('setup_error_generic', 'An error occurred. Please try again.'));
                window.location.reload();
            }
        };

        xhr.onerror = function() {
            hideOverlay();
            alert(label('setup_error_connection', 'Connection error. Please try again.'));
            window.location.reload();
        };

        xhr.send('offset=' + encodeURIComponent(offset || 0));
    }

    function loadDemo() {
        var btn = getEl('btnLoadDemo') || getEl('btnLoadDemoAdmin');
        if (btn) btn.disabled = true;
        var skipBtn = getEl('btnSkipDemo');
        if (skipBtn) skipBtn.disabled = true;

        if (isAdminMode) {
            var loadingEl = getEl('wjSetupLoading');
            if (loadingEl) {
                loadingEl.style.display = 'block';
            }
        } else {
            showLoadingView(
                label('setup_loading_demo', 'Loading demo data...'),
                label('setup_loading_demo_detail', 'Loading sample apron and beanie from the catalogue')
            );
            updateProgress(10);
        }

        loadDemoBatch(0);
    }

    function skipDemo() {
        var btn = getEl('btnSkipDemo');
        if (btn) btn.disabled = true;
        var loadBtn = getEl('btnLoadDemo');
        if (loadBtn) loadBtn.disabled = true;

        showLoadingView(
            label('setup_skip_loading', 'Please wait...'),
            label('setup_skip_loading_sub', 'Preparing your empty catalogue')
        );
        updateProgress(50);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxUrl + '?controller=pjAdminSetup&action=pjActionSkipDemo', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function() {
            updateProgress(100);
            setTimeout(function() {
                showSuccessView(
                    label('setup_success_title', 'Your platform is ready'),
                    label('setup_success_redirect', 'Redirecting to the dashboard...')
                );
                setTimeout(redirectToDashboard, 1500);
            }, 300);
        };

        xhr.onerror = function() {
            hideOverlay();
            alert(label('setup_error_connection_short', 'Connection error.'));
            window.location.reload();
        };

        xhr.send('');
    }

    function removeDemo() {
        if (typeof swal === 'function') {
            swal({
                title: label('setup_remove_confirm_title', 'Remove demo data?'),
                text: label('setup_remove_confirm_text', 'All sample products, categories, clients, and quotes will be permanently removed.'),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: label('setup_btn_remove_confirm', 'Yes, remove'),
                cancelButtonText: label('btn_cancel', 'Cancel'),
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                doRemoveDemo();
            });
        } else if (confirm(label('setup_remove_confirm_fallback', 'Remove all demo data? This cannot be undone.'))) {
            doRemoveDemo();
        }
    }

    function doRemoveDemo() {
        if (isAdminMode) {
            var loadingEl = getEl('wjSetupLoading');
            if (loadingEl) {
                loadingEl.style.display = 'block';
                if (getEl('wjLoadingText')) {
                    getEl('wjLoadingText').textContent = label('setup_removing_demo', 'Removing demo data...');
                }
            }
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxUrl + '?controller=pjAdminSetup&action=pjActionRemoveDemo', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function() {
            try {
                var resp = JSON.parse(xhr.responseText);
                if (resp.status === 'OK') {
                    var msg = formatSummary(resp.data || {}, true);
                    if (typeof swal === 'function') {
                        swal({
                            title: label('setup_swal_removed_title', 'Demo data removed'),
                            text: msg,
                            type: 'success',
                            confirmButtonText: label('setup_btn_ok', 'OK')
                        }, function() {
                            window.location.reload();
                        });
                    } else {
                        alert(msg);
                        window.location.reload();
                    }
                } else {
                    alert(label('setup_error_prefix', 'Error:') + ' ' + (resp.text || label('setup_error_unknown', 'Unknown error')));
                }
            } catch (e) {
                alert(label('setup_error_short', 'An error occurred.'));
                window.location.reload();
            }
        };

        xhr.onerror = function() {
            alert(label('setup_error_connection_short', 'Connection error.'));
        };

        xhr.send('');
    }

    return {
        loadDemo: loadDemo,
        skipDemo: skipDemo,
        removeDemo: removeDemo
    };
})();
