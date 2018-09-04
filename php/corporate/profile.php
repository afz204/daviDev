<?php
$id = $_GET['id'];
$stmt = $config->runQuery("SELECT corporates.id, corporates.CorporateUniqueID, corporates.nama, corporates.telp, corporates.handphone, corporates.fax, corporates.email, corporates.website, corporates.cp, corporates.alamat, corporates.kelurahan, 
corporates.kecamatan, corporates.kodepos, corporates.created_at, bidang_usahas.category, states.lokasi_nama FROM corporates
INNER JOIN bidang_usahas ON bidang_usahas.id = corporates.bidang
INNER JOIN states ON states.lokasi_ID = corporates.provinsi WHERE corporates.id = :a");
$stmt->execute(array(':a' => $id));
$info = $stmt->fetch(PDO::FETCH_LAZY);

$pic = $config->ProductsJoin('corporate_pics.id, corporate_pics.corporate_id, corporate_pics.name, corporate_pics.nomor, corporate_pics.created_at, provinces.name as Provinsi, regencies.name as Kota,
    districts.name as Kec, villages.name as Kel, corporate_pics.alamat', 'corporate_pics',
'LEFT JOIN provinces on provinces.id = corporate_pics.province_id
LEFT JOIN regencies on regencies.id = corporate_pics.city
LEFT JOIN districts on districts.id = corporate_pics.kecamatan
LEFT JOIN villages on villages.id = corporate_pics.kelurahan', " WHERE corporate_id = '". $info['CorporateUniqueID'] ."' ORDER BY corporate_pics.created_at DESC");
$provinsi = $config->Products('id, name', 'provinces');
?>
<div class="row" id="newAdmin" <?=$access['read']?>>
    <div class="col-12 col-sm-8 col-lg-8 order-md-1">
        <div class="card text-secondary bg-white mb-3">
            <div class="card-header">
                Profile Corporate
            </div>
            <div class="card-body">
                <form action="" data-parsley-validate="" method="post" autocomplete="off">
                    <div class="form-group" >
                        <label for="usernameAdmin">Nama Corporate</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['nama']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Bidang Usaha</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['category']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Nomor Telp</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['telp']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Handphone</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['handphone']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Nomor Fax</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['fax']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Alamat Email</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['email']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Alamat Website</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['website']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Contact Person</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['cp']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Alamat</label>
                        <textarea style="text-transform: capitalize;"class="form-control" cols="5" readonly><?=$info['nama']?> </textarea>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Kelurahan</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['kelurahan']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Kecamatan</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['kecamatan']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Provinsi</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['lokasi_nama']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Kode Pos</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=$info['kodepos']?>" readonly>
                    </div>
                    <div class="form-group" >
                        <label for="usernameAdmin">Bergabung Sejak</label>
                        <input style="text-transform: capitalize;" type="text" class="form-control" value="<?=date('d M Y H:m:s', strtotime($info->created_at))?>" readonly>
                    </div>
                    <button type="button" class="btn btn-block btn-outline-dark" onclick="location.href='<?=CORPORATE?>?p=newCP&edit=<?=$info['id']?>';" <?=$access['update']?>>Edit Profile</button>

                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4 col-lg-4 order-md-2">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">List Contact Person</span>
                <button data-toggle="modal" type="button" data-target="#modalPIC" class="btn btn-sm btn-success"><span class="fa fa-fw fa-plus"></span></button>
            </h4>
        <ul class="list-group mb-3 listDataPIC" id="listPIC">
            <?php if($pic->rowCount() > 0) { while ($row = $pic->fetch(PDO::FETCH_LAZY)) {  
              ?>
              <li class="list-group-item d-flex justify-content-between lh-condensed">

                        <div>
                            <h6 class="my-0"><?=$row['name']?></h6>
                            <small class="text-muted"><?=$row['nomor']?></small>
                            <br>
                            <small class="text-muted" style="font-weight: 600;"><?=$row['alamat']?>, <?=$row['Kel']?>, <?=$row['Kec']?></small>
                            <small class="text-muted" style="font-weight: 600;"><?=$row['Kota']?>, <?=$row['Provinsi']?></small>
                        </div>
                        <span class="text-muted">
                            <button <?=$access['delete']?> class="btn btn-sm btn-danger" style="font-size: 12px;" onclick = 'removePIC(<?=$row['id']?>)'> <span class="fa fa-trash"></span> </button>
                        </span>

                    </li>
        <?php } }else { ?>
                    <li class="list-group-item d-flex justify-content-between lh-condensed">

                        <div>
                            <h6 class="my-0">(empty)</h6>
                            <small class="text-muted">(empty)</small>
                        </div>
                        <span class="text-muted">00</span>

                    </li>
                <?php } ?>
            </ul>
    </div>
</div>


<div class="modal fade" id="modalPIC" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <form id="formPIC" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
                    <div class="row">
                        <div class="col-md-12 mb-12">
                            <label for="lastName">Nama Lengkap PIC</label>
                            <input type="text" class="form-control" id="namaPIC" placeholder="" value="" required="">
                            <input type="hidden" id="kodePerusahaan" value="<?=$info['CorporateUniqueID']?>">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="usernameAdmin">Type Document</label>
                        <select class="form-control" name="typePIC" id="typePIC" required>
                            <option value="">:: soft copy / hard copy ::</option>
                            <option value="0">Soft Copy</option>
                            <option value="1">Hard Copy</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-12">
                            <label for="lastName">Alamat Email</label>
                            <input type="text" class="form-control" data-parsley-type="email" id="emailPIC" placeholder="" value="" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-12">
                            <label for="lastName">Nomor HP.</label>
                            <input type="text" class="form-control" data-parsley-type="number" id="nomorPIC" placeholder="" value="" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="usernameAdmin">Provinsi</label>
                        <select class="form-control" name="ProvinsiCorporate" id="ProvinsiCorporate" required>
                            <option value="">:: provinsi ::</option>
                            <?php while ($row = $provinsi->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$row->id?>"><?=$row->name?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usernameAdmin">Kota</label>
                        <select class="form-control" name="KotaCorporate" id="KotaCorporate" required>
                            <option value="">:: kota ::</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usernameAdmin">Kecamatan</label>
                        <select class="form-control" name="kecamatanCorporate" id="kecamatanCorporate" required>
                            <option value="">:: Kecamatan ::</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usernameAdmin">Kelurahan</label>
                        <select class="form-control" name="kelurahanCorporate" id="kelurahanCorporate" required>
                            <option value="">:: Kecamatan ::</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="usernameAdmin">Alamat</label>
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="alamatCorporate" id="alamatCorporate" class="form-control" cols="5" required></textarea>
                    </div>
                    <br>
                    <button class="btn btn-success btn-sm btn-block" type="submit">Submit kebutuhan</button>
                </form>
            </div>
        </div>
    </div>
</div>