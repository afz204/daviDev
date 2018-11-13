<?php 

    $kurir = $config->Products('id, nama_kurir', 'kurirs where status = 1');
    $kurirs = $config->Products('id, nama_kurir', 'kurirs where status = 1');
    $kurirss = $config->Products('id, nama_kurir', 'kurirs where status = 1');
    $charge = $config->ProductsJoin('delivery_charges.id, delivery_charges.price, villages.name', 'delivery_charges',
    'INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', '');
    $charges = $config->ProductsJoin('delivery_charges.id, delivery_charges.price, villages.name', 'delivery_charges',
    'INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', '');

    $payCharge = $config->ProductsJoin('pay_kurirs.id as payChargeID, pay_kurirs.no_trx, pay_kurirs.kurir_id, pay_kurirs.charge_id, pay_kurirs.remarks, pay_kurirs.total, pay_kurirs.weight, pay_kurirs.status, pay_kurirs.created_at, kurirs.nama_kurir, delivery_charges.price, villages.name, users.name as admin', 'pay_kurirs',
    'INNER JOIN kurirs ON kurirs.id = pay_kurirs.kurir_id
    INNER JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
    INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan
    INNER JOIN users ON users.id = delivery_charges.admin_id', " WHERE pay_kurirs.status != '2' ORDER BY pay_kurirs.created_at DESC");

?>

<style>
    .parsley-errors-list{
        display: none;
    }
    .select2-container--bootstrap4.select2-container--focus .select2-selection, .select2-container--bootstrap4.select2-container--open .select2-selection{
        
        border-color: #B94A48;
    }
</style>

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
                        <p id="btnFilterPayKurir"> 
                            <button class="btn btn-sm btn-primary addpayCharge" <?=$access['create']?> type="button"><span class="fa fa-fw fa-plus"></span> charge</button>
                           
                        </p>
                        <form id="FilterPayKurir" methods="post" data-parsley-validate="">
                            <div class="row">
                                <div class="form-group mx-sm-3 mb-2">
                                    <div id="filterPayKurir" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                    <input type="hidden" id='dataPayKurirFilter'>
                                </div>
                                <div class="col-12 col-sm-3 col-lg-3">
                                    <select class="custom-select mr-sm-2" id="selectKurirPay" required="">
                                        <option value="">Kurir Name...</option>
                                        <option value="0">All Kurir</option>
                                        <?php while ($cols = $kurirs->fetch(PDO::FETCH_LAZY)){ ?>
                                        <option value="<?=$cols['id']?>"><?=$cols['nama_kurir']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-3 col-lg-3">
                                    <div class="btn-group mr-2" role="group" aria-label="First group" style="padding-top: 1%">
                                        <button type="submit" class="btn btn-sm btn-outline-success"><span class="fa fa-search"></span> filter</button>
                                        <button type="button" onClick="exportpaykurir('kurir')" class="btn btn-sm btn-outline-info"><span class="fa fa-download"></span> export</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table id="tablePayKurir" class="table table-bordered  <?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover" style="text-transform: capitalize;">
                            <thead class="thead-light">
                            <tr style="text-transform: lowercase;">
                                <th scope="col" width="8%">kurir</th>
                                <th scope="col" width="8%">Transaksi</th>
                                <th scope="col" width="20%">Kirim ke</th>
                                <th scope="col" width="18%">Delivery Date</th>
                                <th scope="col" width="18%">Remarks</th>
                                <th scope="col" width="10%">charge</th>
                                <th scope="col" width="10%">Delivery</th>
                                <th scope="col" width="10%">Subtotal</th>
                                <th scope="col" width="16%">action</th>
                            </tr> 
                            </thead>
                            <tbody>
                            </tbody>
                            <!-- <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total:</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total:</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total:</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot> -->
                        </table>
                        <div class="col-6 col-sm-4 col-lg-4 text-right">
                            <ul class="list-group mb-4">
                               
                                <li class="list-group-item d-flex justify-content-between" style="padding: .25rem .75rem !important;">
                                    <span>Total Transaksi </span>
                                    <strong id="totalPayment">$20</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between" style="padding: .25rem .75rem !important;">
                                    <span>Per Kurir </span>
                                    <strong id="totalPerKurir">$20</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between" style="padding: .25rem .75rem !important;">
                                    <span>Selisih </span>
                                    <strong id="selisih">$20</strong>
                                </li>
                            </ul>
                        </div>
                                        </br>
                        <!-- <form <?=$access['update']?> action="" id="reportPayCharge" data-parsley-validate="" autocomplete="off">
                            <div class="form-row align-items-center">
                                <div class="col-auto my-1">
                                    <input type="hidden" value="<?=$admin[0]['user_id']?>" id="reportPayChargeAdminID">
                                    <input type="hidden" value="<?=URL?>" id="reportPayChargeURL">
                                    <select class="custom-select form-control-sm mr-sm-2" id="reportPayChargeAdmin" required>
                                        <option value="">Choose...</option>
                                        <?php while ($cols = $kurirss->fetch(PDO::FETCH_LAZY)){ ?>
                                        <option value="<?=$cols['id']?>"><?=$cols['nama_kurir']?></option>
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

<div class="modal fade" id="modalKelurahan" role="dialog" aria-labelledby="modalProductAdd" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductAdd">List Kelurahan </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form id="addProductCheckout" data-parsley-validate="">
                <div class="form-group">
                    <input type="hidden" class="form-control" id="noTransaction">
                </div>
                <div class="form-group">
                  <select class="form-control" name="codeSearch" id="codeSearch" required>
                      <option value="">Choose...</option>
                      <?php while ($p = $charges->fetch(PDO::FETCH_LAZY)){ ?>
                      <option value="<?=$p->id?>" data-price="<?=$p->price?>"><?=$p->name?>_(<?=$config->formatPrice($p->price)?>)</option>
                      <?php } ?>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
                <div id="feedback-check"></div>
                <div id="checkProduct">
                    <button type="submit"  class="btn btn-block btn-primary ">submit</button>
                </div>
            </form>
      </div>
    </div>
  </div>
</div>