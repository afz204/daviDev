<?php 

    $kurir = $config->Products('id, nama_kurir', 'kurirs');
    $kurirs = $config->Products('id, nama_kurir', 'kurirs');
    $charge = $config->ProductsJoin('delivery_charges.id, delivery_charges.price, villages.name', 'delivery_charges',
    'INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', '');

    $payCharge = $config->ProductsJoin('pay_kurirs.id as payChargeID, pay_kurirs.no_trx, pay_kurirs.kurir_id, pay_kurirs.charge_id, pay_kurirs.created_at, kurirs.nama_kurir, delivery_charges.price, villages.name, users.name as admin', 'pay_kurirs',
    'INNER JOIN kurirs ON kurirs.id = pay_kurirs.kurir_id
    INNER JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
    INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan
    INNER JOIN users ON users.id = delivery_charges.admin_id', "WHERE pay_kurirs.status = '' ORDER BY pay_kurirs.created_at DESC");

?>
<div id="listPay">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12" id="">
            <div class="card">
                <div class="card-header">
                    Pembayaran Kurir
                </div>
                <div class="card-body">
                    <div id="form-payKurir" class="hidden">
                        <div class="card border-dark mb-3">
                            <div class="card-header bg-transparent border-dark">Form Tambah Pembayaran Kurir</div>
                            <div class="card-body">
                                <form id="payKurir-form" method="post" data-parsley-validate="" autocomplete="off">
                                    <div class="form-group">
                                    <input type="hidden" name="adminPay" id="adminPay" value="<?=$admin[0]['user_id']?>">
                                        <select class="form-control" name="namaKurir" id="namaKurir" required>
                                            <option value="">:: kurir ::</option>
                                            <?php while ($row = $kurir->fetch(PDO::FETCH_LAZY)){ ?>
                                            <option value="<?=$row->id?>"><?=$row->nama_kurir?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" name="kelurahanCharge" id="kelurahanCharge" required>
                                            <option value="">:: delivery charge ::</option>
                                            <?php while ($row = $charge->fetch(PDO::FETCH_LAZY)){ ?>
                                            <option value="<?=$row->id?>"><?=$row->name?> <span class="badge badge-info"><?=$config->formatPrice($row->price)?></span></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" required id="no_trxCharge" placeholder="nomor invoice" required class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-block btn-primary">submit pengeluaran</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="listPayKurir">
                        <p>
                            <button class="btn btn-sm btn-primary addpayCharge" <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> charge</button>
                        </p>
                        <table id="tablePayKurir" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                            <thead class="thead-light">
                            <tr style="text-transform: lowercase;">
                                <th scope="col">Nama Kurir</th>
                                <th scope="col">Nomor Transaksi</th>
                                <th scope="col">Kirim ke</th>
                                <th scope="col">Delivery Charge</th>
                                <th scope="col">Admin id</th>
                                <th scope="col">Created_at</th>
                                <th scope="col">action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $payCharge->fetch(PDO::FETCH_LAZY)){ ?>
                                <tr style="text-transform: lowercase;">
                                    <td><?=$row['nama_kurir']?></td>
                                    <td><?=$row['no_trx']?></td>
                                    <td><?=$row['name']?></td>
                                    <td style="text-align: right"><?=$config->formatPrice($row['price'])?></td>
                                    <td><?=$row['admin']?></td>
                                    <td><i class="small"><?=$row['created_at']?></i></td>
                                    <td>
                                        <button <?=$access['delete']?> class="btn btn-sm btn-danger delPayCharge" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" data-id="<?=$row['payChargeID']?>" data-admin="<?=$admin[0]['user_id']?>" >delete</button>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <form <?=$access['update']?> action="" id="reportPayCharge" data-parsley-validate="" autocomplete="off">
                            <div class="form-row align-items-center">
                                <div class="col-auto my-1">
                                    <input type="hidden" value="<?=$admin[0]['user_id']?>" id="reportPayChargeAdminID">
                                    <input type="hidden" value="<?=URL?>" id="reportPayChargeURL">
                                    <select class="custom-select form-control-sm mr-sm-2" id="reportPayChargeAdmin" required>
                                        <option value="">Choose...</option>
                                        <?php while ($cols = $kurirs->fetch(PDO::FETCH_LAZY)){ ?>
                                        <option value="<?=$cols['id']?>"><?=$cols['nama_kurir']?></option>
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