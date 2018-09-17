<?php

    $category = $config->Category();
    $province = $config->Province();
    $tags = $config->Products('id,name', "categories WHERE parent_id != '0'");
    
?>
<div class="card" <?=$access['create']?>>
    <div class="row justify-content-center card-body">
        <div id="imagesProduct" class="col-12 col-md-4 col-lg-4">
            <div class="card-header">
                <h5 class="card-title">Images Product</h5>
            </div>
            <div class="card-body">
                <form id="uploadImagesProduct" method="post" enctype="multipart/form-data" >
                    <div class="form-group">
                        <input type="hidden" id="ImagesProductID" name="ImagesProductID">
                        <input type="hidden" id="ImagesName" name="ImagesName">
                        <div class="file-loading">
                            <input type="file" id="images" name="images[]" multiple>
                        </div>
                        <br>
                    </div>
                </form>
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
                        <label for="codeProduct">Code Product</label>
                        <input type="text" name="codeProduct" id="codeProduct" placeholder="BDxxxxxx" class="form-control" data-parsley-minLength="3" required="">
                    </div>

                    <div class="form-group">
                        <label for="categoryProduct">Category Product</label>
                        <select class="form-control" name="categoryProduct" id="categoryProduct" required="">
                            <option value="">:: category ::</option>
                            <?php while ($row = $category->fetch(PDO::FETCH_LAZY)){ ?>
                                <option value="<?=$row->id?>"><?=$row->name?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subCatProduct">Sub Category Product</label>
                        <select class="form-control" name="subCatProduct" id="subCatProduct" required="">
                            <option value="">:: sub category ::</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nameProduct">Nama Product</label>
                        <input type="text" name="nameProduct" id="nameProduct" class="form-control" data-parsley-minLength="3" required="">
                        <input type="hidden" name="adminProduct" id="adminProduct" value="<?=$admin[0]['user_id']?>">
                    </div>

                    <div class="form-group">
                        <label for="tagsProduct">Tags Product</label>
                        <!-- <input type="text" name="tagsProduct" id="tagsProduct" placeholder="multiple: use 'koma'" class="form-control" data-parsley-minLength="3" required=""> -->
                        <select class="form-control simple-select2 w-100" multiple="multiple" name="tagsProduct" id="tagsProduct">
                            <?php while($list = $tags->fetch(PDO::FETCH_LAZY)){ ?>
                                <option value="<?=$list['id']?>"><?=$list['name']?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tagsProduct">Cost Price Product</label>
                        <input type="text" name="costProduct" id="costProduct" data-parsley-type="number" class="form-control" data-parsley-minLength="3" required="">
                    </div>

                    <div class="form-group">
                        <label for="tagsProduct">Selling Price Product</label>
                        <input type="text" name="sellProduct" id="sellProduct" data-parsley-type="number" class="form-control" data-parsley-minLength="3" required="">
                    </div>

                    <div class="form-group">
                        <label for="availableProduct">Available In Product</label>
                        <select class="form-control" name="listLokasi" id="listLokasi">
                            <option value="1" selected>:: all province ::</option>
                            <option value="2">:: only province ::</option>
                        </select>
                    </div>

                    <div class="form-group hidden" id="lokasiProduct">
                        <select class="simple-select2 w-100"  id="simple-select2" multiple>
                            <?php while ($rows = $province->fetch(PDO::FETCH_LAZY)){ ?>

                            <option value="<?=$rows->id?>"><?=$rows->name?></option>

                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="shortDesc">Short Description</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" data-parsley-maxLength="255" name="sort" id="shortDesc" class="form-control" rows="2" required=""></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fullDesc">Full Description</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="sort" id="fullDesc" class="form-control" rows="5" required=""></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fullDesc">Important Notes</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="note" id="noteProduct" class="form-control" rows="5" required=""></textarea>
                    </div>

                    <button type="submit" class="btn btn-block btn-outline-primary">submit</button>

                </form>
            </div>
        </div>
    </div>
</div>