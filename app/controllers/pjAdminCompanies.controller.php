<?php
if (! defined("ROOT_PATH")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjAdminCompanies extends pjAdmin
{
    public function pjActionDeleteCompany()
    {
        $this->setAjax(true);

        if (! $this->isXHR()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
        }
        if (! self::isPost()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
        }
        if (! pjAuth::factory()->hasAccess()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
        }
        if (! ($this->_get->toInt('id'))) {
            self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.']);
        }
        $data               = [];
        $data['is_deleted'] = 1;

        if (! pjCompanyModel::factory()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll(array_merge($this->_post->raw(), $data))) {
            self::jsonResponse(['status' => 'ERR', 'code' => 105, 'text' => 'Company has not been deleted.']);
        } else {
            $company_session_arr = $_SESSION[$this->defaultCompany];
            if (! empty($company_session_arr) && $company_session_arr['id'] == $this->_get->toInt('id')) {
                $companies_arr = pjCompanyModel::factory()->where('is_deleted', 0)->where('status', 'T')->findAll()->getData();

                if (! empty($companies_arr)) {
                    $_SESSION[$this->defaultCompany] = $companies_arr[0];
                    self::jsonResponse(['status' => 'OK', 'code' => 201, 'text' => 'Company has been deleted']);
                }
            }
        }

        // pjMultiLangModel::factory()->where('model', 'pjCompany')->where('foreign_id', $this->_get->toInt('id'))->eraseAll();
        self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Company has been deleted']);
        exit;
    }

    public function pjActionDeleteCompanyBulk()
    {
        $this->setAjax(true);
        if (! $this->isXHR()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
        }
        if (! self::isPost()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
        }
        if (! pjAuth::factory()->hasAccess()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
        }
        if (! $this->_post->has('record')) {
            self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.']);
        }
        $record = $this->_post->toArray('record');
        if (empty($record)) {
            self::jsonResponse(['status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.']);
        }
        // pjMultiLangModel::factory()->where('model', 'pjCompany')->whereIn('foreign_id', $record)->eraseAll();
        $data                = [];
        $data['is_deleted']  = 1;
        $company_session_arr = $_SESSION[$this->defaultCompany];

        pjCompanyModel::factory()->whereIn('id', $record)->limit(1)->modifyAll(array_merge($this->_post->raw(), $data));
        if (in_array($company_session_arr['id'], $record)) {

            if (! empty($company_session_arr)) {
                $companies_arr = pjCompanyModel::factory()->where('is_deleted', 0)->where('status', 'T')->findAll()->getData();

                if (! empty($companies_arr)) {
                    $_SESSION[$this->defaultCompany] = $companies_arr[0];
                    self::jsonResponse(['status' => 'OK', 'code' => 201, 'text' => 'Company has been deleted']);
                }
            }
        }

        self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Companies has been deleted.']);
        exit;
    }

    public function pjActionGetCompany()
    {
        $this->setAjax(true);

        if ($this->isXHR()) {
            $pjCompanyModel = pjCompanyModel::factory()->where('is_deleted', 0);
            if ($q = $this->_get->toString('q')) {
                $pjCompanyModel->where("(t1.name LIKE '%$q%')");
            }
            if ($this->_get->toString('status')) {
                $status = $this->_get->toString('status');
                if (in_array($status, ['T', 'F'])) {
                    $pjCompanyModel->where('t1.status', $status);
                }
            }
            $column    = 'name';
            $direction = 'ASC';
            if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), ['ASC', 'DESC'])) {
                $column    = $this->_get->toString('column');
                $direction = strtoupper($this->_get->toString('direction'));
            }
            $total    = $pjCompanyModel->findCount()->getData();
            $rowCount = $this->_get->toInt('rowCount') ?: 10;
            $pages    = ceil($total / $rowCount);
            $page     = $this->_get->toInt('page') ?: 1;
            $offset   = ((int) $page - 1) * $rowCount;
            if ($page > $pages) {
                $page = $pages;
            }
            $company_arr = $pjCompanyModel
                ->select("t1.*, t1.name")
                ->orderBy("`$column` $direction")
                ->limit($rowCount, $offset)
                ->findAll()
                ->getData();
            $data = [];
            foreach ($company_arr as $k => $v) {
                $v['name'] = pjSanitize::clean($v['name']);
                $data[$k]  = $v;
            }
            pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
        }
        exit;
    }

    public function pjActionIndex()
    {
        $this->checkLogin();
        if (! pjAuth::factory()->hasAccess()) {
            $this->sendForbidden();
            return;
        }
        $this->setLocalesData();

        $this->set('has_create', pjAuth::factory('pjAdminCompanies', 'pjActionCreateForm')->hasAccess());
        $this->set('has_update', pjAuth::factory('pjAdminCompanies', 'pjActionUpdateForm')->hasAccess());
        $this->set('has_delete', pjAuth::factory('pjAdminCompanies', 'pjActionDeleteCompany')->hasAccess());
        $this->set('has_delete_bulk', pjAuth::factory('pjAdminCompanies', 'pjActionDeleteCompanyBulk')->hasAccess());

        $this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs('pjAdminCompanies.js');
    }

    // public function pjActionCreate()
    // {
    //     $this->setAjax(true);
    //     if (! pjAuth::factory()->hasAccess()) {
    //         self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
    //     }
    //     if (! $this->isXHR()) {
    //         self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
    //     }
    //     if (! self::isPost()) {
    //         self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
    //     }
    //     if (! $this->_post->toInt('company_create')) {
    //         self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.']);
    //     }
    //     $data           = [];
    //     $data['status'] = $this->_post->check('status') ? 'T' : 'F';
    //     $id             = pjCompanyModel::factory()->setAttributes(array_merge($this->_post->raw(), $data))->insert()->getInsertId();
    //     if ($id !== false && (int) $id > 0) {

    //         self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Company has been added!']);
    //     } else {
    //         self::jsonResponse(['status' => 'ERR', 'code' => 104, 'text' => 'Company could not be added!']);
    //     }
    // }
    public function pjActionCreate()
    {
        $this->setAjax(true);
        if (!pjAuth::factory()->hasAccess()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
        }
        if (!$this->isXHR()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
        }
        if (!self::isPost()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
        }
        if (!$this->_post->toInt('company_create')) {
            self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.']);
        }

        $data = [];
        $data['status'] = $this->_post->check('status') ? 'T' : 'F';

        $id = pjCompanyModel::factory()
            ->setAttributes(array_merge($this->_post->raw(), $data))
            ->insert()
            ->getInsertId();

        if ($id !== false && (int) $id > 0) {

            $id = (int) $id;
            $optionModel = pjOptionModel::factory();
            $option_table = $optionModel->getTable();


            $option_sql = "
                    INSERT INTO `$option_table`
                    (`foreign_id`, `company_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`)
                    SELECT {$id} AS `foreign_id`, {$id} AS `company_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`
                    FROM `$option_table`
                    WHERE `company_id` = 1
                    ";

            $optionModel->reset()->prepare($option_sql)->exec();

            $notificationModel = pjNotificationModel::factory();
            $notification_table = $notificationModel->getTable();
            $notification_sql = "
                INSERT INTO `$notification_table` 
                (`company_id`, `recipient`, `transport`, `variant`, `is_active`)
                SELECT {$id} AS `company_id`, `recipient`, `transport`, `variant`, `is_active`
                FROM `$notification_table`
                WHERE `company_id` = 1
            ";
            $notificationModel->reset()->prepare($notification_sql)->exec();

            $paymentOptionModel = pjPaymentOptionModel::factory();
            $payment_option_table = $paymentOptionModel->getTable();

            $payment_option_sql = "
                INSERT INTO `$payment_option_table`
                (
                    `company_id`, 
                    `foreign_id`,
                    `payment_method`,
                    `merchant_id`,
                    `merchant_email`,
                    `public_key`,
                    `private_key`,
                    `tz`,
                    `success_url`,
                    `failure_url`,
                    `description`,
                    `is_active`,
                    `is_hold_on`,
                    `is_test_mode`,
                    `test_merchant_id`,
                    `test_merchant_email`,
                    `test_public_key`,
                    `test_private_key`,
                    `test_tz`,
                    `type`
                )
                SELECT 
                    {$id} AS `company_id`,
                    `foreign_id`,
                    `payment_method`,
                    `merchant_id`,
                    `merchant_email`,
                    `public_key`,
                    `private_key`,
                    `tz`,
                    `success_url`,
                    `failure_url`,
                    `description`,
                    `is_active`,
                    `is_hold_on`,
                    `is_test_mode`,
                    `test_merchant_id`,
                    `test_merchant_email`,
                    `test_public_key`,
                    `test_private_key`,
                    `test_tz`,
                    `type`
                FROM `$payment_option_table`
                WHERE `company_id` = 1
            ";

            $paymentOptionModel->reset()->prepare($payment_option_sql)->exec();





            self::jsonResponse([
                'status' => 'OK',
                'code'   => 200,
                'text'   => 'Company added and options successfully cloned!'
            ]);
        }

        self::jsonResponse([
            'status' => 'ERR',
            'code'   => 104,
            'text'   => 'Company could not be added!'
        ]);
    }

    public function pjActionSetCompanyAjax()
    {
        // ini_set('display_errors', '1');
        // ini_set('display_startup_errors', '1');
        // error_reporting(E_ALL);
        $this->setAjax(true);
        if (! pjAuth::factory()->hasAccess()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
        }
        $session_company = $_SESSION[$this->defaultCompany];
        if (! empty($session_company)) {
            $companies_arr = pjCompanyModel::factory()->find($session_company['id'])->getData();

            if ($companies_arr['status'] == 'F') {
                self::jsonResponse(['status' => 'ERR', 'code' => 104, 'text' => 'Company could not be set!']);
            } else {
                self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Company already set!']);
            }
        }
    }
    public function pjActionSetCompany()
    {
        $this->setAjax(true);

        if (!pjAuth::factory()->hasAccess()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
        }

        $companyId = $this->_get->toInt('id');
        $returnUrl = $this->_get->toString('return_url');

        $company = pjCompanyModel::factory()->find($companyId)->getData();

        if (!empty($company)) {

            // Save selected company in session
            $_SESSION[$this->defaultCompany] = $company;

            // Redirect to the original page where user was
            if (!empty($returnUrl)) {
                pjUtil::redirect($returnUrl);
            }

            // fallback if no return URL
            pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
        }

        self::jsonResponse([
            'status' => 'ERR',
            'code'   => 104,
            'text'   => 'Company could not be selected!'
        ]);
    }


    public function pjActionUpdate()
    {
        $this->setAjax(true);
        if (! pjAuth::factory()->hasAccess()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
        }
        if (! $this->isXHR()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
        }
        if (! self::isPost()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
        }
        if (! $this->_post->toInt('company_update')) {
            self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.']);
        }
        if (! $this->_post->toInt('id')) {
            self::jsonResponse(['status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.']);
        }
        $data           = [];
        $data['status'] = $this->_post->check('status') ? 'T' : 'F';
        pjCompanyModel::factory()->where('id', $this->_post->toInt('id'))->limit(1)->modifyAll(array_merge($this->_post->raw(), $data));
        if ($i18n_arr = $this->_post->toArray('i18n')) {
            // pjMultiLangModel::factory()->updateMultiLang($i18n_arr, $this->_post->toInt('id'), 'pjCompany', 'data');
        }
        self::jsonResponse(['status' => 'OK', 'code' => 200, 'text' => 'Company has been updated!']);
    }

    public function pjActionSaveCompany()
    {
        $this->setAjax(true);

        if (! $this->isXHR()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
        }

        if (! self::isPost()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
        }

        if (! pjAuth::factory($this->_get->toString('controller'), 'pjActionUpdate')->hasAccess()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Access denied.']);
        }
        $pjCompanyModel = pjCompanyModel::factory();
        $arr            = $pjCompanyModel->find($this->_get->toInt('id'))->getData();
        if (! $arr) {
            self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Company not found.']);
        } else {
            $company_session_arr = $_SESSION[$this->defaultCompany];
            if (! empty($company_session_arr) && $company_session_arr['id'] == $this->_get->toInt('id')) {
                $companies_arr = pjCompanyModel::factory()->where('is_deleted', 0)->where('status', 'T')->findAll()->getData();

                if (! empty($companies_arr)) {
                    $_SESSION[$this->defaultCompany] = $companies_arr[0];
                }
            }
            $pjCompanyModel->reset()->where('id', $this->_get->toInt('id'))->limit(1)->modifyAll([$this->_post->toString('column') => $this->_post->toString('value')]);
        }

        self::jsonResponse(['status' => 'OK', 'code' => 201, 'text' => 'Company has been updated.']);

        exit;
    }

    public function pjActionCreateForm()
    {
        $this->setAjax(true);

        if (! $this->isXHR()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
        }
        if (! self::isGet()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
        }
        $this->setLocalesData();
    }

    public function pjActionUpdateForm()
    {
        $this->setAjax(true);

        if (! $this->isXHR()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.']);
        }
        if (! self::isGet()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.']);
        }
        if ($this->_get->toInt('id')) {
            $id  = $this->_get->toInt('id');
            $arr = pjCompanyModel::factory()->find($id)->getData();
            if (count($arr) === 0) {
                self::jsonResponse(['status' => 'ERR', 'code' => 103, 'text' => 'Company is not found.']);
            }
            $this->set('arr', $arr);

            $this->setLocalesData();
        } else {
            self::jsonResponse(['status' => 'ERR', 'code' => 102, 'text' => 'Missing parameters.']);
        }
    }
}
