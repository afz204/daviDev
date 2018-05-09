
<div class="card" <?=$access['read']?> >
    <div class="card-header">
        List Admin
    </div>
    <div class="card-body">
        <div id="form-admin" class="hidden">
            <div class="row justify-content-center">
                <div class="col-6">
                    <div class="card border-dark mb-3">
                        <div class="card-header bg-transparent border-dark">Form Tambah Admin</div>
                        <div class="card-body">

                            <form id="admin-form" method="post" data-parsley-validate="" autocomplete="off">
                                <div class="form-group">
                                    <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminID">
                                    <input type="text"
                                           data-parsley-minLength="3" data-parsley-maxLength="255"
                                           class="form-control" placeholder="nama admin" id="nameAdmin" required>
                                </div>
                                <div class="form-group">
                                    <input type="text"
                                           data-parsley-minLength="3" data-parsley-type="email" data-parsley-maxLength="255"
                                           class="form-control" placeholder="examples@domain.com" id="emailAdmin" required>
                                </div>
                                <div class="form-group">
                                    <input type="password"
                                           data-parsley-minLength="3" data-parsley-maxLength="255"
                                           class="form-control" placeholder="password admin" id="passwordAdmin" required>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="levelAdmin" id="levelAdmin" required>
                                        <option value="">:: jabatan ::</option>
                                        <?php while ($row = $jabatan->fetch(PDO::FETCH_LAZY)){ ?>
                                            <option value="<?=$row['id']?>"><?=$row['levels']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="roleAdmin" id="roleAdmin" required>
                                        <option value="">:: role ::</option>
                                        <?php while ($row = $roles->fetch(PDO::FETCH_LAZY)){ ?>
                                            <option value="<?=$row['id']?>"><?=$row['roles']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-block btn-primary">submit admin</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div id="listAdmin">
            <p>
                <button class="btn btn-sm btn-primary addAdmin" <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> admin</button>
            </p>
            <table class="table table-bordered <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama Admin</th>
                    <th scope="col">Email</th>
                    <th scope="col">Jabatan</th>
                    <th scope="col">role_id</th>
                    <th scope="col">status</th>
                    <th scope="col">created at</th>
                    <th scope="col">action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1; while ($row = $listAdmin->fetch(PDO::FETCH_LAZY)){
                    if($row['status'] == '1'){
                        $status = '<label class="badge badge-success">Active</label>';
                    }else{
                        $status = '<label class="badge badge-secondary">Disable</label>';
                    }
                    ?>
                    <tr style="text-transform: lowercase;">
                        <td><?=$i++?></td>
                        <td><?=$row['name']?></td>
                        <td><?=$row['email']?></td>
                        <td><?=$row['levels']?></td>
                        <td><?=$row['roles']?></td>
                        <td><?=$status?></td>
                        <td><?=date('d M Y H:m', strtotime($row['created_at']))?></td>
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