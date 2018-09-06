<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 20.00
 */

session_start();
require '../../config/api.php';
$config = new Admin();

$admin = $config->adminID();

if($_GET['type'] == 'generate'){
    $type = $_POST['type'];
    if($type == '1'){
        $field = 'id_trx';
        $table = 'detail_trxs';
        $kode = 'BD_CP';
        $tgl = $config->getDate('Ydmhms');

        $new_code = $kode. $tgl;

    }else{
        $field = 'id_trx';
        $table = 'detail_trxs';
        $kode = 'BD_PR';
        $tgl = $config->getDate('Ydmhms');

        $new_code = $kode. $tgl;
    }
    // $tanggal = $config->getDate('Y-m-d H:m:s');

    // $sql = 'INSERT INTO transaction (transactionID, type, created_by) VALUES (:a, :b, :c)';

    // $stmt = $config->runQuery($sql);
    // $stmt->execute(array(
    //     ':a'    => $new_code,
    //     ':b'    => $kode,
    //     ':c'    => $admin
    // ));

    // if($stmt){
        echo $new_code;
        $logs = $config->saveLogs($new_code, $admin, 'f', 'Generate trx Code');
    // }else{
    //     echo 'Failed!';
    // }


}

if($_GET['type'] == 'deliveryCharges')
{
    $id = $_POST['id'];

    $stmt = $config->runQuery("SELECT delivery_charges.price, villages.id, villages.name FROM delivery_charges LEFT JOIN villages 
        ON villages.id = delivery_charges.id_kelurahan WHERE villages.id = :id ");
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
        # code...
        $data['id'] = $row['id'];
        $data['price'] = '('. $config->formatPrice($row['price']) .')';
        $data['kelurahan'] = $row['name'];
        $data['delivery_charges'] = $row['price'];
    }

    $data = json_encode($data);
    echo $data;
}

if($_GET['type'] == 'cardTemplate')
{
    $id = $_POST['id'];

    $stmt = $config->runQuery("SELECT id, level1, level3 FROM card_messages WHERE level2 = :id ");
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());
}

if($_GET['type'] == 'addProducts')
{
    $id = $_POST['id'];
    $trx = $_POST['trx'];

    //cek di product
    $stmt = $config->runQuery("SELECT * FROM products WHERE product_id = :id ");
    $stmt->execute(array(':id' => $id));

    if($stmt->rowCount() > 0){
        $info = $stmt->fetch(PDO::FETCH_LAZY);

        $cek = $config->runQuery("INSERT INTO transaction_details (id_trx, id_product, product_name, product_price, product_qty) VALUES (:a, :b, :c, :d, :e) ");
        $cek->execute(array(
            ':a' => $trx,
            ':b' => $id,
            ':c' => $info['name_product'],
            ':d' => $info['selling_price'],
            ':e' => '1'
        ));

        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'add product checkout');
        if($cek){
            //echo $config->actionMsg('c', 'detail_trxs');

            //insert to transaction total 
            $trxd = $config->getData('grandTotal', 'transaction', " transactionID = '". $trx ."'");

            $grandTotal = $trxd['grandTotal'] + $info['selling_price'];

            $transaction = $config->runQuery('UPDATE transaction SET grandTotal = :a WHERE transactionID = :b ');
            $transaction->execute(array(':a' => $grandTotal, ':b' => $trx));
            //

            $prod = $config->ProductsJoin('transaction_details.id, transaction_details.id_product,  transaction_details.product_price, transaction_details.product_qty, transaction_details.florist_remarks, products.product_id, products.name_product,
      products.cost_price, products.selling_price, products.note, products.images, products.permalink',
      'transaction_details', 'LEFT JOIN products ON products.product_id = transaction_details.id_product', "WHERE transaction_details.id_trx = '". $trx ."'");

            $data = ''; $proQty = '';
            $images = ''; $title = ''; $id = ''; $qty = ''; $cost = ''; $selling = ''; $price =''; $remarks='';
            while ($row = $prod->fetch(PDO::FETCH_LAZY)) {
                $images = $row['images'];
                $title = $row['name_product'];
                $id = $row['id'];
                $qty = $row['qty'];
                $cost = $config->formatPrice($row['cost_price']);
                $selling = $config->formatPrice($row['selling_price']);
                $price = $row['product_price'];
                $remarks = $row['florist_remarks'];

                if($qty >= 1){
                    $proQty = 'disabled';
                }

                //bawa data
                //totalBarang
                $barang =  $config->runQuery("SELECT id FROM transaction_details WHERE id_trx = :trx");
                $barang->execute(array(':trx' => $trx));
                $totalBarang = $barang->rowCount();

                //total transaction
                $transaction = $config->getData('SUM(product_price) as price, SUM(product_qty) as qty', 'transaction_details', " id_trx = '". $trx ."' ");
            
                $total = $config->formatPrice($transaction['price'] * $transaction['qty']);

                 $data = '<li class="list-group-item" id="ListProduct-'. $id .'">
                  <div class="checkout-content">
                     <div class="chekcout-img">
                        <picture>
                         <a href="http://localhost/bungdav/assets/images/product/'. $images .'" data-toggle="lightbox" data-gallery="example-gallery">
                               <img src="http://localhost/bungdav/assets/images/product/'. $images .'" class="img-fluid img-thumbnail">
                           </a>
                       </picture>
                     </div>
                     <div class="checkout-sometext" style="width: 120%">
                        <div class="title">'. $title .' <div class="pull-right"><button class="btn btn-sm btn-danger deleteListProduct" type="button" data-id="'. $id .'"><span class="fa fa-trash"></span></button></div></div>
                        <div class="count-product">
                           
                           <div class="center">
                              <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="minus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $trx .'" disabled="disabled"><span class="fa fa-minus"></span></button>
                                </div>
                                <input style="text-align: center;" type="text" value="1" id="count-product-number['. $id .']" name="count-product-number['. $id .']" min="1" max="10" class="input-number form-control form-control-sm" placeholder="" aria-label="" aria-describedby="basic-addon1" data-field="count-product-number['. $id .']" data-qty="'. $qty .'">
                                <div class="input-group-append">
                                  <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="plus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $trx .'"><span class="fa fa-plus"></span></button>
                                </div>
                              </div>
                            
                           </div>
                        </div>
                        <div class="text-info" style="font-size: 13px; font-weight: 600;">Cost_price: '. $cost .'</div>
                        <div class="price" style="width: 50%">
                          
                              <div class="input-group mb-3">
                                 <div class="input-group-prepend">
                                     <span class="input-group-text">Rp.</span>
                                   </div>
                                <input type="text" data-parsley-type="number" class="form-control" name="selling_price_product['. $id .']" id="selling_price_product['. $id .']" value="'.$price.'" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                  <button class="btn btn-outline-info selling_price_btn" type="button" data-id="selling_price_product['. $id .']" data-trx="'. $trx .'">Change</button>
                                </div>
                              </div>
                           
                        </div>
                        
                        <div class="important-notes">
                           <div class="note">
                              <form id="remarks_florist" data-parsley-validate="" novalidate="">
                                 <div class="form-group">
                                    <textarea class="form-control" name="isi_remarks['. $id .']" rows="5" required="" placeholder="remarks florist"></textarea>

                                 </div>
                                 <button class="btn btn-block btn-info isi_remarks_btn" type="button" data-id="isi_remarks['. $id .']">remarks</button>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
              </li>';
              $checkoutData = '
              <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                       <h6 class="my-0">Total Harga Barang</h6>
                    </div>
                    <span class="text-muted" id="subTotal">'. $total .'</span>
                 </li>
                 <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                       <h6 class="my-0">Biaya Kirim</h6>
                    </div>
                    <span class="text-muted" id="deliveryCharges">00</span>
                 </li>
                 <!-- <li class="list-group-item d-flex justify-content-between bg-light">
                    <div class="text-danger">
                       <h6 class="my-0">Promo code</h6>
                       <small class="badge badge-danger">#BULANBERKAH</small>
                    </div>
                    <span class="text-danger">-Rp. 100.000.00</span>
                 </li> -->
                 <li class="list-group-item d-flex justify-content-between">
                    <strong>Total Belanja</strong>
                    <strong id="totalTransaction">'. $total .'</strong>
                 </li>
              ';

              $content = array(
                'data' => $data,
                'qty' => $totalBarang,
                'checkout' => $checkoutData
              );
                
            
            }
            echo json_encode($content, true);
        }else{
            echo 'Failed!';
        }
    }else{
        echo 'Product Not Found!';
    }
    
}

if($_GET['type'] == 'changePriceProduct'){
    $a = $_POST['id'];
    $b = $_POST['new_price'];

    $a = explode('[', $a);
    $a = explode(']', $a[1]);

    $id = $a[0];
    
    $update = $config->runQuery("UPDATE transaction_details SET product_price ='". $b ."' WHERE id = '". $id ."' ");
    $update->execute();

    if($update)
    {
        $logs = $config->saveLogs($id, $admin, 'u', 'update price checkout!');
        
        $data = array(
            'msg' => $config->actionMsg('u', 'transaction_details'),
            'price' => $b
        );
        $data = json_encode($data, true);
        echo $data;
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'addRemarksProduct'){
    $a = $_POST['id'];
    $b = $_POST['remarks'];

    $a = explode('[', $a);
    $a = explode(']', $a[1]);

    $id = $a[0];
    
    $update = $config->runQuery("UPDATE transaction_details SET florist_remarks ='". $b ."' WHERE id = '". $id ."' ");
    $update->execute();

    if($update)
    {
        $logs = $config->saveLogs($id, $admin, 'u', 'update price checkout!');
        echo $config->actionMsg('u', 'transaction_details');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'changeQty'){
    $a = $_POST['id'];
    $b = $_POST['types'];
    $c = $_POST['count'];

    $a = explode('[', $a);
    $a = explode(']', $a[1]);

    $id = $a[0];
    
    $cek = $config->getData('id_product, product_price, product_qty', 'transaction_details', " id = '". $id ."' ");

    $newPrice = $cek['product_price'] * $c;
    if($b == 'minus'){

        //echo 'types: minus, newPrice: '. $newPrice . ' id: '.$id; 
        $update = $config->runQuery("UPDATE transaction_details SET product_qty = '". $c ."' WHERE id = '". $id ."' ");
        $update->execute();

        $logs = $config->saveLogs($id, $admin, 'u', 'Mengurangi Qty checkout product!');
        $data = array(
            'price' => $newPrice,
            'id' => $id
        );
        $data = json_encode($data, true);
        echo $data;

        //echo $config->actionMsg('u', 'transaction_details');
    }else{
        
        //echo 'types: plus, newPrice: '. $newPrice . ' id: '.$id; 
        $update = $config->runQuery("UPDATE transaction_details SET product_qty = '". $c ."' WHERE id = '". $id ."' ");
        $update->execute();

        $logs = $config->saveLogs($id, $admin, 'u', 'Menambah Qty checkout product!');
        $data = array(
            'price' => $newPrice,
            'id' => $id
        );
        $data = json_encode($data, true);
        echo $data;
        //echo $config->actionMsg('u', 'transaction_details');
    }

}

if($_GET['type'] == 'listCheckout'){
    $a = $_POST['transctionID'];

    $product = $config->runQuery("SELECT transaction_details.id FROM transaction_details WHERE transaction_details.id_trx = '". $a ."' ");
    $product->execute();
    
    $totalRow = $product->rowCount();

    $delivery = $config->getData('delivery_charge', '  transaction', " transaction.transactionID = '". $a ."'");
    $deliveryCharge = $delivery['delivery_charge'];
    if(empty($delivery['delivery_charge'])) $deliveryCharge = '0';
    
    $total = $config->getData('SUM(detail.product_qty * detail.product_price) as subtotal', '  transaction_details as detail', " detail.id_trx = '". $a ."'");
    $totalTransaction = $total['subtotal'];

    $grandTotal = $totalTransaction + $deliveryCharge;
        $dataContent = '
    <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
               <h6 class="my-0">Total Harga Barang</h6>
            </div>
            <span class="text-muted" id="subTotal">'. $config->formatPrice($totalTransaction) .'</span>
         </li>
         <li class="list-group-item d-flex justify-content-between lh-condensed">
            <div>
               <h6 class="my-0">Biaya Kirim</h6>
            </div>
            <span class="text-muted" id="deliveryCharges">'. $config->formatPrice($deliveryCharge).'</span>
         </li>
         <li class="list-group-item d-flex justify-content-between">
            <strong>Total Belanja</strong>
            <strong id="totalTransaction">'. $config->formatPrice($grandTotal) .'</strong>
         </li>
    ';
    

    
    $data = array(
        'totalRow' => $totalRow,
        'product' => $dataContent,
        'subtotal' => $totalTransaction,
        'delivery_charge' => $deliveryCharge,
        'grandtotal' => $grandTotal
    );

    $data = json_encode($data, true);
    echo $data;
}

if($_GET['type'] == 'deleteProduct'){
    $a = $_POST['dataID'];

    $hapus = $config->delRecord('transaction_details', 'id', $a);

    if($hapus)
    {
        echo $config->actionMsg('d', 'transaction_details');
        $logs = $config->saveLogs($a, $admin, 'd', 'hapus list product checkout');
    }else{
        echo 'Failed';
    }
}
if($_GET['type'] == 'step1'){
    $a = $_POST['TransactionID'];
    $b = $_POST['CustomerID'];
    $c = $_POST['picID'];
    $d = $_POST['namePic'];

    $type = substr($a, 0, 5);

    $data = $config->getDataTable('transactionID', 'transaction', " transactionID = '". $a ."' ");
    if($data->rowCount() > 0 ){
        //edit
        $update = $config->runQuery("UPDATE transaction SET CustomerID = '". $b ."', CustomerName = '". $d ."' WHERE transactionID = '". $a ."' ");
        $update->execute();

        if($update) {
            echo $config->actionMsg('u', 'transaction');
            $logs = $config->saveLogs($a, $admin, 'u', 'Customer');
        } else {
            echo 'Failed!';
        }
    }else{
        //new
        $input = $config->runQuery("INSERT INTO transaction (transactionID, type, CustomerID, CustomerName) VALUES (:a, :b, :c, :d)");
        $input->execute(array(
            ':a'    => $a,
            ':b'    => $type,
            ':c'    => $b,
            ':d'    => $d
        ));
        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'add transactionID');
        if($input)
        {
            echo $config->actionMsg('c', 'transaction');
        }else{
            echo 'Failed!';
        }
    }
}
if($_GET['type'] == 'step2'){
    $a = $_POST['Name'];
    $b = $_POST['Email'];
    $c = $_POST['Provinsi'];
    $d = $_POST['Kota'];
    $e = $_POST['Kec'];
    $f = $_POST['Kel'];
    $g = $_POST['Alamat'];
    $trx = $_POST['TransactionID'];

   
    $data = $config->getDataTable('transactionID', 'transaction', " transactionID = '". $trx ."' ");
    if($data->rowCount() > 0 ){
        //edit
        $update = $config->runQuery("UPDATE transaction SET nama_penerima = :a, email = :b, provinsi_id = :c, kota_id = :d, kecamata_id = :e, kelurahan_id = :f, alamat_penerima = :g WHERE transactionID = :trx");
        $update->execute(array(
            ':a'    => $a,
            ':b'    => $b,
            ':c'    => $c,
            ':d'    => $d,
            ':e'    => $e,
            ':f'    => $f,
            ':g'    => $g,
            ':trx'  => $trx
        ));
        $logs = $config->saveLogs($trx, $admin, 'u', 'update detail transaction');
        if($update)
        {
            $charge = $config->getData('price', '  delivery_charges', " delivery_charges.id_kelurahan = '". $f ."'");

            if($charge) {
                die(json_encode(['response' => 'OK', 'msg' => $charge['price']], JSON_FORCE_OBJECT));
            } else {
                die(json_encode(['response' => 'ERROR', 'msg' => $config->actionMsg('u', 'transaction')], JSON_FORCE_OBJECT));
            }
        }else{
            echo 'Failed!';
        }
    }else{
        //new
        echo 'NEwQ';
    }
}
if($_GET['type'] == 'step3'){
    $a = $_POST['TransactionID'];
    $b = $_POST['deliverCharge'];
    $c = $_POST['deliveryDate'];
    $d = $_POST['deliveryTimes'];
    $e = $_POST['deliveryRemarks'];

   
    $data = $config->getDataTable('TransactionID', 'transaction', " TransactionID = '". $a ."' ");
    if($data->rowCount() > 0 ){
        //edit
        $update = $config->runQuery("UPDATE transaction SET delivery_charge = :a, delivery_date = :b, delivery_time = :c, delivery_marks = :d WHERE TransactionID = :trx");
        $update->execute(array(
            ':a'    => $b,
            ':b'    => $c,
            ':c'    => $d,
            ':d'    => $e,
            ':trx'  => $a
        ));
        $logs = $config->saveLogs($a, $admin, 'u', 'update detail transaction');
        if($update)
        {
            echo $config->actionMsg('u', 'transaction');
        }else{
            echo 'Failed!';
        }
    }else{
        //new
        echo 'NEwQ';
    }
}
if($_GET['type'] == 'step4'){
    $a = $_POST['TransactionID'];
    $b = $_POST['from'];
    $c = $_POST['to'];
    $d = $_POST['msg'];
    $e = $_POST['level1'];
    $f = $_POST['level2'];

   
    $data = $config->getDataTable('transactionID', 'transaction', " transactionID = '". $a ."' ");
    if($data->rowCount() > 0 ){
        //edit
        $update = $config->runQuery("UPDATE transaction SET card_from = :a, card_to = :b, card_template1 = :c, card_template2 = :d, card_isi = :e WHERE transactionID = :trx");
        $update->execute(array(
            ':a'    => $b,
            ':b'    => $c,
            ':c'    => $e,
            ':d'    => $f,
            ':e'    => $d,
            ':trx'  => $a
        ));
        $logs = $config->saveLogs($a, $admin, 'u', 'update messages transaction');
        if($update)
        {
            echo $config->actionMsg('u', 'transaction');
        }else{
            echo 'Failed!';
        }
    }else{
        //new
        echo 'NEwQ';
    }
}
if($_GET['type'] == 'PaymentSelected'){
    $a = $_POST['transctionID'];
    $b = $_POST['paymentID'];


    $stmt = "UPDATE transaction SET paymentID = :pay WHERE transactionID = :trx";
    $stmt = $config->runQuery($stmt);
    $stmt->execute(array(':pay' => $b, ':trx' => $a));

    if($stmt){
        echo $config->actionMsg('u', 'transaction');
        $logs = $config->saveLogs($a, $admin, 'u', 'update payment order');
    }else{
        echo 'Failed!';
    }
}
if($_GET['type'] == 'proccessOrder'){
    $a = $_POST['transctionID'];
    $delivery = $config->getData('delivery_charge', '  transaction', " transaction.transactionID = '". $a ."'");
    $deliveryCharge = 0;
    if($delivery['delivery_charge'] > 0) { $deliveryCharge = $delivery['delivery_charge']; }
    $total = $config->getData('SUM(detail.product_qty * detail.product_price) as subtotal', '  transaction_details as detail', " detail.id_trx = '". $a ."'");
    $totalTransaction = $total['subtotal'];

    $grandTotal = $totalTransaction - $deliveryCharge;


    $stmt = "UPDATE transaction SET statusOrder = '0', grandTotal = '". $grandTotal ."' WHERE transactionID = :trx";
    $stmt = $config->runQuery($stmt);
    $stmt->execute(array(
        ':trx' => $a
    ));

    if($stmt){
        echo $config->actionMsg('u', 'transaction'); 
        $logs = $config->saveLogs($a, $admin, 'u', 'proccess order');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'changeOrderStatus'){
    $a = $_POST['status'];
    $b = $_POST['transctionID'];
	$c = $_POST['types'];
	
	if($c == 'florist'){
		$cek = $config->getData('id_florist, id_kurir', 'transaction', "transactionID ='". $b ."' ");
		
		if(empty($cek['id_florist']) && $c == 'florist')
		{
			echo 'Pilih Florist Terlebih dahulu!';
        }
         elseif(empty($cek['id_kurir'])) {
            echo 'Pilih Kurir Terlebih dahulu!';
        } 
        else { 
            $stmt = "UPDATE transaction SET statusOrder = '". $a ."' WHERE transactionID = '". $b ."'";
			$stmt = $config->runQuery($stmt);
			$stmt->execute();

			if($stmt) {
				echo $config->actionMsg('u', 'transaction');
				$logs = $config->saveLogs($a, $admin, 'u', 'update statusOrder');
			} else {
				echo 'Failed!';
			}
		}
	}
}

if($_GET['type'] == 'addDeliveryCharges'){
    $a = $_POST['transctionID'];
    $b = $_POST['transctionPrice'];


    $stmt = "UPDATE transaction SET delivery_charge = '". $b ."' WHERE transactionID = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();

    if($stmt){
        echo $config->actionMsg('u', 'transaction');
        $logs = $config->saveLogs($a, $admin, 'u', 'update delivery_charge');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'selectFlorist'){
    $a = $_POST['transctionID'];
    $b = $_POST['floristID'];

    $stmt = "UPDATE transaction SET id_florist = '". $b ."' WHERE transactionID = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();

    if($stmt){
        echo $config->actionMsg('u', 'transaction_details');
        $logs = $config->saveLogs($a, $admin, 'u', 'update florist!');
    }else{
        echo 'Failed!';
    }
}
if($_GET['type'] == 'selectKurir'){
    $a = $_POST['transctionID'];
    $b = $_POST['KurirID'];

    $stmt = "UPDATE transaction_details SET id_kurir = '". $b ."' WHERE id_trx = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();

    if($stmt){
        $insert = "INSERT INTO kurir_jobs (TransactionNumber, KurirID, Created_by) VALUES ('". $a ."', '". $b ."', '". $admin ."')";
        $insert = $config->runQuery($insert);
        $insert->execute();
        echo $config->actionMsg('u', 'transaction_details');
        $logs = $config->saveLogs($a, $admin, 'u', 'update kurir!');
    }else{
        echo 'Failed!';
    }
}
if($_GET['type'] == 'removecharges'){
    $a = $_POST['transctionID'];

    $stmt = "UPDATE transaction SET delivery_charge = '' WHERE transactionID = '". $a ."'";
    $stmt = $config->runQuery($stmt);
    $stmt->execute();

    if($stmt){
        echo $config->actionMsg('u', 'transaction');
        $logs = $config->saveLogs($a, $admin, 'u', 'hapus delivery charge!');
    }else{
        echo 'Failed!';
    }
}