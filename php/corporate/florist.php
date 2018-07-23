<?php
$stmt = $config->runQuery("SELECT * FROM florist ORDER BY CreatedDate DESC");
$stmt->execute();
$provinsi = $config->Products('id, name', 'provinces');
?>
<div class="card hidden" id="form_florist" >
    <div class="card-body">
    <form id="formFlorist" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">FloristName</label>
                <input type="text" class="form-control" id="FloristName" autocomplete="text" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">Email</label>
                <input type="email" data-parsley-type="email" class="form-control" id="Email" autocomplete="email" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">Username</label>
                <input type="text" class="form-control" id="Username" placeholder="" autocomplete="text" value="" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_login">Password</label>
                <input type="password" class="form-control" id="Password" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">mobile_phone</label>
                <input type="text" class="form-control" data-parsley-type="number" id="mobile_phone" autocomplete="text" placeholder="" value="" required="">
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
                            <option value="<?=$row->id?>"><?=$row->name?></option>
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
                        <textarea style="text-transform: capitalize;" data-parsley-minLength="5" name="alamatCorporate" id="alamatCorporate" class="form-control" cols="5" required></textarea>
            </div>
        </div>


        <hr class="mb-4">
        <button class="btn btn-success btn-lg btn-block" type="submit">save customer</button>
        </form>
    </div>
</div>
<div <?=$access['read']?> class="card" id="list_florist">
    <div class="card-header">
        List Florist  <span class="pull-right"><button class= "btn btn-sm btn-primary" onclick="hideContent('list_florist', 'form_florist')" style="font-size: 10px;">add florist</button></span>
    </div>
    <div class="card-body">

        <table id="listFlorist" class="table table-bordered <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
            <thead class="thead-light">
            <tr>
                <th scope="col">florist name</th>
                <th scope="col">email</th>
                <th scope="col">user name</th>
                <th scope="col">phone number</th>
                <th scope="col">status</th>
                <th scope="col">join at</th>
                <th scope="col">action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($row = $stmt->fetch(PDO::FETCH_LAZY)){ 
                $status = '<label class="badge badge-sm badge-danger">Not Active</label>';
                if($row['IsActive'] == 1){
                    $status = '<label class="badge badge-sm badge-success">Active</label>';
                }
                 ?>
                <tr style="text-transform: lowercase;">
                    <td><?=$row['FloristName']?></td>
                    <td><?=$row['Email']?></td>
                    <td><?=$row['Username']?></td>
                    <td><?=$row['mobile_phone']?></td>
                    <td><?=$status?></td>
                    <td><?=date('d M Y H:m', strtotime($row['CreatedDate']))?></td>
                    <td >
                        <a href="<?=CORPORATE?>?p=detailOrganic&id=<?=$row['ID']?>" <?=$access['read']?>>
                            <button class="btn btn-sm btn-primary" style="text-transform: uppercase; font-size: 10px; font-weight: 500;">details</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>