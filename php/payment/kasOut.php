<?php
      $KasOut = $config->ProductsJoin('kas.id, kas.nama, kas.qty, kas.harga, kas.satuan, kas.ket, kas.created_at, kas.status, users.name, cat.content as category, subcat.category as subCategory', 'kas_outs AS kas',
      'INNER JOIN users ON users.id = kas.admin_id
      LEFT OUTER JOIN satuans AS cat ON cat.id = kas.type
      LEFT OUTER JOIN satuans AS subcat ON subcat.id = kas.sub_type', "WHERE MONTH(kas.created_at) = MONTH(CURRENT_DATE()) AND YEAR(kas.created_at) = YEAR(CURRENT_DATE()) AND kas.status ='' ORDER BY kas.created_at DESC ");
  $data = $config->Products('id, content', 'satuans WHERE content_id = 0');
//   $admin = $config->Products('id, name', 'users where status = 1');
$kurir = $config->Products('*', 'users where status = 1');
?>
<div id="listKas">
    <div class="row">
    
        <div class="col-12 col-sm-12 col-lg-12" id="listPengeluaranKas">
            <div class="card">
                <div class="card-header">
                    List Pengeluaran
                </div>
                <div class="card-body">
                    <div id="listKasKeluar">
                        <p>
                            <button class="btn btn-sm btn-primary hidden" onclick='addKasOut(<?=$admin[0]['user_id']?>)' <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> pengeluaran</button>
                        </p>
                        <form id="kasOut" methods="post" data-parsley-validate="">
                            <div class="row">
                                <div class="form-group mx-sm-3 mb-2">
                                    <div id="datekasout" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                    <input type="hidden" id='daterangekasout'>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="adminkasout" id="adminkasout" required>
                                        <option value="">:: admin ::</option>
                                        <option value="99">All Admin</option>
                                        <?php while($col = $kurir->fetch(PDO::FETCH_LAZY)) { ?>
                                        <option value="<?=$col['id']?>"><?=$col['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-3 col-lg-3">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" onClick="exportkas('kasout')" type="button"><span class="fa fa-download"></span> export</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table id="table_kas_out" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                            <thead class="thead-light">
                                <tr style="text-transform: lowercase;">
                                    <th scope="col">Category</th>
                                    <th scope="col">Nama Pengeluaran</th>
                                    <th scope="col">Total Biaya</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Admin id</th>
                                    <th scope="col">Created_at</th>
                                    <th scope="col">action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php   while ($row = $KasOut->fetch(PDO::FETCH_LAZY)){ 
                                    if(empty($row['subCategory'])){
                                        $cat = $row['category'];
                                    }else{
                                        $cat = $row['category'] . ' > ' .$row['subCategory'];
                                    }
                                    $total = $row['qty'] * $row['harga'];
                                ?>
                                <tr style="text-transform: lowercase;">
                                    <td width="20%"><?=$cat?></td>
                                    <td width="20%"><?=$row['nama']?></td>
                                    <td width="15%" style="text-align: right;"><?=$config->formatPrice($total)?></td>
                                    <td width="35%"><?=$row['ket']?></td>
                                    <td width="10%"><?=$row['name']?></td>
                                    <td width="15%"><i class="small"><?=$row['created_at']?></i></td>
                                    <td width="10%">
                                        <button <?=$access['delete']?> onclick="delKasOut(<?=$row['id']?>, <?=$admin[0]['user_id']?>)" class="btn btn-sm btn-danger " style="text-transform: uppercase; font-size: 10px; font-weight: 500;" >delete</button>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <!-- <form <?=$access['update']?> action="" id="reportKasOutAdmin" data-parsley-validate="" autocomplete="off">
                            <div class="form-row align-items-center">
                                <div class="col-auto my-1">
                                    <input type="hidden" value="<?=$admin[0]['user_id']?>" id="reportOutAdminID">
                                    <input type="hidden" value="<?=URL?>" id="reportOutURL">
                                    <select class="custom-select form-control-sm mr-sm-2" id="reportOutAdmin" required>
                                        <option value="">Choose...</option>
                                        <?php while ($cols = $listAdmin->fetch(PDO::FETCH_LAZY)){ ?>
                                        <option value="<?=$cols['id']?>"><?=$cols['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-auto my-1">
                                    <button type="submit" class="btn btn-sm btn-success">report</button>
                                </div>
                            </div>
                        </form> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>