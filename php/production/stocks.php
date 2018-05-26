<?php 

    $stocks = $config->ProductsJoin('stocks.id AS idStocks, stocks.nama_barang, stocks.qty, stocks.satuan, stocks.harga, stocks.ket, stocks.created_at, stocks.admin_id, users.name, cat.content AS category, subcat.category AS subcategory ', 'stocks',
    'INNER JOIN users ON users.id = stocks.admin_id LEFT OUTER JOIN satuans AS cat ON cat.id = stocks.cat LEFT OUTER JOIN satuans AS subcat ON subcat.id = stocks.sub_cat', ' WHERE stocks.qty != 0 ORDER BY stocks.created_at DESC');
    $data = $config->Products('id, content', 'satuans WHERE content_id = 0');

?>
<div class="card" <?=$access['read']?>>
    <div class="card-header">
        List Stok Barang
    </div>
    <div class="card-body">
        <div id="formStokcs" class="hidden">
            <div class="card border-dark mb-3">
                <div class="card-header bg-transparent border-dark">Form Tambah Stok Barang</div>
                <div class="card-body">
                    <form id="stock-form" method="post" data-parsley-validate="" autocomplete="off">
                        <div class="form-group">
                            <select name="specStock" name="specSatuan" id="specSatuan" class="form-control" required>
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
                            <input type="hidden" value="<?=$admin[0]['user_id']?>" id="adminStock">
                            <input type="hidden" value="" id="idStock">
                            <input type="text"
                                   data-parsley-minLength="3" data-parsley-maxLength="36" data-parsley-message-maxLength="lebih"
                                   class="form-control" placeholder="nama barang" id="nameStock" required>
                        </div>
                        
                        <div class="form-group hidden" id="stockTmp">
                            <input type="text" class="form-control" id="tmpStock">
                        </div>
                        <div class="form-group">
                            <input type="text"
                                    data-parsley-maxLength="36" data-parsley-type="number" data-parsley-message-maxLength="lebih"
                                   class="form-control" placeholder="quantity" id="qtyStock" required>
                            
                        </div>
                        <div class="form-group">
                            <select name="satuanStock" id="satuanStock" class="form-control" required>
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
                                   data-parsley-minLength="3" data-parsley-maxLength="36" data-parsley-type="number" data-parsley-message-maxLength="lebih"
                                   class="form-control" placeholder="harga barang per satuan" id="hargaStock" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="3" id="ketStock" required placeholder="keterangan barang"></textarea>
                        </div>
                        <button id="btn_add_stock" type="submit" class="btn btn-sm btn-block btn-primary">submit barang</button>
                    </form>
                </div>
            </div>
        </div>
        <div id="listTmp" class="hidden">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-10">
                    <table id="listTmpTable" class="table table-hover <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
                        <thead class="thead-light">
                            <tr style="text-transform: lowercase;">
                                <th scope="col">category</th>
                                <th scope="col">nama barang</th>
                                <th scope="col">terpakai</th>
                                <th scope="col">keterangan</th>
                                <th scope="col">updated_by</th>
                                <th scope="col">updated_date</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="listStok">
            <p>
                <button <?=$access['create']?> class="btn btn-sm btn-primary" onclick="formStock()"  type="button"><span class="fa fa-fw fa-plus"></span> stocks</button>
            </p>
            <table id="tableStok" class="table table-hover <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
            <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col">category</th></th>
                    <th scope="col">spesifikasi</th>
                    <th scope="col">qty</th>
                    <th scope="col">total</th>
                    <th scope="col">keterangan</th>
                    <th scope="col">created at</th>
                    <th scope="col">action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($rows = $stocks->fetch(PDO::FETCH_LAZY)){ 
                if(empty($rows['subcategory'])){
                    $cat = $rows['category'];
                }else{
                    $cat = $rows['category'] . ' > ' .$rows['subcategory'];
                }
                $total = $rows['qty'] * $rows['harga'];
                ?>
                <tr>
                    <td><?=$cat?></td>
                    <td><?=$rows->nama_barang?></td>
                    <td><?=$rows->qty?></td>
                    <td><?=$config->formatPrice($total)?></td>
                    <td><?=$rows->ket?></td>
                    <td style="font-size: 12px;"><?=$rows->created_at?> / <span class="badge badge-info"><?=$rows->name?></span></td>
                    <td>
                        <button <?=$access['update']?> class="btn btn-sm btn-warning" data-toggle="tooltip" title="edit stocks" onclick="editStock(<?=$rows['idStocks']?>)"><span class="fa fa-fw fa-pencil-square-o"></span></button>
                        <button <?=$access['delete']?> class="btn btn-sm btn-info" data-toggle="tooltip" title="views stocks" onclick="viewStock(<?=$rows->idStocks?>)"><span class="fa fa-fw fa-eye"></span></button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            </table>
        </div>
    </div>
</div>