<?php

if (! defined("ROOT_PATH")) {

    header("HTTP/1.1 403 Forbidden");

    exit;
}

class pjInvoice extends pjInvoiceAppController
{

    public $invoiceErrors = 'InvoiceErrors';

    private function sortTimezones(array $array)
    {

        $ordered = [];

        $orderArray = ['-43200', '-39600', '-36000', '-32400', '-28800', '-25200', '-21600', '-18000', '-14400', '-10800', '-7200', '-3600', '0', '3600', '7200', '10800', '14400', '18000', '21600', '25200', '28800', '32400', '36000', '39600', '43200', '46800'];

        foreach ($orderArray as $key) {

            if (array_key_exists($key, $array)) {

                $ordered[$key] = $array[$key];

                unset($array[$key]);
            }
        }

        return $ordered + $array;
    }

    public function pjActionCreate()
    {

        $params = $this->getParams();

        if (! isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT)) {

            return ['status' => 'ERR', 'code' => '101', 'text' => 'Key is not set or invalid'];
        }

        $locale_id = isset($params['locale_id']) && ! empty($params['locale_id']) ? $params['locale_id'] : $this->getLocaleId();

        $config = pjInvoiceConfigModel::factory()->getConfigData($locale_id);

        $config['id'] = null;
        $config['invoice_pdf_link'] = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=generatePdf&id='
            . urlencode(trim($params['uuid']))
            . '&uuid=' . urlencode(trim($params['order_id']));

        unset($config['id']);

        $data       = array_merge($params, $config);
        $invoice_id = pjInvoiceModel::factory($data)->insert()->getInsertId();

        if ($invoice_id !== false && (int) $invoice_id > 0) {

            if (isset($params['items']) && is_array($params['items']) && ! empty($params['items'])) {

                $pjInvoiceItemModel = pjInvoiceItemModel::factory();

                foreach ($params['items'] as $item) {

                    $item['invoice_id'] = $invoice_id;

                    $pjInvoiceItemModel->reset()->setAttributes($item)->insert();
                }
            }
            // die;
            return ['status' => 'OK', 'code' => '200', 'text' => 'Invoice has been created.', 'data' => array_merge($data, ['id' => $invoice_id])];
        } else {

            return ['status' => 'ERR', 'code' => '100', 'text' => 'Invoice has not been created.'];
        }
    }

    public function pjActionDelete()
    {

        $this->setAjax(true);

        if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady()) {

            $response = [];

            if (pjInvoiceModel::factory()->set('id', $_REQUEST['id'])->erase()->getAffectedRows() == 1) {

                pjInvoiceItemModel::factory()->where('invoice_id', $_REQUEST['id'])->eraseAll();

                // $response['code'] = 200;
                self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Order has been deleted']);
            } else {
                self::jsonResponse(['status' => 'ERR', 'code' => 105, 'text' => 'Order has not been deleted.']);

                // $response['code'] = 100;

            }

            pjAppController::jsonResponse($response);
        }

        exit;
    }

    public function pjActionDeleteBulk()
    {

        $this->setAjax(true);

        if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady()) {

            if (isset($_REQUEST['record']) && count($_REQUEST['record']) > 0) {

                pjInvoiceModel::factory()->whereIn('id', $_REQUEST['record'])->eraseAll();

                pjInvoiceItemModel::factory()->whereIn('invoice_id', $_REQUEST['record'])->eraseAll();
                self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Order has been deleted']);
            }
        }

        exit;
    }

    public function pjActionDeleteLogo()
    {

        $this->setAjax(true);

        if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady()) {

            $pjInvoiceConfigModel = pjInvoiceConfigModel::factory();

            $arr = $pjInvoiceConfigModel->find(1)->getData();

            if (! empty($arr) && ! empty($arr['y_logo'])) {

                @clearstatcache();

                if (is_file($arr['y_logo'])) {

                    @unlink($arr['y_logo']);
                }

                $pjInvoiceConfigModel->set('id', 1)->modify(['y_logo' => ':NULL']);
                self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Order has been deleted']);
            }
        }

        exit;
    }

    public function pjActionGetInvoices()
    {
        // ini_set('display_errors', '1');
        // ini_set('display_startup_errors', '1');
        // error_reporting(E_ALL);
        $this->setAjax(true);

        if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady()) {

            $pjInvoiceModel = pjInvoiceModel::factory();

            if (isset($_REQUEST['foreign_id'])) {

                $foreign_arr = $this->get('foreign_arr');

                if ((int) $_REQUEST['foreign_id'] > 0 && $foreign_arr !== false && ! empty($foreign_arr)) {

                    $pjInvoiceModel->where('t1.foreign_id', $_REQUEST['foreign_id']);
                }
            }

            if (isset($_REQUEST['q']) && ! empty($_REQUEST['q'])) {

                $q = $pjInvoiceModel->escapeStr($_REQUEST['q']);

                $q = str_replace(['%', '_'], ['\%', '\_'], $q);

                $pjInvoiceModel

                    ->where('t1.uuid LIKE', "%$q%")

                    ->orWhere('t1.order_id LIKE', "%$q%")

                    ->orWhere('t1.b_company LIKE', "%$q%")

                    ->orWhere('t1.b_name LIKE', "%$q%")

                    ->orWhere('t1.b_email LIKE', "%$q%")

                    ->orWhere('t1.s_company LIKE', "%$q%")

                    ->orWhere('t1.s_name LIKE', "%$q%")

                    ->orWhere('t1.s_email LIKE', "%$q%")

                ;
            }

            // Pijler 5: status filter
            if (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['paid', 'not_paid', 'cancelled'], true)) {
                $pjInvoiceModel->where('t1.status', $_REQUEST['status']);
            }

            $column = 'created';

            $direction = 'DESC';

            if (isset($_REQUEST['direction']) && isset($_REQUEST['column']) && in_array(strtoupper($_REQUEST['direction']), ['ASC', 'DESC'])) {

                $column = $_REQUEST['column'];

                $direction = strtoupper($_REQUEST['direction']);
            }

            $total = $pjInvoiceModel->findCount()->getData();

            $rowCount = isset($_REQUEST['rowCount']) && (int) $_REQUEST['rowCount'] > 0 ? (int) $_REQUEST['rowCount'] : 10;

            $pages = ceil($total / $rowCount);

            $page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;

            $offset = ((int) $page - 1) * $rowCount;

            if ($page > $pages) {

                $page = $pages;
            }

            $data = $pjInvoiceModel->orderBy("`$column` $direction")->limit($rowCount, $offset)->findAll()->getData();

            foreach ($data as $k => $v) {

                $data[$k]['total_formated'] = pjCurrency::formatPrice($v['total']);

                // Pijler 5: customer-weergave (bedrijf als primair, naam als fallback, e-mail als subtitle)
                $name = trim((string) $v['b_name']);
                $company = trim((string) $v['b_company']);
                $email = trim((string) $v['b_email']);
                if ($company !== '') {
                    $data[$k]['customer_label'] = $company;
                    $data[$k]['customer_sub']   = $name !== '' ? $name : $email;
                } else {
                    $data[$k]['customer_label'] = $name !== '' ? $name : $email;
                    $data[$k]['customer_sub']   = $name !== '' && $email !== '' ? $email : '';
                }
            }

            pjAppController::jsonResponse([
                'data'        => $data,
                'total'       => $total,
                'pages'       => $pages,
                'page'        => $page,
                'rowCount'    => $rowCount,
                'column'      => $column,
                'direction'   => $direction,
                'hide_loader' => true, // 👈 custom flag
            ]);

            // pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));

        }

        exit;
    }

    public function pjActionGetItems()
    {

        $this->setAjax(true);

        if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady()) {

            $pjInvoiceItemModel = pjInvoiceItemModel::factory();

            $column = 'id';

            $direction = 'ASC';

            if (isset($_REQUEST['direction']) && isset($_REQUEST['column']) && in_array(strtoupper($_REQUEST['direction']), ['ASC', 'DESC'])) {

                $column = $_REQUEST['column'];

                $direction = strtoupper($_REQUEST['direction']);
            }

            $pjInvoiceItemModel->where('t1.id', -1);

            if (isset($_REQUEST['invoice_id']) && (int) $_REQUEST['invoice_id'] > 0) {

                $pjInvoiceItemModel->reset()->where('t1.invoice_id', $_REQUEST['invoice_id']);
            }

            if (isset($_REQUEST['tmp']) && ! empty($_REQUEST['tmp'])) {

                $pjInvoiceItemModel->reset()->where('t1.tmp', $_REQUEST['tmp']);
            }

            $data = $pjInvoiceItemModel

                ->select('t1.*, t2.currency')

                ->join('pjInvoice', 't2.id=t1.invoice_id', 'left outer')

                ->orderBy("`$column` $direction")->findAll()->getData();

            foreach ($data as $k => $v) {

                $data[$k]['unit_price_formated'] = pjCurrency::formatPrice($v['unit_price']);

                $data[$k]['amount_formated'] = pjCurrency::formatPrice($v['amount']);
            }

            pjAppController::jsonResponse([
                'data'        => $data,

                'column'      => $column,
                'direction'   => $direction,
                'hide_loader' => true, // 👈 custom flag
            ]);
        }

        exit;
    }

    public function pjActionIndex()
    {

        $this->checkLogin();

        if (! $this->isInvoiceReady()) {

            $this->set('status', 2);

            return;
        }

        if (isset($_REQUEST['invoice_post'])) {
            // Pijler 1: bedrijfsgegevens + logo worden op Profiel beheerd, niet meer hier.
            // Deze pagina slaat alleen de invoice-specifieke opties op (factuurnummer + si/qty flags).
            $data = [];
            $data['invoice_number'] = isset($_REQUEST['invoice_number']) ? str_replace(' ', '', $_REQUEST['invoice_number']) : '';

            // Pijler 4: aantal cijfers + reset per jaar
            $digits = isset($_REQUEST['invoice_number_digits']) ? (int) $_REQUEST['invoice_number_digits'] : 5;
            if ($digits < 2) { $digits = 2; }
            if ($digits > 10) { $digits = 10; }
            $data['invoice_number_digits'] = $digits;
            $data['invoice_number_reset_yearly'] = isset($_REQUEST['invoice_number_reset_yearly']) ? 1 : 0;

            // Overige opties (si_include + o_use_qty_unit_price zijn de enige die nog in de UI staan)
            $data['si_include']           = isset($_REQUEST['si_include']) ? 1 : 0;
            $data['o_use_qty_unit_price'] = isset($_REQUEST['o_use_qty_unit_price']) ? 1 : 0;

            pjInvoiceConfigModel::factory()
                ->set('id', 1)
                ->modify($data);

            pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionIndex&err=PIN02");
        }

        $arr = pjInvoiceConfigModel::factory()->find(1)->getData();

        $this
            ->set('arr', $arr)
            ->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
            ->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
            ->appendJs('index.php?controller=pjBase&action=pjActionMessages', PJ_INSTALL_URL, true)
        ;
    }

    public function pjActionInvoices()
    {

        $this->checkLogin();

        if (! $this->isInvoiceReady()) {

            $this->set('status', 2);

            return;
        }

        $this

            ->set('invoice_config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()))

            ->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')

            ->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))

            ->appendCss('plugin_invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
            ->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/')
            ->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/')

            ->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/')
            ->appendJs('index.php?controller=pjBase&action=pjActionMessages', PJ_INSTALL_URL, true)

        ;
    }

    public function pjActionPrint()
    {

        $this->pjActionView();
    }

    public function pjActionSend()
    {

        $this->setAjax(true);

        if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady()) {

            if ($this->_get->check('uuid')) {

                $arr = pjInvoiceModel::factory()

                    ->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')

                    ->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='" . $this->getLocaleId() . "'", 'left outer')

                    ->join('pjMultiLang', "t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='" . $this->getLocaleId() . "'", 'left outer')

                    ->select("t1.*, t2.content as y_country_title, t3.content as b_country_title, t3.content as s_country_title,

						AES_DECRYPT(t1.cc_type, '" . PJ_SALT . "') AS cc_type,

						AES_DECRYPT(t1.cc_num, '" . PJ_SALT . "') AS cc_num,

						AES_DECRYPT(t1.cc_exp_month, '" . PJ_SALT . "') AS cc_exp_month,

						AES_DECRYPT(t1.cc_exp_year, '" . PJ_SALT . "') AS cc_exp_year,

						AES_DECRYPT(t1.cc_code, '" . PJ_SALT . "') AS cc_code")

                    ->where('t1.uuid', $this->_get->toString('id'))

                    ->where('t1.order_id', $this->_get->toString('uuid'))

                    ->limit(1)->findAll()->getDataIndex(0);

                $this->set('arr', $arr);

                $this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));
            }

            if ($this->_post->check('uuid')) {

                // Validate data

                $b_send = ($this->_post->check('b_send') && pjValidation::pjActionEmail($this->_post->toString('b_email')));

                $s_send = ($this->_post->check('s_send') && pjValidation::pjActionEmail($this->_post->toString('b_email')));

                if (! $b_send && ! $s_send) {

                    pjAppController::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'Email(s) not selected.']);
                }

                // Build message

                $arr = pjInvoiceModel::factory()->where('t1.uuid', $this->_post->toString('id'))->where('t1.order_id', $this->_post->toString('uuid'))->limit(1)->findAll()->getDataIndex(0);

                if ($arr === false || empty($arr)) {

                    pjAppController::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Invoice not found.']);
                }

                $arr['items'] = pjInvoiceItemModel::factory()->where('t1.invoice_id', $arr['id'])->findAll()->getData();

                foreach ($arr['items'] as $k => $v) {

                    if ($v['name'] == "Insurance" && $v['unit_price'] == 0 && $v['amount'] == 0) {

                        unset($arr['items'][$k]);
                    } elseif ($v['name'] == "Verzekering" && $v['unit_price'] == 0 && $v['amount'] == 0) {

                        unset($arr['items'][$k]);
                    }
                }

                $confi_arr = pjInvoiceConfigModel::factory()->find(1)->getData();

                $arr['y_logo'] = '<img src="' . PJ_INSTALL_URL . $confi_arr['y_logo'] . '" />';

                $arr['o_use_qty_unit_price'] = $confi_arr['o_use_qty_unit_price'];

                $view_url = PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&id=' . $_REQUEST['id'] . '&uuid=' . $_REQUEST['uuid'];

                $view_url = '<a href="' . $view_url . '">' . $view_url . '</a>';

                // Send message

                $Email = self::getMailer($this->option_arr);
                if (isset($option_arr['o_sender_email']) && ! empty($option_arr['o_sender_email'])) {
                    $Email->setFrom($option_arr['o_sender_email'], $option_arr['o_sender_name']);
                }

                $message = '';

                if ($arr['status'] == 'not_paid') {

                    $message .= '<p>' . __('plugin_invoice_i_send_invoice_link', true) . '</p>';

                    $message .= $view_url . '<br/><br/><br/><br/>';
                }

                $message .= $this->pjActionTokenizer($arr);

                // echo "<pre>"; print_r($message); die;

                $result =
                    $Email
                    ->setTo($this->_post->toString('b_email'))
                    ->setFrom($this->option_arr['o_sender_email'], $this->option_arr['o_sender_name'])
                    ->setSubject(__('plugin_invoice_send_subject', true))
                    ->send($message);

                if ($result) {

                    pjAppController::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.']);
                } else {

                    pjAppController::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Email has not been sent.']);
                }
            }
        }
    }

    private function pjActionTokenizer($a, $locale_id = null)
    {
       
        if (is_null($locale_id)) {

            $locale_id = ! empty($a['locale_id']) ? $a['locale_id'] : $this->getLocaleID();
        }
        $order_arr = pjOrderModel::factory()->where('t1.uuid', $a['order_id'])
            ->limit(1)
            ->findAll()->getDataIndex(0);

        // Get order statuses for PaymentStatus token
        $order_statuses = __('order_statuses', true);
        $payment_status = isset($order_arr['status']) && isset($order_statuses[$order_arr['status']])
            ? $order_statuses[$order_arr['status']]
            : '';

        $config = pjInvoiceConfigModel::factory()->getConfigData($locale_id);
        if (empty(trim($config['y_template'] ?? ''))) {
            $config['y_template'] = pjAppController::getDefaultInvoiceYTemplate();
        }

        // Get company country name from config
        $company_country = '';
        if (!empty($config['y_country'])) {
            $country_data = pjMultiLangModel::factory()
                ->where('t1.model', 'pjCountry')
                ->where('t1.foreign_id', $config['y_country'])
                ->where('t1.field', 'name')
                ->where('t1.locale', $locale_id)
                ->limit(1)
                ->findAll()
                ->getDataIndex(0);
            $company_country = !empty($country_data['content']) ? $country_data['content'] : '';
        }

        $items = "";

        $items       = "";
        $grand_total = 0;

        if (isset($a['items']) && is_array($a['items']) && ! empty($a['items'])) {
            $items .= '<table style="width: 100%; border-collapse: collapse">';
            $items .= '<tr>';
            $items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000">'
                . __('plugin_invoice_i_description', true) . '</td>';
            $items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000; text-align: right">'
                . __('plugin_invoice_i_qty', true) . '</td>';
            $items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000; text-align: right">'
                . __('plugin_invoice_i_unit', true) . '</td>';
            $items .= '<td style="border-bottom: solid 1px #000; border-top: solid 1px #000; text-align: right">'
                . __('plugin_invoice_i_amount', true) . '</td>';
            $items .= '</tr>';

            foreach ($a['items'] as $item) {
                $item_total   = $item['amount'];
                $extras_total = 0;

                // Calculate extras total from description
                if (! empty($item['description'])) {
                    if (preg_match_all('/(Extra:\s*[^()]+)\s*\(([^()]+)\)/u', $item['description'], $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $extra_value       = trim($match[2]);
                            $extra_value_clean = str_replace(['€', ' ', '&euro;'], '', $extra_value);
                            $extra_value_clean = str_replace(',', '.', $extra_value_clean);
                            $extras_total += (float) $extra_value_clean;
                        }
                    }
                }

                // Calculate base amount without extras
                $base_amount     = $item_total - $extras_total;
                $base_unit_price = $item['qty'] > 0 ? $base_amount / $item['qty'] : 0;

                // Main item row (base price without extras)
                $items .= '<tr>';
                $items .= sprintf('<td>%s</td>', pjSanitize::html($item['name']));

                // Qty
                $qty = (fmod($item['qty'], 1) !== 0.0 ? number_format($item['qty'], 2, ',', '') : (int) $item['qty']);
                $items .= sprintf('<td style="text-align: right">%s</td>', $qty);

                // Base unit price (without extras)
                $base_unit_price_formatted = pjCurrency::formatPrice($base_unit_price);
                $items .= sprintf('<td style="text-align: right">%s</td>', $base_unit_price_formatted);

                // Base amount (without extras)
                $base_amount_formatted = pjCurrency::formatPrice($base_amount);
                $items .= sprintf('<td style="text-align: right">%s</td>', $base_amount_formatted);

                $items .= '</tr>';

                // Parse description extras and create separate rows for each extra
                if (! empty($item['description'])) {
                    if (preg_match_all('/(Extra:\s*[^()]+)\s*\(([^()]+)\)/u', $item['description'], $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $extra_name  = trim($match[1]);
                            $extra_value = trim($match[2]);

                            // Step 1: remove currency and spaces
                            $value = preg_replace('/[^\d,.\-]/u', '', $extra_value);

                            // Step 2: detect format
                            if (strpos($value, ',') !== false && strpos($value, '.') === false) {
                                // case: European format (comma as decimal, no dot)
                                $unit_price = (float) str_replace(',', '.', $value);
                            } else {
                                // case: already dot-decimal, or mixed with thousands separator
                                // remove thousand-separators like 1,234.56 or 1.234,56
                                $value      = str_replace(',', '', $value);
                                $unit_price = (float) $value;
                            }
                            $unit_price = $unit_price / $qty;
                            // Create a new row for each extra
                            $items .= '<tr>';
                            $items .= sprintf('<td style="padding-left: 20px; font-size: 11px">%s</td>', pjSanitize::html($extra_name));
                            $items .= sprintf('<td style="text-align: right">%s</td>', $qty); // Empty Qty column
                            $items .= sprintf('<td style="text-align: right; font-size: 11px">%s</td>', pjCurrency::formatPrice($unit_price));
                            $items .= sprintf('<td style="text-align: right; font-size: 11px">%s</td>', $extra_value); // Extra amount
                            $items .= '</tr>';
                        }
                    }
                }

                // Add item total to grand total
                $grand_total += $item_total;
            }

            // Total row
            $items .= '<tr>';
            $items .= '<td colspan="3" style="border-top: solid 1px #000; text-align: right; font-weight: bold">' . __('plugin_invoice_i_total', true) . '</td>';
            $items .= sprintf('<td style="border-top: solid 1px #000; text-align: right; font-weight: bold">%s</td>', pjCurrency::formatPrice($grand_total));
            $items .= '</tr>';

            $items .= '</table>';
        }

        $statuses = __('plugin_invoice_statuses', true);

        $_yesno = __('plugin_invoice_yesno', true);

        return str_replace(

            [

                '{uuid}',

                '{order_id}',

                '{issue_date}',

                '{due_date}',

                '{created}',

                '{modified}',

                '{status}',

                '{subtotal}',

                '{voucher}',

                '{tax}',

                '{shipping}',

                '{total}',

                '{paid_deposit}',

                '{amount_due}',

                '{currency}',

                '{notes}',

                '{y_logo}',

                '{y_company}',

                '{y_name}',

                '{y_street_address}',

                '{y_country}',

                '{y_city}',

                '{y_state}',

                '{y_zip}',

                '{y_phone}',

                '{y_fax}',

                '{y_email}',

                '{y_url}',

                '{b_billing_address}',

                '{b_company}',

                '{b_name}',

                '{b_address}',

                '{b_street_address}',

                '{b_country}',

                '{b_city}',

                '{b_state}',

                '{b_zip}',

                '{b_phone}',

                '{b_fax}',

                '{b_email}',

                '{b_url}',

                '{s_shipping_address}',

                '{s_company}',

                '{s_name}',

                '{s_address}',

                '{s_street_address}',

                '{s_country}',

                '{s_city}',

                '{s_state}',

                '{s_zip}',

                '{s_phone}',

                '{s_fax}',

                '{s_email}',

                '{s_url}',

                '{s_date}',

                '{s_terms}',

                '{s_is_shipped}',

                '{items}',

                '{low_vat_print}',

                '{low_vat_amount}',

                '{high_vat_print}',

                '{high_vat_amount}',

                '{pickup_or_delivery_date}',

                '{discount}',

                '{without_code_discount_print}',

                '{shipping}',

                '{delivery_pickup_date}',

                // Email-style token aliases (for compatibility)
                '{OrderID}',
                '{OrderUUID}',
                '{OrderDate}',
                '{ClientName}',
                '{ClientEmail}',
                '{ClientPhone}',
                '{ShippingName}',
                '{ShippingCompany}',
                '{ShippingAddress1}',
                '{ShippingAddress2}',
                '{ShippingCity}',
                '{ShippingState}',
                '{ShippingZip}',
                '{ShippingCountry}',
                '{ShippingPhone}',
                '{ShippingEmail}',
                '{BillingName}',
                '{BillingCompany}',
                '{BillingAddress1}',
                '{BillingAddress2}',
                '{BillingCity}',
                '{BillingState}',
                '{BillingZip}',
                '{BillingCountry}',
                '{BillingPhone}',
                '{BillingEmail}',
                '{Notes}',
                '{Total}',
                '{Discount}',
                '{Voucher}',
                '{Shipping}',
                '{LowVAT}',
                '{HighVAT}',
                '{Price}',
                '{Products}',
                '{CompanyName}',
                '{CompanyLogo}',
                '{CompanyAddress}',
                '{CompanyZip}',
                '{CompanyCity}',
                '{CompanyCountry}',
                '{CompanyState}',
                '{CompanyPhone}',
                '{CompanyEmail}',
                '{CompanyUrl}',
                '{PaymentStatus}',
                '{PickupOrDeliveryDate}',
                '{WithoutCodeDiscount}',

            ],

            [

                $a['uuid'],

                $a['order_id'],

                pjDateTime::formatDate($a['issue_date'], 'Y-m-d', $this->option_arr['o_date_format']),

                pjDateTime::formatDate($a['due_date'], 'Y-m-d', $this->option_arr['o_date_format']),

                ! empty($a['created']) ? date($this->option_arr['o_date_format'] . " H:i:s", strtotime($a['created'])) : null,

                ! empty($a['modified']) ? date($this->option_arr['o_date_format'] . " H:i:s", strtotime($a['modified'])) : null,

                isset($statuses[$a['status']]) ? $statuses[$a['status']] : (isset($a['status']) ? $a['status'] : ''),
                pjCurrency::formatPrice((isset($a['subtotal']) ? $a['subtotal'] : 0) - (isset($a['shipping']) ? $a['shipping'] : 0)),
                pjCurrency::formatPrice(isset($a['discount']) ? $a['discount'] : 0),
                pjCurrency::formatPrice(isset($a['tax']) ? $a['tax'] : 0),
                pjCurrency::formatPrice(isset($a['shipping']) ? $a['shipping'] : 0),
                pjCurrency::formatPrice(isset($a['total']) ? $a['total'] : 0),
                pjCurrency::formatPrice($a['paid_deposit']),
                pjCurrency::formatPrice($a['amount_due']),

                $a['currency'],

                $a['notes'],

                $a['y_logo'],

                $a['y_company'],

                $a['y_name'],

                $a['y_street_address'],

                $a['y_country_title'],

                $a['y_city'],

                $a['y_state'],

                $a['y_zip'],

                $a['y_phone'],

                $a['y_fax'],

                $a['y_email'],

                $a['y_url'],

                $a['b_billing_address'],

                $a['b_company'],

                $a['b_name'],

                $a['b_address'],

                $a['b_street_address'],

                $a['b_country_title'],

                $a['b_city'],

                $a['b_state'],

                $a['b_zip'],

                $a['b_phone'],

                $a['b_fax'],

                $a['b_email'],

                $a['b_url'],

                $a['s_shipping_address'],

                $a['s_company'],

                $a['s_name'],

                $a['s_address'],

                $a['s_street_address'],

                $a['s_country_title'],

                $a['s_city'],

                $a['s_state'],

                $a['s_zip'],

                $a['s_phone'],

                $a['s_fax'],

                $a['s_email'],

                $a['s_url'],

                pjDateTime::formatDate($a['s_date'], 'Y-m-d', $this->option_arr['o_date_format']),

                $a['s_terms'],

                $_yesno[$a['s_is_shipped']],

                $items,

                '',
                '',

                '',
                pjCurrency::formatPrice(isset($a['tax']) ? $a['tax'] : 0),

                isset($order_arr['delivery_pickup_date']) ? $order_arr['delivery_pickup_date'] : '',
                pjCurrency::formatPrice(isset($a['discount']) ? $a['discount'] : 0),

                '',
                pjCurrency::formatPrice(isset($a['shipping']) ? $a['shipping'] : 0),

                isset($a['delivery_pickup_date']) ? $a['delivery_pickup_date'] : '',

                // Email-style token alias values (for compatibility)
                $a['order_id'],
                $a['uuid'],
                pjDateTime::formatDate($a['issue_date'], 'Y-m-d', $this->option_arr['o_date_format']),
                $a['b_name'],
                $a['b_email'],
                $a['b_phone'],
                $a['s_name'],
                $a['s_company'],
                $a['s_street_address'],
                $a['s_address'],
                $a['s_city'],
                $a['s_state'],
                $a['s_zip'],
                $a['s_country_title'],
                $a['s_phone'],
                $a['s_email'],
                $a['b_name'],
                $a['b_company'],
                $a['b_street_address'],
                $a['b_address'],
                $a['b_city'],
                $a['b_state'],
                $a['b_zip'],
                $a['b_country_title'],
                $a['b_phone'],
                $a['b_email'],
                $a['notes'],
                pjCurrency::formatPrice(isset($a['total']) ? $a['total'] : 0),
                '',
                pjCurrency::formatPrice(isset($a['discount']) ? $a['discount'] : 0),
                pjCurrency::formatPrice(isset($a['shipping']) ? $a['shipping'] : 0),
                '',
                pjCurrency::formatPrice(isset($a['tax']) ? $a['tax'] : 0),
                pjCurrency::formatPrice((isset($a['subtotal']) ? $a['subtotal'] : 0) - (isset($a['shipping']) ? $a['shipping'] : 0)),
                $items,
                !empty($config['y_company']) ? $config['y_company'] : $a['y_company'],
                $a['y_logo'],
                !empty($config['y_street_address']) ? $config['y_street_address'] : $a['y_street_address'],
                !empty($config['y_zip']) ? $config['y_zip'] : $a['y_zip'],
                !empty($config['y_city']) ? $config['y_city'] : $a['y_city'],
                !empty($company_country) ? $company_country : $a['y_country_title'],
                !empty($config['y_state']) ? $config['y_state'] : $a['y_state'],
                !empty($config['y_phone']) ? $config['y_phone'] : $a['y_phone'],
                !empty($config['y_email']) ? $config['y_email'] : $a['y_email'],
                !empty($config['y_url']) ? $config['y_url'] : $a['y_url'],
                $payment_status,
                isset($order_arr['delivery_pickup_date']) ? $order_arr['delivery_pickup_date'] : '',
                '',

            ],

            $config['y_template']

        );
    }

    /**
     * Inline grid editor — alleen status-kolom mag bijgewerkt worden.
     * Voorheen accepteerde dit endpoint elke willekeurige kolom, wat een
     * schrijf-vulnerability vormde. Beperkt tot de whitelisted statussen.
     */
    public function pjActionSaveInvoice()
    {
        $this->setAjax(true);

        if ($this->isXHR()) {
            $id     = (int) $this->_get->toInt('id');
            $column = $this->_post->toString('column');
            $value  = $this->_post->toString('value');

            if ($id > 0 && $column === 'status' && in_array($value, ['paid', 'not_paid', 'cancelled'], true)) {
                pjInvoiceModel::factory()
                    ->where('id', $id)
                    ->limit(1)
                    ->modifyAll(['status' => $value, 'modified' => ':NOW()']);
            }
        }

        exit;
    }

    public function pjActionView()
    {
        $this->setLayout('pjActionEmpty');
        //         $invoice_arr = $this->pjActionGenerateInvoice('132');
        //   echo "<pre>"; print_r($invoice_arr); echo "</pre>"; die;

        $arr = pjInvoiceModel::factory()

            ->join('pjMultiLang', sprintf("t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='%u'", $this->getLocaleId()), 'left outer')

            ->join('pjMultiLang', sprintf("t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='%u'", $this->getLocaleId()), 'left outer')

            ->join('pjMultiLang', sprintf("t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='%u'", $this->getLocaleId()), 'left outer')

            ->select("t1.*, t2.content as y_country_title, t3.content as b_country_title, t3.content as s_country_title,

						AES_DECRYPT(t1.cc_type, '" . PJ_SALT . "') AS cc_type,

						AES_DECRYPT(t1.cc_num, '" . PJ_SALT . "') AS cc_num,

						AES_DECRYPT(t1.cc_exp_month, '" . PJ_SALT . "') AS cc_exp_month,

						AES_DECRYPT(t1.cc_exp_year, '" . PJ_SALT . "') AS cc_exp_year,

						AES_DECRYPT(t1.cc_code, '" . PJ_SALT . "') AS cc_code")

            ->where('t1.uuid', @$_REQUEST['id'])

            ->where('t1.order_id', @$_REQUEST['uuid'])

            ->limit(1)

            ->findAll()

            ->getDataIndex(0);
        // echo "<pre>"; print_r($arr); die;

        if ($arr === false || empty($arr)) {

            pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04");
        }

        $arr['items'] = pjInvoiceItemModel::factory()->where('t1.invoice_id', $arr['id'])->findAll()->getData();
        // echo "<pre>"; print_r($arr['items']); die();

        //die(print_r($arr['items']));

        foreach ($arr['items'] as $k => $v) {

            if ($v['name'] == "Insurance" && $v['unit_price'] == 0 && $v['amount'] == 0) {

                unset($arr['items'][$k]);
            } elseif ($v['name'] == "Verzekering" && $v['unit_price'] == 0 && $v['amount'] == 0) {

                unset($arr['items'][$k]);
            }

            // $result = [];

            // if (preg_match_all('/(Extra:\s*[^()]+)\s*\(([^()]+)\)/u', $v['description'], $matches, PREG_SET_ORDER)) {
            //     foreach ($matches as $match) {
            //         $value = trim($match[2]);

            //         // remove common currency symbols (€, $, £) and HTML entities
            //         $value = preg_replace('/[^\d,.\-]/u', '', $value);

            //         $result[] = [
            //             'name'  => trim($match[1]), // e.g. "Extra: 1"
            //             'value' => $value,          // e.g. "20,00"
            //         ];
            //     }
            // }

            // $v['description'] = $result;

        }
        //   echo "<pre>"; print_r($desc); echo "</pre>"; die;

        $locale_id = ! empty($arr['locale_id']) ? $arr['locale_id'] : $this->getLocaleId();

        $confi_arr = pjInvoiceConfigModel::factory()->getConfigData($locale_id);

        $arr['y_logo'] = '<img src="' . PJ_INSTALL_URL . $confi_arr['y_logo'] . '" />';

        $arr['o_use_qty_unit_price'] = $confi_arr['o_use_qty_unit_price'];

        // Bouw retry-payment URL voor onbetaalde facturen — hergebruikt de bestaande
        // retry_payment.php flow (zelfde hash-schema als e-mails en frontend).
        $retry_url = null;
        if ($arr['status'] === 'not_paid' && !empty($arr['order_id'])) {
            $order_row = pjOrderModel::factory()
                ->where('t1.uuid', $arr['order_id'])
                ->limit(1)
                ->findAll()
                ->getDataIndex(0);
            if (!empty($order_row) && !empty($order_row['created'])) {
                $hash = md5($order_row['uuid'] . $order_row['created'] . PJ_SALT);
                $retry_url = PJ_INSTALL_URL . 'retry_payment.php?uuid=' . urlencode($order_row['uuid']) . '&hash=' . $hash;
            }
        }

        $this

            ->set('arr', $arr)

            ->set('config_arr', $confi_arr)

            ->set('retry_url', $retry_url)

            ->set('template', $this->pjActionTokenizer($arr, $locale_id))

            ->resetCss()

            ->resetJs()

            ->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/')

            ->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/')
            ->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')

            ->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))

            ->appendCss('invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
            ->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/')

            ->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/')

        ;
    }
    /**
     * Genereert de ruwe PDF-bytes voor één factuur.
     * Hergebruikt door generatePdf() (download), pjActionDownloadBulk() (ZIP),
     * en attachInvoiceIfConfigured() (mail-bijlage — Pijler 7).
     *
     * @param string $uuid     Factuurnummer (invoice.uuid)
     * @param string $order_id Bestelnummer (invoice.order_id)
     * @return string|false    PDF bytes, of false bij fout
     */
    public function renderInvoicePdf($uuid, $order_id)
    {
        $dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
        $dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();

        require_once $dm->getPath('tcpdf') . 'tcpdf.php';

        $arr = pjInvoiceModel::factory()
            ->join('pjMultiLang', sprintf("t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='%u'", $this->getLocaleId()), 'left outer')
            ->join('pjMultiLang', sprintf("t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='%u'", $this->getLocaleId()), 'left outer')
            ->join('pjMultiLang', sprintf("t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='%u'", $this->getLocaleId()), 'left outer')
            ->select("t1.*,
                    t2.content as y_country_title,
                    t3.content as b_country_title,
                    t4.content as s_country_title,
                    AES_DECRYPT(t1.cc_type, '" . PJ_SALT . "') AS cc_type,
                    AES_DECRYPT(t1.cc_num, '" . PJ_SALT . "') AS cc_num,
                    AES_DECRYPT(t1.cc_exp_month, '" . PJ_SALT . "') AS cc_exp_month,
                    AES_DECRYPT(t1.cc_exp_year, '" . PJ_SALT . "') AS cc_exp_year,
                    AES_DECRYPT(t1.cc_code, '" . PJ_SALT . "') AS cc_code")
            ->where('t1.uuid', $uuid)
            ->where('t1.order_id', $order_id)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (empty($arr)) {
            return false;
        }

        $items = pjInvoiceItemModel::factory()
            ->where('t1.invoice_id', $arr['id'])
            ->findAll()
            ->getData();

        $locale_id = !empty($arr['locale_id']) ? $arr['locale_id'] : $this->getLocaleId();

        // Workwear: orders are quotes — use schema-safe fetch (no shipping_type column on orders).
        $image_map = array();
        if (!empty($arr['order_id'])) {
            $order_row = pjAppController::fetchOrderForInvoicePdf($arr['order_id'], $locale_id);
            if (!empty($order_row)) {
                pjAppController::mergeOrderIntoInvoicePdfData($arr, $order_row);

                // Product-afbeeldingen: voor elk product in deze order zoek de eerste gallery
                // image en koppel die aan de productnaam (uit pjMultiLang voor de juiste locale).
                try {
                    $galleryTable = pjGalleryModel::factory()->getTable();
                    $mlTable      = pjMultiLangModel::factory()->getTable();
                    $stocks = pjOrderStockModel::factory()
                        ->select("t1.product_id,
                            (SELECT g.small_path FROM `{$galleryTable}` g WHERE g.foreign_id = t1.product_id AND g.model = 'pjProduct' ORDER BY g.id ASC LIMIT 1) AS img_path,
                            (SELECT ml.content FROM `{$mlTable}` ml WHERE ml.foreign_id = t1.product_id AND ml.model = 'pjProduct' AND ml.field = 'name' AND ml.locale = " . (int) $locale_id . " ORDER BY ml.id ASC LIMIT 1) AS prod_name")
                        ->where('t1.order_id', $order_row['id'])
                        ->groupBy('t1.product_id')
                        ->findAll()
                        ->getData();
                    foreach ($stocks as $s) {
                        if (!empty($s['img_path']) && !empty($s['prod_name'])) {
                            $image_map[$s['prod_name']] = $s['img_path'];
                        }
                    }
                } catch (Throwable $e) {
                    // image-lookup faalt → ga gewoon door zonder images
                }
            }
        }

        // Koppel image-paden aan items op basis van itemnaam
        foreach ($items as &$it) {
            if (!empty($image_map[$it['name']])) {
                $it['image'] = $image_map[$it['name']];
            }
        }
        unset($it);
        $arr['items'] = $items;

        // Pijler 2: gebruik het nieuwe template-systeem (buildInvoiceFromTemplate).
        // Fallback op legacy tokenizer als die nieuwe helper om enige reden een
        // lege string oplevert (bijv. bij een test-installatie zonder migratie).
        $html = pjAppController::buildInvoiceFromTemplate($arr, $items, $this->option_arr, $locale_id);
        if (empty(trim($html))) {
            $config_arr = pjInvoiceConfigModel::factory()->getConfigData($locale_id);
            if (empty(trim($config_arr['y_template'] ?? ''))) {
                $config_arr['y_template'] = pjAppController::getDefaultInvoiceYTemplate();
            }
            if (!empty($config_arr['y_logo'])) {
                $arr['y_logo'] = '<img src="' . PJ_INSTALL_URL . $config_arr['y_logo'] . '" />';
            }
            $arr['o_use_qty_unit_price'] = $config_arr['o_use_qty_unit_price'];
            $html = $this->pjActionTokenizer($arr, $locale_id);
        }
        if (empty(trim($html))) {
            $html = '<p>' . htmlspecialchars(__('plugin_invoice_menu_invoices', true) ?: 'Invoice') . '</p>';
        }

        // Footer wordt door pjInvoicePdf::Footer() onderaan elke pagina geplaatst.
        $footerHtml = pjAppController::getInvoiceFooterHtml($this->option_arr, $locale_id);

        require_once PJ_INSTALL_PATH . 'app/classes/pjInvoicePdf.class.php';

        $pdf = new pjInvoicePdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setInvoiceFooterHtml($footerHtml);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->setFooterMargin(15);
        $pdf->setFooterFont(array('dejavusans', '', 8));
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 28); // 28mm bottom margin laat ruimte voor footer
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        // 'S' = return as string (bytes)
        return $pdf->Output('invoice_' . $uuid . '.pdf', 'S');
    }

    public function generatePdf()
    {
        $uuid     = $this->_get->toString('id');
        $order_id = $this->_get->toString('uuid');

        $pdfBytes = $this->renderInvoicePdf($uuid, $order_id);

        if ($pdfBytes === false) {
            pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04');
            return;
        }

        if (ob_get_length()) { ob_end_clean(); }

        $filename = pjAppController::getInvoicePdfFilename($order_id, $uuid);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfBytes));
        echo $pdfBytes;
        exit;
    }

    /**
     * Bulk download: genereert een ZIP met alle geselecteerde factuur-PDF's.
     * Wordt aangeroepen via de datagrid "Kies een actie" dropdown.
     * POST param: record[] = lijst van invoice.id waarden.
     */
    public function pjActionDownloadBulk()
    {
        $this->checkLogin();

        if (!$this->isInvoiceReady()) {
            $this->set('status', 2);
            return;
        }

        $ids = $this->_post->toArray('record');
        if (empty($ids)) {
            pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionInvoices');
            return;
        }

        $invoices = pjInvoiceModel::factory()
            ->select('t1.id, t1.uuid, t1.order_id')
            ->whereIn('t1.id', array_map('intval', $ids))
            ->findAll()
            ->getData();

        if (empty($invoices)) {
            pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04');
            return;
        }

        $tmpZip = tempnam(sys_get_temp_dir(), 'wjinv_');
        $zip = new ZipArchive();
        if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tmpZip);
            pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04');
            return;
        }

        $count = 0;
        foreach ($invoices as $inv) {
            $bytes = $this->renderInvoicePdf($inv['uuid'], $inv['order_id']);
            if ($bytes !== false) {
                // Sanitize filename — ook "2025--01361" mag in een ZIP-bestandsnaam
                $zip->addFromString(pjAppController::getInvoicePdfFilename($inv['order_id'], $inv['uuid']), $bytes);
                $count++;
            }
        }
        $zip->close();

        if ($count === 0) {
            @unlink($tmpZip);
            pjUtil::redirect(PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04');
            return;
        }

        if (ob_get_length()) { ob_end_clean(); }

        $filename = 'facturen_' . date('Ymd_His') . '.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmpZip));
        readfile($tmpZip);
        @unlink($tmpZip);
        exit;
    }
}
