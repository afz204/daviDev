<?php
    $id = $_GET['id'];
    $provinsi = $config->Products('id, name', 'provinces');
    $pics = $config->getData('*', 'florist', "id = '". $id ."'");
    $data = [ $pics ];
?>
<div class="card" id="form_florist" >
    <div class="card-body">
    <form id="formEditFlorist" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">FloristName</label>
                <input type="text" class="form-control" id="FloristName" autocomplete="text" placeholder="" value="<?=isset($data[0]['FloristName']) ? $data[0]['FloristName'] : '' ?>" required="">
                <input type="hidden" class="form-control" id="IDFlorist" autocomplete="text" placeholder="" value="<?=$id?>" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">Email</label>
                <input type="email" data-parsley-type="email" class="form-control" id="Email" autocomplete="email" placeholder="" value="<?=isset($data[0]['Email']) ? $data[0]['Email'] : '' ?>" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">Username</label>
                <input type="text" class="form-control" id="Username" placeholder="" autocomplete="text" value="<?=isset($data[0]['Username']) ? $data[0]['Username'] : '' ?>" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="firstName">mobile_phone</label>
                <input type="text" class="form-control" data-parsley-type="number" id="mobile_phone" autocomplete="text" placeholder="" value="<?=isset($data[0]['mobile_phone']) ? $data[0]['mobile_phone'] : '' ?>" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
            <label for="ProvinsiCorporate">Provinsi</label>
                        <select class="custom-select my-1 mr-sm-2" name="ProvinsiCorporate" id="ProvinsiCorporate" required="">
                            <option value="">:: provinsi ::</option>
                            <?php while ($row = $provinsi->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$row->id?>" <?=$data[0]['province'] == $row->id ? 'selected' : '' ?>><?=$row->name?></option>
                            <?php } ?>
                        </select>
            </div>
            <div class="col-md-6 mb-3">
            <label for="usernameAdmin">Kota</label>
                        <select class="custom-select my-1 mr-sm-2" name="KotaCorporate" id="KotaCorporate" required>
                            <option value="">:: kota ::</option>
                        </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
            <label for="usernameAdmin">Kecamatan</label>
                        <select class="form-control" name="kecamatanCorporate" id="kecamatanCorporate" required>
                            <option value="">:: Kecamatan ::</option>
                        </select>
            </div>
            <div class="col-md-6 mb-3">
            <label for="usernameAdmin">Kelurahan</label>
                        <select class="form-control" name="kelurahanCorporate" id="kelurahanCorporate" required>
                            <option value="">:: Kecamatan ::</option>
                        </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
            <label for="usernameAdmin">Alamat</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="alamatCorporate" id="alamatCorporate" class="form-control" cols="5" required><?=isset($data[0]['alamat']) ? $data[0]['alamat'] : '' ?></textarea>
            </div>
        </div>
        <hr class="mb-4">
        <button class="btn btn-success btn-lg btn-block" type="submit">update florist</button>
        </form>
    </div>
</div>