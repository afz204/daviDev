<?php 
$arrstatusorder = array(
    0 => 'New order',
    1 => 'On Production',
    2 => 'On Delivery',
    3 => 'Success',
    4 => 'Return',
    5 => 'Complain',
);
$badge = 'info';

$arrstatuspaid = array(
    0 => 'UNPAID',
    1 => 'PAID'
);
$order = $config->runQuery('SELECT 
(select GROUP_CONCAT(transaction_details.product_name SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as product, 
(select GROUP_CONCAT(transaction_details.product_price SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as price, 
(select GROUP_CONCAT(transaction_details.product_cost SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as cost, 
(select GROUP_CONCAT(transaction_details.product_qty SEPARATOR ",") from transaction_details where transaction_details.id_trx = transaction.transactionID) as quantity, 
transaction.*, transaction_details.*, villages.name as kelurahan, users.name as admin
FROM transaction 
LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
LEFT JOIN villages ON villages.id = transaction.kelurahan_id 
LEFT JOIN users ON users.id = transaction.created_by 
WHERE transaction.statusOrder NOT IN (0, 1, 2) GROUP BY transaction.transactionID');
$order->execute();

$Listflorist = $config->Products('ID, FloristName', 'florist');
$listkurir = $config->Products('id, nama_kurir', 'kurirs');
?>
<style>
    .card-body {
        padding: unset !important;
        padding-top: 1% !important;
    }
</style>
<div class="card">
    <div class="card-header" <?=$access['read']?>>
        List Order
    </div>
    <div class="card-body">
            <form id="CancelOrder" class="form-inline" methods="post" data-parsley-validate="" style="    padding-left: 1.2222222%;">
                <div class="row">
                    <div class="form-group mx-sm-3">
                    <label for="dateneworder">Delivery Date</label>
                        <div id="dateneworder" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        <input type="hidden" id='daterangeneworder'>
                    </div>
                    <div class="form-group" style="padding-left: 3px; padding-top: 24px">
                    <button class="btn btn-outline-secondary" type="submit"><span class="fa fa-search"></span> filter</button>
                    </div>
                </div>
            </form>
        <div id="TableCancelOrder">
            <table id="tableCancelOrder" class="table table-hover<?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col">Invoice</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">delivery_date</th>
                    <th scope="col">delivery_to</th>
                    <!-- <th scope="col">grand_total</th> -->
                    <th scope="col">status_order</th>
                    <th scope="col">notes</th>
                    <th scope="col">status_paid</th>
                    <th scope="col">created order</th>
                    <th scope="col">florist</th>
                    <th scope="col">kurir</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="selectFlorist" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

            <div class="modal-body">
                <form id="formSelectFlorist" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
                    <div class="form-group">
                        <select class="form-control" name="ListSelectedFlorist" id="ListSelectedFlorist" required>
                            <option value="">:: florist ::</option>
                            <?php while ($row = $Listflorist->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$row->ID?>"><?=$row->FloristName?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" name="IDSelectedFlorist">
                    <button class="btn btn-success btn-sm btn-block" type="submit">Pilih Florist</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="chagestatusorder" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
            <div class="modal-body">
                <form id="formChangeStatusOrder" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
                    <div class="form-group">
                        <select class="form-control" name="listStatusOrder" id="listStatusOrder" required>
                            <option value="">:: change status ::</option>
                            <?php foreach ($arrstatusorder as $key => $val){ ?>
                            <option value="<?=$key?>"><?=$val?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" name="NomorTransaction">
                    <input type="hidden" name="TypeStatus" value="florist">
                    <button class="btn btn-success btn-sm btn-block" type="submit">Change Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalselectkurir" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
            <div class="modal-body">
                <form id="formSelectKurir" method="post" data-parsley-validate="" class="needs-validation" novalidate="" autocomplete="off">
                    <div class="form-group">
                        <select class="form-control" name="listKurir" id="listKurir" required>
                            <option value="">:: pilih kurir ::</option>
                            <?php while ($kr = $listkurir->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$kr['id']?>"><?=$kr['nama_kurir']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" name="TransactionNumberKurir">
                    <button class="btn btn-success btn-sm btn-block" type="submit">Pilih Kurir</button>
                </form>
            </div>
        </div>
    </div>
</div>