
<?php 
$adminlist = $config->FindProducts('*', 'users', 'status = 1');
$listtoken = $config->Products('token.*, users.name', 'token left join users on users.id = token.AdminID');

?>
<div class="card" <?=$access['read']?> >
    <div class="card-header">
        List Token
    </div>
    <div class="card-body">
        <div id="form-admin" class="">
            <div class="row justify-content-center">
                <div class="col-6">
                    <div class="card border-dark mb-3">
                        <div class="card-header bg-transparent border-dark">Form Admin Token</div>
                        <div class="card-body">

                            <form id="token-form" method="post" data-parsley-validate="" autocomplete="off">
                                <div class="form-group">
                                    <select class="form-control" name="listadmin" id="listadmin" required>
                                        <option value="">:: admin ::</option>
                                        <?php while ($row = $adminlist->fetch(PDO::FETCH_LAZY)){ ?>
                                            <option value="<?=$row['id']?>"><?=$row['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="password"
                                           data-parsley-minLength="3" data-parsley-maxLength="255"
                                           class="form-control" placeholder="password token" id="passwordtoken" name="passwordtoken" required>
                                </div>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-block btn-primary">submit token</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div id="">
        
            <table class="table table-bordered <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama Admin</th>
                    <th scope="col">Alias</th>
                    <th scope="col">Status</th>
                    <th scope="col">action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; while ($row = $listtoken->fetch(PDO::FETCH_LAZY)){
                    if($row['Status'] == '0'){
                        $status = '<label class="badge badge-success">Active</label>';
                    }else{
                        $status = '<label class="badge badge-secondary">Disable</label>';
                    }
                    ?>
                    <tr style="text-transform: lowercase;">
                        <td><?=$i++?></td>
                        <td><?=$row['name']?></td>
                        <td><?=$row['alias']?></td>
                        <td><?=$status?></td>
                        <td >
                            <a href="<?=MANAGEMENT?>?p=profile&id=<?=$row['id']?>" <?=$access['read']?>>
                                <button class="btn btn-sm btn-primary" ><span class="fa fa-fw fa-eye"></span></button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>