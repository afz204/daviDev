<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 10/04/2018
 * Time: 15.15
 */
if (isset($_GET['id'])){
    $admin_id = $_GET['id'];

$users = $config->ProductsJoin('users.id, users.name, users.email, users.password, users.jabatan, users.role_id, users.status, users.created_at, levels.levels, levels.ket, roles.roles',
    'users', 'INNER JOIN levels ON levels.id = users.jabatan INNER JOIN roles ON roles.id = users.role_id', 'WHERE users.id = '. $admin_id .' ');

$info = $users->fetch(PDO::FETCH_LAZY);
if($info['status'] == '1'){
    $status = '<li class="list-group-item" style="background: #28a745; color: #fff; font-weight: 600;">active</span></li>';
    $panel = '<a href="#" class="card-link" '. $access['delete'] .' onclick="return confirm(\'Are you sure you want to disabled this item?\');">Disabled</a>';
}else{
    $status = '<li class="list-group-item" style="background: #dc3545; color: #fff; font-weight: 600;">disabled</span></li>';
    $panel = '<a href="#" class="card-link">Enable</a>';

}

//info role akses

$roles = $config->ProductsJoin('menus.id, menus.menu, menus.links, staffs.id_roles', 'menus', 'INNER JOIN staffs ON staffs.id_menu = menus.id', 'WHERE staffs.id_roles = '. $info['role_id']);

?>

<div class="row">
    <div class="col-12 col-md-3 col-lg-3">
        <div class="card">
            <img class="card-img-top" src="<?=URL?>assets/images/admin.jpg" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title"><?=$info['name']?></h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Jabatan: <span class="badge badge-primary"><?=$info['levels']?></span></li>
                <li class="list-group-item">Roles: <span class="badge badge-info"><?=$info['roles']?></span></li>
                <li class="list-group-item">Created_at: <span class="small"><?=$info['created_at']?></span></li>
                <?=$status?>
            </ul>
            <div class="card-body">
                <a href="#" class="card-link">Reset Password</a>
                <?=$panel?>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-9 col-lg-9">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Previllage Users <span class="float-right"><button class="btn btn-sm btn-primary" <?=$access['create']?> onclick="formPrevillage()"><i class="fa fa-fw fa-plus-square"></i> previllage</button></span></h5>
                <hr>
                <div id="form-previllage" class="hidden">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="card border-dark mb-3">
                                <div class="card-header bg-transparent border-dark">Form Tambah Previllage User </div>
                                <div class="card-body">

                                    <form id="form-previllage" method="post" data-parsley-validate="" autocomplete="off">
                                        <div class="form-group">
                                            <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminPrevillage">
                                            <input type="hidden" value="<?=$info['id']?>" id="userPrevillage">
                                            <select class="form-control" name="listMenuPrev" id="listMenuPrev" required>
                                                <option value="">:: menu ::</option>
                                                <?php while ($rows = $menus->fetch(PDO::FETCH_LAZY)){ ?>
                                                <option value="<?=$rows['id']?>"><?=$rows['menu']?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="listSubmenuPrev" id="listSubmenuPrev" required>
                                                <option value="">:: submenu ::</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input previllageUser" type="checkbox" id="inlineCheckbox1" value="2">
                                                <label class="form-check-label" for="inlineCheckbox1">C</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input previllageUser" type="checkbox" id="inlineCheckbox2" value="3" checked>
                                                <label class="form-check-label" for="inlineCheckbox2">R</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input previllageUser" type="checkbox" id="inlineCheckbox3" value="4">
                                                <label class="form-check-label" for="inlineCheckbox3">U</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input previllageUser" type="checkbox" id="inlineCheckbox4" value="8">
                                                <label class="form-check-label" for="inlineCheckbox4">D</label>
                                            </div>
                                        </div>

                                        <p>
                                            <button type="submit" class="btn btn-sm btn-block btn-primary">submit previllage</button>
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="listPrevillages">
                    <div class="row">
                        <?php while ($row = $roles->fetch(PDO::FETCH_LAZY)){ ?>
                            <div class="col-6 col-md-2 col-lg-2" style="padding-bottom: 1%; padding-left: 1% !important; padding-right: 1% !important;">
                                <div class="card">
                                    <div class="card-header">
                                        <?=$row['menu']?>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <?php
                                        $previllage = $config->ProductsJoin('sub_menus.id_menu, sub_menus.submenu, sub_menus.link, menus.menu, previllages.id, previllages.weight', 'sub_menus', 'INNER JOIN menus ON menus.id = sub_menus.id_menu
INNER JOIN previllages ON previllages.id_submenu = sub_menus.id', 'WHERE menus.id = '. $row['id'] .' AND id_admin = '.$info['id']);
                                        while ($cols = $previllage->fetch(PDO::FETCH_LAZY)){
                                            $weg = $config->weightPages($cols['weight']);
                                            $weight = $weg['weight'];
                                            ?>
                                            <li class="list-group-item"><a href="#" onclick="delPrevillage(<?=$cols['id']?>, <?=$admin[0]['user_id']?>, <?=$admin_id?>)"><?=$cols['submenu']?></a> <button class="badge badge-pill badge-primary align-text-bottom updatePrevillages" type="button" data-id="<?=$cols['id']?>" data-toggle="modal" data-target="#updatePrevillage"><?=$weight?></button></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="updatePrevillage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card card-body">
                        <form id="form-updatePrevillage" method="post" data-parsley-validate="" autocomplete="off">
                            <div class="form-group">
                                <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminUpdatePrevillage">
                                <input type="hidden" value="" id="idUpdatePrevillage">

                            </div>

                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input updatePrevillage" type="checkbox" id="inlineCheckbox1" value="2">
                                    <label class="form-check-label" for="inlineCheckbox1">Create</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input updatePrevillage" type="checkbox" id="inlineCheckbox2" value="3">
                                    <label class="form-check-label" for="inlineCheckbox2">Read</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input updatePrevillage" type="checkbox" id="inlineCheckbox3" value="4">
                                    <label class="form-check-label" for="inlineCheckbox3">Update</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input updatePrevillage" type="checkbox" id="inlineCheckbox4" value="8">
                                    <label class="form-check-label" for="inlineCheckbox4">Delete</label>
                                </div>
                            </div>

                            <p>
                                <button type="submit" class="btn btn-sm btn-block btn-primary">update previllage</button>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>




<?php }else{

} ?>
