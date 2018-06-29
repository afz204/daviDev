<?php if(isset($_GET['trx'])){ 
    $type = substr($_GET['trx'], 3, 2);
   
    $provinsi = $config->Products('id, name', 'provinces');
    $kelurahan = $config->ProductsJoin('delivery_charges.id_kelurahan, delivery_charges.price, villages.name', 'delivery_charges', 
    'INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', '');

    $prod = $config->ProductsJoin('transaction_details.id, transaction_details.id_product,  transaction_details.product_price, transaction_details.product_qty, transaction_details.florist_remarks, products.product_id, products.name_product,
      products.cost_price, products.selling_price, products.note, products.images, products.permalink',
      'transaction_details', 'LEFT JOIN products ON products.product_id = transaction_details.id_product', "WHERE transaction_details.id_trx = '". $_GET['trx'] ."'");
    
    if($type == 'CP')
    {
      $corp = $config->runQuery("SELECT corporates.CorporateUniqueID, corporates.nama, corporates.alamat, corporates.kelurahan, 
        corporates.kecamatan, corporates.kodepos, corporates.created_at, 
        provinces.name as Provinsi, regencies.name as Kota,
    districts.name as Kec, villages.name as Kel, corporates.alamat FROM corporates
        INNER JOIN bidang_usahas ON bidang_usahas.id = corporates.bidang
        LEFT JOIN provinces on provinces.id = corporates.provinsi
        LEFT JOIN regencies on regencies.id = corporates.kota
        LEFT JOIN districts on districts.id = corporates.kecamatan
        LEFT JOIN villages on villages.id = corporates.kelurahan");
      $corp->execute();
    }

    //card message
    $card = $config->Products('id, level1', "card_messages WHERE level2 = 'NULL'");
    $totalProduct = $config->Products('id', "transaction_details WHERE id_trx = '". $_GET['trx'] ."' ");
    $totalProduct->execute();
    $totalProduct = $totalProduct->rowCount();

    $ProductTotal = 0;
    if($totalProduct > 0){
      $ProductTotal = $totalProduct;
    }
    // /var_dump($card);
    ?>
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
            <span class="text-muted" id="subTotal">00</span>
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
            <strong id="totalTransaction">00</strong>
         </li>
      </ul>
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
                     <div class="form-group">
                        <div class="row">
                           <div class="col-md-12">
                              <label for="listCorporate">Pilih Corporate:</label>
                              <select class="form-control" name="listCorporate" id="listCorporate" required>
                                 <option value="">Choose...</option>
                                 <?php while ($row = $corp->fetch(PDO::FETCH_LAZY)){ ?>
                                 <option value="<?=$row->CorporateUniqueID?>"><?=$row->nama?></option>
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
                              </select>
                              <div class="help-block with-errors"></div>
                           </div>
                        </div>
                        <input type="text" name="step_0" id="step_0" required>
                     </div>
                  </div>
               </div>
               <div id="step-2">
                  <div id="form-step-1" class="card-body" role="form" data-toggle="validator">
                     <div class="row ">
                        <div class="col-md-6 mb-3 form-group">
                           <label for="firstName">nama_penerima</label>
                           <input type="text" class="form-control" id="nama_penerima"  required>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                           <label for="lastName">email_penerima</label>
                           <input type="email" class="form-control" id="email_penerima" autocomplete="email" required>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                     <div class="row ">
                        <div class="col-md-6 mb-3 form-group">
                           <label for="firstName">provinsi</label>
                           <select class="form-control" name="ProvinsiCorporate" id="ProvinsiCorporate" required>
                              <option value="">Choose...</option>
                              <?php while ($row = $provinsi->fetch(PDO::FETCH_LAZY)){ ?>
                              <option value="<?=$row->id?>"><?=$row->name?></option>
                              <?php } ?>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                           <label for="lastName">kota</label>
                           <select class="form-control" name="KotaCorporate" id="KotaCorporate" required>
                              <option value="">Choose...</option>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                           <label for="lastName">Kecamatan</label>
                           <select class="form-control" name="kecamatanCorporate" id="kecamatanCorporate" required>
                              <option value="">Choose...</option>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3 form-group">
                           <label for="lastName">kelurahan</label>
                           <select class="form-control" name="kelurahanCorporate" id="kelurahanCorporate" required>
                              <option value="">Choose...</option>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                     <div class="row form-group">
                        <div class="col-md-12 mb-3">
                           <label for="firstName">alamat_lengkap</label>
                           <textarea type="text" class="form-control" id="alamat_lengkap" autocomplete="text" required=""></textarea>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                  </div>
                  <input type="text" name="step_1" id="step_1" required>
               </div>
               <div id="step-3">
                  <div id="form-step-2" class="card-body" role="form" data-toggle="validator">
                     <div class="row ">
                        <div class="col-md-12 form-group">
                           <label for="delivery_charges">delivery_charges</label>
                           <select class="form-control" id="delivery_charges" readonly >
                              <option value="">Choose...</option>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                     <div class="row ">
                        <div class="col-md-6 mb-3 form-group">
                           <label for="delivery_dates">tanggal_pengiriman</label>
                           <input type="text" class="form-control" id="delivery_dates" required>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class=" col-md-6 mb-3 form-group">
                           <label for="time_slot">time_slot</label>
                           <select class="form-control" id="time_slot" required>
                              <option value="">Choose...</option>
                              <option value="9am to 2pm">9am to 2pm</option>
                              <option value="3pm to 7pm">3pm to 7pm</option>
                              <option value="8pm to 11pm">8pm to 11pm + rp. 20.000</option>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                     <br/>
                     <h5 class="text-muted">Pilih salah satu dari pilihan dibawah ini untuk memastikan pengiriman berhasil: </h5>
                     <div class="col-md-12 form-group">
                        <div class="custom-control custom-radio ">
                           <input type="radio" class="custom-control-input" id="caseOne" name="radio-stacked" required>
                           <label class="custom-control-label" for="caseOne">Jangan hubungi penerima. Jika penerima tidak ditempat
                           </label>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="custom-control custom-radio ">
                           <input type="radio" class="custom-control-input" id="caseTwo" name="radio-stacked" required>
                           <label class="custom-control-label" for="caseTwo">Tinggalkan didepan pintu, tetangga atau petugas
                           </label>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="custom-control custom-radio ">
                           <input type="radio" class="custom-control-input" id="caseThree" name="radio-stacked" required>
                           <label class="custom-control-label" for="caseThree">Hubungi penerima sebelum pengiriman
                           </label>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                  </div>
                  <input type="text" name="step_2" id="step_2" required>
               </div>
               <div id="step-4">
                  <div id="form-step-3" role="form" class="card-body" data-toggle="validator">
                     <div class="row">
                        <div class="col-md-6 mb-3  form-group">
                           <label for="lastName">from</label>
                           <input type="text" class="form-control" id="from" required>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3  form-group">
                           <label for="lastName">to</label>
                           <input type="text" class="form-control" id="to" required>
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
                              <option value="<?=$crd['id']?>"><?=$crd['level1']?></option>
                              <?php } ?>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-md-6 mb-3  form-group">
                           <label for="lastName">pesan template</label>
                           <select class="form-control" name="template_level2" id="template_level2" required>
                              <option value="">Choose...</option>
                           </select>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                     <div class="row form-group">
                        <div class="col-md-12 mb-3 ">
                           <label for="lastName">isi pesan</label>
                           <textarea name="isi_pesan" id="isi_pesan" cols="30" rows="5" class="form-control" required></textarea>
                           <div class="help-block with-errors"></div>
                        </div>
                     </div>
                     <input type="text" name="step_3" id="step_3" required>
                  </div>
               </div>
               <div id="step-5" class="">
                  <div id="form-step-3" class="card-body" role="form" data-toggle="validator">
                     <div class="row">
                        <div class="col-4 col-md-3 col-lg-3 listPayment">
                           <a href="#step-5" class="card btn border-success">
                              <div class="text-center">
                                 <img src="<?=URL?>assets/images/payment/bca.jpg" width="50%" class="rounded mx-auto d-block" alt="...">
                              </div>
                           </a>
                        </div>
                     </div>
                     <input type="text" name="step_4" id="step_4" autocomplete="address-level2" required>
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
                               <img src="<?=URL?>assets/images/product/<?=$product['images']?>" class="img-fluid img-thumbnail">
                           </a>
                       </picture>
                     </div>
                     <div class="checkout-sometext" style="width: 120%">
                        <div class="title"><?=$product['name_product']?> <div class="pull-right"><button class="btn btn-sm btn-danger deleteListProduct" type="button" data-id="<?=$product['id']?>"><span class="fa fa-trash"></span></button></div></div>
                        <div class="count-product">
                           
                           <div class="center">
                              <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <button class="btn btn-sm btn-outline-secondary btn-number-count"  type="button" data-type="minus" data-field="count-product-number[<?=$product['id']?>]"  <?=$product['product_qty'] > 1 ? '': 'disabled="disabled"' ?> ><span class="fa fa-minus"></span></button>
                                </div>
                                <input style="text-align: center;" type="text" 
                                value="<?=$product['product_qty']?>" 
                                id="count-product-number[<?=$product['id']?>]" 
                                name="count-product-number[<?=$product['id']?>]" 
                                min="1" 
                                max="10" class="input-number form-control form-control-sm" placeholder="" aria-label="" aria-describedby="basic-addon1" 
                                data-field="count-product-number[<?=$product['id']?>]"
                                data-qty="<?=$product['product_qty']?>" >
                                <div class="input-group-append">
                                  <button class="btn btn-sm btn-outline-secondary btn-number-count"  type="button" data-type="plus" data-field="count-product-number[<?=$product['id']?>]"><span class="fa fa-plus"></span></button>
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
                                <input type="text" data-parsley-type="number" class="form-control" name="selling_price_product[<?=$product['id']?>]" id="selling_price_product[<?=$product['id']?>]" value="<?=$product['product_price']?>" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                  <button class="btn btn-outline-info selling_price_btn" type="button" data-id="selling_price_product[<?=$product['id']?>]">Change</button>
                                </div>
                              </div>
                           
                        </div>
                        
                        <div class="important-notes">
                           <div class="note">
                              <form id="remarks_florist" data-parsley-validate="">
                                 <div class="form-group">
                                    <textarea class="form-control" name="isi_remarks[<?=$product['id']?>]" rows="5" required="" placeholder="remarks florist"><?=$product['florist_remarks']?></textarea>

                                 </div>
                                 <button class="btn btn-block btn-info isi_remarks_btn" type="button" data-id="isi_remarks[<?=$product['id']?>]">remarks</button>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
              </li>
              <?php } } else{ echo '<li class="list-group-item"><span class="badge badge-success">Produk kosong!</span></li>';} ?>
            </ul>
            
            
         </div>
      </div>
   </div>
</div>
<?php }else{ ?>
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
<?php } ?>


<!-- Modal -->
<div class="modal fade" id="modalAddProducts" tabindex="-1" role="dialog" aria-labelledby="modalProductAdd" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductAdd">List Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form id="addProductCheckout" data-parsley-validate="">
                <div class="form-group">
                    <input type="text" class="form-control" id="codeSearch" placeholder="Code Products" required>
                    <input type="hidden" class="form-control" id="noTransaction" value="<?=$_GET['trx']?>">
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


