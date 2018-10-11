<?php 

$data = $config->getData('*', 'kurirs', "id = '".$_GET['id']."'");
// $provinsi = $config->Products('id, name', 'provinces');
?>

<div class="card">
    <div class="card-body">
        <div class="col-md-8">
            <form  method="post" id="udpatekurir" data-parsley-validate="" autocomplete="off">
                <div class="form-group">
                    <input type="text" name="nameKurir" id="nameKurir" value="<?=$data['nama_kurir']?>" placeholder="nama kurir" class="form-control" data-parsley-minLength="3" required>
                    <input type="hidden" name="idKurir" id="idKurir" value="<?=$data['id']?>" placeholder="nama kurir" class="form-control" data-parsley-minLength="3" required>
                </div>
                
                <div class="form-group">
                    <input type="text" name="emailKurir"
                            data-parsley-minLength="5"
                            data-parsley-type="email" placeholder="examples@domain.ext" id="emailKurir" value="<?=$data['email']?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="text" name="phoneKurir"
                            data-parsley-minLength="5"
                            data-parsley-type="number" id="phoneKurir" value="<?=$data['phone']?>" placeholder="nomor handphone" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="text" name="waKurir"
                            data-parsley-minLength="5"
                            data-parsley-type="number" id="waKurir" value="<?=$data['wa']?>" placeholder="nomor whatsapp" class="form-control" required>
                </div>
                <div class="form-group">
                    <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="alamatKurir" placeholder="alamat kurir lengkap" id="alamatKurir" class="form-control" rows="2" required><?=$data['alamat']?></textarea>
                </div>
                <button type="submit" class="btn btn-block btn-outline-success" >update</button>

            </form>
        </div>
        <div class="col-md-4">
        </div>
    </div>
</div>