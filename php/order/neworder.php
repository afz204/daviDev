<?php $products = $config->Products('*', "products WHERE status = '1' ");  if(!isset($_GET['trx'])) { ?>
  <div class="row justify-content-center" <?=$access['create']?>>
   <div class="col-12 col-sm-8 col-lg-4">
      <div class="card border-primary mb-3">
         <div class="card-body text-primary">
            <form class="form-inline" method="post" data-parsley-validate="" id="generateOrder" autocomplete="off">
               <label class="my-1 mr-2" for="typeOrder">Type Order</label>
               <select class="custom-select my-1 mr-sm-2" data-parsley-message="Choose one of them" name="typeOrder"  id="typeOrder" required="">
                  <option value="">Choose...</option>
                  <option value="1">Corporate</option>
                  <option value="2">Personal</option>
               </select>
               <button type="submit" class="btn btn-primary btn-block my-1">submit <i class="fa fa-fw fa-barcode"> </i></button>
            </form>
         </div>
      </div>
   </div>
</div>
<?php }else{ $type = substr($_GET['trx'], 3, 2);
  $arrtime = [
		0 => '9am - 1pm',
		1 => '2pm - 5pm',
		2 => '6pm - 8pm',
		3 => '9pm - 0am Rp. 100.000 JABODETABEK',
		4 => '1am - 5am Rp. 200.000 JABODETABEK',
		5 => '6am - 8am Rp. 50.000 JABODETABEK'
	];
  $provinsi = $config->Products('id, name', 'provinces');
  $kota = $config->Products('id, name', 'regencies');
  $kecamatan = $config->Products('id, name', 'districts');
  $card2 = $config->Products('*', 'card_messages');
  $kelurahan = $config->ProductsJoin('delivery_charges.id_kelurahan, delivery_charges.price, villages.id, villages.name', 'delivery_charges', 
  'INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', '');

  $prod = $config->ProductsJoin('transaction_details.id, transaction_details.id_product,  transaction_details.product_price, transaction_details.product_cost, transaction_details.product_qty, transaction_details.florist_remarks, products.product_id, products.name_product,
    products.cost_price, products.selling_price, products.images, products.permalink',
    'transaction_details', 'LEFT JOIN products ON products.product_id = transaction_details.id_product', "WHERE transaction_details.id_trx = '". $_GET['trx'] ."'");
  
  $pic = [];
  if($type == 'CP')
  {
    $corp = $config->runQuery("SELECT corporates.CorporateUniqueID, corporates.nama, corporates.alamat, corporates.kelurahan, 
      corporates.kecamatan, corporates.kodepos, corporates.created_at, 
      provinces.name as Provinsi, regencies.name as Kota,
  districts.name as Kec, villages.name as Kel, corporates.alamat FROM corporates
      LEFT JOIN provinces on provinces.id = corporates.provinsi
      LEFT JOIN regencies on regencies.id = corporates.kota
      LEFT JOIN districts on districts.id = corporates.kecamatan
      LEFT JOIN villages on villages.id = corporates.kelurahan");
    $corp->execute();
  }
  $transaction = $config->getData('*', 'transaction', "transactionID = '". $_GET['trx'] ."'");
  $trx = [$transaction];
  $kelurahanid = 0;

  if(isset($trx)) { $kelurahanid = $trx[0]['kelurahan_id']; }
  $priceCharge = 0;
  if(isset($trx) && $trx[0]['kelurahan_id'] != '') { $idKelurahan = $trx[0]['kelurahan_id']; };
  $priceCharge = $config->getData('price', 'delivery_charges', "id_kelurahan = '". $kelurahanid ."'");
  // $config->_debugvar($trx);
  if(isset($trx)) {
    $pic = $config->Products('id, name', "corporate_pics WHERE corporate_id = '". $trx[0]['CustomerID'] ."' ");
  }
  //card message
  $card = $config->Products('id, level1', "card_messages WHERE level2 = 'NULL'");
  $totalProduct = $config->Products('id', "transaction_details WHERE id_trx = '". $_GET['trx'] ."' ");
  $totalProduct = $totalProduct->rowCount();
  
  $ProductTotal = 0;
  if($totalProduct > 0){
    $ProductTotal = $totalProduct;
  }
  //total transaction
  $delivery = $config->getData('delivery_charge, delivery_charge_time', '  transaction', " transaction.transactionID = '". $_GET['trx'] ."'");
  $deliveryCharge = 0;

  if($delivery['delivery_charge'] > 0) { $deliveryCharge = $delivery['delivery_charge']; }
  $timeslotcharges = 0;
  if($delivery['delivery_charge_time'] > 0) { $timeslotcharges = $delivery['delivery_charge_time']; }

  $total = $config->getData('SUM(detail.product_qty * detail.product_price) as subtotal', '  transaction_details as detail', " detail.id_trx = '". $_GET['trx'] ."'");
  $totalTransaction = $total['subtotal'];

  $grandTotal = $totalTransaction + $deliveryCharge + $timeslotcharges;
  // /var_dump($card);

  $paymentList = $config->Products('ID, PaymentName, AccountName, AccountNumber, PaymentImages', 'payment WHERE Status = 1 ');

  
  ?>
  <style>
  .listPayment span {
  text-align: center;
  display: block;
  width: 100%;
  padding-top: 3%;
  }
  .modal {
  overflow-y:auto;
}
  </style>
<div class="row">
 <div class="col-md-4 order-md-2 mb-4">
    <h4 class="d-flex justify-content-between align-items-center mb-3">
       <span class="text-muted">Ringkasan Belanja</span>
       <span class="badge badge-success badge-pill" id="countProduct"><?=$ProductTotal?></span>
    </h4>
    <div class="card" style="margin-bottom: 0.75rem">
       <form class="p-2 hidden needs-validation" id="redeemPromo">
          <div class="input-group">
             <input type="text" class="form-control" placeholder="Promo code" id="codePromoInput">
             <div class="input-group-append">
                <button type="submit" class="btn btn-danger">Redeem</button>
             </div>
             <div id="validation-feedback"></div>
          </div>
       </form>
       <div id="linkRedem" style="padding: 0.35rem 0.75rem;">
          <button type="button" class="btn btn-link text-danger" onclick="formRedeemPromo()"><span class=" text-muted">Punya Kode Voucher?</span></button>
       </div>
    </div>
    <ul class="list-group mb-3" id="checkoutData">
       <li class="list-group-item d-flex justify-content-between lh-condensed">
          <div>
             <h6 class="my-0">Total Harga Barang</h6>
          </div>
          <span class="text-muted" id="subTotal"><?=$config->formatprice($totalTransaction)?></span>
       </li>
       <li class="list-group-item d-flex justify-content-between lh-condensed">
          <div>
             <h6 class="my-0">Biaya Kirim</h6>
          </div>
          <span class="text-muted" id="deliveryCharges"><?=$config->formatprice($deliveryCharge)?></span>
       </li>
       <li class="list-group-item d-flex justify-content-between lh-condensed">
          <div>
             <h6 class="my-0">Biaya Time Slot</h6>
          </div>
          <span class="text-muted" id="timeslotcharges"><?=$config->formatprice($timeslotcharges)?></span>
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
          <strong id="totalTransaction"><?=$config->formatprice($grandTotal)?></strong>
       </li>

    </ul>
    
    <input style="margin-bottom: 2%;" type="text" class="form-control <?=(isset($trx[0]['paymentID']) && $trx[0]['paymentID']) !='' ? '' : 'hidden' ?>" name="NameInvoice" value="" placeholder="Nama Invoice" >

    <button type="button" id="btnProccessOrder" onclick="proccessOrder('<?=$_GET['trx']?>')" class="btn btn-block btn-outline-success <?=(isset($trx[0]['paymentID']) && $trx[0]['paymentID']) !='' ? '' : 'hidden' ?>">Proccess Order</button>
 </div>
 <div class="col-md-8 order-md-1" id="SmartWizard">
    <input type="hidden" name="nomorTrx" id="nomorTrx" value="<?=$_GET['trx']?>">
    <h4 class="mb-3">Detail Transaction</h4>
    <form action="#" id="myForm" role="form" data-toggle="validator" method="post" accept-charset="utf-8">
       <!-- SmartWizard html -->
       <div id="smartwizard">
          <ul>
             <li><a href="#step-1">Step 1<br /><small>Customer Details</small></a></li>
             <li><a href="#step-2">Step 2<br /><small>Detail Penerima</small></a></li>
             <li><a href="#step-3">Step 3<br /><small>Alamat dan Tempat</small></a></li>
             <li><a href="#step-4">Step 4<br /><small>Kartu Ucapan</small></a></li>
             <li><a href="#step-5">Step 5<br /><small>Pembayaran</small></a></li>
          </ul>
          <div>
             <div id="step-1">
                <div id="form-step-0" class="card-body" role="form" data-toggle="validator">
                   <?php if($type == 'CP') { ?>
                    <div class="form-group">
                      <div class="row">
                         <div class="col-md-12">
                            <label for="listCorporate">Pilih Corporate:</label>
                            <select class="form-control" name="listCorporate" id="listCorporate" required>
                               <option value="">Choose...</option>
                               <?php while ($row = $corp->fetch(PDO::FETCH_LAZY)){ ?>
                               <option value="<?=$row->CorporateUniqueID?>" <?=isset($trx[0]['CustomerID']) && $trx[0]['CustomerID'] == $row->CorporateUniqueID ? 'selected' : '' ?> ><?=$row->nama?></option>
                               <?php } ?>
                            </select>
                            <div class="help-block with-errors"></div>
                         </div>
                      </div>
                      <div class="row">
                         <div class="col-md-12">
                            <label for="listPicCorp">Pilih PIC:</label>
                            <select class="form-control" name="listPicCorp" id="listPicCorp" required>
                               <option value="">Choose...</option>
                               <?php if(isset($trx) && $trx[0]['CustomerID'] != '') { while ($pics = $pic->fetch(PDO::FETCH_LAZY)){ ?>
                               <option value="<?=$pics->id?>" data-name="<?=$pics->name?>" <?=isset($trx[0]['CustomerID']) && $trx[0]['CustomerName'] == $pics->name ? 'selected' : '' ?> ><?=$pics->name?></option>
                               <?php }  } ?>
                            </select>
                            <div class="help-block with-errors"></div>
                         </div>
                      </div>
                      <input type="hidden" name="step_0" id="step_0" required>
                      <input type="hidden" name="typeform" id="typeform" value="corporate" required>
                   </div>
                   <?php } else { ?>
                   <div class="form-group">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label for="OrganicFirstName">First Name</label>
                          <input type="text" class="form-control" id="OrganicFirstName" name="OrganicFirstName" required>
                          <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label for="OrganicLastName">Last Name</label>
                          <input type="text" class="form-control" id="OrganicLastName" name="OrganicLastName" required>
                          <div class="help-block with-errors"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label for="OrganicEmail">Email</label>
                          <input type="email" class="form-control" data-parsley-type="email" id="OrganicEmail" name="OrganicEmail" required>
                          <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label for="OrganicMobileNumber">Mobile Number</label>
                          <input type="text" class="form-control" data-parsley-type="number" id="OrganicMobileNumber" name="OrganicMobileNumber" required>
                          <div class="help-block with-errors"></div>
                        </div>
                      </div>
                      <input type="hidden" name="step_0" id="step_0" required>
                      <input type="hidden" name="typeform" id="typeform" value="organic" required>
                   </div>
                   <?php } ?>
                </div>
             </div>
             <div id="step-2">
                <div id="form-step-1" class="card-body" role="form" data-toggle="validator">
                   <div class="row ">
                      <div class="col-md-6 mb-3 form-group">
                         <label for="firstName">nama_penerima</label>
                         <input type="text" class="form-control" id="nama_penerima" value="<?=isset($trx[0]['nama_penerima']) ? $trx[0]['nama_penerima'] : '' ?>"  required>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 mb-3 form-group">
                         <label for="lastName">email_penerima</label>
                         <input type="email" class="form-control" id="email_penerima" value="<?=isset($trx[0]['email']) ? $trx[0]['email'] : '' ?>" autocomplete="email" >
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 mb-3 form-group">
                         <label for="lastName">hp_penerima</label>
                         <input type="text" class="form-control" id="hp_penerima" data-parsley-type="number" value="<?=isset($trx[0]['hp_penerima']) ? $trx[0]['hp_penerima'] : '' ?>" autocomplete="number" >
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                   <div class="row ">
                      <div class="col-md-6 mb-3 form-group">
                         <label for="firstName">provinsi</label>
                         <select class="form-control" name="ProvinsiCorporate" id="ProvinsiCorporate">
                            <option value="">Choose...</option>
                            <?php while ($row = $provinsi->fetch(PDO::FETCH_LAZY)){ ?>
                            <option value="<?=$row->id?>" <?=isset($trx[0]['provinsi_id']) && $trx[0]['provinsi_id'] == $row->id ? 'selected' : '' ?>><?=$row->name?></option>
                            <?php } ?>
                         </select>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 mb-3 form-group">
                         <label for="lastName">kota</label>
                         <select class="form-control" name="KotaCorporate" id="KotaCorporate">
                            <option value="">Choose...</option>
                            <?php if(isset($trx) && $trx[0]['kota_id'] != '') { while ($k = $kota->fetch(PDO::FETCH_LAZY)){ ?>
                               <option value="<?=$k->id?>" <?=isset($trx[0]['kota_id']) && $trx[0]['kota_id']== $k->id ? 'selected' : '' ?> ><?=$k->name?></option>
                               <?php }  } ?>
                         </select>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 mb-3 form-group">
                         <label for="lastName">Kecamatan</label>
                         <select class="form-control" name="kecamatanCorporate" id="kecamatanCorporate">
                            <option value="">Choose...</option>
                            <?php if(isset($trx) && $trx[0]['kecamata_id'] != '') { while ($kec = $kecamatan->fetch(PDO::FETCH_LAZY)){ ?>
                              <option value="<?=$kec->id?>" <?=isset($trx[0]['kecamata_id']) && $trx[0]['kecamata_id'] == $kec->id ? 'selected' : '' ?> ><?=$kec->name?></option>
                               <?php }  } ?>
                         </select>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 mb-3 form-group">
                         <label for="lastName">kelurahan</label>
                         <select class="form-control" name="kelurahanCorporate" id="kelurahanCorporate">
                            <option value="">Choose...</option>
                            <?php if(isset($trx) && $trx[0]['kelurahan_id'] != '') { while ($kel = $kelurahan->fetch(PDO::FETCH_LAZY)){ ?>
                               <option value="<?=$kel->id?>" <?=isset($trx[0]['kelurahan_id']) && $trx[0]['kelurahan_id'] == $kel->id ? 'selected' : '' ?> ><?=$kel->name?></option>
                               <?php }  } ?>
                         </select>
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                   <div class="row form-group">
                      <div class="col-md-12 mb-3">
                         <label for="firstName">alamat_lengkap</label>
                         <textarea type="text" class="form-control" id="alamat_lengkap" autocomplete="text" required=""><?=isset($trx[0]['alamat_penerima']) ? $trx[0]['alamat_penerima'] : '' ?></textarea>
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                </div>
                <input type="hidden" name="step_1" id="step_1" required>
             </div>
             <div id="step-3">
                <div id="form-step-2" class="card-body" role="form" data-toggle="validator">
                   <div class="row ">
                      <div class="col-md-12 form-group">
                        <label for="firstName">delivery_charges</label>
                         <input type="text" class="form-control" id="delivery_charges" value="<?=$priceCharge['price']?>"  readonly="readonly">
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                   <div class="row ">
                      <div class="col-md-6 form-group">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input AddDeliveryChargesClass" id="AddDeliveryCharges" data-trx="<?=$_GET['trx']?>" <?=isset($trx[0]['delivery_charge']) && $trx[0]['delivery_charge'] !='' ? 'checked' : '' ?> >
                          <label class="custom-control-label" for="AddDeliveryCharges">Tambah ke Biaya Kirim</label>
                        </div>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 form-group  <?=isset($trx[0]['delivery_charge']) && $trx[0]['delivery_charge'] !='' ? '' : 'hidden' ?>" id="manual_delivery_charges">
                          <div class="input-group mb-3">
                               <div class="input-group-prepend">
                                   <span class="input-group-text">Rp.</span>
                                 </div>
                              <input type="text" data-parsley-type="number" class="form-control" name="delivery_charges_values" id="delivery_charges_values" value="<?=isset($trx[0]['delivery_charge']) && $trx[0]['delivery_charge'] !='' ? $trx[0]['delivery_charge'] : 0 ?>" aria-describedby="basic-addon2">
                              <div class="input-group-append">
                                <button class="btn btn-outline-primary delivery_charges_values_btn" data-trx = "<?=$_GET['trx']?>" type="button" >biaya kirim</button>
                              </div>
                            </div>
                      </div>
                   </div>
                   <div class="row ">
                      <div class="col-md-6 mb-3 form-group">
                         <label for="delivery_dates">tanggal_pengiriman</label>
                         <input type="text" class="form-control" id="delivery_dates" value="<?=isset($trx[0]['delivery_date']) && $trx[0]['delivery_date'] !='' ? $trx[0]['delivery_charge'] : '0' ?>" required>
                         <input type="hidden" class="form-control" name="delivery_dates" value="<?=isset($trx[0]['delivery_date']) && $trx[0]['delivery_date'] !='' ? $trx[0]['delivery_date'] : $config->getDate("Y-m-d") ?>" required>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class=" col-md-6 mb-3 form-group">
                         <label for="time_slot">time_slot</label>
                         <select class="form-control" id="time_slot" onchange="timeslotcharge('<?=$_GET['trx']?>')" name="time_slot" required disabled>
                            <option value="">Choose...</option>
                            <?php if(isset($trx[0]['delivery_time']) && $trx[0]['delivery_time'] !=''){ foreach($arrtime as $key => $val){ ?>
                              <option value="<?=$key?>" <?=$trx[0]['delivery_time'] == $key ? 'selected' : '' ?>><?=$val?></option>
                            <?php } } ?>
                         </select>
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                   <br/>
                   <h5 class="text-muted">Pilih salah satu dari pilihan dibawah ini untuk memastikan pengiriman berhasil: </h5>
                   <div class="col-md-12 form-group">
                      <div class="custom-control custom-radio ">
                         <input type="radio" class="custom-control-input" id="Jangan hubungi penerima. Jika penerima tidak ditempat"  value="Jangan hubungi penerima. Jika penerima tidak ditempat" name="radio-remarks" <?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] == 'Jangan hubungi penerima. Jika penerima tidak ditempat' ? 'checked' : '' ?> required>
                         <label class="custom-control-label" for="Jangan hubungi penerima. Jika penerima tidak ditempat">Jangan hubungi penerima. Jika penerima tidak ditempat
                         </label>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="custom-control custom-radio ">
                         <input type="radio" class="custom-control-input" id="Tinggalkan didepan pintu, tetangga atau petugas"  value="Tinggalkan didepan pintu, tetangga atau petugas" name="radio-remarks" <?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] == 'Tinggalkan didepan pintu, tetangga atau petugas' ? 'checked' : '' ?> required>
                         <label class="custom-control-label" for="Tinggalkan didepan pintu, tetangga atau petugas">Tinggalkan didepan pintu, tetangga atau petugas
                         </label>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="custom-control custom-radio ">
                         <input type="radio" class="custom-control-input" value="Hubungi penerima sebelum pengiriman"  id="Hubungi penerima sebelum pengiriman" name="radio-remarks" <?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] == 'Hubungi penerima sebelum pengiriman' ? 'checked' : '' ?> required>
                         <label class="custom-control-label" for="Hubungi penerima sebelum pengiriman">Hubungi penerima sebelum pengiriman
                         </label>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="custom-control custom-radio ">
                         <input type="radio" class="custom-control-input" value="<?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] !='' ? $trx[0]['delivery_marks'] : 'custom-remarks-kurir' ?>"  id="<?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] !='' ? $trx[0]['delivery_marks'] : 'custom-remarks-kurir' ?>" name="radio-remarks" <?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] == $trx[0]['delivery_marks'] ? 'checked' : '' ?> required>
                         <label class="custom-control-label" for="<?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] !='' ? $trx[0]['delivery_marks'] : 'custom-remarks-kurir' ?>">
                            <input name="custom-remaks" type="text" class="form-control" id="<?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] !='' ? $trx[0]['delivery_marks'] : 'custom-remarks-kurir' ?>" value="<?=isset($trx[0]['delivery_marks']) && $trx[0]['delivery_marks'] !='' ? $trx[0]['delivery_marks'] : 'custom-remarks-kurir' ?>" >
                         </label>
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                </div>
                <input type="hidden" name="step_2" id="step_2" required>
             </div>
             <div id="step-4">
                <div id="form-step-3" role="form" class="card-body" data-toggle="validator">
                   <div class="row">
                      <div class="col-md-6 mb-3  form-group">
                         <label for="lastName">from</label>
                         <input type="text" class="form-control" id="from" value="<?=isset($trx[0]['card_from']) ? $trx[0]['card_from'] : '' ?>" required>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 mb-3  form-group">
                         <label for="lastName">to</label>
                         <input type="text" class="form-control" id="to" value="<?=isset($trx[0]['card_to']) ? $trx[0]['card_to'] : '' ?>" required>
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                   <div class="row">
                      <div class="col-md-6 mb-3  form-group">
                         <label for="lastName">template</label>
                         <select class="form-control" name="template" id="template_level1" required>
                            <option value="">Choose...</option>
                            <?php while ($crd = $card->fetch(PDO::FETCH_LAZY)) {
                               ?>
                            <option value="<?=$crd['id']?>" <?=isset($trx[0]['card_template1']) && $trx[0]['card_template1'] == $crd['level1'] ? 'selected' : '' ?> data-name="<?=$crd['level1']?>"><?=$crd['level1']?></option>
                            <?php } ?>
                         </select>
                         <div class="help-block with-errors"></div>
                      </div>
                      <div class="col-md-6 mb-3  form-group">
                         <label for="lastName">pesan template</label>
                         <select class="form-control" name="template_level2" id="template_level2" required>
                          <?php if(isset($trx) && $trx[0]['card_template2'] != '') { while ($crd2 = $card2->fetch(PDO::FETCH_LAZY)){ ?>
                              <option value="<?=$crd2->id?>" <?=isset($trx[0]['card_template2']) && $trx[0]['card_template2'] == $crd2->level1 ? 'selected' : '' ?> data-name="<?=$crd['level2']?>"><?=$crd2->level1?></option>
                               <?php }  } ?>
                         </select>
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                   <div class="row form-group">
                      <div class="col-md-12 mb-3 ">
                         <label for="lastName">isi pesan</label>
                         <textarea name="isi_pesan" id="isi_pesan" cols="30" rows="5" class="form-control" required><?=$trx[0]['card_isi']?></textarea>
                         <div class="help-block with-errors"></div>
                      </div>
                   </div>
                   <input type="hidden" name="step_3" id="step_3" required>
                </div>
             </div>
             <div id="step-5" class="">
                <div id="form-step-3" class="card-body" role="form" data-toggle="validator">
                   <div class="row">
                    <?php while ($row = $paymentList->fetch(PDO::FETCH_LAZY)) {
                      ?>
                      <div class="col-4 col-md-3 col-lg-3 listPayment">
                         <a href="#" class="card btn border-success" onclick="selectPayment('<?=$_GET['trx']?>', <?=$row['ID']?>)">
                            <div class="text-center">
                               <img src="<?=URL?>assets/images/payment/<?=$row['PaymentImages']?>.jpg" width="50%" class="rounded mx-auto d-block" alt="...">
                            </div>
                         </a>
                         <?=isset($trx[0]['paymentID']) && $trx[0]['paymentID'] !='' ? '<span class="fa fa-check-circle-o text-success"></span>' : '' ?>
                      </div>
                    <?php } ?>
                   </div>
                   <input type="hidden" name="step_4" id="step_4" autocomplete="address-level2" required>
                </div>
             </div>
          </div>
       </div>
    </form>
 </div>
</div>
<div class="row" style="margin-bottom: 1rem; margin-top: 1rem"  id="listProduct">
 <div class="col-md-8">
    <div class="card">
          <div class="card-header bg-white">
             Detail Produk 
             <button class="btn btn-sm btn-primary float-right" onclick="modalListProduct()"><span class="fa fa-plus"></span> product</button>
             <button class="btn btn-sm btn-primary float-right" onclick="showcustomproduct()"><span class="fa fa-plus"></span> custom product</button>
          </div>
       <div class="card-body">

          <ul class="list-group list-group-flush " id="listProductsData">
             <?php if($prod->rowCount() > 0) { while ($product = $prod->fetch(PDO::FETCH_LAZY)) {
               ?>
               <li class="list-group-item" id="ListProduct-<?=$product['id']?>">
                <div class="checkout-content">
                   <div class="chekcout-img">
                      <picture>
                       <a href="<?=URL?>assets/images/product/<?=$product['images']?>" data-toggle="lightbox" data-gallery="example-gallery">
                             <img src="<?=URL?>assets/images/product/<?=$product['images']?>" class="img-fluid img-thumbnail" width="30%">
                         </a>
                     </picture>
                   </div>
                   <div class="checkout-sometext" style="width: 120%">
                      <div class="title"><?=$product['name_product']?> <div class="pull-right"><button class="btn btn-sm btn-danger deleteListProduct" type="button" data-id="<?=$product['id']?>" data-trx="<?=$_GET['trx']?>"><span class="fa fa-trash"></span></button></div></div>
                      <div class="count-product">
                         
                         <div class="center">
                            <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                <button class="btn btn-sm btn-outline-secondary btn-number-count"  type="button" data-type="minus" data-field="count-product-number[<?=$product['id']?>]"  <?=$product['product_qty'] > 1 ? '': 'disabled="disabled"' ?>  data-trx="<?=$_GET['trx']?>"><span class="fa fa-minus"></span></button>
                              </div>
                              <input style="text-align: center;" type="text" 
                              value="<?=$product['product_qty']?>" 
                              id="count-product-number[<?=$product['id']?>]" 
                              name="count-product-number[<?=$product['id']?>]" 
                              min="1" 
                              max="10" class="input-number form-control form-control-sm" placeholder="" aria-label="" aria-describedby="basic-addon1" 
                              data-field="count-product-number[<?=$product['id']?>]"
                              data-qty="<?=$product['product_qty']?>"
                              data-transactionID = "<?=$_GET['trx']?>">
                              <div class="input-group-append">
                                <button class="btn btn-sm btn-outline-secondary btn-number-count"  type="button" data-type="plus" data-field="count-product-number[<?=$product['id']?>]" data-trx="<?=$_GET['trx']?>"><span class="fa fa-plus"></span></button>
                              </div>
                            </div>
                          
                         </div>
                      </div>
                      <div class="text-info" style="font-size: 13px; font-weight: 600;">Cost_price: <?=$config->formatPrice($product['cost_price'])?></div>
                      <div class="price" style="width: 50%">
                        
                            <div class="input-group mb-3">
                               <div class="input-group-prepend">
                                   <span class="input-group-text">Rp.</span>
                                 </div>
                              <input type="text" data-parsley-type="number" class="form-control" name="cost_price_product[<?=$product['id']?>]" id="cost_price_product[<?=$product['id']?>]" value="<?=$product['product_cost']?>" aria-describedby="basic-addon2" data-transactionID = "<?=$_GET['trx']?>">
                              <div class="input-group-append">
                                <button class="btn btn-outline-info cost_price_btn" type="button" data-id="cost_price_product[<?=$product['id']?>]" data-trx="<?=$_GET['trx']?>">Cost Price</button>
                              </div>
                            </div>

                            <div class="input-group mb-3">
                               <div class="input-group-prepend">
                                   <span class="input-group-text">Rp.</span>
                                 </div>
                              <input type="text" data-parsley-type="number" class="form-control" name="selling_price_product[<?=$product['id']?>]" id="selling_price_product[<?=$product['id']?>]" value="<?=$product['product_price']?>" aria-describedby="basic-addon2" data-transactionID = "<?=$_GET['trx']?>">
                              <div class="input-group-append">
                                <button class="btn btn-outline-info selling_price_btn" type="button" data-id="selling_price_product[<?=$product['id']?>]" data-trx="<?=$_GET['trx']?>">Selling Price</button>
                              </div>
                            </div>
                         
                      </div>
                      
                      <div class="important-notes">
                         <div class="note">
                            <form id="remarks_florist" data-parsley-validate="">
                               <div class="form-group">
                                  <textarea class="form-control remarks-florist-tambahan" name="isi_remarks[<?=$product['id']?>]" data-id="<?=$product['id']?>" rows="5" required="" placeholder="remarks florist"><?=$product['florist_remarks']?></textarea>

                               </div>
                            </form>
                         </div>
                      </div>
                   </div>
                </div>
            </li>
            <?php } } else{ echo '<li id="textproductkosong" class="list-group-item"><span class="badge badge-success">Produk kosong!</span></li>';} ?>
          </ul>
          
          
       </div>
    </div>
 </div>
</div>
<?php } ?>


<!-- Modal -->
<div class="modal fade" id="modalAddProducts" role="dialog" aria-labelledby="modalProductAdd" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductAdd">List Product <a href="javascript:;" onclick="showcustomproduct()"><span class="badge badge-sm badge-success">Custom Product</span></a></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form id="addProductCheckout" data-parsley-validate="">
                <div class="form-group">
                    <input type="hidden" class="form-control" id="noTransaction" value="<?=$_GET['trx']?>">
                </div>
                <div class="form-group">
                  <select class="form-control" name="codeSearch" id="codeSearch" required>
                      <option value="">Choose...</option>
                      <?php while ($p = $products->fetch(PDO::FETCH_LAZY)){ ?>
                      <option value="<?=$p->product_id?>" data-images="<?=$p->images?>"><?=$p->name_product?>_(<?=$config->formatPrice($p->selling_price)?>)</option>
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


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" id="modalCustomProduct" role="dialog" aria-labelledby="customProduct" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customProduct">Custom Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row justify-content-center card-body">
            <div id="imagesProduct" class="col-12 col-md-6 col-lg-6">
                <form id="uploadImagesProduct" method="post" enctype="multipart/form-data" >
                    <div class="form-group">
                        <input type="hidden" id="ImagesProductID" name="ImagesProductID">
                        <input type="hidden" id="ImagesName" name="ImagesName">
                        <div class="file-loading">
                            <input type="file" id="images" name="images[]" multiple>
                        </div>
                        <br>
                    </div>
                </form>
              <div id="kv-success-2" class="alert alert-success" style="margin-top:10px;display:none"></div>
            </div>
            <div id="detailProduct" class="col-12 col-md-6 col-lg-6">
              <form  method="post" action="" name="caracustomfrom" data-parsley-validate="">

                <div class="form-group">
                    <label for="codeProduct">Code Product</label>
                    <input type="text" name="codeProduct" id="codeProduct" name="codeProduct" placeholder="BDxxxxxx" class="form-control" data-parsley-minLength="3" required="" readonly="readonly">
                    <input type="hidden" name="transactionID" id="transactionID" value="<?=$_GET['trx']?>" placeholder="BDxxxxxx" class="form-control" data-parsley-minLength="3" required="" readonly="readonly">
                </div>

                <div class="form-group">
                    <label for="nameProduct">Nama Product</label>
                    <input type="text" name="nameProduct" id="nameProduct" class="form-control" data-parsley-minLength="3" required="" readonly="readonly">
                </div>

                <div class="form-group">
                    <label for="tagsProduct">Cost Price Product</label>
                    <input type="text" name="costProduct" id="costProduct" data-parsley-type="number" class="form-control" data-parsley-minLength="3" required="">
                </div>

                <div class="form-group">
                    <label for="tagsProduct">Selling Price Product</label>
                    <input type="text" name="sellProduct" id="sellProduct" data-parsley-type="number" class="form-control" data-parsley-minLength="3" required="">
                </div>

                <div class="form-group">
                    <label for="shortDesc">Description Product</label>
                    <textarea style="text-transform: capitalize;" data-parsley-minLength="5" data-parsley-maxLength="255" name="shortDesc" id="shortDesc" class="form-control" rows="2" required=""></textarea>
                </div>

                <div class="form-group">
                    <label for="shortDesc">Remarks Florist</label>
                    <textarea  style="text-transform: capitalize;" data-parsley-minLength="5" data-parsley-maxLength="255" name="remkarsfloris" id="remkarsfloris" class="form-control" rows="2" required=""></textarea>
                </div>

                <button type="submit" name="makan" class="btn btn-block btn-outline-primary">submit</button>

              </form>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 

if(isset($_POST['makan']))
   { 
     
    $product_id = $_POST['codeProduct'];
    $transactionID = $_POST['transactionID'];
    $name_product = $_POST['nameProduct'];
    $cost_price = $_POST['costProduct'];
    $selling_price = $_POST['sellProduct'];
    $full_desc = $_POST['shortDesc'];
    $remkarsfloris = $_POST['remkarsfloris'];
    $images = strtolower(str_replace(" ", "_", $name_product)).'.jpg';
    $permalink = str_replace(' ', '_', strtolower($name_product));
    $created_at = $config->getDate('Y-m-d H:m:s');

    $sql = "INSERT INTO products (product_id, name_product, cost_price, selling_price, full_desc, images, permalink, created_at, admin_id) 
    VALUES ('".$product_id."', '".$name_product."', '".$cost_price."', '".$selling_price."', '".$full_desc."', '".$images."', '".$permalink."', '".$created_at."', '".$admin."')";
    $stmt = $config->runQuery($sql);
    $stmt->execute();
    // $reff = $config->lastInsertId();
    // $logs = $config->saveLogs($reff, $admin, 'c', 'new custom products');

      $cek = $config->runQuery("INSERT INTO transaction_details (id_trx, id_product, product_name, product_price, product_cost, product_qty, florist_remarks) VALUES (:a, :b, :c, :d, :e, :f, :g) ");
      $cek->execute(array(
          ':a' => $transactionID,
          ':b' => $product_id,
          ':c' => $name_product,
          ':d' => $selling_price,
          ':e' => $cost_price,
          ':f' => '1',
          ':g' => $remkarsfloris
      ));

      echo "<meta http-equiv='refresh' content='0'>";
      // $reff = $config->lastInsertId();
      // $logs = $config->saveLogs($reff, $admin, 'c', 'add product checkout');
  }


?>


