<?php 

$order = $config->ProductsJoin('transaction.transactionID, transaction.type, transaction.CustomerName, transaction.grandTotal, transaction_details.delivery_date, transaction_details.delivery_time, transaction.statusOrder, villages.name as kelurahan, transaction_details.id_florist', 'transaction', 'LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID LEFT JOIN villages ON villages.id = transaction_details.kelurahan_id', 'WHERE transaction.statusOrder = "0" GROUP BY transaction.transactionID');

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
                    <th scope="col" width="10%">transaction_code</th>
                    <th scope="col" width="10%">type</th>
                    <!-- <th scope="col" width="10%">customer_name</th> -->
                    <th scope="col" width="20%">delivery_date</th>
                    <th scope="col" width="10%">delivery_to</th>
                    <th scope="col" width="10%">florist</th>
                    <!-- <th scope="col" width="10%">grand_total</th> -->
                    <th scope="col" width="25%">status_order</th>
                    <th scope="col" width="20%">ACTION</th>
                </tr>
                </thead>
                <tbody>
                <?php  while($rows = $order->fetch(PDO::FETCH_LAZY)){
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

                    $statusOrder = '<select class="custom-select my-1 mr-sm-2" name="changeOrderStatus"  id="changeOrderStatus" required="">
                  <option value="">Change Status</option>
                  <option value="1" data-trx = '. $rows['transactionID'] .'>On Production</option>
               </select>';

               $button='<a href="'. URL .'order/?p=detailtrx&trx='. $rows['transactionID'] .'">
			    <button type="button" class="btn btn-outline-primary"><span class="fa fa-eye"></span></button></a>
               ';
			   $Kirim = Date('d-M-Y', strtotime($rows['delivery_date']));
			   if(Date('Y-m-d', strtotime($rows['delivery_date'])) == $config->getdate('Y-m-d')) { $Kirim = '<label class="badge badge-sm badge-danger">'. Date('d-M-Y', strtotime($rows['delivery_date'])) .'</label>';} ;
			   
                    ?>
                    <tr>
                        <td><?=$rows['transactionID']?></td>
                        <td><?=$type?></td>
                        <!-- <td><?=$rows['CustomerName']?></td> -->
                        <td><?=$Kirim?> <span class="text-danger small"><?=$rows['delivery_time']?></span></td>
                        <td><?=$rows['kelurahan']?></td>
                        <td><?=$florist?></td>
                        <!-- <td><?=$config->formatPrice($grandTotal)?></td> -->
                        <td><?=$statusOrder?></td>
                        <td><?=$button?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="selectFlorist" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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