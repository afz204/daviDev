<div class="row" id="newAdmin">
   <div class="col-12 col-sm-4 col-lg-4 order-md-1">
      <div class="card text-secondary bg-white mb-3">
         <div class="card-header">
            Profile Customer
         </div>
         <div class="card-body">
            <form action="" data-parsley-validate="" method="post" autocomplete="off" novalidate="">
               <div class="form-group">
                  <label for="usernameAdmin">FullName</label>
                  <input style="text-transform: capitalize;" type="text" class="form-control" value="refnaldi hakim" readonly="">
               </div>
               <div class="form-group">
                  <label for="usernameAdmin">Jenis Kelamin</label>
                  <input style="text-transform: capitalize;" type="text" class="form-control" value="Male" readonly="">
               </div>
               <div class="form-group">
                  <label for="usernameAdmin">Nomor Telp</label>
                  <input style="text-transform: capitalize;" type="text" class="form-control" value="0218383949348" readonly="">
               </div>
               <div class="form-group">
                  <label for="usernameAdmin">Handphone</label>
                  <input style="text-transform: capitalize;" type="text" class="form-control" value="082210364609" readonly="">
               </div>
               <div class="form-group">
                  <label for="usernameAdmin">Tanggal Lahir</label>
                  <input style="text-transform: capitalize;" type="text" class="form-control" value="00000" readonly="">
               </div>
               <div class="form-group">
                  <label for="usernameAdmin">Alamat Email</label>
                  <input style="text-transform: capitalize;" type="text" class="form-control" value="arfan@invasma.com" readonly="">
               </div>
               <button type="submit" class="btn btn-block btn-outline-dark">Edit Profile</button>
            </form>
         </div>
      </div>
   </div>
   <div class="col-12 col-sm-8 col-lg-8 order-md-2">
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
                        <label for="firstName">password</label>
                        <input type="password" class="form-control" id="password" value="1200" autocomplete="password" placeholder="" value="" readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">point</label>
                        <input type="text" class="form-control" id="point" value="1200" autocomplete="text" placeholder="" value="" readonly>
                        <div class="invalid-feedback">
                           Valid last name is required.
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">Share Link</label>
                        <input type="text" class="form-control" id="share_link" value="5AE249BDC3619" autocomplete="text" placeholder="" readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">status</label>
                        <input type="text" class="form-control" id="status_cutomer" value="active" autocomplete="text" placeholder="" readonly>
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
                  Customer Address
                  </button>
               </h5>
            </div>
            <div id="collapseAddress" class="collapse" aria-labelledby="customer_address" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">Provinsi</label>
                        <input type="text" class="form-control" id="provinsi" value="sultan" autocomplete="text" placeholder=""  readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">Kota</label>
                        <input type="text" class="form-control" id="status" value="maja" autocomplete="text" placeholder="" readonly>
                        <div class="invalid-feedback">
                           Valid last name is required.
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="firstName">Kelurahan</label>
                        <input type="text" class="form-control" id="link" value="cagar" autocomplete="text" placeholder=""  readonly>
                        <div class="invalid-feedback">
                           Valid first name is required.
                        </div>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label for="lastName">Kecamatan</label>
                        <input type="text" class="form-control" id="kecamatan" value="ceger" autocomplete="text" placeholder="" readonly>
                        <div class="invalid-feedback">
                           Valid last name is required.
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 mb-3">
                        <label for="firstName">Kelurahan</label>
                        <textarea type="text" class="form-control" id="alamat" autocomplete="text" placeholder="" value="" readonly>jalan kiayi maja 1 nomor 12</textarea>
                     </div>
                  </div>
               </div>
            </div>
            <div class="card-header bg-primary collapse-card" id="customer_history">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="true" aria-controls="collapseHistory">
                  Customer History
                  </button>
               </h5>
            </div>
            <div id="collapseHistory" class="collapse" aria-labelledby="customer_history" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="card-header bg-primary collapse-card" id="customer_favorite">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseFavorite" aria-expanded="true" aria-controls="collapseFavorite">
                  Customer Favorite
                  </button>
               </h5>
            </div>
            <div id="collapseFavorite" class="collapse" aria-labelledby="customer_favorite" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                  <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     
                  </div>
               </div>
            </div>
            <div class="card-header bg-primary collapse-card" id="cutomer_prod_wish">
               <h5 class="mb-0">
                  <button class="btn btn-primary btn-block text-left" data-toggle="collapse" data-target="#collapseProdWish" aria-expanded="true" aria-controls="collapseProdWish">
                  Customer Product WishList
                  </button>
               </h5>
            </div>
            <div id="collapseProdWish" class="collapse" aria-labelledby="cutomer_prod_wish" data-parent="#accordion">
               <div class="card-body">
                  <div class="row">
                  <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     <div class="col-6 col-sm-4 col-lg-4 product-list-col">
                        <div class="card">
                           <img class="card-img-top " src="<?=URL?>assets/images/product/bunga_malem_jumat.jpg" alt="Card image cap">
                           <div class="product-img-list"></div>
                           <div class="card-body product-list">
                              <h5 class="card-title product-list-title">nama bunga cantik</h5>
                              <p class="card-text product-list-content">content cantik .....</p>
                              <a href="#" class="btn btn-sm btn-block btn-primary product-list-btn">details</a>
                           </div>
                        </div>
                     </div>
                     
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>