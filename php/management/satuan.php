<?php 
    $satuan = $config->ProductsJoin('con.id, con.content AS content, con.created_at, con.admin_at, cat.content_id AS catID, cat.category, subcat.category_id AS subcatID, subcat.subcategory AS sbCategory, usr.name',
    ' satuans AS con ', ' LEFT OUTER JOIN satuans AS cat ON cat.content_id = con.id
    LEFT OUTER JOIN satuans AS subcat ON subcat.category_id = cat.id INNER JOIN users AS usr ON usr.id = con.admin_at ', 'WHERE con.content_id =0
    ORDER BY con.created_at DESC');
    $data = $config->Products('id, content', 'satuans WHERE content_id = 0');
?>
<div class="card" <?=$access['read']?> >
    <div class="card-header">
        List Satuan
    </div>
    <div class="card-body">
        <div id="form-satuan" class="hidden">
            <div class="row justify-content-center">
                <div class="col-6">
                    <div class="card border-dark mb-3">
                        <div class="card-header bg-transparent border-dark">Form Tambah Satuan</div>
                        <div class="card-body">

                            <form id="satuanForm" method="post" data-parsley-validate="" autocomplete="off">
                                
                                <div class="form-group">
                                    <select class="form-control" name="specSatuan" id="specSatuan" required>
                                        <option value="">:: spesifikasi ::</option>
                                        <?php while ($row = $data->fetch(PDO::FETCH_LAZY)){ ?>
                                            <option value="<?=$row['id']?>"><?=$row['content']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group hidden" id="satuanCat">
                                    <select class="form-control" name="catSatuan" id="catSatuan" >
                                        <option value="">:: category ::</option>
                                    </select>
                                </div>
                                <div class="form-group hidden" id="satuanSubCat">
                                    <select class="form-control" name="subCatSatuan" id="subCatSatuan" >
                                        <option value="">:: category ::</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" id="namaSatuan" required class="form-control">
                                    <input type="hidden" id="adminSatuan" value="<?=$admin[0]['user_id']?>" class="form-control">
                                </div>
                                
                                <p>
                                    <button type="submit" class="btn btn-sm btn-block btn-primary">submit satuan</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div id="listSatuan">
            <p>
                <button class="btn btn-sm btn-primary " onclick="formSatuan()" <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> satuan</button>
            </p>
            <table id="tbListSatuan" class="table table-bordered <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                <thead class="thead-light">
                <tr>
                    <th scope="col">content</th>
                    <th scope="col">category</th>
                    <th scope="col">subcategory</th>
                    <th scope="col">created_at</th>
                    <th scope="col">admin_id</th>
                    <th scope="col">action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $satuan->fetch(PDO::FETCH_LAZY)){
                    ?>
                    <tr style="text-transform: lowercase;">
                        <td><?=$row->content?></td>
                        <td><?=$row->category?></td>
                        <td><?=$row->sbCategory?></td>
                        <td><?=$row->created_at?></td>
                        <td><?=$row->name?></td>
                        <td></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>