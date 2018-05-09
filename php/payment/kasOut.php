<?php
      $KasOut = $config->ProductsJoin('kas.id, kas.nama, kas.qty, kas.harga, kas.satuan, kas.ket, kas.created_at, kas.status, users.name, cat.content as category, subcat.category as subCategory', 'kas_outs AS kas',
      'INNER JOIN users ON users.id = kas.admin_id
      LEFT OUTER JOIN satuans AS cat ON cat.id = kas.type
      LEFT OUTER JOIN satuans AS subcat ON subcat.id = kas.sub_type', "WHERE kas.status ='' ");
  $data = $config->Products('id, content', 'satuans WHERE content_id = 0');
?>
<div id="listKas">
    <div class="row">
<!--        <div class="col-12 col-sm-4 col-lg-4" id="listPemasukanKas">-->
<!--            <div class="card">-->
<!--                <div class="card-header">-->
<!--                    List Pemasukan-->
<!--                </div>-->
<!--                <div class="card-body">-->
<!---->
<!--                    <div class="jumbotron">-->
<!--                        ASAP-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
        <div class="col-12 col-sm-12 col-lg-12" id="listPengeluaranKas">
            <div class="card">
                <div class="card-header">
                    List Pengeluaran
                </div>
                <div class="card-body">
                    <div id="form-kasKeluar" class="hidden">
                        <div class="card border-dark mb-3">
                            <div class="card-header bg-transparent border-dark">Form Tambah Pengeluaran Kas</div>
                            <div class="card-body">
                            <form id="belanja-form" method="post" data-parsley-validate="" autocomplete="off">
                                    <div class="form-group">
                                        <select class="form-control" name="specSatuan" id="specSatuan" required>
                                            <option value="">:: category ::</option>
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
                                        <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminBelanja">
                                        <input type="text"
                                               data-parsley-minLength="3" data-parsley-maxLength="36" data-parsley-message-maxLength="lebih"
                                               class="form-control" placeholder="nama pengeluaran" id="nameBelanja" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text"
                                               data-parsley-type="number"
                                               class="form-control" placeholder="quantity belanja" id="qtyBelanja" required>
                                    </div>
                                    <div class="form-group">
                                        <select id="satuanBelanja" class="form-control" required>
                                            <option value="">:: satuan harga ::</option>
                                            <option value="tangkai">tangkai</option>
                                            <option value="helai">helai</option>
                                            <option value="buah">buah</option>
                                            <option value="ikat">ikat</option>
                                            <option value="bungkus">bungkus</option>
                                            <option value="dus">dus</option>
                                            <option value="menter">menter</option>
                                            <option value="lusin">lusin</option>
                                            <option value="kodi">kodi</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text"
                                               data-parsley-type="number"
                                               class="form-control" placeholder="harga satuan" id="hargaBelanja" required>
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" rows="5" id="ketBelanja" required placeholder="keterangan pengeluaran"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-block btn-primary">submit belanjaan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="listKasKeluar">
                        <p>
                            <button class="btn btn-sm btn-primary" onclick='addKasOut(<?=$admin[0]['user_id']?>)' <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> pengeluaran</button>
                        </p>
                        <table id="tableKasOut" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
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
                        <form <?=$access['update']?> action="" id="reportKasOutAdmin" data-parsley-validate="" autocomplete="off">
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>