<?php 

    $kurir = $config->Products('id, nama_kurir', 'kurirs');
    $kurirs = $config->Products('id, nama_kurir', 'kurirs');
    $charge = $config->ProductsJoin('delivery_charges.id, delivery_charges.price, villages.name', 'delivery_charges',
    'INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', '');

    $payCharge = $config->ProductsJoin('pay_kurirs.id as payChargeID, pay_kurirs.no_trx, pay_kurirs.kurir_id, pay_kurirs.charge_id, pay_kurirs.remarks, pay_kurirs.total, pay_kurirs.weight, pay_kurirs.status, pay_kurirs.created_at, kurirs.nama_kurir, delivery_charges.price, villages.name, users.name as admin', 'pay_kurirs',
    'INNER JOIN kurirs ON kurirs.id = pay_kurirs.kurir_id
    INNER JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
    INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan
    INNER JOIN users ON users.id = delivery_charges.admin_id', " ORDER BY pay_kurirs.created_at DESC");

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
                                            <option value="<?=$row->id?>" data-prices="<?=$row->price?>"><?=$row->name?> <span class="badge badge-info"><?=$config->formatPrice($row->price)?></span></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" required id="no_trxCharge" placeholder="nomor invoice" required class="form-control">
                                    </div>
                                    <div id="btnPayKurir">
                                            <button type="submit" class="btn btn-sm btn-block btn-primary">submit pengeluaran</button>

                                    </div>
                                    
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
                                <th scope="col">Remarks</th>
                                <th scope="col">Remarks Charge</th>
                                <th scope="col">Delivery Charge</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">action</th>
                            </tr> 
                            </thead>
                            <tbody>
                            <?php while ($row = $payCharge->fetch(PDO::FETCH_LAZY)){ 
                                $remarks = '<span class="badge badge-secondary">unset</span>';
                                $total = '<span class="badge badge-secondary">unset</span>';
                                if(!empty($row['remarks'])){
                                    $remarks = $row['remarks'];
                                    $total = $config->formatPrice($row['weight']);
                                }
                                $subtotal = $row['price'] + $row['weight'];
                                ?>
                                <tr style="text-transform: lowercase;">
                                    <td><?=$row['nama_kurir']?></td>
                                    <td><?=$row['no_trx']?></td>
                                    <td><?=$row['name']?></td>
                                    <td><?=$remarks?></td>
                                    <td><?=$total?></td>
                                    <td style="text-align: right"><?=$config->formatPrice($row['total'])?></td>
                                    <td style="text-align: right"><?=$config->formatPrice($subtotal)?></td>
                                    <td>
                                        <?php if(empty($row['remarks'])){ ?>
                                        <div class="btn-group">
                                          <button style="text-transform: uppercase; font-size: 10px; font-weight: 500;" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            remarks
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" onclick="remarks(1, <?=$row['payChargeID']?>)">parking</a>
                                            <a class="dropdown-item" href="#" onclick="remarks(2, <?=$row['payChargeID']?>)">standing</a>
                                            <a class="dropdown-item" href="#" onclick="remarks(3, <?=$row['payChargeID']?>)">time remarks</a>
                                          </div>
                                        </div>
                                    <?php }if(empty($row['status'])){ ?>
                                        <button <?=$access['delete']?> class="btn btn-sm btn-warning" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" onclick="payDelivery(<?=$row['payChargeID']?>)" >pay</button>
                                    <?php }else{ ?>
                                        <button class="btn btn-sm btn-success" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" disabled="">paid</button>
                                    <?php }  ?>
                                        <button <?=$access['delete']?> class="btn btn-sm btn-danger delPayCharge" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" data-id="<?=$row['payChargeID']?>" data-admin="<?=$admin[0]['user_id']?>" <?=empty($row['status']) ? '' : 'disabled=""'?> >delete </button>

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

<div class="modal fade" id="modalPayParking" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Parking</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formParkir" data-parsley-validate="" autocomplete="off">
          <div class="form-group">
            <input type="text" class="form-control" id="biayaParkir" data-parsley-type="number" placeholder="biaya parkir" required="">
            <input type="hidden" id="numberRecord" value="">
          </div>
          <div class="form-group">
            <textarea class="form-control" id="tempatParkir" placeholder="tempat parkir" required=""></textarea>
          </div>
          <div id="btnParkir">
              <button type="submit" class="btn btn-block btn-primary">save remarks</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>