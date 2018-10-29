<?php
    $kelurahan = $config->Products('delivery_charges.id as idcarge, delivery_charges.id_kelurahan, villages.name as villagename, delivery_charges.price as harga', 'delivery_charges LEFT JOIN villages ON villages.id = delivery_charges.id_kelurahan');
    $arrtime = [
        0 => '9am - 1pm',
        1 => '2pm - 5pm',
        2 => '6pm - 8pm',
        3 => '9pm - 0am Rp. 100.000 JABODETABEK',
        4 => '1am - 5am Rp. 200.000 JABODETABEK',
        5 => '6am - 8am Rp. 50.000 JABODETABEK'
    ];
    $trx = '0'; if(isset($_GET['trx'])) { $trx = $_GET['trx']; }

    $provinsi = $config->Products('id, name', 'provinces');
    $prov = [];
    while($pr = $provinsi->fetch(PDO::FETCH_LAZY)){
        $prov[$pr['id']] = array(
            'name' => $pr['name'],
        );
    }
    $regencies = $config->Products('id, name', 'regencies');
    $reg = [];
    while($re = $regencies->fetch(PDO::FETCH_LAZY)){
        $reg[$re['id']] = array(
            'name' => $re['name'],
        );
    }
    $districts = $config->Products('id, name', 'districts');
    $dis = [];
    while($di = $districts->fetch(PDO::FETCH_LAZY)){
        $dis[$di['id']] = array(
            'name' => $di['name'],
        );
    }
    $villages = $config->Products('id, name', 'villages');
    $vil = [];
    while($vi = $villages->fetch(PDO::FETCH_LAZY)){
        $vil[$vi['id']] = array(
            'name' => $vi['name'],
        );
    }
    $transaction = $config->ProductsJoin('transaction.*, transaction_details.*, transaction_details.id as productID, customer.*, corporates.*, payment.PaymentName', 'transaction', 
    'LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID 
    LEFT JOIN customer ON customer.ID = transaction.CustomerID 
    LEFT JOIN corporates ON corporates.CorporateUniqueID = transaction.CustomerID 
    LEFT JOIN payment ON payment.ID = transaction.paymentID', "WHERE transaction.transactionID = '". $trx ."'");
    $product = [];
    $data = [];
    while($trx = $transaction->fetch(PDO::FETCH_LAZY)){
        $data = array(
            'transactionID' => $trx['transactionID'],
            'type' => $trx['type'],
            'CustomerID' => $trx['CustomerID'],
            'CustomerName' => $trx['CustomerName'],
            'CorporateNames' => $trx['nama'],
            'OrganicNames' => $trx['FullName'],
            'invoice_name' => $trx['invoice_name'],
            'delivery_charge' => $trx['delivery_charge'],
            'delivery_time_charge' => $trx['delivery_time_charge'],
            'grandTotal' => $trx['grandTotal'],
            'paymentID' => $trx['paymentID'],
            'statusOrder' => $trx['statusOrder'],
            'statusPaid' => $trx['statusPaid'],
            'created_date' => $trx['created_date'],
            'id_florist' => $trx['id_florist'],
            'id_kurir' => $trx['id_kurir'],
            'nama_penerima' => $trx['nama_penerima'],
            'email' => $trx['email'],
            'provinsi_id' => $prov[$trx['provinsi_id']]['name'],
            'kota_id' => $reg[$trx['kota_id']]['name'],
            'kecamata_id' => $dis[$trx['kecamata_id']]['name'],
            'kelurahan' => $trx['kelurahan_id'],
            'alamat_penerima' => $trx['alamat_penerima'],
            'delivery_date' => $trx['delivery_date'],
            'delivery_time' => $trx['delivery_time'],
            'delivery_marks' => $trx['delivery_marks'],
            'card_from' => $trx['card_from'],
            'card_to' => $trx['card_to'],
            'card_template1' => $trx['card_template1'],
            'card_template2' => $trx['card_template2'],
            'card_isi' => $trx['card_isi'],
            'hp_penerima' => $trx['hp_penerima'],
        );
        
        $prod = $config->Products('product_id, category_id, subcategory_id, name_product, cost_price, selling_price, available_on, sort_desc, full_desc, note, images', "products WHERE product_id = '". $trx['id_product'] ."' ");
        
        while($p = $prod->fetch(PDO::FETCH_LAZY)){
            $product[] = array(
                'product_id'    => $p['product_id'],
                'category_idcategory_id'    => $p['category_id'],
                'subcategory_id'    => $p['subcategory_id'],
                'name_product'    => $p['name_product'],
                'cost_price'    => $p['cost_price'],
                'selling_price'    => $p['selling_price'],
                'sort_desc'    => $p['sort_desc'],
                'full_desc'    => $p['full_desc'],
                'note'    => $p['note'],
                'images'    => $p['images'],
                'florist_remarks'    => $trx['florist_remarks'],
                'product_qty'    => $trx['product_qty'],
                'product_price'    => $trx['product_price'],
                'product_cost'    => $trx['product_cost'] != '' ? $trx['product_cost'] : $p['cost_price'],
                'delivery_charge'    => $trx['delivery_charge'],
                'delivery_charge_time'    => $trx['delivery_charge_time'],
                'productTransactionID'    => $trx['productID']
            );
        }
    }
	$category = $config->Category();
	$cat = $config->Products('id, name', 'categories WHERE parent_id != 0 ');

?>
<style>

</style>

<div class="card" <?=$access['create']?>>
   <div class="row justify-content-center card-body">
      <div id="imagesProduct" class="col-12 col-md-4 col-lg-4">
         <div class="card-header">
            <h5 class="card-title">Product</h5> 
            <!-- <button class="btn btn-sm btn-warning" onclick="editproduct()">Edit</button> -->
         </div>
         <div class="card-body">
            <?php foreach($product as $prod) { ?>
            <picture>
               <a href="<?=URL.'assets/images/product/'.$prod['images']?>" data-toggle="lightbox" data-gallery="example-gallery">
               <img src="<?=URL.'assets/images/product/'.$prod['images']?>" class="img-fluid img-thumbnail">
               </a>
            </picture>
            <table class="table table-bordered table-responsive">
                <tr id="btn-product" class="">
                    <td colspan="3">
                    <button class="btn btn-sm btn-block btn-danger " onclick="hapusproduct(<?=$prod['productTransactionID']?>)">Hapus</button>
                    </td>
                </tr>
                <tr>
                    <td width="40%">Nama Product</td>
                    <td width="5%">:</td>
                    <td width="55%"><?=$config->_parsingproductname($prod['name_product'])?></td>
                </tr>
                <tr>
                    <td width="40%">Cost Price</td>
                    <td width="5%">:</td>
                    <td width="55%"><?=$config->formatPrice($prod['product_cost'])?></td>
                </tr>
                <tr>
                    <td width="40%">Selling Price</td>
                    <td width="5%">:</td>
                    <td width="55%"><?=$config->formatPrice($prod['product_price'])?></td>
                </tr>
                <tr>
                    <td width="40%">Delivery Charge</td>
                    <td width="5%">:</td>
                    <td width="55%"><?=$config->formatPrice($prod['delivery_charge'])?></td>
                </tr>
                <tr>
                    <td width="40%">Time Charge</td>
                    <td width="5%">:</td>
                    <td width="55%"><?=$config->formatPrice($prod['delivery_charge_time'])?></td>
                </tr>
                <tr>
                    <td width="40%">Quantity</td>
                    <td width="5%">:</td>
                    <td width="55%"><?=$prod['product_qty']?></td>
                </tr>
                <tr>
                    <td width="40%">Description</td>
                    <td width="5%">:</td>
                    <td width="55%"><?=$prod['full_desc']?></td>
                </tr>
            </table>
            <div class="title" style="text-align: center; font-size: 18px;">
               
            </div>
            <?php if($prod['florist_remarks'] != '') { ?>
            <div class="desc" style="padding: 1%; background-color: #dc3545; border-radius: 4px; margin-bottom: 5%; color: #fff;
    font-weight: 500;
    font-size: 14px;">
               <span style="text-align: center; font-size: 14px; text-transform: capitalize;"><?=$prod['florist_remarks']?> </span>
            </div>
            <?php } } ?>
         </div>
      </div>
      <div id="detailProduct" class="col-12 col-md-8 col-lg-8">
         <div class="card-header">
            <h5 class="card-title">Detail Transaction</h5>
         </div>
         <div class="card-body">
            <div class="btn-group" role="group" aria-label="Basic example">
               <button type="button" class="btn btn-outline-primary" onClick="window.open('<?=URL?>php/ajax/print_spk.php?transactionID=<?=$_GET['trx']?>');"><span class="fa fa-print"></span> SPK</button>
               <button type="button" class="btn btn-outline-danger" onClick="window.open('<?=URL?>php/ajax/print_do.php?transactionID=<?=$_GET['trx']?>');"><span class="fa fa-print"></span> DO</button>
               <button type="button" class="btn btn-outline-success" onClick="window.open('<?=URL?>php/ajax/print_invoice.php?transactionID=<?=$_GET['trx']?>');"><span class="fa fa-print"></span> Invoice</button>
               <button type="button" class="btn btn-outline-warning"  onClick="window.open('<?=URL?>php/ajax/print_cardmsg.php?transactionID=<?=$_GET['trx']?>');"><span class="fa fa-print"></span> Card Messages</button>
               <button type="button" class="btn btn-outline-info" id="send-email" data-trx="<?=$_GET['trx']?>"><span class="fa fa-paper-plane"></span> Send Email</button>
               <button type="button" class="btn btn-outline-danger"  onClick="window.location.href='<?=ORDER?>?p=neworder&trx=<?=$_GET['trx']?>'"><span class="fa fa-pencil"></span> Edit Data</button>
            </div>
         </div>
         <div id="accordion" style="padding-top: 1%;">
            <div class="card-header bg-primary collapse-card" id="customer_details">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseDetails" aria-expanded="true" aria-controls="collapseDetails">
                  Details Customer 
                  </button>
                  <button class="btn btn-sm btn-warning" onclick="editcustomer()">Edit</button>
               </h5>
            </div>
            <input type="hidden" class="form-control" id="TransactionNumber"  value="<?=$data['transactionID']?>">
            <div id="collapseDetails" class="collapse show" aria-labelledby="customer_details" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                     <?php if(empty($data['OrganicNames'])){ ?>
                     <div class="col-md-6 mb-3">
                        <label for="firstName">corporate_name</label>
                        <input type="corporate_name" class="form-control" id="corporate_name"  value="<?=$data['CorporateNames']?>" autocomplete="text" placeholder="" value="" readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="firstName">corporate pic</label>
                        <input type="corporate_name" class="form-control" id="corporate_name"  value="<?=$data['CustomerName']?>" autocomplete="text" placeholder="" value="" readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="firstName">Referensi Invoice</label>
                        <input type="corporate_name" class="form-control" id="invoice_name"  value="<?=$data['invoice_name']?>" autocomplete="text" placeholder="" value="" readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <?php }else{ ?>
                     <div class="col-md-6 mb-3">
                        <label for="firstName">customer name</label>
                        <input type="corporate_name" class="form-control" id="corporate_name"  value="<?=$data['OrganicNames']?>" autocomplete="text" placeholder="" value="" readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <?php } ?>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                        <button class="btn-block btn btn-success btn-customer hidden" onclick="customer()">UPDATE</button>
                    </div>
                    </div>
               </div>
            </div>
         </div>
         <div class="card-header bg-primary collapse-card" id="customer_address">
            <h5 class="mb-0">
               <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseAddress" aria-expanded="true" aria-controls="collapseAddress">
               Detail Penerima
               </button>
               <button class="btn btn-sm btn-warning" onclick="editpenerima()">Edit</button>
            </h5>
         </div>
         <div id="collapseAddress" class="collapse show" aria-labelledby="customer_address" data-parent="#accordion">
            <div class="card-body">
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label for="firstName">nama penerima</label>
                     <input type="text" class="form-control" id="nama_penerima" value="<?=$data['nama_penerima']?>" autocomplete="text" placeholder=""  readonly>
                     <div class="invalid-feedback">
                        Valid first name is required.
                     </div>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label for="lastName">email penerima</label>
                     <input type="text" class="form-control" id="email" value="<?=$data['email']?>" autocomplete="text" placeholder="" readonly>
                     <div class="invalid-feedback">
                        Valid last name is required.
                     </div>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label for="lastName">nomor_handphone</label>
                     <input type="text" class="form-control" id="hp_penerima" value="<?=$data['hp_penerima']?>" autocomplete="text" placeholder="" readonly>
                     <div class="invalid-feedback">
                        Valid last name is required.
                     </div>
                  </div>
               </div>
               <div class="row">
                    <div class="col-md-12">
                        <button class="btn-block btn btn-success btn-penerima hidden" onclick="penerima()">UPDATE</button>
                    </div>
               </div>
            </div>
         </div>
         <div class="card-header bg-primary collapse-card" id="customer_history">
            <h5 class="mb-0">
               <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="true" aria-controls="collapseHistory">
               Alamat dan Tempat   
               </button>
               <button class="btn btn-sm btn-warning" onclick="editalamatempat()">Edit</button>
            </h5>
         </div>
         <div id="collapseHistory" class="collapse show" aria-labelledby="customer_history" data-parent="#accordion">
            <div class="card-body">
                <div class="row">
                  <div class="col-md-12 mb-3">
                     <label for="firstName">alamat lengkap</label>
                     <textarea type="text" class="form-control" id="alamat_penerima" autocomplete="text" placeholder="" value="" readonly><?=$data['alamat_penerima']?></textarea>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label for="firstName">kelurahan</label>
                     <select class="form-control" name="kelurahan_id" id="kelurahan_id">
                        <option value="">:: kelurahan ::</option>
                        <?php while($kel = $kelurahan->fetch(PDO::FETCH_LAZY)) { ?>
                            <option value="<?=$kel['idcarge']?>" data-price="<?=$kel['harga']?>" <?=$kel['id_kelurahan'] == $data['kelurahan'] ? 'selected' : ''?> ><?=$kel['villagename']?> || <?=$config->formatPrice($kel['harga'])?></option>
                        <?php } ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label for="lastName">tanggal kirim</label>
                     <input type="text" class="form-control" id="delivery_date" value="<?=$data['delivery_date']?>" autocomplete="text" placeholder="" readonly>
                     <div class="invalid-feedback">
                        Valid last name is required.
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label for="firstName">time slot</label>
                        <select class="form-control" id="time_slot" name="time_slot" readonly>
                        <?php foreach($arrtime as $key => $val) { ?>
                            <option value="<?=$key?>" <?=$key == $data['delivery_time'] ? 'selected' : '' ?> ><?=$val?></option>
                        <?php } ?>
                        </select>
                  </div>
               </div>
               <div class="row">
                    <div class="col-md-12">
                        <button class="btn-block btn btn-success btn-alamatempat hidden" onclick="alamatempat()">UPDATE</button>
                    </div>
               </div>
            </div>
         </div>
         <div class="card-header bg-primary collapse-card" id="customer_favorite">
            <h5 class="mb-0">
               <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseFavorite" aria-expanded="true" aria-controls="collapseFavorite">
               Kartu Ucapan
               </button>
                <button class="btn btn-sm btn-warning" onclick="editcardmsg()">Edit</button>
            </h5>
         </div>
         <div id="collapseFavorite" class="collapse show" aria-labelledby="customer_favorite" data-parent="#accordion">
            <div class="card-body">
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label for="firstName">from</label>
                     <input type="text" class="form-control" id="card_from" value="<?=$data['card_from']?>" autocomplete="text" placeholder=""  readonly>
                     <div class="invalid-feedback">
                        Valid first name is required.
                     </div>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label for="lastName">to</label>
                     <input type="text" class="form-control" id="card_to" value="<?=$data['card_to']?>" autocomplete="text" placeholder="" readonly>
                     <div class="invalid-feedback">
                        Valid last name is required.
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12 mb-3">
                     <label for="firstName">isi card</label>
                     <textarea type="text" class="form-control" id="card_isi" autocomplete="text" placeholder="" value="" readonly><?=$data['card_isi']?></textarea>
                  </div>
               </div>
               <div class="row">
                    <div class="col-md-12">
                        <button class="btn-block btn btn-success btn-msg hidden" onclick="submitmsg()">UPDATE</button>
                    </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>