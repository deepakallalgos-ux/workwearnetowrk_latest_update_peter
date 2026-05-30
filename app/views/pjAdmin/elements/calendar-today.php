<?php
/**
 * Compact "Today" widget for delivery and pickup (dashboard / orders).
 * Shows today and tomorrow only.
 *
 * Expected variables: $calWidgetId, $calDeliveryActive, $calPickupActive
 */
if (!isset($calWidgetId) || (!$calDeliveryActive && !$calPickupActive)) return;

$calLabels = [
    'title'     => __('calendar_title', true) ?: 'Schedule',
    'delivery'  => __('calendar_delivery', true) ?: 'Delivery',
    'pickup'    => __('calendar_pickup', true) ?: 'Pickup',
    'no_events' => __('calendar_no_events', true) ?: 'No delivery or pickup slots scheduled',
    'today'     => __('calendar_today', true) ?: 'Today',
];
?>

<?php if (!defined('WJ_CAL_TODAY_CSS')): ?>
<?php define('WJ_CAL_TODAY_CSS', true); ?>
<style>
.wj-calt {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    padding: 16px 20px;
    margin-bottom: 20px;
}
.wj-calt-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}
.wj-calt-title {
    font-size: 15px;
    font-weight: 600;
    color: #1a1a2e;
    margin: 0;
    flex: 1;
}
.wj-calt-title i { color: var(--admin-primary, #0a5114); margin-right: 4px; }
.wj-calt-link {
    font-size: 12px;
    color: var(--admin-primary, #0a5114);
    text-decoration: none;
}
.wj-calt-link:hover { text-decoration: underline; }
.wj-calt-section { margin-bottom: 10px; }
.wj-calt-section:last-child { margin-bottom: 0; }
.wj-calt-date {
    font-size: 11px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    padding-bottom: 4px;
    margin-bottom: 6px;
    border-bottom: 1px solid #f0f0f4;
}
.wj-calt-event {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 8px;
    background: #f8f7fc;
    margin-bottom: 4px;
    cursor: pointer;
    transition: all .15s;
    border-left: 3px solid transparent;
}
.wj-calt-event:hover { background: #eaf4ec; transform: translateX(2px); }
.wj-calt-event.type-delivery { border-left-color: var(--admin-primary, #0a5114); }
.wj-calt-event.type-pickup { border-left-color: var(--wj-accent, #F7148B); }
.wj-calt-event.type-shipping { border-left-color: var(--admin-info, #3b82f6); }
.wj-calt-event.overdue { border-left-color: var(--admin-danger, #dc2626); background: #fef2f2; }
.wj-calt-event.overdue:hover { background: #fde8e8; }
.wj-calt-time { font-size: 13px; font-weight: 700; color: #333; min-width: 40px; }
.wj-calt-badge {
    font-size: 9px; font-weight: 600; padding: 2px 6px;
    border-radius: 10px; color: #fff; white-space: nowrap;
}
.wj-calt-badge.bd { background: var(--admin-primary, #0a5114); }
.wj-calt-badge.bp { background: var(--wj-accent, #F7148B); }
.wj-calt-badge.bs { background: var(--admin-info, #3b82f6); }
.wj-calt-badge.bo { background: var(--admin-danger, #dc2626); }
.wj-calt-client { flex: 1; font-size: 13px; color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.wj-calt-total { font-size: 12px; font-weight: 600; color: #555; white-space: nowrap; }
.wj-calt-empty { text-align: center; padding: 16px; color: #bbb; font-size: 12px; }
.wj-calt-empty i { margin-right: 4px; }
.wj-calt-loading { text-align: center; padding: 16px; color: #bbb; }
.wj-calt-count {
    font-size: 11px; font-weight: 600; padding: 2px 8px;
    border-radius: 10px; background: #eaf4ec; color: var(--admin-primary, #0a5114);
}
.wj-calt-toggle {
    width: 28px; height: 28px; border-radius: 6px;
    border: 1px solid #e2e2e8; background: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #999; font-size: 12px; transition: all .15s; margin-left: auto;
}
.wj-calt-toggle:hover { border-color: var(--admin-primary, #0a5114); color: var(--admin-primary, #0a5114); }
.wj-calt.collapsed .wj-calt-body,
.wj-calt.collapsed .wj-calt-header h4,
.wj-calt.collapsed .wj-calt-link { display: none; }
.wj-calt.collapsed { padding: 8px 16px; }
.wj-calt.collapsed .wj-calt-header { margin-bottom: 0; }
.wj-calt-collapsed-label { display: none; font-size: 13px; color: #1a1a2e; font-weight: 600; }
.wj-calt-collapsed-label i { color: var(--admin-primary, #0a5114); margin-right: 4px; }
.wj-calt.collapsed .wj-calt-collapsed-label { display: inline; }
</style>
<?php endif; ?>

<div class="wj-calt" id="<?php echo $calWidgetId; ?>"
     data-wj-cal-today
     data-delivery-active="<?php echo $calDeliveryActive ? '1' : '0'; ?>"
     data-pickup-active="<?php echo $calPickupActive ? '1' : '0'; ?>">

    <div class="wj-calt-header">
        <h4 class="wj-calt-title"><i class="fa fa-calendar"></i> <?php echo pjSanitize::html($calLabels['today']); ?></h4>
        <span class="wj-calt-collapsed-label"><i class="fa fa-calendar"></i> <?php echo pjSanitize::html($calLabels['today']); ?></span>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionCalendar" class="wj-calt-link">Full calendar &rarr;</a>
        <button type="button" class="wj-calt-toggle" data-calt-toggle title="Show or hide schedule"><i class="fa fa-chevron-up"></i></button>
    </div>

    <div class="wj-calt-body" data-calt-body>
        <div class="wj-calt-loading"><i class="fa fa-spinner fa-spin"></i></div>
    </div>
</div>

<?php if (!defined('WJ_CAL_TODAY_JS')): ?>
<?php define('WJ_CAL_TODAY_JS', true); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
var $ = window.jQuery;
if (!$) return;

var DAYS_EN = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
var MONTHS_EN = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function formatDateDisplay(d) {
    return DAYS_EN[d.getDay()] + ' ' + d.getDate() + ' ' + MONTHS_EN[d.getMonth()];
}

function pad(n) { return n < 10 ? '0' + n : '' + n; }
function isoDate(d) { return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()); }

$('[data-wj-cal-today]').each(function() {
    var $w = $(this);
    var widgetId = $w.attr('id');
    var $body = $w.find('[data-calt-body]');

    // Collapsed state from localStorage
    var colKey = 'wj_calt_collapsed_' + widgetId;
    if (localStorage.getItem(colKey) === '1') {
        $w.addClass('collapsed');
        $w.find('[data-calt-toggle] i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }
    $w.on('click', '[data-calt-toggle]', function() {
        var collapsed = $w.toggleClass('collapsed').hasClass('collapsed');
        $(this).find('i').toggleClass('fa-chevron-up', !collapsed).toggleClass('fa-chevron-down', collapsed);
        localStorage.setItem(colKey, collapsed ? '1' : '0');
    });
    var labels = {
        delivery: <?php echo json_encode($calLabels['delivery']); ?>,
        pickup: <?php echo json_encode($calLabels['pickup']); ?>,
        shipping: <?php echo json_encode(__('order_shipping_type_shipping', true) ?: 'Shipping'); ?>,
        no_events: <?php echo json_encode($calLabels['no_events']); ?>
    };

    var today = new Date();
    var tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    var todayStr = isoDate(today);
    var tomorrowStr = isoDate(tomorrow);

    $.ajax({
        url: 'index.php?controller=pjAdminOrders&action=pjActionGetCalendarEvents',
        data: { start: todayStr, end: tomorrowStr, include_overdue: 1 },
        dataType: 'json'
    }).done(function(resp) {
        var events = (resp && resp.events) ? resp.events : [];

        // Deduplicate (overdue may also fall in today/tomorrow range)
        var seen = {};
        events = events.filter(function(e) { if (seen[e.id]) return false; seen[e.id] = true; return true; });

        var overdueEvents = events.filter(function(e) { return e.overdue && e.date !== todayStr && e.date !== tomorrowStr; });
        var todayEvents = events.filter(function(e) { return e.date === todayStr; });
        var tomorrowEvents = events.filter(function(e) { return e.date === tomorrowStr && !e.overdue; });

        var totalCount = overdueEvents.length + todayEvents.length + tomorrowEvents.length;
        if (totalCount > 0) {
            $w.find('.wj-calt-title').append(' <span class="wj-calt-count">' + totalCount + '</span>');
        }

        var html = '';

        if (totalCount === 0) {
            html += '<div class="wj-calt-empty"><i class="fa fa-calendar-o"></i> ' + labels.no_events + '</div>';
        } else {
            if (overdueEvents.length > 0) {
                html += '<div class="wj-calt-section">';
                html += '<div class="wj-calt-date" style="color:var(--admin-danger,#dc2626);">Overdue (' + overdueEvents.length + ')</div>';
                overdueEvents.sort(function(a,b) { return (a.date||'').localeCompare(b.date||''); });
                overdueEvents.forEach(function(e) { html += renderEvent(e, labels); });
                html += '</div>';
            }
            if (todayEvents.length > 0) {
                html += '<div class="wj-calt-section">';
                html += '<div class="wj-calt-date">Today &mdash; ' + formatDateDisplay(today) + ' (' + todayEvents.length + ')</div>';
                todayEvents.sort(function(a,b) { return (a.time||'').localeCompare(b.time||''); });
                todayEvents.forEach(function(e) { html += renderEvent(e, labels); });
                html += '</div>';
            }
            if (tomorrowEvents.length > 0) {
                html += '<div class="wj-calt-section">';
                html += '<div class="wj-calt-date">Tomorrow &mdash; ' + formatDateDisplay(tomorrow) + ' (' + tomorrowEvents.length + ')</div>';
                tomorrowEvents.sort(function(a,b) { return (a.time||'').localeCompare(b.time||''); });
                tomorrowEvents.forEach(function(e) { html += renderEvent(e, labels); });
                html += '</div>';
            }
        }

        $body.html(html);
    }).fail(function() {
        $body.html('<div class="wj-calt-empty"><i class="fa fa-calendar-o"></i> ' + labels.no_events + '</div>');
    });

    $w.on('click', '.wj-calt-event', function() {
        var id = $(this).data('order-id');
        if (id) window.location.href = 'index.php?controller=pjAdminOrders&action=pjActionUpdate&id=' + id;
    });
});

function renderEvent(e, labels) {
    var tc = 'type-' + e.type;
    var isOverdue = e.overdue === true;
    var bc = isOverdue ? 'bo' : (e.type === 'delivery' ? 'bd' : (e.type === 'pickup' ? 'bp' : 'bs'));
    var bl = e.type === 'delivery' ? labels.delivery : (e.type === 'pickup' ? labels.pickup : labels.shipping);
    if (isOverdue && e.overdue_label) bl += ' (' + e.overdue_label + ')';
    tc += isOverdue ? ' overdue' : '';
    var h = '<div class="wj-calt-event ' + tc + '" data-order-id="' + e.id + '">';
    h += '<span class="wj-calt-time">' + (e.time || '-') + '</span>';
    h += '<span class="wj-calt-badge ' + bc + '">' + bl + '</span>';
    h += '<span class="wj-calt-client">' + escHtml(e.client_name || 'Guest') + (e.address ? ' &mdash; ' + escHtml(e.address) : '') + '</span>';
    if (e.total_formatted) h += '<span class="wj-calt-total">' + e.total_formatted + '</span>';
    h += '</div>';
    return h;
}

function escHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

});
</script>
<?php endif; ?>
