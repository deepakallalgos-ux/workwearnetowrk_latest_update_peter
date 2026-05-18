<?php
$flatfile_mode = isset($tpl['flatfile_mode']) ? $tpl['flatfile_mode'] : true;
$arr = isset($tpl['arr']) ? $tpl['arr'] : array();
$stock_arr = isset($tpl['stock_arr']) ? $tpl['stock_arr'] : array();
$brand_name = isset($tpl['brand_name']) ? $tpl['brand_name'] : '';
$category_name = isset($tpl['category_name']) ? $tpl['category_name'] : '';
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Flatfile SKU Editor</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <form method="post">

        <?php if (!empty($stock_arr)) {
            $stock = $stock_arr[0];
        ?>
            <input type="hidden" name="product_id" value="<?php echo $tpl['arr']['id']; ?>">
            <input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>">
            <input type="hidden" name="stock_id" value="<?php echo $tpl['stock_arr'][0]['id']; ?>">
            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <label><?php __('product_status'); ?></label>
                        <select name="status[<?php echo $stock['id']; ?>]" class="form-control">
                            <option value="T" <?php echo ($stock['status'] == 'T') ? 'selected' : ''; ?>>Available</option>
                            <option value="F" <?php echo ($stock['status'] == 'F') ? 'selected' : ''; ?>>Hidden</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?php __('product_sku'); ?></label>
                        <input name="sku" class="form-control" value="<?php echo isset($arr['sku']) ? $arr['sku'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label><?php __('product_name'); ?></label>
                        <input class="form-control" name="name"
                            value="<?php echo isset($arr['i18n'][1]['name']) ? $arr['i18n'][1]['name'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label><?php __('product_model'); ?></label>
                        <input id="model" name="model" class="form-control" value="<?php echo isset($arr['model']) ? $arr['model'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label><?php __('product_model_name'); ?></label>
                        <input name="model_name" class="form-control" value="<?php echo isset($arr['model_name']) ? $arr['model_name'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php __('product_brand'); ?></label>

                        <select name="brand_id" id="brand_id" class="form-control select-item required">

                            <?php
                            foreach ($tpl['brand_arr'] as $brand) {
                            ?>
                                <option value="<?php echo $brand['data']['id']; ?>"
                                    <?php echo in_array($brand['data']['id'], $tpl['pb_arr']) ? 'selected="selected"' : ''; ?>>
                                    <?php echo str_repeat("-----", $brand['deep']) . " " . pjSanitize::html($brand['data']['name']); ?>
                                </option>
                            <?php
                            }
                            ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?php __('product_category'); ?></label>

                        <select name="category_id" id="category_id"

                            class="form-control select-item required">

                            <?php
                            foreach ($tpl['category_arr'] as $category) {
                            ?>
                                <option value="<?php echo $category['data']['id']; ?>"
                                    <?php echo in_array($category['data']['id'], $tpl['pc_arr']) ? 'selected="selected"' : ''; ?>>

                                    <?php echo str_repeat("-----", $category['deep']) . " " . pjSanitize::html($category['data']['name']); ?>

                                </option>
                            <?php
                            }
                            ?>

                        </select>

                    </div>

                    <div class="form-group">
                        <label><?php __('product_article_number'); ?></label>
                        <input name="stock_article_number[<?php echo $stock['id']; ?>]" class="form-control"
                            value="<?php echo $stock['article_number']; ?>">
                    </div>

                    <div class="form-group">
                        <label><?php __('product_article_name'); ?></label>
                        <input name="stock_article_name[<?php echo $stock['id']; ?>]" class="form-control"
                            value="<?php echo $stock['article_name']; ?>">
                    </div>

                    <div class="form-group">
                        <label><?php __('product_ean'); ?></label>
                        <input name="stock_ean[<?php echo $stock['id']; ?>]" class="form-control"
                            value="<?php echo $stock['ean']; ?>">
                    </div>

                    <div class="form-group">
                        <label><?php __('product_stock_qty'); ?></label>
                        <input name="stock_qty[<?php echo $stock['id']; ?>]" class="form-control"
                            value="<?php echo $stock['qty']; ?>">
                    </div>

                    <div class="form-group">
                        <label><?php __('product_stock_price'); ?></label>
                        <input name="stock_price[<?php echo $stock['id']; ?>]" class="form-control"
                            value="<?php echo $stock['price']; ?>">
                    </div>
                    <div class="form-group">
                        <label><?php __('import_size'); ?></label>
                        <input name="size"
                            class="form-control"
                            value="<?php echo $tpl['stock_arr'][0]['size']; ?>">
                    </div>

                    <div class="form-group">
                        <label><?php __('import_color'); ?></label>
                        <input name="color"
                            class="form-control"
                            value="<?php echo $tpl['stock_arr'][0]['color']; ?>">
                    </div>


                    <div class="form-group">
                        <label><?php __('product_short_desc'); ?></label>
                        <textarea name="short_desc" class="form-control"><?php echo isset($arr['i18n'][1]['short_desc']) ? $arr['i18n'][1]['short_desc'] : ''; ?></textarea>
                    </div>



                </div>


                <div class="col-md-6">

                    <div class="form-group">
                        <label><?php __('product_full_desc'); ?></label>
                        <textarea name="full_desc" class="form-control mceEditor" rows="20"><?php echo isset($arr['i18n'][1]['full_desc']) ? $arr['i18n'][1]['full_desc'] : ''; ?></textarea>
                    </div>

                    <div class="form-group">

                        <label><?php __('product_stock_image'); ?></label><br>
                        <?php if (!empty($stock['small_path'])) { ?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btnImageStock" rel="<?php echo $stock['image_id']; ?>">
                                <img src="<?php echo PJ_INSTALL_URL . $stock['small_path']; ?>"
                                    class="in-stock"
                                    style="max-width:350px;">
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary btn-outline btn-sm btnImageStock">
                                <?php __('product_stock_choose_image'); ?>
                            </a>
                        <?php } ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&action=pjActionUpdate&id=<?php echo $stock['product_id']; ?>&tab=photos"
                            class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader"
                            style="margin-left:10px;">
                            <?php __('lblUploadManageImages') ?>
                        </a>
                        <div class="boxStockImageId">
                            <div class="form-group">
                                <input type="hidden"
                                    name="stock_image_id[<?php echo $stock['id']; ?>]"
                                    value="<?php echo $stock['image_id']; ?>"
                                    class="required" />
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalImageStock" tabindex="-1" role="dialog" aria-labelledby="myImageStockibutesLabel">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myImageStockibutesLabel"><?php __('product_stock_img_title'); ?></h4>
                                </div>
                                <div class="modal-body">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php __('btnClose'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>

            </div>

        <?php } ?>
        <div class="hr-line-dashed"></div>

        <div class="clearfix">
            <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                <span class="ladda-label"><?php __('btnSave'); ?></span>
                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
            </button>
            <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionProductsFlatFileIndex"><?php __('btnCancel'); ?></a>
        </div><!-- /.clearfix -->
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        var model = document.getElementById("model");

        if (model) {
            model.addEventListener("change", function() {
                alert("WARNING: If you change the model you break the product family.");
            });
        }

    });
</script>