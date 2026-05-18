<?php
if (! defined("ROOT_PATH")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjJabbApi extends pjAppController
{

    private function writeLog($message)
    {
        $file = PJ_INSTALL_PATH . 'import_debug_api.txt';
        $date = date("Y-m-d H:i:s");

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        file_put_contents($file, "[" . $date . "] " . $message . PHP_EOL, FILE_APPEND);
    }
    protected function initializePermissions()
    {
        $pair                      = [];
        $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();
        $cnt                       = $pjAuthUserPermissionModel->where("t1.user_id", $this->getUserId())->findCount()->getData();
        if ($cnt > 0) {
            $pair = $pjAuthUserPermissionModel
                ->reset()
                ->select("t1.*, t2.`key`")
                ->join('pjAuthPermission', 't2.id=t1.permission_id', 'left')
                ->where("t1.user_id", $this->getUserId())
                ->findAll()->getDataPair(null, 'key');
        } else {
            $pair = pjAuthRolePermissionModel::factory()
                ->select("t1.*, t2.`key`")
                ->join('pjAuthPermission', 't2.id=t1.permission_id', 'left')
                ->where("t1.role_id", $this->getRoleId())
                ->findAll()->getDataPair(null, 'key');
        }
        $this->session->setData($this->defaultPermissions, $pair);
    }

    public function pjActionLogin()
    {
        header("Content-Type: application/json");

        if (!self::isPost()) {
            pjAppController::jsonResponse([
                'status' => 'ERR',
                'code'   => 405,
                'message' => 'POST request required'
            ]);
        }

        $email      = trim($this->_post->toString('email'));
        $password   = $this->_post->toString('password');
        $locale_id  = $this->getLocaleId();

        // ---------------- VALIDATION ----------------
        if (!pjValidation::pjActionNotEmpty($email) || !pjValidation::pjActionEmail($email)) {
            pjAppController::jsonResponse([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'Invalid email'
            ]);
        }

        if (!pjValidation::pjActionNotEmpty($password)) {
            pjAppController::jsonResponse([
                'status'  => 'ERR',
                'code'    => 401,
                'message' => 'Password required'
            ]);
        }

        // ---------------- STEP 1 : FIND CLIENT ----------------
        $client = pjClientModel::factory()
            ->where('t1.email', $email)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($client)) {
            pjAppController::jsonResponse([
                'status'  => 'ERR',
                'code'    => 402,
                'message' => 'User not found'
            ]);
        }

        $client = $client[0];

        // ---------------- STEP 2 : PASSWORD CHECK ----------------
        if ($client['password'] != $password) {
            pjAppController::jsonResponse([
                'status'  => 'ERR',
                'code'    => 403,
                'message' => 'Invalid password'
            ]);
        }

        // ---------------- STATUS CHECK ----------------
        if ($client['status'] != 'T') {
            pjAppController::jsonResponse([
                'status'  => 'ERR',
                'code'    => 404,
                'message' => 'Account disabled'
            ]);
        }

        // ---------------- STEP 3 : LOAD FULL CLIENT DETAILS ----------------
        $client_details = pjClientModel::factory()
            ->join('pjAddress', 't2.client_id = t1.id', 'left')
            ->join('pjBaseCountry', 't3.id = t2.country_id', 'left')
            ->join(
                'pjBaseMultiLang',
                "t4.model='pjBaseCountry' AND t4.foreign_id=t3.id AND t4.field='name' AND t4.locale='$locale_id'",
                'left'
            )
            ->join('pjCompany', 't5.id = t1.company_id', 'left')
            ->where('t1.id', $client['id'])
            ->orderBy('t2.is_default_shipping DESC, t2.id ASC')
            ->select("
            t1.*,
            t2.id AS address_id,
            t2.country_id,
            t4.content AS country_name,
            t2.state,
            t2.city,
            t2.zip,
            t2.address_1,
            t2.address_2,
            t2.name AS address_name,
            t2.is_default_shipping,
            t2.is_default_billing,
            t5.name AS company_name
        ")
            ->limit(1)
            ->findAll()
            ->getData();

        $client_details = $client_details[0];

        // ---------------- TOKEN GENERATE ----------------
        $api_login_token = bin2hex(random_bytes(32));
        $current_login   = date("Y-m-d H:i:s");

        pjClientModel::factory()
            ->reset()
            ->set('id', $client['id'])
            ->modify([
                'api_login_token' => $api_login_token,
                'current_login'   => $current_login
            ]);

        unset($client_details['password']);

        // ---------------- SUCCESS RESPONSE ----------------
        pjAppController::jsonResponse([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Login successful',
            'client'  => $client_details,
            'data'    => [
                'id'              => $client['id'],
                'email'           => $client['email'],
                'company_id'      => $client['company_id'],
                'api_login_token' => $api_login_token,
                'current_login'   => $current_login
            ]
        ]);
    }
    public function pjActionRegister()
    {
        header("Content-Type: application/json");

        // Read JSON or Form POST
        $raw_input = file_get_contents("php://input");
        $input     = json_decode($raw_input, true);

        $client_name = isset($input['client_name']) ? trim($input['client_name']) : trim($this->_post->toString('client_name'));
        $email       = isset($input['email']) ? trim($input['email']) : trim($this->_post->toString('email'));
        $password    = isset($input['password']) ? $input['password'] : $this->_post->toString('password');
        $phone       = isset($input['phone']) ? trim($input['phone']) : trim($this->_post->toString('phone'));

        $company_id = $_SESSION[$this->defaultCompany]['id'] ?? NULL;

        // ---------------- VALIDATION ----------------
        $errors = [];

        if (!pjValidation::pjActionNotEmpty($client_name)) {
            $errors[] = "Name is required";
        }

        if (!pjValidation::pjActionNotEmpty($email)) {
            $errors[] = "Email is required";
        } elseif (!pjValidation::pjActionEmail($email)) {
            $errors[] = "Invalid email format";
        }

        if (!pjValidation::pjActionNotEmpty($password)) {
            $errors[] = "Password is required";
        }

        if (!empty($errors)) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'Validation failed',
                'errors'  => $errors
            ]);
            exit;
        }

        // ---------------- CHECK DUPLICATE ----------------
        $pjClientModel = pjClientModel::factory();

        if ($pjClientModel->where('t1.email', $email)->findCount()->getData() > 0) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 409,
                'message' => 'Email already registered'
            ]);
            exit;
        }

        // ---------------- INSERT CLIENT ----------------
        $data = [
            'client_name' => $client_name,
            'email'       => $email,
            'password'    =>  $password,
            'phone'      => $phone,
            'company_id' => $company_id,
            'status'     => 'T',
            'created'    => date("Y-m-d H:i:s")
        ];

        $client_id = $pjClientModel
            ->reset()
            ->setAttributes($data)
            ->insert()
            ->getInsertId();

        if (!$client_id) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 500,
                'message' => 'Registration failed'
            ]);
            exit;
        }

        // ---------------- TOKEN ----------------
        $api_login_token = bin2hex(random_bytes(32));
        $current_login   = date("Y-m-d H:i:s");

        $pjClientModel->reset()
            ->setAttributes(['id' => $client_id])
            ->modify([
                'api_login_token' => $api_login_token,
                'current_login'   => $current_login
            ]);

        // ---------------- SEND EMAIL ----------------
        $notificationModel = pjNotificationModel::factory()
            ->where('recipient', 'client')
            ->where('transport', 'email')
            ->where('variant', 'account');

        if ((int)$company_id > 0) {
            $notificationModel->where('t1.company_id', $company_id);
        }

        $notification = $notificationModel
            ->findAll()
            ->getDataIndex(0);

        if ((int)$notification['id'] > 0 && $notification['is_active'] == 1) {

            $resp = pjAppController::pjActionGetSubjectMessage($notification, $this->getLocaleId());
            $lang_message = $resp['lang_message'];
            $lang_subject = $resp['lang_subject'];

            if (count($lang_message) === 1 && count($lang_subject) === 1) {

                $arr = $pjClientModel->reset()->find($client_id)->getData();

                $search  = ['{ClientName}', '{ClientPassword}', '{ClientEmail}', '{ClientPhone}', '{ClientURL}', '{StoreName}'];
                $replace = [
                    $arr['client_name'],
                    $password, // original password
                    $arr['email'],
                    $arr['phone'],
                    $arr['url'],
                    __('lblStoreName', true)
                ];

                $subject_client = str_replace($search, $replace, $lang_subject[0]['content']);
                $message_client = str_replace($search, $replace, $lang_message[0]['content']);
                $message = pjUtil::textToHtml($message_client);

                $Email = self::getMailer($this->option_arr);
                if ($this->option_arr['o_send_email'] == 'flexmail') {

                    $r = $this->sendFlexMail($arr['email'], $subject_client, $message, $this->option_arr);
                } else {

                    $r = $Email

                        ->setTo($arr['email'])

                        ->setSubject($subject_client)

                        ->send($message);
                }

                // $Email->setTo($arr['email'])
                //     ->setSubject($subject_client)
                //     ->send(pjUtil::textToHtml($message_client));
            }
        }

        // ---------------- SUCCESS ----------------
        echo json_encode([
            'status'  => 'OK',
            'code'    => 201,
            'message' => 'Registration successful',
            'data'    => [
                'id'              => $client_id,
                'client_name'     => $client_name,
                'email'           => $email,
                'api_login_token' => $api_login_token,
                'current_login'   => $current_login
            ]
        ]);
        exit;
    }

    public function logout()
    {
        header("Content-Type: application/json");

        $token = $this->_post->toString('token');
        $email = $this->_post->toString('email') ?? $this->_get->toString('email');

        if (! $token || ! $email) {
            echo json_encode([
                'status'  => 'ERROR',
                'code'    => 400,
                'message' => 'Missing token or email',
            ]);
            exit;
        }

        // Check if user with this token exists
        $user = pjAuthUserModel::factory()
            ->where('t1.email', $email)
            ->where('t1.api_login_token', $token)
            ->limit(1)
            ->findAll()
            ->getData();

        if (count($user) === 1) {
            // Invalidate the token (set to NULL or empty string)
            $data                    = [];
            $data['api_login_token'] = 'sdsd';
            pjAuthUserModel::factory()->reset()->setAttributes(['id' => $user[0]['id']])->modify($data);
            echo json_encode([
                'status'  => 'OK',
                'code'    => 200,
                'message' => 'Logout successful',
            ]);
        } else {
            echo json_encode([
                'status'  => 'ERROR',
                'code'    => 401,
                'message' => 'Invalid token or user not found',
            ]);
        }

        exit;
    }
    public function pjActionForgot()
    {
        header("Content-Type: application/json");

        // Read JSON or Form
        $raw_input = file_get_contents("php://input");
        $input     = json_decode($raw_input, true);

        $email = isset($input['email'])
            ? trim($input['email'])
            : trim($this->_post->toString('email'));

        $company_id = $_SESSION[$this->defaultCompany]['id'] ?? NULL;

        // ---------------- IP BLOCK CHECK ----------------
        $is_ip_blocked = pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr);
        if ($is_ip_blocked == true) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 403,
                'message' => 'IP_BLOCKED'
            ]);
            exit;
        }

        // ---------------- VALIDATION ----------------
        if (!pjValidation::pjActionNotEmpty($email) || !pjValidation::pjActionEmail($email)) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 123,
                'message' => __('system_123', true)
            ]);
            exit;
        }

        // ---------------- FIND CLIENT ----------------
        $pjClientModel = pjClientModel::factory()
            ->where('t1.email', $email);

        if ((int)$company_id > 0) {
            $pjClientModel->where('t1.company_id', $company_id);
        }

        $arr = $pjClientModel
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($arr)) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 124,
                'message' => __('system_124', true)
            ]);
            exit;
        }

        $arr = $arr[0];

        // ---------------- EMAIL TEMPLATE ----------------
        $notificationModel = pjNotificationModel::factory()
            ->where('recipient', 'client')
            ->where('transport', 'email')
            ->where('variant', 'forgot');

        if ((int)$company_id > 0) {
            $notificationModel->where('t1.company_id', $company_id);
        }

        $notification = $notificationModel
            ->findAll()
            ->getDataIndex(0);

        if ((int)$notification['id'] > 0 && $notification['is_active'] == 1) {

            $resp = pjAppController::pjActionGetSubjectMessage(
                $notification,
                $this->getLocaleId()
            );

            $lang_message = $resp['lang_message'];
            $lang_subject = $resp['lang_subject'];

            if (count($lang_message) === 1 && count($lang_subject) === 1) {

                // SAME placeholders as old system
                $search  = [
                    '{ClientName}',
                    '{ClientPassword}',
                    '{ClientEmail}',
                    '{ClientPhone}',
                    '{ClientURL}',
                    '{StoreName}'
                ];

                $replace = [
                    $arr['client_name'],
                    $arr['password'], // encrypted value (same as old)
                    $arr['email'],
                    $arr['phone'],
                    $arr['url'],
                    __('lblStoreName', true)
                ];

                $subject_client = str_replace(
                    $search,
                    $replace,
                    $lang_subject[0]['content']
                );

                $message_client = str_replace(
                    $search,
                    $replace,
                    $lang_message[0]['content']
                );

                $Email = self::getMailer($this->option_arr);
                $message_client = pjUtil::textToHtml($message_client);
                // $r = $Email
                //     ->setTo($arr['email'])
                //     ->setSubject($subject_client)
                //     ->send(pjUtil::textToHtml($message_client));
                if ($this->option_arr['o_send_email'] == 'flexmail') {

                    $r = $this->sendFlexMail($arr['email'], $subject_client, $message_client, $this->option_arr);
                } else {

                    $r = $Email

                        ->setTo($arr['email'])

                        ->setSubject($subject_client)

                        ->send($message_client);
                }


                if ($r) {
                    echo json_encode([
                        'status' => 'OK',
                        'code'   => 213,
                        'message' => __('system_213', true)
                    ]);
                    exit;
                }
            }
        }

        // ---------------- FAIL ----------------
        echo json_encode([
            'status' => 'ERR',
            'code'   => 125,
            'message' => __('system_125', true)
        ]);
        exit;
    }


    public function pjActionProduct_old()
    {
        header("Content-Type: application/json");

        $raw  = file_get_contents("php://input");
        $json = json_decode($raw, true);

        $id = isset($json['product_id'])
            ? (int)$json['product_id']
            : (
                $this->_post->check('product_id')
                ? $this->_post->toInt('product_id')
                : $this->_get->toInt('product_id')
            );

        if ($id <= 0) {
            echo json_encode(['status' => 'ERR', 'code' => 400, 'message' => 'Invalid product id']);
            exit;
        }

        if (pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr)) {
            echo json_encode(['status' => 'ERR', 'code' => 403, 'message' => 'IP_BLOCKED']);
            exit;
        }

        $pjGalleryModel = pjGalleryModel::factory();
        $pjStockModel   = pjStockModel::factory();
        $pjAttributeModel = pjAttributeModel::factory();

        /* PRODUCT */

        $product = pjProductModel::factory()
            ->select(sprintf(
                "t1.*,
            t2.content AS name,
            t3.content AS full_desc,
            t4.content AS short_desc,

            (SELECT MIN(price) FROM %2\$s WHERE product_id=t1.id AND (qty>0 OR t1.is_digital='1')) AS price,
            (SELECT MAX(price) FROM %2\$s WHERE product_id=t1.id AND (qty>0 OR t1.is_digital='1')) AS max_price,

            (SELECT CONCAT_WS('~:~',medium_path,large_path)
            FROM %1\$s WHERE foreign_id=t1.id
            ORDER BY sort ASC,id ASC LIMIT 1) AS pic",

                $pjGalleryModel->getTable(),
                $pjStockModel->getTable()
            ))
            ->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
            ->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='full_desc'", 'left')
            ->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t4.field='short_desc'", 'left')
            ->find($id)
            ->getData();

        if (empty($product) || $product['status'] == 2) {
            echo json_encode(['status' => 'ERR', 'code' => 404, 'message' => 'Product not found']);
            exit;
        }

        if (!empty($product['pic'])) {
            list($m, $l) = explode('~:~', $product['pic']);
            $product['pic_medium'] = PJ_INSTALL_URL . $m;
            $product['pic_large'] = PJ_INSTALL_URL . $l;
        }

        /* GALLERY */

        $gallery = $pjGalleryModel
            ->select('small_path,medium_path,large_path,alt')
            ->where('foreign_id', $id)
            ->orderBy('sort ASC')
            ->findAll()->getData();

        foreach ($gallery as &$g) {
            $g['small_path'] = PJ_INSTALL_URL . $g['small_path'];
            $g['medium_path'] = PJ_INSTALL_URL . $g['medium_path'];
            $g['large_path'] = PJ_INSTALL_URL . $g['large_path'];
        }

        /* ATTRIBUTES */

        $attrs = $pjAttributeModel
            ->select('t1.id,t1.parent_id,t2.content AS name')
            ->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
            ->where('t1.product_id', $id)
            ->orderBy('order_group ASC,order_item ASC')
            ->findAll()->getData();

        $attr_arr = [];
        foreach ($attrs as $a) {
            if ($a['parent_id'] == 0) {
                $attr_arr[$a['id']] = $a;
            } else {
                $attr_arr[$a['parent_id']]['child'][] = $a;
            }
        }

        /* STOCK */

        $stock_arr = $pjStockModel
            ->reset()
            ->select('t1.*,t2.small_path')
            ->join('pjGallery', 't2.id=t1.image_id', 'left')
            ->where('t1.product_id', $id)
            ->where('t1.qty > 0')
            ->where('t1.status', 'T')
            ->findAll()->getData();

        $variants = [];
        $sizes = [];

        foreach ($stock_arr as $s) {

            $attrs = pjStockAttributeModel::factory()
                ->where('stock_id', $s['id'])
                ->orderBy('attribute_id ASC')
                ->findAll()
                ->getDataPair('attribute_parent_id', 'attribute_id');

            $variants[] = [
                'stock_id' => $s['id'],
                'sku' => $s['article_number'],
                'price' => (float)$s['price'],
                'qty' => (int)$s['qty'],
                'attributes' => $attrs
            ];

            $size_attr_id = null;
            $color_attr_id = null;

            foreach ($attr_arr as $parent) {

                if (strtolower($parent['name']) == 'size') {
                    $size_attr_id = $parent['id'];
                }

                if (strtolower($parent['name']) == 'color') {
                    $color_attr_id = $parent['id'];
                }
            }

            if ($size_attr_id && $color_attr_id) {

                $size_val = $attrs[$size_attr_id] ?? null;
                $color_val = $attrs[$color_attr_id] ?? null;

                if ($size_val && $color_val) {

                    $size_name = '';
                    $color_name = '';

                    foreach ($attr_arr[$size_attr_id]['child'] as $child) {
                        if ($child['id'] == $size_val) {
                            $size_name = $child['name'];
                        }
                    }

                    foreach ($attr_arr[$color_attr_id]['child'] as $child) {
                        if ($child['id'] == $color_val) {
                            $color_name = $child['name'];
                        }
                    }

                    if (!isset($sizes[$size_val])) {
                        $sizes[$size_val] = [
                            'id' => $size_val,
                            'name' => $size_name,
                            'colors' => []
                        ];
                    }

                    $sizes[$size_val]['colors'][] = [
                        'id' => $color_val,
                        'name' => $color_name,
                        'stock_id' => $s['id'],
                        'sku' => $s['article_number'],
                        'price' => (float)$s['price'],
                        'qty' => (int)$s['qty']
                    ];
                }
            }
        }

        $product_object = [

            'id' => $product['id'],
            'sku' => $product['sku'],
            'name' => $product['name'],
            'short_desc' => $product['short_desc'],
            'full_desc' => $product['full_desc'],

            'pricing' => [
                'min_price' => $product['price'],
                'max_price' => $product['max_price']
            ],

            'images' => [
                'main' => [
                    'medium' => $product['pic_medium'] ?? null,
                    'large' => $product['pic_large'] ?? null
                ],
                'gallery' => $gallery
            ],

            'attributes' => array_values($attr_arr),

            'variants' => $variants,

            'sizes' => array_values($sizes)
        ];

        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'data' => $product_object
        ]);

        exit;
    }

    // public function pjActionProduct()
    // {
    //     header("Content-Type: application/json");

    //     $raw  = file_get_contents("php://input");
    //     $json = json_decode($raw, true);

    //     $id = isset($json['product_id'])
    //         ? (int)$json['product_id']
    //         : (
    //             $this->_post->check('product_id')
    //             ? $this->_post->toInt('product_id')
    //             : $this->_get->toInt('product_id')
    //         );

    //     if ($id <= 0) {
    //         echo json_encode(['status' => 'ERR', 'code' => 400, 'message' => 'Invalid product id']);
    //         exit;
    //     }

    //     if (pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr)) {
    //         echo json_encode(['status' => 'ERR', 'code' => 403, 'message' => 'IP_BLOCKED']);
    //         exit;
    //     }

    //     $pjGalleryModel   = pjGalleryModel::factory();
    //     $pjStockModel     = pjStockModel::factory();
    //     $pjAttributeModel = pjAttributeModel::factory();

    //     $product = pjProductModel::factory()
    //         ->select(sprintf(
    //             "t1.*,
    //         t2.content AS name,
    //         t3.content AS full_desc,
    //         t4.content AS short_desc,
    //         (SELECT MIN(price) FROM %2\$s WHERE product_id=t1.id AND (qty>0 OR t1.is_digital='1')) AS price,
    //         (SELECT MAX(price) FROM %2\$s WHERE product_id=t1.id AND (qty>0 OR t1.is_digital='1')) AS max_price,
    //         (SELECT CONCAT_WS('~:~',medium_path,large_path)
    //         FROM %1\$s WHERE foreign_id=t1.id
    //         ORDER BY sort ASC,id ASC LIMIT 1) AS pic",
    //             $pjGalleryModel->getTable(),
    //             $pjStockModel->getTable()
    //         ))
    //         ->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
    //         ->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='full_desc'", 'left')
    //         ->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t3.field='short_desc'", 'left')
    //         ->find($id)
    //         ->getData();

    //     if (empty($product) || $product['status'] == 2) {
    //         echo json_encode(['status' => 'ERR', 'code' => 404, 'message' => 'Product not found']);
    //         exit;
    //     }

    //     if (!empty($product['pic'])) {
    //         list($m, $l) = explode('~:~', $product['pic']);
    //         $product['pic_medium'] = PJ_INSTALL_URL . $m;
    //         $product['pic_large']  = PJ_INSTALL_URL . $l;
    //     }

    //     $apiResolveDisplayRel = static function (array $row) {
    //         if (!empty($row['default_path']) && is_file(PJ_INSTALL_PATH . $row['default_path'])) {
    //             return $row['default_path'];
    //         }
    //         return !empty($row['large_path']) ? $row['large_path'] : null;
    //     };

    //     $apiStockImagePayload = static function (array $s) use ($apiResolveDisplayRel) {
    //         $small   = isset($s['_g_small']) ? $s['_g_small'] : null;
    //         $medium  = isset($s['_g_medium']) ? $s['_g_medium'] : null;
    //         $large   = isset($s['_g_large']) ? $s['_g_large'] : null;
    //         $default = isset($s['_g_default']) ? $s['_g_default'] : null;
    //         $relDisp = $apiResolveDisplayRel(['default_path' => $default, 'large_path' => $large]);
    //         return [
    //             'small'   => $small ? PJ_INSTALL_URL . $small : null,
    //             'medium'  => $medium ? PJ_INSTALL_URL . $medium : null,
    //             'large'   => $large ? PJ_INSTALL_URL . $large : null,
    //             'display' => $relDisp ? PJ_INSTALL_URL . $relDisp : null,
    //             'alt'     => isset($s['_g_alt']) ? $s['_g_alt'] : '',
    //         ];
    //     };

    //     $gallery = $pjGalleryModel
    //         ->reset()
    //         ->select('small_path,medium_path,large_path,alt')
    //         ->where('foreign_id', $id)
    //         ->orderBy('sort ASC')
    //         ->findAll()
    //         ->getData();

    //     foreach ($gallery as &$g) {
    //         $relDisplay       = $apiResolveDisplayRel($g);
    //         $g['display']     = $relDisplay ? PJ_INSTALL_URL . $relDisplay : null;
    //         $g['small_path']  = !empty($g['small_path']) ? PJ_INSTALL_URL . $g['small_path'] : null;
    //         $g['medium_path'] = !empty($g['medium_path']) ? PJ_INSTALL_URL . $g['medium_path'] : null;
    //         $g['large_path']  = !empty($g['large_path']) ? PJ_INSTALL_URL . $g['large_path'] : null;
    //     }
    //     unset($g);

    //     $attrs = $pjAttributeModel
    //         ->select('t1.id,t1.parent_id,t2.content AS name')
    //         ->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
    //         ->where('t1.product_id', $id)
    //         ->orderBy('order_group ASC,order_item ASC')
    //         ->findAll()->getData();

    //     $attr_arr = [];
    //     foreach ($attrs as $a) {
    //         if ($a['parent_id'] == 0) {
    //             $attr_arr[$a['id']] = $a;
    //         } else {
    //             $attr_arr[$a['parent_id']]['child'][] = $a;
    //         }
    //     }

    //     $stock_arr = $pjStockModel
    //         ->reset()
    //         ->select('t1.*, t2.small_path AS _g_small, t2.medium_path AS _g_medium, t2.large_path AS _g_large, t2.alt AS _g_alt')
    //         ->join('pjGallery', 't2.id=t1.image_id', 'left')
    //         ->where('t1.product_id', $id)
    //         ->where('t1.qty > 0')
    //         ->where('t1.status', 'T')
    //         ->findAll()->getData();

    //     $variants = [];
    //     $sizes    = [];

    //     foreach ($stock_arr as $s) {
    //         $stkAttrs = pjStockAttributeModel::factory()
    //             ->where('stock_id', $s['id'])
    //             ->orderBy('attribute_id ASC')
    //             ->findAll()
    //             ->getDataPair('attribute_parent_id', 'attribute_id');

    //         $variant_images = $apiStockImagePayload($s);

    //         $variants[] = [
    //             'stock_id'   => $s['id'],
    //             'sku'        => $s['article_number'],
    //             'price'      => (float) $s['price'],
    //             'qty'        => (int) $s['qty'],
    //             'attributes' => $stkAttrs,
    //             'images'     => $variant_images,
    //         ];

    //         $size_attr_id  = null;
    //         $color_attr_id = null;
    //         foreach ($attr_arr as $parent) {
    //             if (strtolower($parent['name']) === 'size') {
    //                 $size_attr_id = $parent['id'];
    //             }
    //             if (strtolower($parent['name']) === 'color') {
    //                 $color_attr_id = $parent['id'];
    //             }
    //         }

    //         if ($size_attr_id && $color_attr_id) {
    //             $size_val  = $stkAttrs[$size_attr_id] ?? null;
    //             $color_val = $stkAttrs[$color_attr_id] ?? null;
    //             if ($size_val && $color_val) {
    //                 $size_name  = '';
    //                 $color_name = '';
    //                 foreach ($attr_arr[$size_attr_id]['child'] as $child) {
    //                     if ($child['id'] == $size_val) {
    //                         $size_name = $child['name'];
    //                     }
    //                 }
    //                 foreach ($attr_arr[$color_attr_id]['child'] as $child) {
    //                     if ($child['id'] == $color_val) {
    //                         $color_name = $child['name'];
    //                     }
    //                 }
    //                 if (!isset($sizes[$size_val])) {
    //                     $sizes[$size_val] = [
    //                         'id'     => $size_val,
    //                         'name'   => $size_name,
    //                         'colors' => [],
    //                     ];
    //                 }
    //                 $sizes[$size_val]['colors'][] = [
    //                     'id'       => $color_val,
    //                     'name'     => $color_name,
    //                     'stock_id' => $s['id'],
    //                     'sku'      => $s['article_number'],
    //                     'price'    => (float) $s['price'],
    //                     'qty'      => (int) $s['qty'],
    //                     'images'   => $variant_images,
    //                 ];
    //             }
    //         }
    //     }

    //     $main_display = $product['pic_large'] ?? null;
    //     if (!empty($gallery[0]['display'])) {
    //         $main_display = $gallery[0]['display'];
    //     }

    //     echo json_encode([
    //         'status' => 'OK',
    //         'code'   => 200,
    //         'data'   => [
    //             'id'         => $product['id'],
    //             'sku'        => $product['sku'],
    //             'name'       => $product['name'],
    //             'short_desc' => $product['short_desc'],
    //             'full_desc'  => $product['full_desc'],
    //             'pricing'    => ['min_price' => $product['price'], 'max_price' => $product['max_price']],
    //             'images'     => [
    //                 'main'    => ['medium' => $product['pic_medium'] ?? null, 'large' => $product['pic_large'] ?? null, 'display' => $main_display],
    //                 'gallery' => $gallery,
    //             ],
    //             'attributes' => array_values($attr_arr),
    //             'variants'   => $variants,
    //             'sizes'      => array_values($sizes),
    //         ],
    //     ]);

    //     exit;
    // }

    /**
     * Paste this entire method into your API controller class (replace existing pjActionProduct).
     * Fix applied: short_desc join uses t4.field='short_desc' (not t3).
     */
    public function pjActionProduct()
    {
        header("Content-Type: application/json; charset=utf-8");

        $raw  = file_get_contents("php://input");
        $json = json_decode($raw, true);

        $id = isset($json['product_id'])
            ? (int) $json['product_id']
            : (
                $this->_post->check('product_id')
                ? $this->_post->toInt('product_id')
                : $this->_get->toInt('product_id')
            );

        if ($id <= 0) {
            echo json_encode(['status' => 'ERR', 'code' => 400, 'message' => 'Invalid product id']);
            exit;
        }

        if (pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr)) {
            echo json_encode(['status' => 'ERR', 'code' => 403, 'message' => 'IP_BLOCKED']);
            exit;
        }

        $pjGalleryModel   = pjGalleryModel::factory();
        $pjStockModel     = pjStockModel::factory();
        $pjAttributeModel = pjAttributeModel::factory();

        $product = pjProductModel::factory()
            ->select(sprintf(
                "t1.*,
            t2.content AS name,
            t3.content AS full_desc,
            t4.content AS short_desc,
            (SELECT MIN(price) FROM %2\$s WHERE product_id=t1.id AND (qty>0 OR t1.is_digital='1')) AS price,
            (SELECT MAX(price) FROM %2\$s WHERE product_id=t1.id AND (qty>0 OR t1.is_digital='1')) AS max_price,
            (SELECT CONCAT_WS('~:~',medium_path,large_path)
            FROM %1\$s WHERE foreign_id=t1.id
            ORDER BY sort ASC,id ASC LIMIT 1) AS pic",
                $pjGalleryModel->getTable(),
                $pjStockModel->getTable()
            ))
            ->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
            ->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='full_desc'", 'left')
            ->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t4.field='short_desc'", 'left')
            ->find($id)
            ->getData();

        if (empty($product) || $product['status'] == 2) {
            echo json_encode(['status' => 'ERR', 'code' => 404, 'message' => 'Product not found']);
            exit;
        }

        if (!empty($product['pic'])) {
            list($m, $l) = explode('~:~', $product['pic']);
            $product['pic_medium'] = PJ_INSTALL_URL . $m;
            $product['pic_large']  = PJ_INSTALL_URL . $l;
        }

        $apiResolveDisplayRel = static function (array $row) {
            if (!empty($row['default_path']) && is_file(PJ_INSTALL_PATH . $row['default_path'])) {
                return $row['default_path'];
            }
            return !empty($row['large_path']) ? $row['large_path'] : null;
        };

        $apiStockImagePayload = static function (array $s) use ($apiResolveDisplayRel) {
            $small   = isset($s['_g_small']) ? $s['_g_small'] : null;
            $medium  = isset($s['_g_medium']) ? $s['_g_medium'] : null;
            $large   = isset($s['_g_large']) ? $s['_g_large'] : null;
            $default = isset($s['_g_default']) ? $s['_g_default'] : null;
            $relDisp = $apiResolveDisplayRel(['default_path' => $default, 'large_path' => $large]);
            return [
                'small'   => $small ? PJ_INSTALL_URL . $small : null,
                'medium'  => $medium ? PJ_INSTALL_URL . $medium : null,
                'large'   => $large ? PJ_INSTALL_URL . $large : null,
                'display' => $relDisp ? PJ_INSTALL_URL . $relDisp : null,
                'alt'     => isset($s['_g_alt']) ? $s['_g_alt'] : '',
            ];
        };

        $gallery = $pjGalleryModel
            ->reset()
            ->select('small_path,medium_path,large_path,alt')
            ->where('foreign_id', $id)
            ->orderBy('sort ASC')
            ->findAll()
            ->getData();

        foreach ($gallery as &$g) {
            $relDisplay       = $apiResolveDisplayRel($g);
            $g['display']     = $relDisplay ? PJ_INSTALL_URL . $relDisplay : null;
            $g['small_path']  = !empty($g['small_path']) ? PJ_INSTALL_URL . $g['small_path'] : null;
            $g['medium_path'] = !empty($g['medium_path']) ? PJ_INSTALL_URL . $g['medium_path'] : null;
            $g['large_path']  = !empty($g['large_path']) ? PJ_INSTALL_URL . $g['large_path'] : null;
        }
        unset($g);

        $attrs = $pjAttributeModel
            ->select('t1.id,t1.parent_id,t2.content AS name')
            ->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
            ->where('t1.product_id', $id)
            ->orderBy('order_group ASC,order_item ASC')
            ->findAll()->getData();

        $attr_arr = [];
        foreach ($attrs as $a) {
            $pid = isset($a['parent_id']) ? (int) $a['parent_id'] : 0;
            if ($pid === 0) {
                $attr_arr[$a['id']] = $a;
            } else {
                $attr_arr[$a['parent_id']]['child'][] = $a;
            }
        }

        $size_attr_id  = null;
        $color_attr_id = null;
        foreach ($attr_arr as $parent) {
            $n = isset($parent['name']) ? strtolower(trim($parent['name'])) : '';
            if ($n === 'size') {
                $size_attr_id = (int) $parent['id'];
            } elseif ($n === 'color' || $n === 'colour') {
                $color_attr_id = (int) $parent['id'];
            }
        }

        $stock_arr = $pjStockModel
            ->reset()
            ->select('t1.*, t2.small_path AS _g_small, t2.medium_path AS _g_medium, t2.large_path AS _g_large, t2.alt AS _g_alt')
            ->join('pjGallery', 't2.id=t1.image_id', 'left')
            ->where('t1.product_id', $id)
            ->where('t1.qty > 0')
            ->where('t1.status', 'T')
            ->findAll()->getData();

        $variants = [];
        $sizes    = [];

        foreach ($stock_arr as $s) {
            $stkAttrs = pjStockAttributeModel::factory()
                ->where('stock_id', $s['id'])
                ->orderBy('attribute_id ASC')
                ->findAll()
                ->getDataPair('attribute_parent_id', 'attribute_id');

            $variant_images = $apiStockImagePayload($s);

            $variants[] = [
                'stock_id'   => (int) $s['id'],
                'sku'        => $s['article_number'],
                'price'      => (float) $s['price'],
                'qty'        => (int) $s['qty'],
                'attributes' => $stkAttrs,
                'images'     => $variant_images,
            ];

            if ($size_attr_id && $color_attr_id) {
                $size_val  = isset($stkAttrs[$size_attr_id]) ? $stkAttrs[$size_attr_id] : null;
                $color_val = isset($stkAttrs[$color_attr_id]) ? $stkAttrs[$color_attr_id] : null;
                if ($size_val && $color_val) {
                    $size_name  = '';
                    $color_name = '';
                    if (!empty($attr_arr[$size_attr_id]['child'])) {
                        foreach ($attr_arr[$size_attr_id]['child'] as $child) {
                            if ((string) $child['id'] === (string) $size_val) {
                                $size_name = $child['name'];
                                break;
                            }
                        }
                    }
                    if (!empty($attr_arr[$color_attr_id]['child'])) {
                        foreach ($attr_arr[$color_attr_id]['child'] as $child) {
                            if ((string) $child['id'] === (string) $color_val) {
                                $color_name = $child['name'];
                                break;
                            }
                        }
                    }
                    if (!isset($sizes[$size_val])) {
                        $sizes[$size_val] = [
                            'id'     => (int) $size_val,
                            'name'   => $size_name,
                            'colors' => [],
                        ];
                    }
                    $sizes[$size_val]['colors'][] = [
                        'id'       => (int) $color_val,
                        'name'     => $color_name,
                        'stock_id' => (int) $s['id'],
                        'sku'      => $s['article_number'],
                        'price'    => (float) $s['price'],
                        'qty'      => (int) $s['qty'],
                        'images'   => $variant_images,
                    ];
                }
            }
        }

        $main_display = $product['pic_large'] ?? null;
        if (!empty($gallery[0]['display'])) {
            $main_display = $gallery[0]['display'];
        }

        $payload = [
            'status' => 'OK',
            'code'   => 200,
            'data'   => [
                'id'         => (int) $product['id'],
                'sku'        => $product['sku'],
                'name'       => $product['name'],
                'short_desc' => $product['short_desc'],
                'full_desc'  => $product['full_desc'],
                'pricing'    => [
                    'min_price' => $product['price'] !== null && $product['price'] !== '' ? (float) $product['price'] : null,
                    'max_price' => $product['max_price'] !== null && $product['max_price'] !== '' ? (float) $product['max_price'] : null,
                ],
                'images'     => [
                    'main'    => [
                        'medium'  => $product['pic_medium'] ?? null,
                        'large'   => $product['pic_large'] ?? null,
                        'display' => $main_display,
                    ],
                    'gallery' => $gallery,
                ],
                'attributes' => array_values($attr_arr),
                'variants'   => $variants,
                'sizes'      => array_values($sizes),
            ],
        ];

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    public function pjActionProducts()
    {
        header("Content-Type: application/json");

        $q           = $this->_post->toString('q');
        $category_id = $this->_post->toInt('category_id');
        $page        = $this->_post->toInt('page');
        $sort        = $this->_post->toString('sort');

        // ---------------- IP BLOCK ----------------
        if (pjBase::isBlockedIp(pjUtil::getClientIp(), $this->option_arr)) {
            echo json_encode(['status' => 'ERR', 'code' => 403, 'message' => 'IP_BLOCKED']);
            exit;
        }

        // ---------------- CART QTY MAP ----------------
        $order_arr = [];
        foreach ($this->get('cart_arr') as $c) {
            if ($c['is_cart'] == 0) {

                if (!isset($order_arr[$c['stock_id']])) {
                    $order_arr[$c['stock_id']] = 0;
                }
                $order_arr[$c['stock_id']] += $c['qty'];
            }
        }

        // ---------------- PRODUCT MODEL ----------------
        $pjProductModel = pjProductModel::factory()
            ->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
            ->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='" . $this->getLocaleId() . "' AND t3.field='short_desc'", 'left')
            ->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='" . $this->getLocaleId() . "' AND t4.field='full_desc'", 'left')
            ->where('t1.status !=', 2);

        // ---------------- CATEGORY ----------------
        if ($category_id > 0) {
            $cat = pjCategoryModel::factory()->find($category_id)->getData();
            if ($cat) {
                $ids = pjCategoryModel::factory()
                    ->reset()
                    ->where("lft BETWEEN {$cat['lft']} AND {$cat['rgt']}")
                    ->findAll()->getDataPair(null, 'id');
            } else {
                $ids = [$category_id];
            }

            $pjProductModel->where(sprintf(
                "t1.id IN (SELECT product_id FROM %s WHERE category_id IN (%s))",
                pjProductCategoryModel::factory()->getTable(),
                implode(',', $ids)
            ));
        }

        // ---------------- SEARCH ----------------
        if (!empty($q)) {
            $q = str_replace(['_', '%'], ['\_', '\%'], trim($q));
            $pjProductModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%' OR t4.content LIKE '%$q%')");
        }

        // ---------------- PAGINATION ----------------
        $page = $page > 0 ? $page : 1;
        $row_count = (int)$this->option_arr['o_products_per_page'] ?: 10;
        $offset = ($page - 1) * $row_count;
        /* ---------------- COUNT WITH PRICE CONDITION ---------------- */

        $count = $pjProductModel
            ->select(sprintf(
                "t1.id,
        (SELECT MIN(price) FROM %1\$s WHERE product_id=t1.id AND qty>0 LIMIT 1) AS min_price,
        (SELECT MAX(price) FROM %1\$s WHERE product_id=t1.id AND qty>0 LIMIT 1) AS max_price",
                pjStockModel::factory()->getTable()
            ))
            ->findAll()
            ->getData();

        $count = array_filter($count, function ($p) {
            return ($p['min_price'] > 0 || $p['max_price'] > 0);
        });

        $count = count($count);
        // $count = $pjProductModel->findCount()->getData();
        $pages = ceil($count / $row_count);

        // ---------------- SORT ----------------
        switch ($sort) {
            case 'newest':
                $pjProductModel->orderBy('t1.id DESC');
                break;
            case 'price_asc':
                $pjProductModel->orderBy('IF(t1.is_digital=1,price,min_price) ASC');
                break;
            case 'price_desc':
                $pjProductModel->orderBy('IF(t1.is_digital=1,price,min_price) DESC');
                break;
            case 'name_asc':
                $pjProductModel->orderBy('name ASC');
                break;
            case 'name_desc':
                $pjProductModel->orderBy('name DESC');
                break;
            default:
                $pjProductModel->orderBy('is_featured DESC, name ASC');
        }

        // ---------------- PRODUCTS ----------------
        $products = $pjProductModel
            ->select(sprintf(
                "t1.*, 
             t2.content AS name,
             t3.content AS short_desc,
             t4.content AS full_desc,

             (SELECT price FROM %1\$s WHERE product_id=t1.id AND t1.is_digital=1 LIMIT 1) AS price,
             (SELECT MIN(price) FROM %1\$s WHERE product_id=t1.id AND qty>0 LIMIT 1) AS min_price,
             (SELECT MAX(price) FROM %1\$s WHERE product_id=t1.id AND qty>0 LIMIT 1) AS max_price,
             (SELECT id FROM %1\$s WHERE product_id=t1.id AND (qty>0 OR t1.is_digital=1) ORDER BY price ASC LIMIT 1) AS stockId,
             (SELECT qty FROM %1\$s WHERE id=stockId LIMIT 1) AS stockQty,

             (SELECT GROUP_CONCAT(CONCAT_WS('_',STA.attribute_id,STA.attribute_parent_id,ST.id,ST.qty))
              FROM %2\$s STA
              INNER JOIN %1\$s ST ON ST.id=STA.stock_id
              WHERE STA.product_id=t1.id LIMIT 1) AS stockId_attr,

             (SELECT GROUP_CONCAT(category_id) FROM %3\$s WHERE product_id=t1.id) AS category_ids,

             (SELECT GROUP_CONCAT(CONCAT_WS('.',id,
                IF(type='single',NULL,
                   (SELECT id FROM %5\$s WHERE extra_id=te.id ORDER BY price ASC LIMIT 1)
                )))
              FROM %4\$s te WHERE product_id=t1.id AND is_mandatory='1') AS m_extras",
                pjStockModel::factory()->getTable(),
                pjStockAttributeModel::factory()->getTable(),
                pjProductCategoryModel::factory()->getTable(),
                pjExtraModel::factory()->getTable(),
                pjExtraItemModel::factory()->getTable()
            ))
            ->limit($row_count, $offset)
            ->findAll()
            ->toArray('category_ids', ',')
            ->toArray('m_extras', ',')
            ->getData();

        // ---------------- IMAGES ----------------
        $images_map = [];

        if ($products) {

            $ids = array_column($products, 'id');

            $imgs = pjGalleryModel::factory()
                ->whereIn('foreign_id', $ids)
                ->orderBy('sort ASC')
                ->findAll()
                ->getData();

            foreach ($imgs as $i) {

                $i['small_path']  = PJ_INSTALL_URL . $i['small_path'];
                $i['medium_path'] = PJ_INSTALL_URL . $i['medium_path'];
                $i['large_path']  = PJ_INSTALL_URL . $i['large_path'];
                $i['source_path'] = PJ_INSTALL_URL . $i['source_path'];

                $images_map[$i['foreign_id']][] = $i;
            }
        }

        // echo "<pre>";
        // print_r($images_map);
        // die;

        // ---------------- ATTRIBUTES ----------------
        $product_attr_arr = [];

        if ($products) {
            $ids = array_column($products, 'id');

            $attrs = pjAttributeModel::factory()
                ->select("t1.id,t1.product_id,t1.parent_id,t2.content AS name")
                ->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
                ->whereIn('t1.product_id', $ids)
                ->orderBy('order_group ASC, order_item ASC')
                ->findAll()->getData();

            foreach ($attrs as $a) {
                if ((int)$a['parent_id'] == 0) {
                    $product_attr_arr[$a['product_id']][$a['id']] = $a;
                } else {
                    $product_attr_arr[$a['product_id']][$a['parent_id']]['child'][] = $a;
                }
            }
        }
        /* ---------------- BUILD CLEAN PRODUCT OBJECT ---------------- */

        $final_products = [];

        foreach ($products as $p) {
            if ($p['min_price'] > 0 || $p['max_price'] > 0) {


                // echo "<pre>"; print_r($p); die;
                $product_id = $p['id'];

                // Attach images
                $product_images = $images_map[$product_id] ?? [];

                // Attach attributes
                $product_attributes = isset($product_attr_arr[$product_id])
                    ? array_values($product_attr_arr[$product_id])
                    : [];

                // In cart quantity (based on stock)
                $in_cart_qty = 0;
                if (!empty($p['stockId']) && isset($order_arr[$p['stockId']])) {
                    $in_cart_qty = $order_arr[$p['stockId']];
                }

                $final_products[] = [
                    'id'          => $p['id'],
                    'sku'         => $p['sku'],
                    'status'      => $p['status'],
                    'is_digital'  => $p['is_digital'],
                    'is_featured' => $p['is_featured'],

                    'name'        => $p['name'],
                    'short_desc'  => $p['short_desc'],
                    'full_desc'   => $p['full_desc'],

                    'pricing' => [
                        'price'      => $p['price'],
                        'min_price'  => $p['min_price'],
                        'max_price'  => $p['max_price']
                    ],

                    'stock' => [
                        'stock_id' => $p['stockId'],
                        'qty'      => $p['stockQty']
                    ],

                    'images'     => $product_images,
                    'attributes' => $product_attributes,

                    'in_cart_qty' => $in_cart_qty
                ];
            }
        }
        /* ---------------- RESPONSE ---------------- */

        echo json_encode([
            'status' => 'OK',
            'code'   => 200,
            'data'   => [
                'products'   => $final_products,
                'pagination' => [
                    'page'  => $page,
                    'pages' => $pages,
                    'total' => $count,
                    'limit' => $row_count
                ]
            ]
        ]);

        exit;
    }

    public function pjActionForgotPassword()
    {
        header("Content-Type: application/json");
        $token = $this->_post->toString('token');

        $email = $this->_post->toString('email') ?? $this->_get->toString('email');
        // 1. Validate input
        if (
            empty($email) ||
            ! pjValidation::pjActionNotEmpty($email) ||
            ! pjValidation::pjActionEmail($email)
        ) {
            echo json_encode(['status' => 'ERR', 'code' => 400, 'message' => 'Invalid email']);
            exit;
        }

        // 2. Find user
        $pjAuthUserModel = pjAuthUserModel::factory();
        $user            = $pjAuthUserModel
            ->select('t1.*,t2.role')
            ->join("pjAuthRole", "t1.role_id=t2.id", "left")
            ->where('t1.email', $email)
            // ->where('t1.api_login_token', $token)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);
        if (! $user) {
            echo json_encode(['status' => 'ERR', 'code' => 404, 'message' => 'User not found']);
            exit;
        }

        // 3. Build tokens and reset URL
        $reset_hash = sha1(PJ_SALT . $user['email'] . $user['pswd_modified'] . PJ_SALT);
        $reset_url  = sprintf(
            "%sindex.php?controller=pjBase&action=pjActionReset&email=%s&hash=%s",
            PJ_INSTALL_URL,
            urlencode($user['email']),
            $reset_hash
        );

        $tokens = [
            '{Name}'  => $user['name'],
            '{Email}' => $user['email'],
            '{Phone}' => $user['phone'],
            '{URL}'   => $reset_url,
        ];

        $email_sent = false;
        $sms_sent   = false;

        // 4. Send email to admin (if enabled)
        if ($this->option_arr['o_forgot_contact_admin'] == 'Yes') {
            $master_email = $this->getMasterAdminEmail();
            $subject      = $this->getI18nContent("o_forgot_contact_admin_subject", $this->getLocaleId());
            $message      = $this->getI18nContent("o_forgot_contact_admin_message", $this->getLocaleId());

            if (! empty($subject) && ! empty($message)) {
                $subject = str_ireplace(array_keys($tokens), $tokens, $subject);
                $message = str_ireplace(array_keys($tokens), $tokens, $message);

                $pjEmail = self::getMailer($this->option_arr);
                $pjEmail->setTo($master_email);
                $pjEmail->setSubject(stripslashes($subject));
                $pjEmail->send($message);
            }
        }

        // 5. Send email to user (if enabled)
        if ($this->option_arr['o_forgot_email_confirmation'] == 'Yes') {
            $subject = $this->getI18nContent("o_forgot_email_subject", $this->getLocaleId());
            $message = $this->getI18nContent("o_forgot_email_message", $this->getLocaleId());

            if (! empty($subject) && ! empty($message)) {
                $subject = str_ireplace(array_keys($tokens), $tokens, $subject);
                $message = str_ireplace(array_keys($tokens), $tokens, $message);

                $pjEmail = self::getMailer($this->option_arr);
                $pjEmail->setTo($user['email']);
                $pjEmail->setSubject(stripslashes($subject));

                if ($pjEmail->send($message)) {
                    $email_sent = true;
                }
            }
        }

        // 6. Send SMS (if enabled)
        if ($this->option_arr['o_forgot_sms_confirmation'] == 'Yes') {
            if (! empty($user['phone']) && ! empty($this->option_arr['o_forgot_sms_message'])) {
                $message = $this->getI18nContent("o_forgot_sms_message", $this->getLocaleId());
                $message = str_ireplace(array_keys($tokens), $tokens, $message);

                $sms_params = [
                    'text'   => $message,
                    'number' => $user['phone'],
                    'type'   => 'unicode',
                    'key'    => md5($this->option_arr['private_key'] . PJ_SALT),
                ];
                $response = pjBaseSms::init($sms_params)->pjActionSend();
                $sms_sent = ($response == 1);
            }
        }

        // 7. Final response
        if ($email_sent || $sms_sent) {
            echo json_encode([
                'status'  => 'OK',
                'code'    => 200,
                'message' => 'Password reset instructions sent',
                'data'    => [
                    'email_sent' => $email_sent,
                    'sms_sent'   => $sms_sent,
                    'reset_url'  => $reset_url, // include for debugging, remove in production
                ],
            ]);
        } else {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 500,
                'message' => 'Unable to send reset instructions',
            ]);
        }
        exit;
    }

    public function pjActionChangePassword()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        // Validate input
        if (
            ! isset($params['user_id'], $params['old_password'], $params['new_password'])
            || (int) $params['user_id'] <= 0
            || empty($params['old_password'])
            || empty($params['new_password'])
        ) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 400,
                'text'   => 'Missing or invalid parameters', // Missing parameters
            ]);
            exit;
        }

        // Fetch user
        $user = pjAuthUserModel::factory()->find($params['user_id'])->getData();
        if (! $user) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 404,
                'text'   => 'User not found',
            ]);
            exit;
        }

        // Verify old password
        if ($user['password'] !== $params['old_password']) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 401,
                'text'   => 'Old password does not match',
            ]);
            exit;
        }

        // Update new password
        $update_data = [
            'password'      => $params['new_password'],
            'pswd_modified' => ':NOW()',
            'ip'            => pjUtil::getClientIp(),
        ];

        pjAuthUserModel::factory()->set('id', $params['user_id'])->modify($update_data);

        echo json_encode([
            'status' => 'OK',
            'code'   => 200,
            'text'   => 'Password has been updated.', // Successfully updated
        ]);
        exit;
    }

    public function pjActionGetCountries()
    {
        header("Content-Type: application/json");

        $params    = $this->_post->raw();
        $countries = pjBaseCountryModel::factory()
            ->select('t1.*, t2.content AS name')
            ->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId() . "'", 'left outer')
            ->where('t1.status', 'T')
            ->orderBy('`name` ASC')->findAll()->getData();
        echo json_encode([
            'status' => 'OK',
            'code'   => 200,
            'data'   => $countries,
            // 'text'   => __('plugin_base_error_bodies_ARRAY_PU01', true, true), // Successfully updated
        ]);
        exit;
    }

    public function pjActionGetCategory()
    {
        header("Content-Type: application/json");

        $params = $this->_get->raw();

        // &#128313; Pagination
        $rowCount = isset($params['rowCount']) ? (int)$params['rowCount'] : 50;
        $page     = isset($params['page']) ? (int)$params['page'] : 1;

        if ($page < 1) $page = 1;
        if ($rowCount < 1) $rowCount = 50;

        $pjCategoryModel = pjCategoryModel::factory();

        // &#128313; Get category tree
        $data  = $pjCategoryModel->getNode($this->getLocaleId(), 1);
        $total = count($data);

        $pages = $total > 0 ? ceil($total / $rowCount) : 1;
        if ($page > $pages) $page = $pages;

        $offset = ($page - 1) * $rowCount;

        // &#128313; Get product count per category
        $c_arr = $pjCategoryModel
            ->reset()
            ->select(sprintf(
                "t1.id,
            (SELECT COUNT(*) 
             FROM `%s`
             WHERE `category_id` = `t1`.`id`
             LIMIT 1) AS `products`",
                pjProductCategoryModel::factory()->getTable()
            ))
            ->findAll()
            ->getDataPair('id', 'products');

        $data = array_slice($data, $offset, $rowCount);

        $formatted = [];
        $stack = [];

        foreach ($data as $k => $category) {

            $cat_id     = (int)$category['data']['id'];
            $parent_id  = (int)$category['data']['parent_id'];
            $level      = (int)$category['deep'];

            $item = [
                'id'        => $cat_id,
                'name'      => $category['data']['name'],
                'parent_id' => $parent_id,
                'level'     => $level,
                'products'  => (int) @$c_arr[$cat_id],
                'up'        => 0,
                'down'      => 0
            ];

            // Tree movement logic (optional for UI arrows)
            if (!isset($stack[$level . "|" . $parent_id])) {
                $stack[$level . "|" . $parent_id] = 0;
            }

            $stack[$level . "|" . $parent_id] += 1;

            if ($stack[$level . "|" . $parent_id] > 1) {
                $item['up'] = 1;
            }

            if (
                isset($data[$k + 1]) &&
                (
                    $data[$k + 1]['deep'] == $level ||
                    $stack[$level . "|" . $parent_id] < $category['siblings']
                )
            ) {
                $item['down'] = 1;
            }

            $formatted[] = $item;
        }

        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Category list fetched successfully.',
            'data'    => $formatted,
            'pagination' => [
                'total'    => $total,
                'pages'    => $pages,
                'page'     => $page,
                'rowCount' => $rowCount
            ]
        ]);

        exit;
    }


    public function pjActionUpdateProfile()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        // &#128313; Validate client_id
        if (!isset($params['client_id']) || empty($params['client_id'])) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'client_id is required.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];

        $pjClientModel = pjClientModel::factory();

        // &#128313; Check client exists
        $client = $pjClientModel
            ->where('id', $client_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($client)) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 404,
                'message' => 'Client not found.'
            ]);
            exit;
        }

        // &#128313; Email duplicate check
        if (isset($params['email']) && !empty($params['email'])) {

            $exists = $pjClientModel
                ->reset()
                ->where('email', $params['email'])
                ->where('id !=', $client_id)
                ->findCount()
                ->getData();

            if ($exists > 0) {
                echo json_encode([
                    'status'  => 'ERR',
                    'code'    => 409,
                    'message' => 'Email already exists.'
                ]);
                exit;
            }
        }

        /* ================= UPDATE CLIENT ================= */

        $update_data = [];

        if (isset($params['client_name'])) $update_data['client_name'] = $params['client_name'];
        if (isset($params['email']))       $update_data['email']       = $params['email'];
        if (isset($params['phone']))       $update_data['phone']       = $params['phone'];

        if (!empty($update_data)) {
            $pjClientModel
                ->reset()
                ->set('id', $client_id)
                ->modify($update_data);
        }

        /* ================= DEFAULT ADDRESS ================= */

        $pjAddressModel = pjAddressModel::factory();

        $defaultAddress = $pjAddressModel
            ->where('client_id', $client_id)
            ->where('is_default_shipping', 1)
            ->limit(1)
            ->findAll()
            ->getData();

        $address_data = [
            'client_id'  => $client_id,
            'country_id' => $params['country_id'] ?? null,
            'state'      => $params['state'] ?? null,
            'city'       => $params['city'] ?? null,
            'zip'        => $params['zip'] ?? null,
            'address_1'  => $params['address_1'] ?? null,
            'address_2'  => $params['address_2'] ?? null,
            'name'       => $params['address_name'] ?? 'Default Address',
            'is_default_shipping' => 1,
            'is_default_billing'  => 1
        ];

        if (!empty($defaultAddress)) {

            $pjAddressModel
                ->reset()
                ->set('id', $defaultAddress[0]['id'])
                ->modify($address_data);
        } else {

            $pjAddressModel
                ->reset()
                ->setAttributes($address_data)
                ->insert();
        }

        /* ================= FETCH UPDATED CLIENT (SAME AS LOGIN) ================= */

        $locale_id = $this->getLocaleId();

        $updated_client = $pjClientModel
            ->reset()
            ->join(
                'pjAddress',
                't2.client_id = t1.id',
                'left'
            )
            ->join(
                'pjBaseCountry',
                't3.id = t2.country_id',
                'left'
            )
            ->join(
                'pjBaseMultiLang',
                "t4.model='pjBaseCountry' AND t4.foreign_id=t3.id AND t4.field='name' AND t4.locale='$locale_id'",
                'left'
            )
            ->join(
                'pjCompany',
                't5.id = t1.company_id',
                'left'
            )
            ->where('t1.id', $client_id)
            ->orderBy('t2.is_default_shipping DESC, t2.id ASC')
            ->select("
            t1.*,
            t2.id AS address_id,
            t1.company_id,
            t2.country_id,
            t4.content AS country_name,
            t2.state,
            t2.city,
            t2.zip,
            t2.address_1,
            t2.address_2,
            t2.name AS address_name,
            t2.is_default_shipping,
            t2.is_default_billing,
            t5.name AS company_name
        ")
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($updated_client)) {
            unset($updated_client[0]['password']);
        }

        /* ================= RESPONSE ================= */

        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Profile updated successfully.',
            'client'  => $updated_client[0] ?? null,
            'data'    => [
                'id'         => $updated_client[0]['id'] ?? null,
                'email'      => $updated_client[0]['email'] ?? null,
                'company_id' => $updated_client[0]['company_id'] ?? null
            ]
        ]);

        exit;
    }
    public function pjActionGetCompany()
    {
        header("Content-Type: application/json");

        $params = $this->_get->raw();

        $pjCompanyModel = pjCompanyModel::factory()->where('is_deleted', 0);

        /* ================= SEARCH ================= */

        if (!empty($params['q'])) {
            $q = pjSanitize::clean($params['q']);
            $pjCompanyModel->where("(t1.name LIKE '%$q%')");
        }

        /* ================= STATUS FILTER ================= */

        if (!empty($params['status']) && in_array($params['status'], ['T', 'F'])) {
            $pjCompanyModel->where('t1.status', $params['status']);
        }

        /* ================= SORTING ================= */

        $column    = !empty($params['column']) ? $params['column'] : 'name';
        $direction = (!empty($params['direction']) && in_array(strtoupper($params['direction']), ['ASC', 'DESC']))
            ? strtoupper($params['direction'])
            : 'ASC';

        /* ================= PAGINATION ================= */

        $rowCount = !empty($params['rowCount']) ? (int)$params['rowCount'] : 10;
        $page     = !empty($params['page']) ? (int)$params['page'] : 1;

        if ($page < 1) $page = 1;
        if ($rowCount < 1) $rowCount = 10;

        $total = $pjCompanyModel->findCount()->getData();
        $pages = $total > 0 ? ceil($total / $rowCount) : 1;

        if ($page > $pages) $page = $pages;

        $offset = ($page - 1) * $rowCount;

        /* ================= FETCH DATA ================= */

        $company_arr = $pjCompanyModel
            ->reset()
            ->where('is_deleted', 0)
            ->select("t1.*")
            ->orderBy("`$column` $direction")
            ->limit($rowCount, $offset)
            ->findAll()
            ->getData();

        $data = [];
        foreach ($company_arr as $k => $v) {
            $data[$k] = [
                'id'     => (int)$v['id'],
                'name'   => pjSanitize::clean($v['name']),
                'status' => $v['status'],
            ];
        }

        echo json_encode([
            'status' => 'OK',
            'code'   => 200,
            'data'   => $data,
            'pagination' => [
                'total'    => $total,
                'pages'    => $pages,
                'page'     => $page,
                'rowCount' => $rowCount
            ],
            'sort' => [
                'column'    => $column,
                'direction' => $direction
            ]
        ]);

        exit;
    }

    public function pjActionAddToCart()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        if (empty($params['client_id']) || empty($params['product_id']) || empty($params['qty'])) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'client_id, product_id and qty are required.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];
        $qty       = (int)$params['qty'];

        if ($qty <= 0) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'Quantity must be greater than 0.'
            ]);
            exit;
        }

        $hash = md5(PJ_SALT . $client_id);

        $post = $params;

        unset($post['client_id'], $post['qty'], $post['action']);
        if (isset($post['extra']) && (empty($post['extra']))) {
            unset($post['extra']);
        }
        $key = serialize($post);

        /* &#128293; CHECK EXISTING ROW DIRECTLY IN DB */
        $existing = pjCartModel::factory()
            ->where('hash', $hash)
            ->where('is_cart', '0')
            ->where('key_data', $key)
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($existing)) {

            // Update existing qty
            $new_qty = $existing[0]['qty'] + $qty;

            pjCartModel::factory()
                ->reset()
                ->where('id', $existing[0]['id'])
                ->limit(1)
                ->modifyAll(['qty' => $new_qty]);
        } else {

            // Insert new
            pjCartModel::factory()
                ->reset()
                ->setAttributes([
                    'hash'       => $hash,
                    'key_data'   => $key,
                    'product_id' => $post['product_id'],
                    'stock_id'   => $post['stock_id'] ?? null,
                    'qty'        => $qty,
                    'is_cart'        => '0'
                ])
                ->insert();
        }

        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Product added to cart successfully.'
        ]);
        exit;
    }

    private function buildCartData_old($cart_arr)
    {
        $arr        = [];
        $extra_arr  = [];
        $attr_arr   = [];
        $stock_arr  = [];
        $tax_arr    = [];
        $image_arr  = [];

        $order_arr  = [
            'subtotal' => 0,
            'tax'      => 0,
            'shipping' => 0,
            'total'    => 0
        ];

        if (empty($cart_arr)) {
            return [
                'arr'       => [],
                'extra_arr' => [],
                'order_arr' => $order_arr,
                'attr_arr'  => [],
                'stock_arr' => [],
                'tax_arr'   => [],
                'image_arr' => []
            ];
        }

        /* ================= COLLECT IDS ================= */

        $product_ids = [];
        $stock_ids   = [];

        foreach ($cart_arr as $item) {

            $product_ids[] = (int)$item['product_id'];

            if (!empty($item['stock_id'])) {
                $stock_ids[] = (int)$item['stock_id'];
            }
        }

        $product_ids = array_unique($product_ids);
        $stock_ids   = array_unique($stock_ids);

        /* ================= LOAD PRODUCTS ================= */

        $product_data = pjProductModel::factory()
            ->whereIn('t1.id', $product_ids)
            ->findAll()
            ->getData();

        $product_map = [];
        foreach ($product_data as $p) {
            $product_map[(int)$p['id']] = $p;
        }

        /* ================= LOAD STOCK ================= */

        if (!empty($stock_ids)) {

            $stock_data = pjStockModel::factory()
                ->whereIn('t1.id', $stock_ids)
                ->findAll()
                ->getData();

            foreach ($stock_data as $s) {
                $stock_arr[(int)$s['id']] = $s;
            }
        }

        /* ================= LOAD IMAGES ================= */

        $gallery_data = pjGalleryModel::factory()
            ->whereIn('foreign_id', $product_ids)
            ->orderBy('sort ASC')
            ->findAll()
            ->getData();

        foreach ($gallery_data as $g) {
            $image_arr[$g['foreign_id']][] = [
                'small_path'  => PJ_INSTALL_URL . $g['small_path'],
                'medium_path' => PJ_INSTALL_URL . $g['medium_path'],
                'large_path'  => PJ_INSTALL_URL . $g['large_path'],
                'alt'         => $g['alt']
            ];
        }

        /* ================= BUILD CART ITEMS ================= */

        foreach ($cart_arr as $item) {

            $product_id = (int)$item['product_id'];
            $stock_id   = (int)$item['stock_id'];
            $qty        = (int)$item['qty'];

            if (!isset($product_map[$product_id])) {
                continue;
            }

            $product = $product_map[$product_id];

            /* ===== FIXED PRICE LOGIC ===== */

            $price = 0;

            if (!empty($stock_id) && isset($stock_arr[$stock_id])) {

                $price = (float)$stock_arr[$stock_id]['price'];
            } else {

                $price = (float)$product['price'];
            }

            $subtotal = $price * $qty;

            $order_arr['subtotal'] += $subtotal;

            /* ===== PRODUCT IMAGES ===== */

            $product_images = $image_arr[$product_id] ?? [];

            /* ===== MAIN IMAGE ===== */

            $main_image = null;

            if (!empty($product_images)) {
                $main_image = $product_images[0];
            }

            $arr[] = [
                'product' => [
                    'id'         => $product['id'],
                    'company_id' => $product['company_id'],
                    'sku'        => $product['sku'],
                    'status'     => $product['status'],
                    'is_digital' => $product['is_digital'],
                    'name'       => $product['name'],
                    'short_desc' => $product['short_desc'] ?? null,
                    'full_desc'  => $product['full_desc'] ?? null,
                    'pic_medium' => $main_image['medium_path'] ?? null,
                    'pic_large'  => $main_image['large_path'] ?? null
                ],
                'gallery'  => $product_images,
                'qty'      => $qty,
                'price'    => $price,
                'subtotal' => $subtotal,
                'hash'     => md5($item['key_data'])
            ];
        }

        /* ================= TAX ================= */

        $tax_percent = isset($this->option_arr['o_tax'])
            ? (float)$this->option_arr['o_tax']
            : 0;

        $order_arr['tax'] = ($order_arr['subtotal'] * $tax_percent) / 100;

        /* ================= SHIPPING ================= */

        $shipping = isset($this->option_arr['o_shipping'])
            ? (float)$this->option_arr['o_shipping']
            : 0;

        $order_arr['shipping'] = $shipping;

        /* ================= TOTAL ================= */

        $order_arr['total'] =
            $order_arr['subtotal'] +
            $order_arr['tax'] +
            $order_arr['shipping'];

        return [
            'arr'       => $arr,
            'extra_arr' => $extra_arr,
            'order_arr' => $order_arr,
            'attr_arr'  => $attr_arr,
            'stock_arr' => $stock_arr,
            'tax_arr'   => $tax_arr,
            'image_arr' => $image_arr
        ];
    }

    private function buildCartData($cart_arr)
    {
        $locale_id = $this->getLocaleId();

        $arr        = [];
        $extra_arr  = [];
        $attr_arr   = [];
        $stock_arr  = [];
        $tax_arr    = [];
        $image_arr  = [];

        $order_arr  = [
            'subtotal' => 0,
            'tax'      => 0,
            'shipping' => 0,
            'total'    => 0
        ];

        if (empty($cart_arr)) {
            return [
                'arr'       => [],
                'extra_arr' => [],
                'order_arr' => $order_arr,
                'attr_arr'  => [],
                'stock_arr' => [],
                'tax_arr'   => [],
                'image_arr' => []
            ];
        }

        /* ================= MERGE DUPLICATE CART ================= */

        $unique = [];

        foreach ($cart_arr as $item) {
            $key = $item['key_data'];

            if (!isset($unique[$key])) {
                $unique[$key] = $item;
            } else {
                $unique[$key]['qty'] += $item['qty'];
            }
        }

        $cart_arr = array_values($unique);

        /* ================= COLLECT IDS ================= */

        $product_ids = [];
        $stock_ids   = [];
        $attr_ids    = [];
        $attr_value_ids = [];

        foreach ($cart_arr as $item) {

            $data = unserialize($item['key_data']);

            $product_ids[] = (int)$data['product_id'];

            if (!empty($data['stock_id'])) {
                $stock_ids[] = (int)$data['stock_id'];
            }

            // &#9989; collect attribute ids
            if (!empty($data['attr'])) {
                foreach ($data['attr'] as $a_id => $v_id) {
                    $attr_ids[] = (int)$a_id;
                    $attr_value_ids[] = (int)$v_id;
                }
            }
        }

        $product_ids = array_unique($product_ids);
        $stock_ids   = array_unique($stock_ids);
        $attr_ids = array_unique($attr_ids);
        $attr_value_ids = array_unique($attr_value_ids);

        /* ================= LOAD PRODUCTS ================= */

        $products = pjProductModel::factory()
            ->select("t1.*,
        t2.content AS name,
        t3.content AS full_desc,
        t4.content AS short_desc")
            ->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='$locale_id' AND t2.field='name'", 'left')
            ->join('pjMultiLang', "t3.model='pjProduct' AND t3.foreign_id=t1.id AND t3.locale='$locale_id' AND t3.field='full_desc'", 'left')
            ->join('pjMultiLang', "t4.model='pjProduct' AND t4.foreign_id=t1.id AND t4.locale='$locale_id' AND t4.field='short_desc'", 'left')
            ->whereIn('t1.id', $product_ids)
            ->findAll()
            ->getData();

        $product_map = [];
        foreach ($products as $p) {
            $product_map[$p['id']] = $p;
        }

        /* ================= LOAD STOCK ================= */

        if (!empty($stock_ids)) {

            $stocks = pjStockModel::factory()
                ->whereIn('t1.id', $stock_ids)
                ->findAll()
                ->getData();

            foreach ($stocks as $s) {
                $stock_arr[$s['id']] = $s;
            }
        }

        /* ================= LOAD ATTRIBUTES ================= */

        $attribute_map = [];
        $attribute_value_map = [];

        if (!empty($attr_ids) || !empty($attr_value_ids)) {

            $all_attr_ids = array_unique(array_merge($attr_ids, $attr_value_ids));

            $attrs = pjAttributeModel::factory()
                ->select("t1.*, t2.content AS name")
                ->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.locale='$locale_id' AND t2.field='name'", 'left')
                ->whereIn('t1.id', $all_attr_ids)
                ->findAll()
                ->getData();

            foreach ($attrs as $a) {
                if ((int)$a['parent_id'] === 0) {
                    $attribute_map[$a['id']] = $a['name'];
                } else {
                    $attribute_value_map[$a['id']] = $a['name'];
                }
            }
        }

        /* ================= LOAD GALLERY ================= */

        $gallery = pjGalleryModel::factory()
            ->whereIn('foreign_id', $product_ids)
            ->orderBy('sort ASC')
            ->findAll()
            ->getData();

        foreach ($gallery as $g) {

            $image_arr[$g['foreign_id']][] = [
                'small_path'  => PJ_INSTALL_URL . $g['small_path'],
                'medium_path' => PJ_INSTALL_URL . $g['medium_path'],
                'large_path'  => PJ_INSTALL_URL . $g['large_path'],
                'alt'         => $g['alt']
            ];
        }

        /* ================= LOAD EXTRAS ================= */

        $extra_prices = [];

        $extras = pjExtraModel::factory()
            ->select('id,price')
            ->findAll()
            ->getData();

        foreach ($extras as $e) {
            $extra_prices[$e['id']] = (float)$e['price'];
        }

        $extra_item_prices = [];

        $extra_items = pjExtraItemModel::factory()
            ->select('id,extra_id,price')
            ->findAll()
            ->getData();

        foreach ($extra_items as $ei) {
            $extra_item_prices[$ei['extra_id']][$ei['id']] = (float)$ei['price'];
        }

        /* ================= BUILD CART ================= */

        foreach ($cart_arr as $item) {

            $data = unserialize($item['key_data']);

            $product_id = (int)$data['product_id'];
            $stock_id   = (int)($data['stock_id'] ?? 0);
            $qty        = (int)$item['qty'];

            if (!isset($product_map[$product_id])) {
                continue;
            }

            $product = $product_map[$product_id];

            /* ===== BASE PRICE ===== */

            $price = $product['price'];

            if (!empty($stock_id) && isset($stock_arr[$stock_id])) {
                $price = $stock_arr[$stock_id]['price'];
            }

            /* ===== ATTRIBUTES (UPDATED ONLY THIS PART) ===== */

            $attributes = [];

            if (!empty($data['attr'])) {
                foreach ($data['attr'] as $a_id => $v_id) {
                    $attributes[$a_id] = [
                        'attribute_id'   => $a_id,
                        'attribute_name' => $attribute_map[$a_id] ?? '',
                        'value_id'       => $v_id,
                        'value_name'     => $attribute_value_map[$v_id] ?? ''
                    ];
                }
            }

            /* ===== EXTRAS ===== */

            $extras = $data['extra'] ?? [];

            $extra_total = 0;

            if (!empty($extras)) {

                foreach ($extras as $ex) {

                    if (strpos($ex, ".") !== false) {

                        list($extra_id, $extra_item_id) = explode(".", $ex);

                        if (isset($extra_item_prices[$extra_id][$extra_item_id])) {
                            $extra_total += $extra_item_prices[$extra_id][$extra_item_id];
                        }
                    } else {

                        if (isset($extra_prices[$ex])) {
                            $extra_total += $extra_prices[$ex];
                        }
                    }
                }
            }

            /* ===== FINAL PRICE ===== */

            $item_price = $price + $extra_total;

            $subtotal = $item_price * $qty;

            $order_arr['subtotal'] += $subtotal;

            /* ===== IMAGE ===== */

            $product_images = $image_arr[$product_id] ?? [];
            $main_image = $product_images[0] ?? null;

            /* ===== CART ITEM ===== */

            $arr[] = [

                'product' => [
                    'id'         => $product['id'],
                    'company_id' => $product['company_id'],
                    'sku'        => $product['sku'],
                    'status'     => $product['status'],
                    'is_digital' => $product['is_digital'],
                    'name'       => $product['name'],
                    'short_desc' => $product['short_desc'],
                    'full_desc'  => $product['full_desc'],
                    'pic_medium' => $main_image['medium_path'] ?? null,
                    'pic_large'  => $main_image['large_path'] ?? null
                ],

                'gallery' => $product_images,

                'qty' => $qty,

                'price' => $item_price,

                'extra_price' => $extra_total,

                'subtotal' => $subtotal,

                'attributes' => $attributes,

                'extras' => $extras,

                'hash' => md5($item['key_data'])
            ];
        }

        /* ================= TAX ================= */

        $tax_percent = isset($this->option_arr['o_tax'])
            ? (float)$this->option_arr['o_tax']
            : 0;

        $order_arr['tax'] = ($order_arr['subtotal'] * $tax_percent) / 100;

        /* ================= SHIPPING ================= */

        $shipping = isset($this->option_arr['o_shipping'])
            ? (float)$this->option_arr['o_shipping']
            : 0;

        $order_arr['shipping'] = $shipping;

        /* ================= TOTAL ================= */

        $order_arr['total'] =
            $order_arr['subtotal'] +
            $order_arr['tax'] +
            $order_arr['shipping'];

        return [
            'arr'       => $arr,
            'extra_arr' => $extra_arr,
            'order_arr' => $order_arr,
            'attr_arr'  => $attr_arr,
            'stock_arr' => $stock_arr,
            'tax_arr'   => $tax_arr,
            'image_arr' => $image_arr
        ];
    }
    public function pjActionGetCart()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        if (empty($params['client_id'])) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'client_id is required.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];
        $hash = md5(PJ_SALT . $client_id);

        $cart_items = pjCartModel::factory()
            ->where('hash', $hash)
            ->where('is_cart', '0')
            ->findAll()
            ->getData();
        if (empty($cart_items)) {
            echo json_encode([
                'status' => 'OK',
                'code'   => 200,
                'message' => 'Cart is empty.',
                'data'   => []
            ]);
            exit;
        }

        $data = $this->buildCartData($cart_items);
        // echo "<pre>";
        // print_r($data);
        // die;
        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Cart fetched successfully.',
            'data'    => [
                'items'   => $data['arr'],
                'summary' => $data['order_arr']
            ]
        ]);

        exit;
    }

    public function pjActionEmptyCart()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        if (empty($params['client_id'])) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 400,
                'message' => 'client_id is required.'
            ]);
            exit;
        }

        $hash = md5(PJ_SALT . (int)$params['client_id']);
        $cart = new pjShoppingCart(pjCartModel::factory(), $hash);

        if (!$cart->isEmpty()) {
            $cart->clear();

            echo json_encode([
                'status' => 'OK',
                'code'   => 200,
                'message' => 'Cart emptied successfully.'
            ]);
            exit;
        }

        echo json_encode([
            'status' => 'ERR',
            'code'   => 404,
            'message' => 'Cart already empty.'
        ]);
        exit;
    }

    public function pjActionRemoveFromCart()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        /* ================= VALIDATION ================= */

        if (empty($params['client_id']) || empty($params['hash'])) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'client_id and hash are required.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];
        $hash_key  = trim($params['hash']);

        $cart_hash = md5(PJ_SALT . $client_id);

        /* ================= CHECK ITEM EXISTS ================= */

        $exists = pjCartModel::factory()
            ->where('hash', $cart_hash)
            ->where(sprintf("MD5(`key_data`) = '%s'", pjCartModel::factory()->escapeStr($hash_key)))
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($exists)) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 404,
                'message' => 'Cart item not found.'
            ]);
            exit;
        }

        /* ================= REMOVE ITEM ================= */

        pjCartModel::factory()
            ->reset()
            ->where('hash', $cart_hash)
            ->where(sprintf("MD5(`key_data`) = '%s'", pjCartModel::factory()->escapeStr($hash_key)))
            ->limit(1)
            ->eraseAll();

        /* ================= RETURN UPDATED CART ================= */

        $remaining = pjCartModel::factory()
            ->where('hash', $cart_hash)
            ->findAll()
            ->getData();

        $cart_count = 0;
        foreach ($remaining as $item) {
            $cart_count += $item['qty'];
        }

        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Item removed successfully.',
            'data'    => [
                'cart_count' => $cart_count
            ]
        ]);

        exit;
    }

    public function pjActionUpdateCart()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        /* ================= VALIDATION ================= */

        if (empty($params['client_id']) || empty($params['qty']) || !is_array($params['qty'])) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'client_id and qty array are required.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];
        $cart_hash = md5(PJ_SALT . $client_id);
        $qty_arr   = $params['qty'];

        /* ================= PROCESS EACH ITEM ================= */

        foreach ($qty_arr as $hash => $qty) {

            $qty = (int)$qty;

            // Find matching row
            $item = pjCartModel::factory()
                ->where('hash', $cart_hash)
                ->where(sprintf("MD5(`key_data`) = '%s'", pjCartModel::factory()->escapeStr($hash)))
                ->limit(1)
                ->findAll()
                ->getData();

            if (empty($item)) {
                continue;
            }

            if ($qty > 0) {
                // Update quantity
                pjCartModel::factory()
                    ->reset()
                    ->where('id', $item[0]['id'])
                    ->limit(1)
                    ->modifyAll(['qty' => $qty]);
            } else {
                // Remove item if qty = 0
                pjCartModel::factory()
                    ->reset()
                    ->where('id', $item[0]['id'])
                    ->limit(1)
                    ->eraseAll();
            }
        }

        /* ================= RETURN UPDATED CART COUNT ================= */

        $remaining = pjCartModel::factory()
            ->where('hash', $cart_hash)
            ->findAll()
            ->getData();

        $cart_count = 0;
        foreach ($remaining as $item) {
            $cart_count += $item['qty'];
        }

        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Cart updated successfully.',
            'data'    => [
                'cart_count' => $cart_count
            ]
        ]);

        exit;
    }

    public function pjActionClearCart()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        /* ================= VALIDATION ================= */

        if (empty($params['client_id'])) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'client_id is required.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];
        $cart_hash = md5(PJ_SALT . $client_id);

        /* ================= CHECK IF CART HAS ITEMS ================= */

        $count = pjCartModel::factory()
            ->where('hash', $cart_hash)
            ->where('is_cart', '0')
            ->findCount()
            ->getData();

        if ($count == 0) {
            echo json_encode([
                'status'  => 'OK',
                'code'    => 200,
                'message' => 'Cart already empty.',
                'data'    => [
                    'cart_count' => 0
                ]
            ]);
            exit;
        }

        /* ================= CLEAR CART ================= */

        pjCartModel::factory()
            ->reset()
            ->where('hash', $cart_hash)
            ->eraseAll();

        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Cart cleared successfully.',
            'data'    => [
                'cart_count' => 0
            ]
        ]);

        exit;
    }


    public function pjActionCreateOrder()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        /* ================= VALIDATION ================= */

        if (
            empty($params['client_id']) ||
            empty($params['full_name']) ||
            empty($params['email']) ||
            empty($params['phone']) ||
            empty($params['address_1']) ||
            empty($params['city']) ||
            empty($params['state']) ||
            empty($params['zip'])
        ) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 400,
                'message' => 'Missing required fields.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];
        $cart_hash = md5(PJ_SALT . $client_id);

        /* ================= LOAD CART ================= */

        $cart_items = pjCartModel::factory()
            ->where('hash', $cart_hash)
            ->where('is_cart', '0')
            ->findAll()
            ->getData();

        if (empty($cart_items)) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 404,
                'message' => 'Cart is empty.'
            ]);
            exit;
        }

        /* ================= CALCULATE CART ================= */

        $cart_data = $this->buildCartData($cart_items);

        $subtotal = $cart_data['order_arr']['subtotal'];
        $tax      = $cart_data['order_arr']['tax'];
        $shipping = $cart_data['order_arr']['shipping'];
        $total    = $cart_data['order_arr']['total'];

        /* ================= CREATE ORDER ================= */

        $order_data = [
            'uuid'           => pjUtil::uuid(),
            'client_id'      => $client_id,
            'company_id'     => '0',
            'locale_id'      => $this->getLocaleId(),
            'status'         => 'new',
            'payment_method' => $params['payment_method'] ?? 'cash',

            'price'          => $subtotal,
            'tax'            => $tax,
            'shipping'       => $shipping,
            'total'          => $total,

            'ip'             => $_SERVER['REMOTE_ADDR'],

            /* ===== BILLING ===== */

            'b_name'        => $params['full_name'],
            'b_country_id'  => $params['country_id'] ?? 0,
            'b_state'       => $params['state'],
            'b_city'        => $params['city'],
            'b_zip'         => $params['zip'],
            'b_address_1'   => $params['address_1'],
            'b_address_2'   => $params['address_2'] ?? null,

            /* ===== SHIPPING ===== */

            'same_as'       => 1,
            's_name'        => $params['full_name'],
            's_country_id'  => $params['country_id'] ?? 0,
            's_state'       => $params['state'],
            's_city'        => $params['city'],
            's_zip'         => $params['zip'],
            's_address_1'   => $params['address_1'],
            's_address_2'   => $params['address_2'] ?? null,

            'notes'         => $params['company_name'] ?? null
        ];

        $quote_id = pjQuoteModel::factory()
            ->setAttributes($order_data)
            ->insert()
            ->getInsertId();

        if (!$quote_id) {
            echo json_encode([
                'status' => 'ERR',
                'code'   => 500,
                'message' => 'Order creation failed.'
            ]);
            exit;
        }

        /* ================= INSERT ORDER ITEMS ================= */

        foreach ($cart_items as $item) {

            $item_data = unserialize($item['key_data']);

            $stock = pjStockModel::factory()
                ->find($item['stock_id'])
                ->getData();

            $base_price = $stock['price'];

            /* ===== EXTRA PRICE ===== */

            $extra_total = 0;

            if (!empty($item_data['extra'])) {

                foreach ($item_data['extra'] as $ex) {

                    if (strpos($ex, '.') !== false) {

                        list($extra_id, $extra_item_id) = explode('.', $ex);

                        $extra_item = pjExtraItemModel::factory()
                            ->find($extra_item_id)
                            ->getData();

                        if ($extra_item) {
                            $extra_total += $extra_item['price'];
                        }
                    } else {

                        $extra = pjExtraModel::factory()
                            ->find($ex)
                            ->getData();

                        if ($extra) {
                            $extra_total += $extra['price'];
                        }
                    }
                }
            }

            $price = $base_price + $extra_total;

            $order_stock_id = pjQuoteStockModel::factory()
                ->setAttributes([
                    'quote_id'   => $quote_id,
                    'product_id' => $item['product_id'],
                    'stock_id'   => $item['stock_id'],
                    'price'      => $price,
                    'qty'        => $item['qty']
                ])
                ->insert()
                ->getInsertId();

            /* ===== INSERT EXTRAS ===== */

            if (!empty($item_data['extra'])) {

                foreach ($item_data['extra'] as $ex) {

                    $extra_data = [
                        'quote_id'       => $quote_id,
                        'quote_stock_id' => $order_stock_id
                    ];

                    if (strpos($ex, '.') !== false) {

                        list($extra_data['extra_id'], $extra_data['extra_item_id']) = explode('.', $ex);
                    } else {

                        $extra_data['extra_id'] = $ex;
                        $extra_data['extra_item_id'] = null;
                    }

                    pjQuoteExtraModel::factory()
                        ->setAttributes($extra_data)
                        ->insert();
                }
            }
        }

        /* ================= SEND EMAIL ================= */

        $pjQuoteModel = pjQuoteModel::factory();

        $order_arr = $pjQuoteModel
            ->reset()
            ->find($quote_id)
            ->getData();

        pjFront::pjActionConfirmSend(
            $this->option_arr,
            $order_arr,
            PJ_SALT,
            'quote',
            $this->getLocaleId()
        );

        /* ================= CLEAR CART ================= */

        pjCartModel::factory()
            ->reset()
            ->where('hash', $cart_hash)
            ->where('is_cart', '0')
            ->eraseAll();

        echo json_encode([
            'status' => 'OK',
            'code'   => 200,
            'message' => 'Order placed successfully.',
            'data'   => [
                'quote_id' => $quote_id,
                'total'    => $total
            ]
        ]);

        exit;
    }
    public function pjActionGetOrders()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        /* ================= REQUIRED ================= */



        $company_id = (int)0;

        /* ================= BASE QUERY ================= */

        $pjQuoteModel = pjQuoteModel::factory()
            ->join('pjClient', 't2.id=t1.client_id', 'left outer')
            ->where('t1.company_id', $company_id);

        /* ================= SEARCH ================= */

        if (!empty($params['q'])) {
            $q = trim($params['q']);
            $q = str_replace(['%', '_'], ['\%', '\_'], $q);

            $pjQuoteModel->where("
            (t1.uuid LIKE '%$q%' 
            OR t2.client_name LIKE '%$q%'
            OR t2.email LIKE '%$q%'
            OR t1.s_name LIKE '%$q%'
            OR t1.b_name LIKE '%$q%')
        ");
        }

        /* ================= FILTERS ================= */

        if (!empty($params['status'])) {
            $pjQuoteModel->where('t1.status', $params['status']);
        }

        if (!empty($params['payment_method'])) {
            $pjQuoteModel->where('t1.payment_method', $params['payment_method']);
        }

        if (!empty($params['total_from'])) {
            $pjQuoteModel->where('t1.total >=', (float)$params['total_from']);
        }

        if (!empty($params['total_to'])) {
            $pjQuoteModel->where('t1.total <=', (float)$params['total_to']);
        }

        if (!empty($params['date_from'])) {
            $pjQuoteModel->where('DATE(t1.created) >=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $pjQuoteModel->where('DATE(t1.created) <=', $params['date_to']);
        }

        /* ================= SORT ================= */

        $column    = !empty($params['column']) ? $params['column'] : 't1.id';
        $direction = (!empty($params['direction']) && strtoupper($params['direction']) === 'ASC') ? 'ASC' : 'DESC';

        /* ================= PAGINATION ================= */

        $rowCount = !empty($params['rowCount']) ? (int)$params['rowCount'] : 10;
        $page     = !empty($params['page']) ? (int)$params['page'] : 1;

        $total = $pjQuoteModel->findCount()->getData();
        $pages = ceil($total / $rowCount);
        $offset = ($page - 1) * $rowCount;

        if ($page > $pages) {
            $page = $pages;
        }

        /* ================= FETCH DATA ================= */

        $data = $pjQuoteModel
            ->select('t1.id, t1.uuid, t1.total, t1.status, t1.created, t1.client_id, t2.client_name')
            ->orderBy("$column $direction")
            ->limit($rowCount, $offset)
            ->findAll()
            ->getData();

        /* ================= FORMAT ================= */

        foreach ($data as $k => $v) {
            $data[$k]['total_formatted'] = number_format($v['total'], 2);
            $data[$k]['created_formatted'] = date("Y-m-d H:i:s", strtotime($v['created']));
        }

        /* ================= RESPONSE ================= */

        echo json_encode([
            'status' => 'OK',
            'code'   => 200,
            'data'   => $data,
            'pagination' => [
                'total'    => $total,
                'pages'    => $pages,
                'page'     => $page,
                'rowCount' => $rowCount
            ],
            'sort' => [
                'column'    => $column,
                'direction' => $direction
            ]
        ]);

        exit;
    }
    // public function pjActionQuoteDetails()
    // {
    //     header("Content-Type: application/json");

    //     $params = $this->_post->raw();

    //     /* ================= VALIDATION ================= */

    //     if (empty($params['quote_id']) && empty($params['uuid'])) {
    //         echo json_encode([
    //             'status' => 'ERR',
    //             'code'   => 400,
    //             'message' => 'quote_id or uuid is required.'
    //         ]);
    //         exit;
    //     }

    //     $quote_id = $params['quote_id'] ?? null;
    //     $uuid     = $params['uuid'] ?? null;
    //     $client_id = $params['client_id'] ?? null;

    //     /* ================= FETCH QUOTE ================= */

    //     $pjQuoteModel = pjQuoteModel::factory();

    //     if (!empty($quote_id)) {
    //         $pjQuoteModel->where('t1.id', (int)$quote_id);
    //     } else {
    //         $pjQuoteModel->where('t1.uuid', $uuid);
    //     }
    //     // echo "<pre>";
    //     // print_r($client_id);
    //     // die;
    //     $quote = $pjQuoteModel
    //         ->where('t1.client_id', $client_id)
    //         ->select("t1.*")
    //         ->limit(1)
    //         ->findAll()
    //         ->getData();

    //     if (empty($quote)) {
    //         echo json_encode([
    //             'status' => 'ERR',
    //             'code'   => 404,
    //             'message' => 'Quote not found.'
    //         ]);
    //         exit;
    //     }

    //     $quote = $quote[0];

    //     /* ================= GET ITEMS ================= */

    //     $items = pjQuoteStockModel::factory()
    //         ->where('quote_id', $quote['id'])
    //         ->findAll()
    //         ->getData();

    //     $final_items = [];

    //     foreach ($items as $item) {

    //         $product = pjProductModel::factory()
    //             ->select("t1.*, t2.content AS name")
    //             ->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
    //             ->find($item['product_id'])
    //             ->getData();

    //         $stock = pjStockModel::factory()
    //             ->find($item['stock_id'])
    //             ->getData();

    //         /* ===== EXTRAS ===== */

    //         $extras = pjQuoteExtraModel::factory()
    //             ->where('quote_id', $quote['id'])
    //             ->where('quote_stock_id', $item['id'])
    //             ->findAll()
    //             ->getData();

    //         $extra_data = [];

    //         foreach ($extras as $ex) {

    //             $extra_name = '';
    //             $extra_price = 0;

    //             if (!empty($ex['extra_item_id'])) {

    //                 $ei = pjExtraItemModel::factory()
    //                     ->select("t1.*, t2.content AS name")
    //                     ->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left')
    //                     ->find($ex['extra_item_id'])
    //                     ->getData();

    //                 if ($ei) {
    //                     $extra_name  = $ei['name'];
    //                     $extra_price = $ei['price'];
    //                 }
    //             } else {

    //                 $e = pjExtraModel::factory()
    //                     ->select("t1.*, t2.content AS name")
    //                     ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left')
    //                     ->find($ex['extra_id'])
    //                     ->getData();

    //                 if ($e) {
    //                     $extra_name  = $e['name'];
    //                     $extra_price = $e['price'];
    //                 }
    //             }

    //             $extra_data[] = [
    //                 'name'  => $extra_name,
    //                 'price' => (float)$extra_price
    //             ];
    //         }

    //         $final_items[] = [
    //             'product_id' => $product['id'],
    //             'name'       => $product['name'],
    //             'sku'        => $product['sku'],
    //             'stock_id'   => $stock['id'],
    //             'price'      => (float)$item['price'],
    //             'qty'        => (int)$item['qty'],
    //             'subtotal'   => (float)$item['price'] * $item['qty'],
    //             'extras'     => $extra_data
    //         ];
    //     }

    //     /* ================= RESPONSE ================= */

    //     $response = [

    //         'quote' => [
    //             'id' => $quote['id'],
    //             'uuid' => $quote['uuid'],
    //             'status' => $quote['status'],
    //             'payment_method' => $quote['payment_method'],
    //             'total' => (float)$quote['total'],
    //             'tax'   => (float)$quote['tax'],
    //             'shipping' => (float)$quote['shipping'],
    //             'created' => $quote['created']
    //         ],

    //         'customer' => [
    //             'name'  => $quote['b_name'],
    //             'email' => $quote['email'] ?? null,
    //             'phone' => $quote['phone'] ?? null
    //         ],

    //         'billing' => [
    //             'address' => $quote['b_address_1'],
    //             'city'    => $quote['b_city'],
    //             'state'   => $quote['b_state'],
    //             'zip'     => $quote['b_zip']
    //         ],

    //         'shipping' => [
    //             'address' => $quote['s_address_1'],
    //             'city'    => $quote['s_city'],
    //             'state'   => $quote['s_state'],
    //             'zip'     => $quote['s_zip']
    //         ],

    //         'items' => $final_items
    //     ];

    //     echo json_encode([
    //         'status' => 'OK',
    //         'code'   => 200,
    //         'message' => 'Quote fetched successfully.',
    //         'data'   => $response
    //     ]);

    //     exit;
    // }
    public function pjActionQuoteDetails()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        // $this->writeLog("==== QUOTE API START ====");
        // $this->writeLog($params);

        /* ================= VALIDATION ================= */

        if (empty($params['quote_id']) && empty($params['uuid'])) {
            // $this->writeLog("VALIDATION FAILED");

            echo json_encode([
                'status' => 'ERR',
                'code'   => 400,
                'message' => 'quote_id or uuid is required.'
            ]);
            exit;
        }

        $quote_id = $params['quote_id'] ?? null;
        $uuid     = $params['uuid'] ?? null;
        $client_id = $params['client_id'] ?? null;

        /* ================= FETCH QUOTE ================= */

        $pjQuoteModel = pjQuoteModel::factory();

        if (!empty($quote_id)) {
            $pjQuoteModel->where('t1.id', (int)$quote_id);
        } else {
            $pjQuoteModel->where('t1.uuid', $uuid);
        }

        $quote = $pjQuoteModel
            ->where('t1.client_id', $client_id)
            ->select("t1.*")
            ->limit(1)
            ->findAll()
            ->getData();

        // $this->writeLog("QUOTE DATA:");
        // $this->writeLog($quote);

        if (empty($quote)) {
            // $this->writeLog("QUOTE NOT FOUND");

            echo json_encode([
                'status' => 'ERR',
                'code'   => 404,
                'message' => 'Quote not found.'
            ]);
            exit;
        }

        $quote = $quote[0];

        /* ================= GET ITEMS ================= */

        $items = pjQuoteStockModel::factory()
            ->where('quote_id', $quote['id'])
            ->findAll()
            ->getData();

        // $this->writeLog("RAW ITEMS:");
        // $this->writeLog($items);

        $final_items = [];

        /* ================= LOAD ATTRIBUTE MAP ================= */

        $pjStockAttributeModel = pjStockAttributeModel::factory();
        $pjAttributeModel = pjAttributeModel::factory();

        $attribute_map = [];

        $attrs = $pjAttributeModel
            ->select("t1.id, t1.parent_id, t2.content AS name")
            ->join('pjMultiLang', "t2.model='pjAttribute' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
            ->findAll()
            ->getData();

        foreach ($attrs as $a) {
            $attribute_map[$a['id']] = [
                'name' => $a['name'],
                'parent_id' => $a['parent_id']
            ];
        }

        /* ================= LOOP ITEMS ================= */

        foreach ($items as $item) {

            // $this->writeLog("---- ITEM START ----");
            // $this->writeLog($item);

            /* ===== SAFETY CHECK ===== */
            if (empty($item['product_id']) || empty($item['stock_id'])) {
                // $this->writeLog("SKIPPED ITEM (NULL product_id OR stock_id)");
                continue;
            }

            $product = pjProductModel::factory()
                ->select("t1.*, t2.content AS name")
                ->join('pjMultiLang', "t2.model='pjProduct' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='name'", 'left')
                ->find($item['product_id'])
                ->getData();

            // $this->writeLog("PRODUCT:");
            // $this->writeLog($product);

            if (empty($product)) {
                // $this->writeLog("SKIPPED ITEM (PRODUCT NOT FOUND)");
                continue;
            }

            $stock = pjStockModel::factory()
                ->find($item['stock_id'])
                ->getData();

            // $this->writeLog("STOCK:");
            // $this->writeLog($stock);

            if (empty($stock)) {
                // $this->writeLog("SKIPPED ITEM (STOCK NOT FOUND)");
                continue;
            }

            /* ===== ATTRIBUTES ===== */

            $attr_raw = $pjStockAttributeModel
                ->reset()
                ->where('stock_id', $item['stock_id'])
                ->orderBy('attribute_id ASC')
                ->findAll()
                ->getDataPair('attribute_parent_id', 'attribute_id');

            // $this->writeLog("ATTR RAW:");
            // $this->writeLog($attr_raw);

            $attributes = [];

            if (!empty($attr_raw)) {
                foreach ($attr_raw as $parent_id => $child_id) {

                    $attributes[] = [
                        'attribute_id'   => $parent_id,
                        'attribute_name' => $attribute_map[$parent_id]['name'] ?? '',
                        'value_id'       => $child_id,
                        'value_name'     => $attribute_map[$child_id]['name'] ?? ''
                    ];
                }
            }

            // $this->writeLog("ATTR FINAL:");
            // $this->writeLog($attributes);

            /* ===== EXTRAS ===== */

            $extras = pjQuoteExtraModel::factory()
                ->where('quote_id', $quote['id'])
                ->where('quote_stock_id', $item['id'])
                ->findAll()
                ->getData();

            $extra_data = [];

            foreach ($extras as $ex) {

                $extra_name = '';
                $extra_price = 0;

                if (!empty($ex['extra_item_id'])) {

                    $ei = pjExtraItemModel::factory()
                        ->select("t1.*, t2.content AS name")
                        ->join('pjMultiLang', "t2.model='pjExtraItem' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left')
                        ->find($ex['extra_item_id'])
                        ->getData();

                    if ($ei) {
                        $extra_name  = $ei['name'];
                        $extra_price = $ei['price'];
                    }
                } else {

                    $e = pjExtraModel::factory()
                        ->select("t1.*, t2.content AS name")
                        ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.locale='" . $this->getLocaleId() . "' AND t2.field='extra_name'", 'left')
                        ->find($ex['extra_id'])
                        ->getData();

                    if ($e) {
                        $extra_name  = $e['name'];
                        $extra_price = $e['price'];
                    }
                }

                $extra_data[] = [
                    'name'  => $extra_name,
                    'price' => (float)$extra_price
                ];
            }

            $final_items[] = [
                'product_id' => $product['id'],
                'name'       => $product['name'],
                'sku'        => $product['sku'],
                'stock_id'   => $stock['id'],
                'price'      => (float)$item['price'],
                'qty'        => (int)$item['qty'],
                'subtotal'   => (float)$item['price'] * $item['qty'],
                'attributes' => $attributes,
                'extras'     => $extra_data
            ];
        }

        /* ================= RESPONSE ================= */

        $response = [

            'quote' => [
                'id' => $quote['id'],
                'uuid' => $quote['uuid'],
                'status' => $quote['status'],
                'payment_method' => $quote['payment_method'],
                'total' => (float)$quote['total'],
                'tax'   => (float)$quote['tax'],
                'shipping' => (float)$quote['shipping'],
                'created' => $quote['created']
            ],

            'customer' => [
                'name'  => $quote['b_name'],
                'email' => $quote['email'] ?? null,
                'phone' => $quote['phone'] ?? null
            ],

            'billing' => [
                'address' => $quote['b_address_1'],
                'city'    => $quote['b_city'],
                'state'   => $quote['b_state'],
                'zip'     => $quote['b_zip']
            ],

            'shipping' => [
                'address' => $quote['s_address_1'],
                'city'    => $quote['s_city'],
                'state'   => $quote['s_state'],
                'zip'     => $quote['s_zip']
            ],

            'items' => $final_items
        ];

        // $this->writeLog("FINAL RESPONSE:");
        // $this->writeLog($response);

        echo json_encode([
            'status' => 'OK',
            'code'   => 200,
            'message' => 'Quote fetched successfully.',
            'data'   => $response
        ]);

        exit;
    }
}
