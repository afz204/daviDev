<?php 
$arrstatusorder = array(
    0 => 'New order',
    1 => 'On Production',
    2 => 'On Delivery',
    3 => 'Success',
    4 => 'Return',
    5 => 'Complain',
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
WHERE transaction.statusOrder = "0" GROUP BY transaction.transactionID');
$order->execute();

$Listflorist = $config->Products('ID, FloristName', 'florist');
?>
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
                    <th scope="col">Type</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Sender Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total Payment</th>
                    <!-- <th scope="col">customer_name</th> -->
                    <th scope="col">delivery_date</th>
                    <th scope="col">delivery_to</th>
                    <!-- <th scope="col">grand_total</th> -->
                    <th scope="col">status_order</th>
                    <th scope="col">created order</th>
                    <th scope="col">created by</th>
                    <th scope="col">florist</th>
                    <th scope="col">ACTION</th>
                </tr>
                </thead>
                <tbody>
                <?php  while($rows = $order->fetch(PDO::FETCH_LAZY)){
                    $product = str_replace(',', '</br>', '<span class="badge badge-info">'.$rows['product'].'</span></br>');
                    $price = str_replace(',', '</br>', '<span class="badge badge-info">'.$rows['price'].'</span></br>');
                    $qty = str_replace(',', '</br>', '<span class="badge badge-info">'.$rows['quantity'].'</span></br>');

                    $type = 'ORGANIC'; 
                    if($rows['type'] == 'BD_CP'){
                    	$type = 'CORPORATE';
                    }

                    if(empty($rows['id_florist'])){
                    	$florist = '<button class="btn btn-sm btn-primary" onclick="selectFlorist(\''. $rows['transactionID'] .'\')" style="font-size: 12px;">select florist</button>';
                    }else{
                        $data = $config->getData('ID, FloristName', 'florist', "ID = '". $rows['id_florist'] ."'");
                        $florist = '<span class="badge badge-sm badge-success">'. $data['FloristName'] .'</span>';
                    }
                    $grandTotal = '0';
                    if(!empty($rows['grandTotal'])){
                    	$grandTotal = $rows['grandTotal'];
                    }

                    $status = [];
                    foreach($arrstatusorder as $key => $val) {
                        $status[$key] = '<option value="'. $key.'" data-trx = '. $rows['transactionID'] .'>'. $val .'</option>';
                    }
                    $button='<a href="'. URL .'order/?p=detailtrx&trx='. $rows['transactionID'] .'">
                        <button type="button" class="btn btn-sm btn-info">Details</button></a>
                    ';
                    $Kirim = Date('d-M-Y', strtotime($rows['delivery_date']));
                    $createorder = Date('d/M/Y', strtotime($rows['created_date']));
                    if(Date('Y-m-d', strtotime($rows['delivery_date'])) == $config->getdate('Y-m-d')) { $Kirim = '<label class="badge badge-sm badge-danger">'. Date('d-M-Y', strtotime($rows['delivery_date'])) .'</label>';} ;
                    
                    ?>
                    <tr>
                        <td><?=$rows['transactionID']?></td>
                        <td><?=$type?></td>
                        <td> <?=$product?></td>
                        <td><?=$rows['CustomerName']?></td>
                        <td> <?=$price?></td>
                        <td> <?=$qty?></td>
                        <td><?=$config->formatprice($rows['grandTotal'])?></td>
                        <!-- <td><?=$rows['CustomerName']?></td> -->
                        <td><?=$Kirim?> <span class="text-danger small"><?=$rows['delivery_time']?></span></td>
                        <td><?=$rows['kelurahan']?></td>
                        <!-- <td><?=$config->formatPrice($grandTotal)?></td> -->
                        <td>
                            <select class="custom-select my-1 mr-sm-2" name="changeOrderStatus"  id="changeOrderStatus" required="">
                                <option value="">Change Status</option>
                                <?php foreach($arrstatusorder as $key => $val) { ?>
                                    <option value="<?=$key?>" data-trx = '<?=$rows['transactionID'] ?>' <?=$rows['statusOrder'] == $key ? 'selected': '' ?> ><?=$val?></option>';
                                <?php } ?>
                            </select>
                        </td>
                        <td><?=$createorder?></td>
                        <td><?=$rows['admin']?></td>
                        <td><?=$florist?></td>
                        <td><?=$button?> </td>
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