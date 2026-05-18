<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit SKU</h2>
        <p class="m-b-none"><i class="fa fa-info-circle"></i> Edit single flatfile row</p>
    </div>
</div>

<div class="row wrapper wrapper-content animated fadeInRight">

    <div class="col-lg-12">

        <div class="row">

            <!-- LEFT SIDE EDIT -->

            <div class="col-md-6">

                <form method="post">

                    <div class="panel panel-default">
                        <div class="panel-body">

                            <div class="form-group">
                                <label>Status</label>

                                <select name="status" class="form-control">
                                    <option value="1" <?php echo $tpl['row']['status'] == 1 ? 'selected' : ''; ?>>Available</option>
                                    <option value="0" <?php echo $tpl['row']['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                                </select>

                            </div>


                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" name="sku" value="<?php echo $tpl['row']['sku']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Model</label>
                                <input id="model" type="text" name="model" value="<?php echo $tpl['row']['model']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Model Name</label>
                                <input type="text" name="model_name" value="<?php echo $tpl['row']['model_name']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Article Number</label>
                                <input type="text" name="article_number" value="<?php echo $tpl['row']['article_number']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Article Name</label>
                                <input type="text" name="article_name" value="<?php echo $tpl['row']['article_name']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>EAN</label>
                                <input type="text" name="ean" value="<?php echo $tpl['row']['ean']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Stock</label>
                                <input type="text" name="qty" value="<?php echo $tpl['row']['qty']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Price</label>
                                <input type="text" name="price" value="<?php echo $tpl['row']['price']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Name (EN)</label>
                                <input type="text" name="name_en" value="<?php echo $tpl['row']['name_en']; ?>" class="form-control">
                            </div>


                            <div class="form-group">
                                <label>Short Description</label>
                                <textarea name="short_desc_en" class="form-control"><?php echo $tpl['row']['short_desc_en']; ?></textarea>
                            </div>


                            <div class="form-group">
                                <label>Full Description</label>
                                <textarea name="full_description_en" class="form-control"><?php echo $tpl['row']['full_description_en']; ?></textarea>
                            </div>

                            <button class="btn btn-primary btn-lg">Save</button>

                        </div>
                    </div>

                </form>

            </div>


            <!-- RIGHT SIDE PREVIEW -->

            <div class="col-md-6">

                <div class="panel panel-default">
                    <div class="panel-body">

                        <p>
                            <img src="<?php echo PJ_INSTALL_URL . $tpl['row']['image']; ?>" width="200">
                        </p>

                        <p><b>Model:</b> <?php echo $tpl['row']['model']; ?></p>
                        <p><b>Model Name:</b> <?php echo $tpl['row']['model_name']; ?></p>
                        <p><b>SKU:</b> <?php echo $tpl['row']['sku']; ?></p>
                        <p><b>Article Number:</b> <?php echo $tpl['row']['article_number']; ?></p>
                        <p><b>Article Name:</b> <?php echo $tpl['row']['article_name']; ?></p>
                        <p><b>EAN:</b> <?php echo $tpl['row']['ean']; ?></p>
                        <p><b>Stock:</b> <?php echo $tpl['row']['qty']; ?></p>
                        <p><b>Price:</b> <?php echo $tpl['row']['price']; ?></p>

                        <p><b>Name:</b> <?php echo $tpl['row']['name_en']; ?></p>

                        <p><b>Short Description:</b> <?php echo $tpl['row']['short_desc_en']; ?></p>

                        <p><b>Full Description:</b> <?php echo $tpl['row']['full_description_en']; ?></p>

                    </div>
                </div>

            </div>


        </div>

    </div>

</div>