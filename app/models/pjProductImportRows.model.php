<?php
if (!defined("ROOT_PATH")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjProductImportRowsModel extends pjAppModel
{
    protected $primaryKey = 'id';

    protected $table = 'product_import_rows';

    protected $schema = array(

        array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),

        array('name' => 'import_id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'company_id', 'type' => 'int', 'default' => ':NULL'),

        array('name' => 'w_number', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'model', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'model_name', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'sku', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'status', 'type' => 'int', 'default' => '1'),

        array('name' => 'brand', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'category', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'article_number', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'article_name', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'ean', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'qty', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),

        array('name' => 'size', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'color', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'name_en', 'type' => 'varchar', 'default' => ':NULL'),

        array('name' => 'short_desc_en', 'type' => 'text', 'default' => ':NULL'),
        array('name' => 'full_description_en', 'type' => 'text', 'default' => ':NULL'),

        array('name' => 'image', 'type' => 'text', 'default' => ':NULL'),

        array('name' => 'row_status', 'type' => 'varchar', 'default' => 'active'),
        array('name' => 'sync_status', 'type' => 'varchar', 'default' => 'pending'),

        array('name' => 'created_at', 'type' => 'datetime', 'default' => ':NULL'),

    );

    public static function factory($attr = array())
    {
        return new self($attr);
    }
}
