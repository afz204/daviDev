<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 14.26
 */

$order = $config->ProductsJoin('transaction.transactionID, transaction.type, transaction.CustomerName, transaction.grandTotal, transaction_details.delivery_date, transaction_details.delivery_time, transaction.statusOrder, villages.name as kelurahan, transaction_details.id_florist, transaction_details.id_kurir', 'transaction', 'LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID LEFT JOIN villages ON villages.id = transaction_details.kelurahan_id', 'WHERE transaction.statusOrder = "2" GROUP BY transaction.transactionID');

$kurir = $config->Products('id, nama_kurir', 'kurirs');

$kurirdata = [];
while($k = $kurir->fetch(PDO::FETCH_LAZY)){
    $kurirdata[$k['id']] = $k['nama_kurir'];
}
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
                    <th scope="col" width="10%">transaction_code</th>
                    <th scope="col" width="10%">type</th>
                    <th scope="col" width="10%">customer_name</th>
                    <th scope="col" width="10%">delivery_date</th>
                    <th scope="col" width="10%">delivery_to</th>
                    <th scope="col" width="10%">kurir</th>
                    <th scope="col" width="10%">grand_total</th>
                    <th scope="col" width="10%">status_order</th>
                    <th scope="col" width="25%">ACTION</th>
                </tr>
                </thead>
                <tbody>
                <?php  while($rows = $order->fetch(PDO::FETCH_LAZY)){
                    $type = 'ORGANIC'; 
                    if($rows['type'] == 'BD_CP'){
                    	$type = 'CORPORATE';
                    }

                    if(empty($rows['id_kurir'])){
                    	$kurir = '<button class="btn btn-sm btn-primary" style="font-size: 12px;">select kurir</button>';
                    }else{
                        $kurir = $rows['id_kurir'];
                    }
                    $grandTotal = '0';
                    if(!empty($rows['grandTotal'])){
                    	$grandTotal = $rows['grandTotal'];
                    }

                    $statusOrder = '<select class="custom-select my-1 mr-sm-2" name="changeOrderStatus"  id="changeOrderStatus" required="">
                  <option value="">Change Status</option>
                  <option value="3" data-trx = '. $rows['transactionID'] .'>Success</option>
                  <option value="4" data-trx = '. $rows['transactionID'] .'>Return</option>
                  <option value="5" data-trx = '. $rows['transactionID'] .'>Complain</option>
               </select>';

               $button='
               <div class="btn-group" role="group" aria-label="First group">
			    <button type="button" class="btn btn-outline-primary"><span class="fa fa-eye"></span></button>
			  </div>
               ';
                    ?>
                    <tr>
                        <td><?=$rows['transactionID']?></td>
                        <td><?=$type?></td>
                        <td><?=$rows['CustomerName']?></td>
                        <td><?=Date('d-M-Y', strtotime($rows['delivery_date']))?> <span class="text-danger small"><?=$rows['delivery_time']?></span></td>
                        <td><?=$rows['kelurahan']?></td>
                        <td><?=$kurirdata[$rows['id_kurir']]?></td>
                        <td><?=$config->formatPrice($grandTotal)?></td>
                        <td><?=$statusOrder?></td>
                        <td><?=$button?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>