<?php
if (!defined("ROOT_PATH")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

/**
 * Post-install setup wizard: load or skip sample quote data.
 */
class pjAdminSetup extends pjAdmin
{
    const DEMO_CATEGORY_PREFIX = '[DEMO] ';
    const DEMO_ORDER_NOTE = '[DEMO] Sample quote';
    const DEMO_CSV_RELATIVE = 'app/web/upload/imports/1778833811_1778054615_1777911568_B2BASIC_READY_comma_updated.csv';
    const DEMO_MODEL_PREFIX = 'B2B-';
    /** Product models from the sample CSV to load as demo (not the full file). */
    const DEMO_SAMPLE_MODELS = ['B2B-APRN', 'B2B-BEA'];
    const DEMO_IMPORT_BATCH = 20;

    public function beforeFilter()
    {
        pjAppController::beforeFilter();
        return true;
    }

    public function pjActionWelcome()
    {
        $this->checkLogin();

        $wizardCompleted = $this->isWizardCompleted();
        if (!$wizardCompleted) {
            $this->setLayout('pjAdminSetup');
            $this->appendCss('admin-datagrid-listings.css', PJ_CSS_PATH);
        }

        $hasDemoProducts = self::hasDemoData();

        $this->set('wizardCompleted', $wizardCompleted);
        $this->set('hasDemoProducts', $hasDemoProducts);
        if ($wizardCompleted) {
            $this->appendCss('admin-datagrid-listings.css', PJ_CSS_PATH);
        }
        $this->set('hasDemoData', $hasDemoProducts);
        $this->appendJs('pjAdminSetup.js');
    }

    public function pjActionLoadDemo()
    {
        $this->setAjax(true);

        if (!$this->isXHR() || !$this->isLoged()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => __('setup_unauthorized', true)]);
        }

        try {
            $offset = $this->_post->check('offset') ? (int) $this->_post->toInt('offset') : 0;
            $limit  = self::DEMO_IMPORT_BATCH;

            if ($offset === 0) {
                @set_time_limit(300);
                @ini_set('memory_limit', '512M');
                unset($_SESSION['pj_demo_csv_rows'], $_SESSION['pj_demo_import_stats'], $_SESSION['pj_demo_product_models']);
                $this->loadDemoCsvIntoSession();
                $_SESSION['pj_demo_import_stats'] = [
                    'stocks' => 0,
                    'images' => 0,
                ];
                $_SESSION['pj_demo_product_models'] = [];
            }

            $rows  = $this->getDemoCsvRows();
            $total = count($rows);
            if ($total === 0) {
                throw new Exception('Demo catalogue file is empty.');
            }

            $batch = array_slice($rows, $offset, $limit);
            $stats = &$_SESSION['pj_demo_import_stats'];

            foreach ($batch as $data) {
                $this->importDemoCsvRow($data, $stats);
            }

            $nextOffset = $offset + count($batch);

            if ($nextOffset < $total) {
                self::jsonResponse([
                    'status' => 'CONTINUE',
                    'code'   => 200,
                    'offset' => $nextOffset,
                    'total'  => $total,
                    'data'   => $stats,
                ]);
                exit;
            }

            $results = [
                'categories' => $this->countDemoCategories(),
                'products'   => count($_SESSION['pj_demo_product_models'] ?? []),
                'stocks'     => (int) ($stats['stocks'] ?? 0),
                'images'     => (int) ($stats['images'] ?? 0),
            ];

            unset($_SESSION['pj_demo_csv_rows'], $_SESSION['pj_demo_import_stats'], $_SESSION['pj_demo_product_models']);
            $this->demoClearTempImages();

            $clientIds = $this->createDemoClients();
            $results['clients'] = count($clientIds);
            $results['orders'] = $this->createDemoOrders($clientIds);

            $this->markWizardCompleted();

            self::jsonResponse([
                'status' => 'OK',
                'code'   => 200,
                'text'   => __('setup_api_loaded', true),
                'data'   => $results,
            ]);
        } catch (Exception $e) {
            unset($_SESSION['pj_demo_csv_rows'], $_SESSION['pj_demo_import_stats'], $_SESSION['pj_demo_product_models']);
            $this->demoClearTempImages();
            self::jsonResponse([
                'status' => 'ERR',
                'code'   => 500,
                'text'   => __('setup_error_loading_demo', true) . ' ' . $e->getMessage(),
            ]);
        }
        exit;
    }

    public function pjActionSkipDemo()
    {
        $this->setAjax(true);

        if (!$this->isXHR() || !$this->isLoged()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => __('setup_unauthorized', true)]);
        }

        $this->markWizardCompleted();

        self::jsonResponse([
            'status' => 'OK',
            'code'   => 200,
            'text'   => __('setup_api_completed', true),
        ]);
        exit;
    }

    public function pjActionRemoveDemo()
    {
        $this->setAjax(true);

        if (!$this->isXHR() || !$this->isLoged()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100, 'text' => __('setup_unauthorized', true)]);
        }

        try {
            $results = [
                'orders'     => $this->removeDemoOrders(),
                'clients'    => $this->removeDemoClients(),
                'products'   => $this->removeDemoProducts(),
                'categories' => $this->removeDemoCategories(),
                'images'     => $this->removeDemoImages(),
            ];

            self::jsonResponse([
                'status' => 'OK',
                'code'   => 200,
                'text'   => __('setup_api_removed', true),
                'data'   => $results,
            ]);
        } catch (Exception $e) {
            self::jsonResponse([
                'status' => 'ERR',
                'code'   => 500,
                'text'   => __('setup_error_removing_demo', true) . ' ' . $e->getMessage(),
            ]);
        }
        exit;
    }

    public function pjActionHasDemoData()
    {
        $this->setAjax(true);

        if (!$this->isXHR() || !$this->isLoged()) {
            self::jsonResponse(['status' => 'ERR', 'code' => 100]);
        }

        $count = self::hasDemoData() ? 1 : 0;

        self::jsonResponse([
            'status'   => 'OK',
            'code'     => 200,
            'has_demo' => (int) $count > 0,
            'count'    => (int) $count,
        ]);
        exit;
    }

    /**
     * Whether sample/demo seed data is still in the database (any company).
     */
    public static function hasDemoData()
    {
        try {
            $demoProducts = pjProductModel::factory()
                ->where("(t1.model LIKE '" . self::DEMO_MODEL_PREFIX . "%' OR t1.sku LIKE 'DEMO-%' OR t1.sku LIKE '" . self::DEMO_MODEL_PREFIX . "%')")
                ->limit(1)
                ->findAll()
                ->getData();
            if (!empty($demoProducts)) {
                return true;
            }

            $demoClients = pjClientModel::factory()
                ->where("t1.email LIKE 'demo-%@example.com'")
                ->limit(1)
                ->findAll()
                ->getData();
            if (!empty($demoClients)) {
                return true;
            }

            $demoOrders = pjOrderModel::factory()
                ->where("t1.notes LIKE '%" . self::DEMO_ORDER_NOTE . "%'")
                ->limit(1)
                ->findAll()
                ->getData();

            return !empty($demoOrders);
        } catch (Exception $e) {
            return false;
        }
    }

    private function getCompanyId()
    {
        if (isset($_SESSION[$this->defaultCompany]['id']) && (int) $_SESSION[$this->defaultCompany]['id'] > 0) {
            return (int) $_SESSION[$this->defaultCompany]['id'];
        }
        return 1;
    }

    private function isWizardCompleted()
    {
        try {
            $arr = pjOptionModel::factory()
                ->where("t1.`foreign_id` = 1 AND t1.`key` = 'o_setup_wizard_completed'")
                ->limit(1)
                ->findAll()
                ->getData();
            if (empty($arr)) {
                return true;
            }
            return $arr[0]['value'] == '1';
        } catch (Exception $e) {
            return true;
        }
    }

    private function markWizardCompleted()
    {
        $arr = pjOptionModel::factory()
            ->where("t1.`foreign_id` = 1 AND t1.`key` = 'o_setup_wizard_completed'")
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($arr)) {
            pjOptionModel::factory()
                ->where("`foreign_id` = 1 AND `key` = 'o_setup_wizard_completed'")
                ->limit(1)
                ->modifyAll(['value' => '1']);
        } else {
            pjOptionModel::factory()
                ->setAttributes([
                    'foreign_id' => 1,
                    'company_id' => $this->getCompanyId(),
                    'key'        => 'o_setup_wizard_completed',
                    'value'      => '1',
                    'type'       => 'int',
                    'label'      => 'Setup wizard completed',
                    'tab_id'     => 0,
                    'order'      => 0,
                    'is_visible' => 0,
                    'style'      => '',
                ])
                ->insert();
        }
    }

    private function getLocaleIds()
    {
        $locales = [];
        $rows = pjLocaleModel::factory()->findAll()->getData();
        foreach ($rows as $row) {
            if (!empty($row['language_iso'])) {
                $locales[$row['language_iso']] = (int) $row['id'];
            }
        }
        if (empty($locales) && !empty($rows)) {
            foreach ($rows as $row) {
                if (!empty($row['is_default'])) {
                    $locales['en-GB'] = (int) $row['id'];
                }
            }
            if (empty($locales)) {
                $locales['en-GB'] = (int) $rows[0]['id'];
            }
        }
        return $locales;
    }

    private function getDefaultLocaleId()
    {
        return $this->getEnglishLocaleId();
    }

    /**
     * Locale used for all demo seed content (English only).
     */
    private function getEnglishLocaleId()
    {
        $locales = $this->getLocaleIds();
        foreach (['en-GB', 'en', 'en-US', 'en_US'] as $iso) {
            if (isset($locales[$iso])) {
                return (int) $locales[$iso];
            }
        }
        if (!empty($locales)) {
            return (int) reset($locales);
        }
        return 1;
    }

    /**
     * Save multilingual fields for demo data in English only (no copies to other locales).
     */
    private function demoSaveEnglishLang($foreignId, $model, array $fields)
    {
        $localeId = $this->getEnglishLocaleId();
        if ($localeId <= 0 || empty($fields)) {
            return;
        }

        pjMultiLangModel::factory()
            ->where('model', $model)
            ->where('foreign_id', $foreignId)
            ->where('locale !=', $localeId)
            ->eraseAll();

        $i18n = [$localeId => $fields];

        $hasLang = pjMultiLangModel::factory()
            ->where('t1.model', $model)
            ->where('t1.foreign_id', $foreignId)
            ->where('t1.locale', $localeId)
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($hasLang)) {
            pjMultiLangModel::factory()->updateMultiLang($i18n, $foreignId, $model, 'data');
        } else {
            pjMultiLangModel::factory()->saveMultiLang($i18n, $foreignId, $model, 'data');
        }
    }

    private function saveMultiLang($foreignId, $model, $fieldsByLocale)
    {
        $locales = $this->getLocaleIds();
        $i18n = [];
        foreach ($fieldsByLocale as $iso => $fields) {
            if (isset($locales[$iso])) {
                $i18n[$locales[$iso]] = $fields;
            }
        }
        if (!empty($i18n)) {
            pjMultiLangModel::factory()->saveMultiLang($i18n, $foreignId, $model, 'data');
        }
    }

    /**
     * Copy the first defined locale block to every active locale (demo content).
     */
    private function saveDemoMultiLang($foreignId, $model, $fieldsByLocale)
    {
        $source = null;
        foreach ($fieldsByLocale as $fields) {
            if (!empty($fields) && is_array($fields)) {
                $source = $fields;
                break;
            }
        }
        if (empty($source)) {
            return;
        }

        $i18n = [];
        foreach ($this->getLocaleIds() as $localeId) {
            $i18n[$localeId] = $source;
        }
        if (empty($i18n)) {
            return;
        }

        $hasLang = pjMultiLangModel::factory()
            ->where('t1.model', $model)
            ->where('t1.foreign_id', $foreignId)
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($hasLang)) {
            pjMultiLangModel::factory()->updateMultiLang($i18n, $foreignId, $model, 'data');
        } else {
            pjMultiLangModel::factory()->saveMultiLang($i18n, $foreignId, $model, 'data');
        }
    }

    private function getDemoCsvPath()
    {
        $path = PJ_INSTALL_PATH . self::DEMO_CSV_RELATIVE;
        if (!is_file($path)) {
            throw new Exception('Demo catalogue file not found.');
        }
        return $path;
    }

    private function loadDemoCsvIntoSession()
    {
        if (!empty($_SESSION['pj_demo_csv_rows'])) {
            return;
        }

        $handle = fopen($this->getDemoCsvPath(), 'r');
        if (!$handle) {
            throw new Exception('Unable to read demo catalogue file.');
        }

        $header = fgetcsv($handle, 0, ';');
        if (empty($header)) {
            fclose($handle);
            throw new Exception('Demo catalogue file has no header row.');
        }
        $header = array_map('trim', $header);

        $rows = [];
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }
            $rows[] = array_combine($header, $row);
        }
        fclose($handle);

        $_SESSION['pj_demo_csv_rows'] = $rows;
    }

    private function getDemoCsvRows()
    {
        $this->loadDemoCsvIntoSession();
        $rows = isset($_SESSION['pj_demo_csv_rows']) ? $_SESSION['pj_demo_csv_rows'] : [];
        if (empty($rows) || empty(self::DEMO_SAMPLE_MODELS)) {
            return $rows;
        }

        $allowed = array_flip(self::DEMO_SAMPLE_MODELS);
        $filtered = [];
        foreach ($rows as $row) {
            $model = isset($row['model']) ? trim($row['model']) : '';
            if ($model !== '' && isset($allowed[$model])) {
                $filtered[] = $row;
            }
        }

        return $filtered;
    }

    private function countDemoCategories()
    {
        $localeId = $this->getDefaultLocaleId();
        return (int) pjMultiLangModel::factory()
            ->where('t1.model', 'pjCategory')
            ->where('t1.field', 'name')
            ->where('t1.locale', $localeId)
            ->where("t1.content LIKE '" . self::DEMO_CATEGORY_PREFIX . "%'")
            ->findCount()
            ->getData();
    }

    private function parseDemoDecimal($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $value = trim(str_replace(',', '.', (string) $value));
        return is_numeric($value) ? (float) $value : 0;
    }

    private function parseDemoQty($value)
    {
        return (int) round($this->parseDemoDecimal($value));
    }

    private function parseDemoEan($value)
    {
        if ($value === null || $value === '') {
            return '';
        }
        $value = trim(str_replace(',', '.', (string) $value));
        if (stripos($value, 'e') !== false && is_numeric($value)) {
            return number_format((float) $value, 0, '', '');
        }
        return $value;
    }

    private function importDemoCsvRow(array $data, array &$stats)
    {
        if (empty($data['model'])) {
            return;
        }

        $companyId = $this->getCompanyId();
        $model = trim($data['model']);
        $status = (isset($data['status']) && (string) $data['status'] === '1') ? 'T' : 'F';

        $productId = $this->demoGetOrCreateProduct(
            $model,
            isset($data['model_name']) ? $data['model_name'] : '',
            isset($data['sku']) ? $data['sku'] : '',
            $companyId
        );

        if (!$productId) {
            return;
        }

        if (empty($_SESSION['pj_demo_product_models'][$model])) {
            $_SESSION['pj_demo_product_models'][$model] = true;
        }

        $categoryName = self::DEMO_CATEGORY_PREFIX . trim(isset($data['category']) ? $data['category'] : 'Catalogue');
        $this->demoAssignBrand($productId, isset($data['brand']) ? $data['brand'] : '', $companyId);
        $this->demoAssignCategory($productId, $categoryName, $companyId);
        $this->demoUpdateProductLang(
            $productId,
            isset($data['name_en']) ? $data['name_en'] : '',
            isset($data['short_desc_en']) ? $data['short_desc_en'] : ''
        );

        $imgId = $this->demoImportGalleryImages($productId, $data);
        if ($imgId) {
            $stats['images']++;
        }

        $this->demoAddStock(
            $productId,
            isset($data['size']) ? $data['size'] : '',
            isset($data['color']) ? $data['color'] : '',
            isset($data['article_number']) ? $data['article_number'] : '',
            isset($data['article_name']) ? $data['article_name'] : '',
            $this->parseDemoEan(isset($data['ean']) ? $data['ean'] : ''),
            $this->parseDemoQty(isset($data['qty']) ? $data['qty'] : 0),
            $this->parseDemoDecimal(isset($data['price']) ? $data['price'] : 0),
            $imgId,
            $status,
            $companyId
        );

        $stats['stocks']++;
    }

    private function demoGetOrCreateProduct($model, $model_name, $sku, $company_id)
    {
        $product = pjProductModel::factory()
            ->where('model', $model)
            ->where('company_id', $company_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($product)) {
            return (int) $product[0]['id'];
        }

        $product_id = pjProductModel::factory()
            ->setAttributes([
                'model'       => $model,
                'model_name'  => $model_name,
                'sku'         => $sku,
                'status'      => 1,
                'company_id'  => $company_id,
                'is_featured' => 0,
                'is_digital'  => 0,
                'created_by'  => $this->getUserId(),
            ])
            ->insert()
            ->getInsertId();

        return $product_id ? (int) $product_id : 0;
    }

    private function demoAssignBrand($product_id, $brand, $company_id)
    {
        if (empty($brand)) {
            return;
        }

        $brand = trim($brand);

        $existing_brand = pjProductBrandModel::factory()
            ->select('t1.brand_id')
            ->where('t1.product_id', $product_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($existing_brand)) {
            $brand_id = $existing_brand[0]['brand_id'];
            $this->demoSaveEnglishLang($brand_id, 'pjBrand', ['name' => $brand]);
            return;
        }

        $englishLocaleId = $this->getEnglishLocaleId();
        $brand_data = pjBrandModel::factory()
            ->select('t1.id')
            ->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjBrand"', 'inner')
            ->where('t2.field', 'name')
            ->where('t2.locale', $englishLocaleId)
            ->where('LOWER(t2.content)', strtolower($brand))
            ->where('t1.company_id', $company_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($brand_data)) {
            $brand_id = pjBrandModel::factory()->saveNode([
                'company_id' => $company_id,
                'parent_id'  => 1,
            ], 1);

            $this->demoSaveEnglishLang($brand_id, 'pjBrand', ['name' => $brand]);
        } else {
            $brand_id = $brand_data[0]['id'];
        }

        pjProductBrandModel::factory()->where('product_id', $product_id)->eraseAll();
        pjProductBrandModel::factory()
            ->set('product_id', $product_id)
            ->set('brand_id', $brand_id)
            ->set('company_id', $company_id)
            ->insert();
    }

    private function demoAssignCategory($product_id, $category_name, $company_id)
    {
        if (empty($category_name)) {
            return;
        }

        $category_name = trim($category_name);

        $existing_category = pjProductCategoryModel::factory()
            ->select('t1.category_id')
            ->where('t1.product_id', $product_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($existing_category)) {
            $category_id = $existing_category[0]['category_id'];
            $this->demoSaveEnglishLang($category_id, 'pjCategory', ['name' => $category_name]);
            return;
        }

        $englishLocaleId = $this->getEnglishLocaleId();
        $category = pjCategoryModel::factory()
            ->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjCategory"', 'inner')
            ->where('t2.field', 'name')
            ->where('t2.locale', $englishLocaleId)
            ->where('LOWER(t2.content)', strtolower($category_name))
            ->where('t1.company_id', $company_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($category)) {
            $category_id = pjCategoryModel::factory()->saveNode([
                'company_id' => $company_id,
                'parent_id'  => 1,
            ], 1);

            $this->demoSaveEnglishLang($category_id, 'pjCategory', ['name' => $category_name]);
        } else {
            $category_id = $category[0]['id'];
        }

        pjProductCategoryModel::factory()->where('product_id', $product_id)->eraseAll();
        pjProductCategoryModel::factory()
            ->set('product_id', $product_id)
            ->set('category_id', $category_id)
            ->set('company_id', $company_id)
            ->insert();
    }

    private function demoUpdateProductLang($product_id, $name, $short)
    {
        $name  = trim($name);
        $short = trim($short);
        $full  = $short !== '' ? '<p>' . htmlspecialchars($short, ENT_QUOTES, 'UTF-8') . '</p>' : '';

        $this->demoSaveEnglishLang($product_id, 'pjProduct', [
            'name'       => $name,
            'short_desc' => $short,
            'full_desc'  => $full,
        ]);
    }

    private function demoGetOrCreateAttribute($product_id, $group_name, $item_name, $company_id)
    {
        $group = pjAttributeModel::factory()
            ->select('t1.id')
            ->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjAttribute"', 'inner')
            ->where('t2.field', 'name')
            ->where('t2.content', $group_name)
            ->where('t1.product_id', $product_id)
            ->where('t1.parent_id IS NULL')
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($group)) {
            $group_id = pjAttributeModel::factory()
                ->setAttributes([
                    'product_id'  => $product_id,
                    'company_id'  => $company_id,
                    'order_group' => 0,
                ])
                ->insert()
                ->getInsertId();

            $this->demoSaveEnglishLang($group_id, 'pjAttribute', ['name' => $group_name]);
        } else {
            $group_id = $group[0]['id'];
        }

        $item = pjAttributeModel::factory()
            ->select('t1.id')
            ->join('pjMultiLang', 't2.foreign_id=t1.id AND t2.model="pjAttribute"', 'inner')
            ->where('t2.field', 'name')
            ->where('t2.content', $item_name)
            ->where('t1.parent_id', $group_id)
            ->where('t1.product_id', $product_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($item)) {
            $item_id = pjAttributeModel::factory()
                ->setAttributes([
                    'product_id'  => $product_id,
                    'parent_id'   => $group_id,
                    'company_id'  => $company_id,
                    'order_group' => 0,
                    'order_item'  => 0,
                ])
                ->insert()
                ->getInsertId();

            $this->demoSaveEnglishLang($item_id, 'pjAttribute', ['name' => $item_name]);
        } else {
            $item_id = $item[0]['id'];
        }

        return [
            'parent_id' => $group_id,
            'id'        => $item_id,
        ];
    }

    private function demoAddStock($product_id, $size, $color, $article_number, $article_name, $ean, $qty, $price, $img_id, $status, $company_id)
    {
        $article_number = trim($article_number);
        $size_attr  = $this->demoGetOrCreateAttribute($product_id, 'Size', $size, $company_id);
        $color_attr = $this->demoGetOrCreateAttribute($product_id, 'Color', $color, $company_id);

        if (!$size_attr || !$color_attr) {
            return;
        }

        $existing = pjStockModel::factory()
            ->where('product_id', $product_id)
            ->where('TRIM(article_number)', $article_number)
            ->limit(1)
            ->findAll()
            ->getData();

        if (!empty($existing)) {
            $stock_id = (int) $existing[0]['id'];
            pjStockModel::factory()
                ->reset()
                ->set('id', $stock_id)
                ->modify([
                    'article_name' => $article_name,
                    'ean'          => $ean,
                    'qty'          => $qty,
                    'price'        => $price,
                    'image_id'     => $img_id,
                    'status'       => $status,
                ]);
            pjStockAttributeModel::factory()->where('stock_id', $stock_id)->eraseAll();
        } else {
            $stock_id = pjStockModel::factory()
                ->set('company_id', $company_id)
                ->set('product_id', $product_id)
                ->set('status', $status)
                ->set('image_id', $img_id)
                ->set('article_name', $article_name)
                ->set('article_number', $article_number)
                ->set('ean', $ean)
                ->set('qty', $qty)
                ->set('price', $price)
                ->insert()
                ->getInsertId();
        }

        if (!$stock_id) {
            return;
        }

        pjStockAttributeModel::factory()
            ->set('stock_id', $stock_id)
            ->set('product_id', $product_id)
            ->set('company_id', $company_id)
            ->set('attribute_parent_id', $size_attr['parent_id'])
            ->set('attribute_id', $size_attr['id'])
            ->insert();

        pjStockAttributeModel::factory()
            ->set('stock_id', $stock_id)
            ->set('product_id', $product_id)
            ->set('company_id', $company_id)
            ->set('attribute_parent_id', $color_attr['parent_id'])
            ->set('attribute_id', $color_attr['id'])
            ->insert();
    }

    private function demoDownloadImage($url)
    {
        if (empty($url)) {
            return false;
        }

        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);

        $ext = 'jpg';
        if ($mime == 'image/png') {
            $ext = 'png';
        } elseif ($mime == 'image/gif') {
            $ext = 'gif';
        } elseif ($mime == 'image/webp') {
            $ext = 'webp';
        }

        $filename = uniqid('demo_', true) . '.' . $ext;
        $folder = PJ_INSTALL_PATH . 'app/web/upload/csv-images/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $localPath = $folder . $filename;
        file_put_contents($localPath, $imageData);

        return str_replace(PJ_INSTALL_PATH, '', $localPath);
    }

    private function demoImportGalleryImages($product_id, array $data)
    {
        if (empty($data['image'])) {
            return null;
        }

        $images = explode(',', $data['image']);
        $GalleryModel = pjGalleryModel::factory();
        $Image = new pjImage();

        foreach ($images as $img) {
            $img = trim($img);
            if (!$img) {
                continue;
            }

            $tmp_file = $this->demoDownloadImage($img);
            if (!$tmp_file) {
                continue;
            }

            $full_path = PJ_INSTALL_PATH . $tmp_file;
            if (!$Image->loadImage($full_path)) {
                continue;
            }

            $hash = md5(uniqid(rand(), true));
            $source_path = PJ_UPLOAD_PATH . 'source/' . $product_id . '_' . $hash . '.' . $Image->getExtension();

            if (!$Image->saveImage(PJ_INSTALL_PATH . $source_path)) {
                continue;
            }

            $image_info = getimagesize(PJ_INSTALL_PATH . $source_path);
            $data_insert = [
                'foreign_id'    => $product_id,
                'model'         => 'pjProduct',
                'mime_type'     => $image_info['mime'],
                'source_path'   => $source_path,
                'source_size'   => filesize(PJ_INSTALL_PATH . $source_path),
                'source_width'  => $image_info[0],
                'source_height' => $image_info[1],
                'name'          => basename($img),
                'sort'          => 1,
                'created'       => date('Y-m-d H:i:s'),
            ];

            $data_insert = array_merge($data_insert, $this->demoBuildFromSource($Image, $data_insert));

            return (int) $GalleryModel
                ->reset()
                ->setAttributes($data_insert)
                ->insert()
                ->getInsertId();
        }

        return null;
    }

    private function demoBuildFromSource($Image, $data)
    {
        $arr = [];
        $source_full = PJ_INSTALL_PATH . $data['source_path'];
        $small_path  = 'app/web/upload/small/' . basename($data['source_path']);
        $medium_path = 'app/web/upload/medium/' . basename($data['source_path']);
        $large_path  = 'app/web/upload/large/' . basename($data['source_path']);

        $Image->loadImage($source_full);
        if ($Image->resize(80, 106) && $Image->saveImage(PJ_INSTALL_PATH . $small_path)) {
            $size = getimagesize(PJ_INSTALL_PATH . $small_path);
            $arr['small_path']   = $small_path;
            $arr['small_size']   = filesize(PJ_INSTALL_PATH . $small_path);
            $arr['small_width']  = $size[0];
            $arr['small_height'] = $size[1];
        }

        $Image->loadImage($source_full);
        if ($Image->resize(300, 400) && $Image->saveImage(PJ_INSTALL_PATH . $medium_path)) {
            $size = getimagesize(PJ_INSTALL_PATH . $medium_path);
            $arr['medium_path']   = $medium_path;
            $arr['medium_size']   = filesize(PJ_INSTALL_PATH . $medium_path);
            $arr['medium_width']  = $size[0];
            $arr['medium_height'] = $size[1];
        }

        $Image->loadImage($source_full);
        if ($Image->resize(320, 240) && $Image->saveImage(PJ_INSTALL_PATH . $large_path)) {
            $size = getimagesize(PJ_INSTALL_PATH . $large_path);
            $arr['large_path']   = $large_path;
            $arr['large_size']   = filesize(PJ_INSTALL_PATH . $large_path);
            $arr['large_width']  = $size[0];
            $arr['large_height'] = $size[1];
        }

        return $arr;
    }

    private function demoClearTempImages()
    {
        $tmp_folder = PJ_INSTALL_PATH . 'app/web/upload/csv-images/';
        if (!is_dir($tmp_folder)) {
            return;
        }

        $files = glob($tmp_folder . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function removeDemoCategories()
    {
        $count = 0;
        $localeId = $this->getDefaultLocaleId();
        $rows = pjMultiLangModel::factory()
            ->select('t1.foreign_id')
            ->where('t1.model', 'pjCategory')
            ->where('t1.field', 'name')
            ->where('t1.locale', $localeId)
            ->where("t1.content LIKE '" . self::DEMO_CATEGORY_PREFIX . "%'")
            ->findAll()
            ->getData();

        foreach ($rows as $row) {
            $catId = (int) $row['foreign_id'];
            if ($catId <= 1) {
                continue;
            }
            pjMultiLangModel::factory()
                ->where('model', 'pjCategory')
                ->where('foreign_id', $catId)
                ->eraseAll();
            pjProductCategoryModel::factory()
                ->where('category_id', $catId)
                ->eraseAll();
            pjCategoryModel::factory()->deleteNode($catId);
            $count++;
        }

        if ($count > 0) {
            pjCategoryModel::factory()->rebuildTree(1, 1);
        }

        return $count;
    }

    private function removeDemoImages()
    {
        $count = 0;
        $products = pjProductModel::factory()
            ->where('t1.company_id', $this->getCompanyId())
            ->where("(t1.model LIKE '" . self::DEMO_MODEL_PREFIX . "%' OR t1.sku LIKE 'DEMO-%' OR t1.sku LIKE '" . self::DEMO_MODEL_PREFIX . "%')")
            ->findAll()
            ->getData();

        foreach ($products as $product) {
            $galleries = pjGalleryModel::factory()
                ->where('foreign_id', $product['id'])
                ->where('model', 'pjProduct')
                ->findAll()
                ->getData();
            foreach ($galleries as $gallery) {
                foreach (['small', 'medium', 'large', 'source'] as $size) {
                    $key = $size . '_path';
                    if (!empty($gallery[$key]) && is_file($gallery[$key])) {
                        @unlink($gallery[$key]);
                    }
                }
                pjGalleryModel::factory()->set('id', $gallery['id'])->erase();
                $count++;
            }
        }

        return $count;
    }

    private function removeDemoProducts()
    {
        $count = 0;
        $companyId = $this->getCompanyId();
        $products = pjProductModel::factory()
            ->where('t1.company_id', $companyId)
            ->where("(t1.model LIKE '" . self::DEMO_MODEL_PREFIX . "%' OR t1.sku LIKE 'DEMO-%' OR t1.sku LIKE '" . self::DEMO_MODEL_PREFIX . "%')")
            ->findAll()
            ->getData();

        foreach ($products as $product) {
            $pid = (int) $product['id'];

            pjOrderStockModel::factory()->where('product_id', $pid)->eraseAll();
            pjStockAttributeModel::factory()->where('product_id', $pid)->eraseAll();
            pjStockModel::factory()->where('product_id', $pid)->eraseAll();
            pjProductCategoryModel::factory()->where('product_id', $pid)->eraseAll();
            try {
                pjProductBrandModel::factory()->where('product_id', $pid)->eraseAll();
            } catch (Exception $e) {
            }
            pjMultiLangModel::factory()->where('model', 'pjProduct')->where('foreign_id', $pid)->eraseAll();

            try {
                pjAttributeModel::factory()->where('product_id', $pid)->eraseAll();
            } catch (Exception $e) {
            }

            pjProductModel::factory()->set('id', $pid)->erase();
            $count++;
        }

        return $count;
    }

    private function getDemoClientData()
    {
        return [
            [
                'email'       => 'demo-alice@example.com',
                'client_name' => 'Alice Construction Ltd',
                'phone'       => '+44 20 7946 0958',
            ],
            [
                'email'       => 'demo-bob@example.com',
                'client_name' => "Bob's Plumbing Services",
                'phone'       => '+44 161 496 0123',
            ],
            [
                'email'       => 'demo-carol@example.com',
                'client_name' => 'Carol Retail Group',
                'phone'       => '+44 113 496 0789',
            ],
        ];
    }

    private function createDemoClients()
    {
        $clientIds = [];
        $companyId = $this->getCompanyId();

        foreach ($this->getDemoClientData() as $client) {
            $existing = pjClientModel::factory()
                ->where('t1.email', $client['email'])
                ->limit(1)
                ->findAll()
                ->getData();

            if (!empty($existing)) {
                $clientIds[] = (int) $existing[0]['id'];
                continue;
            }

            $clientId = pjClientModel::factory()
                ->setAttributes([
                    'company_id'  => $companyId,
                    'email'       => $client['email'],
                    'password'    => 'demo123',
                    'client_name' => $client['client_name'],
                    'phone'       => $client['phone'],
                    'url'         => 'https://example.com',
                    'status'      => 'T',
                    'created'     => date('Y-m-d H:i:s'),
                ])
                ->insert()
                ->getInsertId();

            if ($clientId && (int) $clientId > 0) {
                $clientIds[] = (int) $clientId;
            }
        }

        return $clientIds;
    }

    private function removeDemoClients()
    {
        $count = 0;
        $clients = pjClientModel::factory()
            ->where("t1.email LIKE 'demo-%@example.com'")
            ->findAll()
            ->getData();

        foreach ($clients as $client) {
            $cid = (int) $client['id'];
            try {
                pjAddressModel::factory()->where('client_id', $cid)->eraseAll();
            } catch (Exception $e) {
            }
            pjClientModel::factory()->set('id', $cid)->erase();
            $count++;
        }

        return $count;
    }

    private function createDemoOrders($clientIds)
    {
        if (empty($clientIds)) {
            return 0;
        }

        // Always replace demo quotes so dates match the dashboard week/previous-period ranges.
        $this->removeDemoOrders();

        $companyId = $this->getCompanyId();
        $localeId = $this->getDefaultLocaleId();

        $countryId = 0;
        try {
            $countries = pjBaseCountryModel::factory()
                ->where('t1.alpha_2', 'GB')
                ->limit(1)
                ->findAll()
                ->getData();
            if (empty($countries)) {
                $countries = pjBaseCountryModel::factory()->limit(1)->findAll()->getData();
            }
            if (!empty($countries)) {
                $countryId = (int) $countries[0]['id'];
            }
        } catch (Exception $e) {
        }

        // Dates aligned with dashboard week filter: current = last 7 days, previous = days 8–13 ago.
        $orderDefs = [
            ['client_idx' => 0, 'status' => 'new', 'days_ago' => 1, 'articles' => ['B2B-APRNBL', 'B2B-BEABL']],
            ['client_idx' => 1, 'status' => 'pending', 'days_ago' => 4, 'articles' => ['B2B-APRNGR']],
            ['client_idx' => 0, 'status' => 'completed', 'days_ago' => 6, 'articles' => ['B2B-BEAGR']],
            ['client_idx' => 2, 'status' => 'completed', 'days_ago' => 8, 'articles' => ['B2B-APRNNA']],
            ['client_idx' => 1, 'status' => 'pending', 'days_ago' => 10, 'articles' => ['B2B-BEABL', 'B2B-APRNBL']],
            ['client_idx' => 2, 'status' => 'new', 'days_ago' => 12, 'articles' => ['B2B-APRNGR']],
        ];

        $clients = $this->getDemoClientData();
        $orderCount = 0;

        foreach ($orderDefs as $def) {
            $clientIdx = $def['client_idx'] % count($clientIds);
            $clientId = $clientIds[$clientIdx];
            $client = $clients[$clientIdx];

            $lineTotal = 0;
            $lines = [];
            foreach ($def['articles'] as $article) {
                $stocks = pjStockModel::factory()
                    ->where('t1.company_id', $companyId)
                    ->where('TRIM(t1.article_number)', trim($article))
                    ->limit(1)
                    ->findAll()
                    ->getData();
                if (empty($stocks)) {
                    continue;
                }
                $stock = $stocks[0];
                $qty = 2;
                $price = (float) $stock['price'];
                $lineTotal += $price * $qty;
                $lines[] = ['stock' => $stock, 'qty' => $qty, 'price' => $price];
            }
            if (empty($lines)) {
                continue;
            }

            $shipping = 9.95;
            $tax = round($lineTotal * 0.2, 2);
            $total = round($lineTotal + $shipping + $tax, 2);
            $created = date('Y-m-d H:i:s', strtotime('-' . (int) $def['days_ago'] . ' days'));

            $orderId = pjOrderModel::factory()
                ->setAttributes([
                    'company_id'     => $companyId,
                    'uuid'           => pjUtil::uuid(),
                    'client_id'      => $clientId,
                    'locale_id'      => $localeId,
                    'status'         => $def['status'],
                    'payment_method' => 'bank',
                    'price'          => $lineTotal,
                    'discount'       => 0,
                    'insurance'      => 0,
                    'shipping'       => $shipping,
                    'tax'            => $tax,
                    'total'          => $total,
                    'notes'          => self::DEMO_ORDER_NOTE,
                    'created'        => $created,
                    'ip'             => '127.0.0.1',
                    'same_as'        => 1,
                    'b_name'         => $client['client_name'],
                    'b_country_id'   => $countryId,
                    'b_city'         => 'London',
                    'b_zip'          => 'SW1A 1AA',
                    'b_address_1'    => '1 Demo Street',
                    's_name'         => $client['client_name'],
                    's_country_id'   => $countryId,
                    's_city'         => 'London',
                    's_zip'          => 'SW1A 1AA',
                    's_address_1'    => '1 Demo Street',
                ])
                ->insert()
                ->getInsertId();

            if (!$orderId) {
                continue;
            }

            foreach ($lines as $line) {
                pjOrderStockModel::factory()
                    ->setAttributes([
                        'order_id'   => $orderId,
                        'company_id' => $companyId,
                        'stock_id'   => $line['stock']['id'],
                        'product_id' => $line['stock']['product_id'],
                        'price'      => $line['price'],
                        'qty'        => $line['qty'],
                    ])
                    ->insert();
            }

            $orderCount++;
        }

        return $orderCount;
    }

    private function removeDemoOrders()
    {
        $count = 0;
        $orders = pjOrderModel::factory()
            ->where("t1.notes LIKE '%" . self::DEMO_ORDER_NOTE . "%'")
            ->findAll()
            ->getData();

        foreach ($orders as $order) {
            $oid = (int) $order['id'];
            try {
                pjOrderExtraModel::factory()->where('order_id', $oid)->eraseAll();
            } catch (Exception $e) {
            }
            pjOrderStockModel::factory()->where('order_id', $oid)->eraseAll();

            if (!empty($order['uuid']) && class_exists('pjInvoiceModel')) {
                try {
                    $invoices = pjInvoiceModel::factory()
                        ->where('t1.order_id', $order['uuid'])
                        ->findAll()
                        ->getData();
                    foreach ($invoices as $inv) {
                        if (class_exists('pjInvoiceItemModel')) {
                            pjInvoiceItemModel::factory()
                                ->where('invoice_id', $inv['id'])
                                ->eraseAll();
                        }
                        pjInvoiceModel::factory()->set('id', $inv['id'])->erase();
                    }
                } catch (Exception $e) {
                }
            }

            pjOrderModel::factory()->set('id', $oid)->erase();
            $count++;
        }

        return $count;
    }
}
