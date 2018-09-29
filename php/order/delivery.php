<?php 
$arrstatusorder = array(
    0 => 'New order',
    1 => 'On Production',
    2 => 'On Delivery',
    3 => 'Success',
    4 => 'Return',
    5 => 'Complain',
);
$arrstatuspaid = array(
    0 => 'UNPAID',
    1 => 'PAID'
);
$arrtime = [
    0 => '9am - 1pm',
    1 => '2pm - 5pm',
    2 => '6pm - 8pm',
    3 => '9pm - 0am',
    4 => '1am - 5am',
    5 => '6am - 8am'
];
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
WHERE transaction.statusOrder = "2" GROUP BY transaction.transactionID');
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
        <div id="listOrder">
            <table id="tableOrder" class="table table-hover<?=$device['device']=='MOBILE' ? 'table-responsive' : ''?> table-condensed table-hover">
                <thead class="thead-light">
                <tr style="text-transform: lowercase;">
                    <th scope="col">Invoice</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Sender Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total Payment</th>
                    <!-- <th scope="col">customer_name</th> -->
                    <th scope="col">delivery_date</th>
                    <!-- <th scope="col">delivery_to</th> -->
                    <!-- <th scope="col">grand_total</th> -->
                    <th scope="col">status_order</th>
                    <th scope="col">status_paid</th>
                    <th scope="col">created order</th>
                    <th scope="col">created by</th>
                    <th scope="col">florist</th>
                    <th scope="col">kurir</th>
                    <!-- <th scope="col">ACTION</th> -->
                </tr>
                </thead>
                <tbody>
                <?php  while($rows = $order->fetch(PDO::FETCH_LAZY)){

                    $product = explode(',', $rows['product']);
                    $price = explode(',', $rows['price']);
                    $quantity = explode(',', $rows['quantity']);
                 
                    $type = [ 'nama' => 'ORGANIC' ]; 
                    if($rows['type'] == 'BD_CP'){
                    	$type = $config->getData('*', 'corporates', "CorporateUniqueID = '". $rows['CustomerID'] ."'");
                    }
                    if(empty($rows['id_florist'])){
                    	$florist = '<button class="btn btn-sm btn-primary" onclick="selectFlorist(\''. $rows['transactionID'] .'\')" style="font-size: 12px;">select florist</button>';
                    }else{
                        $data = $config->getData('ID, FloristName', 'florist', "ID = '". $rows['id_florist'] ."'");
                        $florist = '<span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span>';
                    }
                    if(empty($rows['id_kurir'])){
                    	$kurir = '<button class="btn btn-sm btn-primary" onclick="pilihKurir(\''. $rows['transactionID'] .'\')" style="font-size: 12px;">select kurir</button>';
                    }else{
                        $data = $config->getData('id, nama_kurir', 'kurirs', "id = '". $rows['id_kurir'] ."'");
                        $kurir = '<span class="badge badge-sm badge-success">'. $data['nama_kurir'] .'</span>';
                    }
                    
                    $grandTotal = '0';
                    if(!empty($rows['grandTotal'])){
                    	$grandTotal = $rows['grandTotal'];
                    }
                    $button='<a href="'. URL .'order/?p=detailtrx&trx='. $rows['transactionID'] .'">
                        <button type="button" class="btn btn-sm btn-info">Details</button></a>
                    ';
                    $Kirim = Date('d-M-Y', strtotime($rows['delivery_date']));
                    $createorder = Date('d/M/Y', strtotime($rows['created_date']));
                    ?>
                    <tr <?=Date('Y-m-d', strtotime($rows['delivery_date'])) == $config->getdate('Y-m-d') ? 'style="background-color:#dc3545 !important; color: #fff !important; font-weight: 500 !important;"' : '' ?> >
                    <td><a href="<?=URL?>order/?p=detailtrx&trx=<?=$rows["transactionID"]?>" target="_blank" ><?=$rows['transactionID']?></a></td>
                        <td> <?php foreach($product as $val => $key) { echo '<span class="badge badge-info">'.$key.'</span></br>'; } ?> </td>
                        <td><?=$rows['CustomerName']?> <small class="badge badge-sm badge-info"><?=$type['nama']?></small></td>
                        <td> <?php foreach($price as $val => $key) { echo '<span class="badge badge-info">'.$config->formatprice($key).'</span></br>'; } ?> </td>
                        <td> <?php foreach($quantity as $val => $key) { echo '<span class="badge badge-info">'.$key.'</span></br>'; } ?> </td>
                        <td><?=$config->formatprice($rows['grandTotal'])?></td>
                        <td><?=$Kirim?> <span class="small"><?=$arrtime[$rows['delivery_time']]?></span></td>
                        <!-- <td><?=$rows['kelurahan']?></td> -->
                        <td><span class="badge badge-sm badge-info"><?=$arrstatusorder[$rows['statusOrder']]?></span></td>
                        <td><span class="badge badge-sm badge-<?=$rows['statusPaid'] == 1 ? 'success' : 'warning'?>"><?=$arrstatuspaid[$rows['statusPaid']]?></span></td>
                        <td><?=$createorder?></td>
                        <td><?=$rows['admin']?></td>
                        <td><?=$florist?></td>
                        <td><?=$kurir?></td>
                        <!-- <td><?=$button?> </td> -->
                    </tr>
                <?php } ?>
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