<ul class="list-group list-group-flush " id="listProducts">
   <?php if($prod->rowCount() > 0) { while ($product = $prod->fetch(PDO::FETCH_LAZY)) {
      ?>
   <li class="list-group-item">
      <div class="checkout-content">
         <div class="chekcout-img">
            <picture>
               <a href="<?=URL?>assets/images/product/the_beautifully_flowers.jpg" data-toggle="lightbox" data-gallery="example-gallery">
               <img src="<?=URL?>assets/images/product/the_beautifully_flowers.jpg" class="img-fluid img-thumbnail">
               </a>
            </picture>
         </div>
         <div class="checkout-sometext">
            <div class="title">the veautifully flowers</div>
            <div class="count-product">
               <div class="center">
                  <div class="input-group mb-3">
                     <div class="input-group-prepend">
                        <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="minus" disabled="disabled"><span class="fa fa-minus"></span></button>
                     </div>
                     <input style="text-align: center;" type="text" value="1" id="count-product-number" name="count-product-number" min="1" max="10" class="input-number form-control form-control-sm" placeholder="" aria-label="" aria-describedby="basic-addon1">
                     <div class="input-group-append">
                        <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="plus"><span class="fa fa-plus"></span></button>
                     </div>
                  </div>
               </div>
            </div>
            <div class="price">Rp. 550.000.00</div>
            <div class="important-notes">
               <div class="note">
                  Contextual classes also work with .list-group-item-action. Note the addition of the hover styles here not present in the previous example. Also supported is the .active state; apply it to indicate an active selection on a contextual list group item.
               </div>
            </div>
         </div>
      </div>
   </li>
   <?php } } else{ echo '<li class="list-group-item"><span class="badge badge-success">Produk kosong!</span></li>';} ?>
</ul>