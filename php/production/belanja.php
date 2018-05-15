<?php
    $belanja = $config->ProductsJoin('kas.id, kas.nama, kas.qty, kas.harga, kas.satuan, kas.ket, kas.created_at, kas.status, users.name, cat.content as category, subcat.category as subCategory', 'kas_outs AS kas',
        'INNER JOIN users ON users.id = kas.admin_id
        LEFT OUTER JOIN satuans AS cat ON cat.id = kas.type
        LEFT OUTER JOIN satuans AS subcat ON subcat.id = kas.sub_type', "WHERE DATE(kas.created_at)= CURDATE() AND kas.status ='' ");
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
        <div class="col-12 col-sm-12 col-lg-12" id="listPengeluaranBelanja">
            <div class="card">
                <div class="card-header">
                    List Belanja
                </div>
                <div class="card-body">
                    <div id="form_belanja" class="hidden">
                        <div class="card border-dark mb-3">
                            <div class="card-header bg-transparent border-dark">Form Tambah Belanja</div>
                            <div class="card-body">
                                <form id="belanjaForm" method="post" data-parsley-validate="" autocomplete="off">
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
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="CheckStok" value="1">
                                        <label class="form-check-label" for="CheckStok">Masukan ke stok barang</label>
                                    </div>
                                    <button id="btn_prod_belanja" type="submit" class="btn btn-sm btn-block btn-primary">submit belanjaan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="listbelanja">
                        <p>
                            <button <?=$access['create']?> class="btn btn-sm btn-primary" onclick="addBelanja()"  type="button"><span class="fa fa-fw fa-plus"></span> belanja</button>
                        </p>
                        <table id="tableBelanja" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                            <thead class="thead-light">
                                <tr style="text-transform: lowercase;">
                                    <th scope="col">Category</th>
                                    <th scope="col">Nama Pengeluaran</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Harga</th>
                                    <th scope="col">Total Biaya</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Admin id</th>
                                    <th scope="col">Created_at</th>
                                    <th scope="col">action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php   while ($row = $belanja->fetch(PDO::FETCH_LAZY)){ 
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
                                    <td width="20%"><?=$row['qty']?> (<?=$row['satuan']?>)</td>
                                    <td width="15%" style="text-align: right;"><?=$config->formatPrice($row->harga)?> / <?=$row['satuan']?></td>
                                    <td width="15%" style="text-align: right;"><?=$config->formatPrice($total)?></td>
                                    <td width="35%"><?=$row['ket']?></td>
                                    <td width="10%"><?=$row['name']?></td>
                                    <td width="15%"><i class="small"><?=$row['created_at']?></i></td>
                                    <td width="10%">
                                        <button <?=$access['delete']?> onclick="delBelanja(<?=$row['id']?>, <?=$admin[0]['user_id']?>)" class="btn btn-sm btn-danger " style="text-transform: uppercase; font-size: 10px; font-weight: 500;" >delete</button>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>