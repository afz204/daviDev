<?php 
$arrtypefile = [
    'soft copy',
    'hard copy'
];
$provinsi = $config->Products('id, name', 'provinces');
$regencies = $config->Products('id, name', 'regencies');
$districts = $config->Products('id, name', 'districts');
$villages = $config->Products('id, name', 'villages');

$data = $config->getData('corporate_pics.*, provinces.name as ProvinsiName, regencies.name as KotaName, districts.name as Kecamatan, villages.name as Kelurahan', 'corporate_pics
LEFT JOIN provinces on provinces.id = corporate_pics.province_id
LEFT JOIN regencies on regencies.id = corporate_pics.city
LEFT JOIN districts on districts.id = corporate_pics.kecamatan
LEFT JOIN villages on villages.id = corporate_pics.kelurahan
', "corporate_pics.id =".$_GET['ID']);

?>

<div class="card">
    <div class="card-body">
    <form id="updatePIC" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
            <div class="form-group">
                <label for="lastName">Nama Lengkap PIC</label>
                <input type="text" class="form-control" id="namaPIC" placeholder="" value="<?=$data['name']?>" required="">
                <input type="hidden" id="kodePerusahaan" value="<?=$data['corporate_id']?>">
                <input type="hidden" id="kodePIC" value="<?=$_GET['ID']?>">
            </div>
            <div class="form-group">
                <label for="lastName">Referensi Invoice Name</label>
                <input type="text" class="form-control" id="ReferensiInvoice" placeholder="" value="<?=$data['InvoiceReferensi']?>" required="">
            </div>
        <div class="form-group">
            <label for="usernameAdmin">Type Document</label>
            <select class="form-control" name="typePIC" id="typePIC" required>
                <option value="">:: soft copy / hard copy ::</option>
                <option value="0" <?=$data['type'] == 0 ? 'selected': ''?> >Soft Copy</option>
                <option value="1" <?=$data['type'] == 1 ? 'selected': ''?> >Hard Copy</option>
            </select>
        </div>
        
            <div class="form-group">
                <label for="lastName">Alamat Email</label>
                <input type="text" class="form-control" data-parsley-type="email" id="emailPIC" placeholder="" value="<?=$data['email']?>" required="">
            </div>
        
        
            <div class="form-group">
                <label for="lastName">Nomor HP.</label>
                <input type="text" class="form-control" data-parsley-type="number" id="nomorPIC" placeholder="" value="<?=$data['nomor']?>" required="">
            </div>
        <div class="form-group">
            <label for="usernameAdmin">Alamat</label>
            <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="alamatCorporate" id="alamatCorporate" class="form-control" cols="5" required><?=$data['alamat']?></textarea>
        </div>
        <br>
        <button class="btn btn-success btn-sm btn-block" type="submit">UPDATE</button>
    </form>
    </div>
</div>