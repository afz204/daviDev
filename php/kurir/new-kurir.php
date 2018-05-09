<?php 

$provinsi = $config->Products('id, name', 'provinces');

?>
<div class="row justify-content-center" <?=$access['create']?>>
    <div class="col-12 col-sm-8 col-lg-6">

        <div id="messageKurir" class="alert alert-dismissible fade show" role="alert">
            <div id="isiPesan"></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="card text-white bg-info mb-3">
            <div class="card-header">
                New Kurir
            </div>
            <div class="card-body">

                <form  method="post" id="newKurir" data-parsley-validate="" autocomplete="off">
                    <div class="form-group">
                        <input type="text" name="nameKurir" id="nameKurir" placeholder="nama kurir" class="form-control" data-parsley-minLength="3" required>
                        <input type="hidden" name="adminKurir" id="adminKurir" value="<?=$admin[0]['user_id']?>" class="form-control" data-parsley-minLength="3" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="emailKurir"
                               data-parsley-minLength="5"
                               data-parsley-type="email" placeholder="examples@domain.ext" id="emailKurir" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="phoneKurir"
                               data-parsley-minLength="5"
                               data-parsley-type="number" id="phoneKurir" placeholder="nomor handphone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="waKurir"
                               data-parsley-minLength="5"
                               data-parsley-type="number" id="waKurir" placeholder="nomor whatsapp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="ProvinsiCorporate" id="ProvinsiCorporate" required>
                            <option value="">:: provinsi ::</option>
                            <?php while ($row = $provinsi->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$row->id?>"><?=$row->name?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="KotaCorporate" id="KotaCorporate" required>
                            <option value="">:: kota ::</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="kecamatanCorporate" id="kecamatanCorporate" required>
                            <option value="">:: kecamatan ::</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="kelurahanCorporate" id="kelurahanCorporate" required>
                            <option value="">:: kelurahan ::</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="alamatKurir" placeholder="alamat kurir lengkap" id="alamatKurir" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-outline-light" >submit</button>

                </form>
            </div>
        </div>
    </div>
</div>