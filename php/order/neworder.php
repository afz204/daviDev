<?php if(isset($_GET['trx'])){ 
    $provinsi = $config->Products('id, name', 'provinces');
    $kelurahan = $config->ProductsJoin('delivery_charges.id_kelurahan, delivery_charges.price, villages.name', 'delivery_charges', 
    'INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', '');
    
    ?>
    <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">Your cart</span>
            <button class="btn btn-success btn-sm"><span class="fa fa-plus"></span></button>
            <!-- <span class="badge badge-secondary badge-pill">3</span> -->
          </h4>
          <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Product name</h6>
                <small class="text-muted">Brief description</small>
              </div>
              <span class="text-muted">$12</span>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Second product</h6>
                <small class="text-muted">Brief description</small>
              </div>
              <span class="text-muted">$8</span>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Third item</h6>
                <small class="text-muted">Brief description</small>
              </div>
              <span class="text-muted">$5</span>
            </li>
            <li class="list-group-item d-flex justify-content-between bg-light">
              <div class="text-success">
                <h6 class="my-0">Promo code</h6>
                <small>EXAMPLECODE</small>
              </div>
              <span class="text-success">-$5</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Total (USD)</span>
              <strong>$20</strong>
            </li>
          </ul>

          <form class="card p-2">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Promo code">
              <div class="input-group-append">
                <button type="submit" class="btn btn-secondary">Redeem</button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-8 order-md-1">
        <h4 class="mb-3">Detail Transaction</h4>
        <div id="accordion">
         <div class="card">
            <div class="card-header bg-primary collapse-card" id="customer_details">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseDetails" aria-expanded="true" aria-controls="collapseDetails">
                  Customer Details
                  </button>
               </h5>
            </div>
            <div id="collapseDetails" class="collapse" aria-labelledby="customer_details" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">full_name</label>
                        <input type="text" class="form-control" id="full_name" autocomplete="text" placeholder="" value="" >
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">email</label>
                        <input type="text" class="form-control" id="email" autocomplete="text" placeholder="" value="" >
                        <div class="invalid-feedback">
                           Valid last name is required.
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">Kode</label>
                        <input type="text" class="form-control" id="share_link" value="+68" autocomplete="text" placeholder="" readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">nomor_handphone</label>
                        <input type="text" class="form-control" id="nomor_handphone" autocomplete="text" placeholder="">
                        <div class="invalid-feedback">
                           Valid last name is required.
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
               </h5>
            </div>
            <div id="collapseAddress" class="collapse" aria-labelledby="customer_address" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">nama_penerima</label>
                        <input type="text" class="form-control" id="nama_penerima" autocomplete="text" placeholder="" >
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">email_penerima</label>
                        <input type="text" class="form-control" id="email_penerima" autocomplete="text" placeholder="">
                        <div class="invalid-feedback">
                           Valid last name is required.
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">provinsi</label>
                        <select class="form-control" name="ProvinsiCorporate" id="ProvinsiCorporate" required>
                                <option value="">:: provinsi ::</option>
                                <?php while ($row = $provinsi->fetch(PDO::FETCH_LAZY)){ ?>
                                <option value="<?=$row->id?>"><?=$row->name?></option>
                                <?php } ?>
                            </select>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">kota</label>
                        <select class="form-control" name="KotaCorporate" id="KotaCorporate" required>
                                <option value="">:: kota ::</option>
                            </select>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">Kecamatan</label>
                        
                            <select class="form-control" name="kecamatanCorporate" id="kecamatanCorporate" required>
                                <option value="">:: kecamatan ::</option>
                            </select>
                       
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">kelurahan</label>
                        
                        <select class="form-control" name="kelurahanCorporate" id="kelurahanCorporate" required>
                                <option value="">:: kelurahan ::</option>
                            </select>
                       
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 mb-3">
                        <label for="firstName">alamat_lengkap</label>
                        <textarea type="text" class="form-control" id="alamat_lengkap" autocomplete="text" placeholder="" value=""></textarea>
                     </div>
                  </div>
               </div>
            </div>
            <div class="card-header bg-primary collapse-card" id="customer_history">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="true" aria-controls="collapseHistory">
                  Alamat dan Tempat
                  </button>
               </h5>
            </div>
            <div id="collapseHistory" class="collapse" aria-labelledby="customer_history" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                        
                    <select class="simple-select2 js-states" name="delivery_charges" id="delivery_charges" required>
                                <option value="">:: delivery charge ::</option>
                                <?php while ($rows = $kelurahan->fetch(PDO::FETCH_LAZY)){ ?>
                                <option value="<?=$rows->id_kelurahan?>"><?=$rows->name?> <?=$rows->price?></option>
                                <?php } ?>
                            </select>
                     </div>
                     
                  </div>
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">tanggal pengiriman</label>
                        <input type="text" class="form-control" id="delivery_dates" autocomplete="text" placeholder="" value="" >
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">time_slot</label>
                        <select class="form-control" name="time_slot" id="time_slot" required>
                                <option value="">:: time_slot ::</option>
                                <option value="9-2">:: 9am ~ 2pm ::</option>
                                <option value="3-7">:: 3pm ~ 7pm ::</option>
                            </select>
                     </div>
                  </div>
                  <br/>
                  <h5 class="text-muted">Pilih salah satu dari pilihan dibawah ini untuk memastikan pengiriman berhasil: </h5>
                  <div class="col-md-12">
                  <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="customControlValidation2" name="radio-stacked" required>
                        <label class="custom-control-label" for="customControlValidation2">Jangan hubungi penerima. Jika penerima tidak ditempat
</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="customControlValidation2" name="radio-stacked" required>
                        <label class="custom-control-label" for="customControlValidation2">Tinggalkan didepan pintu, tetangga atau petugas

</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="customControlValidation2" name="radio-stacked" required>
                        <label class="custom-control-label" for="customControlValidation2">Hubungi penerima sebelum pengiriman

</label>
                    </div>
                  </div>
               </div>
            </div>
            <div class="card-header bg-primary collapse-card" id="customer_favorite">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseFavorite" aria-expanded="true" aria-controls="collapseFavorite">
                  Kartu Ucapan
                  </button>
               </h5>
            </div>
            <div id="collapseFavorite" class="collapse" aria-labelledby="customer_favorite" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lastName">from</label>
                        <input type="text" class="form-control" id="from" autocomplete="text" placeholder="" >
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">to</label>
                        <input type="text" class="form-control" id="to" autocomplete="text" placeholder="" >
                     </div>
                  </div>
                  <div class="row">
                  <div class="col-md-6 mb-3">
                        <label for="lastName">template</label>
                        <select class="form-control" name="template" id="template" required>
                                <option value="">:: time_slot ::</option>
                                <option value="9-2">:: 9am ~ 2pm ::</option>
                                <option value="3-7">:: 3pm ~ 7pm ::</option>
                            </select>
                     </div>
                  <div class="col-md-6 mb-3">
                        <label for="lastName">pesan template</label>
                        <select class="form-control" name="pesan_template" id="pesan_template" required>
                                <option value="">:: time_slot ::</option>
                                <option value="9-2">:: 9am ~ 2pm ::</option>
                                <option value="3-7">:: 3pm ~ 7pm ::</option>
                            </select>
                     </div>
                  <div class="col-md-12 mb-3">
                        <label for="lastName">isi pesan</label>
                        <textarea name="isi_pesan" id="isi_pesan" cols="30" rows="5" class="form-control"></textarea>
                     </div>
                  </div>
               </div>
            </div>
            <div class="card-header bg-primary collapse-card" id="cutomer_prod_wish">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseProdWish" aria-expanded="true" aria-controls="collapseProdWish">
                  Pembayaran
                  </button>
               </h5>
            </div>
            <div id="collapseProdWish" class="collapse" aria-labelledby="cutomer_prod_wish" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                    <div class="col-4 col-md-4 col-lg-4">
                    <a href="">
                    <div class="text-center">
                         <img src="<?=URL?>assets/images/payment/bca.jpg" width="50%" class="rounded mx-auto d-block" alt="...">
                    </div>
                    </a>
                    </div>
                    <div class="col-4 col-md-4 col-lg-4">
                    <a href="">
                    <div class="text-center">
                         <img src="<?=URL?>assets/images/payment/bca.jpg" width="50%" class="rounded mx-auto d-block" alt="...">
                    </div>
                    </a>
                    </div>
                    <div class="col-4 col-md-4 col-lg-4">
                    <a href="">
                    <div class="text-center">
                         <img src="<?=URL?>assets/images/payment/bca.jpg" width="50%" class="rounded mx-auto d-block" alt="...">
                    </div>
                    </a>
                    </div>
                  </div>
               </div>
            </div>
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


