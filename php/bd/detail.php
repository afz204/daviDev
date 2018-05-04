<?php 

$d = $_GET['id'];
	$prod = $config->Products('category_id, subcategory_id, name_product, cost_price, selling_price, available_on, sort_desc, full_desc, note, images', 'products WHERE id = '.$d);

	$product = $prod->fetch(PDO::FETCH_LAZY);

	$category = $config->Category();
	$cat = $config->Products('id, name', 'categories WHERE category_id != 0 ');
    $province = $config->Province();

?>

<div class="card" <?=$access['create']?>>
    <div class="row justify-content-center card-body">
        <div id="imagesProduct" class="col-12 col-md-4 col-lg-4">
            <div class="card-header">
                <h5 class="card-title">Images Product</h5>
            </div>
            <div class="card-body">
                <picture>
				  <!-- <source srcset="http://localhost/bungdav/assets/images/product/bunga_malem_jumat.jpg" type="image/svg+xml"> -->
				  <!-- <img src="<?=URL.'assets/images/product/bunga_malem_jumat.jpg'?>" class="img-fluid img-thumbnail" alt="..."> -->
				  <a href="<?=URL.'assets/images/product/'.$product['images']?>" data-toggle="lightbox" data-gallery="example-gallery">
					    <img src="<?=URL.'assets/images/product/'.$product['images']?>" class="img-fluid img-thumbnail">
					</a>
				</picture>

                <div id="kv-success-2" class="alert alert-success" style="margin-top:10px;display:none"></div>
            </div>
        </div>
        <div id="detailProduct" class="col-12 col-md-8 col-lg-8">
            <div class="card-header">
                <h5 class="card-title">Detail Product</h5>
            </div>
            <div class="card-body">

                <form  method="post" id="newProduct" data-parsley-validate="" autocomplete="off" enctype="multipart/form-data">


                    <div class="form-group">
                        <label for="categoryProduct">Category Product</label>
                        <select class="form-control" name="categoryProduct" id="categoryProduct" readonly>
                            <option value="">:: category ::</option>
                            <?php while ($row = $category->fetch(PDO::FETCH_LAZY)){
                            	?>
                                <option value="<?=$row->id?>" <?=$row['id'] == $product['category_id'] ? 'selected' : ''?>><?=$row->name?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subCatProduct">Sub Category Product</label>
                        <select class="form-control" name="subCatProduct" id="subCatProduct" readonly>
                        	<?php while ($cols = $cat->fetch(PDO::FETCH_LAZY)) { ?>
                            <option value="<?=$cols['id']?>" <?=$cols['id'] == $product['subcategory_id'] ? 'selected' : ''?>><?=$cols['name']?></option>
                            <?php  } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nameProduct">Nama Product</label>
                        <input type="text" name="nameProduct" id="nameProduct" class="form-control" value="<?=str_replace('_', ' ', $product->name_product)?>" data-parsley-minLength="3" readonly>
                        <input type="hidden" name="adminProduct" id="adminProduct" value="<?=$admin['id']?>">
                    </div>

                    <div class="form-group">
                        <label for="tagsProduct">Tags Product</label>
                        <input type="text" name="tagsProduct" id="tagsProduct" placeholder="multiple: use 'koma'" class="form-control" data-parsley-minLength="3" readonly>
                    </div>

                    <div class="form-group">
                        <label for="tagsProduct">Cost Price Product</label>
                        <input type="text" name="costProduct" id="costProduct" data-parsley-type="number" value="<?=$config->formatPrice($product->cost_price)?>" class="form-control" data-parsley-minLength="3" readonly>
                    </div>

                    <div class="form-group">
                        <label for="tagsProduct">Selling Price Product</label>
                        <input type="text" name="sellProduct" id="sellProduct" data-parsley-type="number" value="<?=$config->formatPrice($product->selling_price)?>" class="form-control" data-parsley-minLength="3" readonly>
                    </div>

                    <div class="form-group " id="lokasiProduct">
                        <select class="simple-select2 w-100"  id="simple-select2" multiple>
                            <?php while ($rows = $province->fetch(PDO::FETCH_LAZY)){ ?>

                            <option value="<?=$rows->id?>"><?=$rows->name?></option>

                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="shortDesc">Short Description</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" data-parsley-maxLength="255" name="sort" id="shortDesc" class="form-control" rows="2" readonly><?=$product->sort_desc?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fullDesc">Full Description</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" value="<?=$product->note?>" name="sort" id="fullDesc" class="form-control" rows="5" readonly><?=$product->full_desc?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fullDesc">Important Notes</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" value="<?=$product->note?>" name="note" id="noteProduct" class="form-control" rows="5" readonly><?=$product->note?></textarea>
                    </div>

                    <button type="submit" class="btn btn-block btn-outline-primary" >submit</button>

                </form>
            </div>
        </div>
    </div>
</div>