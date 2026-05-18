<?php
$flatfile_mode = true;
$product = isset($tpl['arr']) ? $tpl['arr'] : array();
$stock   = isset($tpl['stock']) ? $tpl['stock'] : array();
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit SKU (Flatfile)</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
<form action="" method="post">

<div class="row">

<div class="col-md-6">

<div class="form-group">
<label>Status</label>
<select name="status" class="form-control">
<option value="T" <?php echo (isset($stock['status']) && $stock['status']=='T')?'selected':''; ?>>Available</option>
<option value="F" <?php echo (isset($stock['status']) && $stock['status']=='F')?'selected':''; ?>>Unavailable</option>
</select>
</div>

<div class="form-group">
<label>SKU</label>
<input type="text" name="sku" class="form-control" value="<?php echo isset($product['sku']) ? $product['sku'] : ''; ?>">
</div>

<div class="form-group">
<label>Model</label>
<input id="model" type="text" name="model" class="form-control" value="<?php echo isset($product['model']) ? $product['model'] : ''; ?>">
</div>

<div class="form-group">
<label>Model Name</label>
<input type="text" name="model_name" class="form-control" value="<?php echo isset($product['model_name']) ? $product['model_name'] : ''; ?>">
</div>

<div class="form-group">
<label>Article Number</label>
<input type="text" name="article_number" class="form-control" value="<?php echo isset($stock['article_number']) ? $stock['article_number'] : ''; ?>">
</div>

<div class="form-group">
<label>Article Name</label>
<input type="text" name="article_name" class="form-control" value="<?php echo isset($stock['article_name']) ? $stock['article_name'] : ''; ?>">
</div>

<div class="form-group">
<label>EAN</label>
<input type="text" name="ean" class="form-control" value="<?php echo isset($stock['ean']) ? $stock['ean'] : ''; ?>">
</div>

<div class="form-group">
<label>Size</label>
<input type="text" name="size" class="form-control" value="<?php echo isset($stock['size']) ? $stock['size'] : ''; ?>">
</div>

<div class="form-group">
<label>Color</label>
<input type="text" name="color" class="form-control" value="<?php echo isset($stock['color']) ? $stock['color'] : ''; ?>">
</div>

<div class="form-group">
<label>Stock</label>
<input type="number" name="qty" class="form-control" value="<?php echo isset($stock['qty']) ? $stock['qty'] : ''; ?>">
</div>

<div class="form-group">
<label>Price</label>
<input type="text" name="price" class="form-control" value="<?php echo isset($stock['price']) ? $stock['price'] : ''; ?>">
</div>

<div class="form-group">
<label>Name (EN)</label>
<input type="text" name="name_en" class="form-control"
value="<?php echo isset($product['i18n'][1]['name']) ? $product['i18n'][1]['name'] : ''; ?>">
</div>

<div class="form-group">
<label>Short Description</label>
<textarea name="short_desc_en" class="form-control"><?php echo isset($product['i18n'][1]['short_desc']) ? $product['i18n'][1]['short_desc'] : ''; ?></textarea>
</div>

<button class="btn btn-primary btn-lg">Save</button>

</div>

<div class="col-md-6">

<div class="form-group">
<label>Full Description</label>
<textarea id="full_desc" name="full_description_en" class="form-control" rows="20"><?php echo isset($product['i18n'][1]['full_desc']) ? $product['i18n'][1]['full_desc'] : ''; ?></textarea>
</div>

<?php if(!empty($stock['image'])){ ?>
<img src="<?php echo PJ_INSTALL_URL . $stock['image']; ?>" style="max-width:300px;">
<?php } ?>

</div>

</div>

</form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

var model = document.getElementById("model");

if(model){

model.addEventListener("change", function(){

alert("WARNING: If you change the model you break the product family.");

});

}

});
</script>
