<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 20.00
 */

require '../../config/config.php';
require '../../config/Mail.php';
$config = new Admin();

$admin = $config->adminID();

if($_GET['type'] == 'customproduct'){
    $product_id = $_POST['product_id'];
    $transactionID = $_POST['transactionID'];
    $name_product = $_POST['name_product'];
    $cost_price = $_POST['cost_price'];
    $selling_price = $_POST['selling_price'];
    $full_desc = $_POST['full_desc'];
    $remkarsfloris = $_POST['remkarsfloris'];
    $images = strtolower(str_replace(" ", "_", $name_product)).'.jpg';
    $permalink = str_replace(' ', '_', strtolower($name_product));
    $created_at = $config->getDate('Y-m-d H:m:s');

    $data = $config->getData('*', 'products', "product_id = '".$id."'");

    if($data['id']) {
        $udpate = $config->runQuery("UPDATE products SET name_product ='".$name_product."', cost_price ='".$cost_price."', selling_price ='".$selling_price."', full_desc ='".$full_desc."', images ='".$images."', permalink ='".$permalink."', created_at ='".$created_at."', admin_id ='".$admin."' WHERE product_id ='".$product_id."' ");
        $udpate->execute();
        $logs = $config->saveLogs($product_id, $admin, 'u', 'update custom products');
    } else {
        $sql = "INSERT INTO products (product_id, name_product, cost_price, selling_price, full_desc, images, permalink, created_at, admin_id) 
        VALUES (:product_id, :name_product, :cost_price, :selling_price, :full_desc, :images, :permalink, :created_at, :admin_id)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':product_id' => $product_id,
            ':name_product' => $name_product,
            ':cost_price' => $cost_price,
            ':selling_price' => $selling_price,
            ':full_desc' => $full_desc,
            ':images' => $images,
            ':permalink' => $permalink,
            ':created_at' => $created_at,
            ':admin_id' => $admin
        ));
    
        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'new custom products');

        if ($stmt) {
            //  $config->actionMsg('c', 'products');
    
            
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
    
            $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'add product checkout');
            if($cek){
                //echo $config->actionMsg('c', 'detail_trxs');
    
                //insert to transaction total 
                $transactionIDd = $config->getData('grandTotal', 'transaction', " transactionID = '". $transactionID ."'");
    
                $grandTotal = $transactionIDd['grandTotal'] + $selling_price;
    
                $transaction = $config->runQuery("UPDATE transaction SET grandTotal = :a WHERE transactionID = :b ");
                $transaction->execute(array(':a' => $grandTotal, ':b' => $transactionID));
                //
    
                $prod = $config->ProductsJoin('transaction_details.id, transaction_details.id_product,  transaction_details.product_price, transaction_details.product_cost,  transaction_details.product_qty, transaction_details.florist_remarks, products.product_id, products.name_product,
                    products.cost_price, products.selling_price, products.note, products.images, products.permalink',
                    'transaction_details', 'LEFT JOIN products ON products.product_id = transaction_details.id_product', "WHERE transaction_details.id_trx = '". $transactionID ."'");
    
                $data = ''; $proQty = '';
                $images = ''; $title = ''; $id = ''; $qty = ''; $cost = ''; $selling = ''; $price =''; $remarks='';
                while ($row = $prod->fetch(PDO::FETCH_LAZY)) {
                    $images = $row['images'];
                    $title = $row['name_product'];
                    $id = $row['id'];
                    $qty = $row['qty'];
                    $cost = $config->formatPrice($row['cost_price']);
                    $selling = $config->formatPrice($row['selling_price']);
                    $costprice = $row['product_cost'];
                    $price = $row['product_price'];
                    $remarks = $row['florist_remarks'];
    
                    if($qty >= 1){
                        $proQty = 'disabled';
                    }
    
                    //bawa data
                    //totalBarang
                    $barang =  $config->runQuery("SELECT id FROM transaction_details WHERE id_trx = :trx");
                    $barang->execute(array(':trx' => $transactionID));
                    $totalBarang = $barang->rowCount();
    
                    //total transaction
                    $transaction = $config->getData('SUM(product_price) as price, SUM(product_qty) as qty', 'transaction_details', " id_trx = '". $transactionID ."' ");
                
                    $total = $config->formatPrice($transaction['price'] * $transaction['qty']);
    
                     $data = '<li class="list-group-item" id="ListProduct-'. $id .'">
                      <div class="checkout-content">
                         <div class="chekcout-img">
                            <picture>
                             <a href="'. $config->url() .'assets/images/product/'. $images .'" data-toggle="lightbox" data-gallery="example-gallery">
                                   <img src="'. $config->url() .'assets/images/product/'. $images .'" class="img-fluid img-thumbnail" width="50%">
                               </a>
                           </picture>
                         </div>
                         <div class="checkout-sometext" style="width: 120%">
                            <div class="title">'. $title .' <div class="pull-right"><button class="btn btn-sm btn-danger deleteListProduct" type="button" data-id="'. $id .'"><span class="fa fa-trash"></span></button></div></div>
                            <div class="count-product">
                               
                               <div class="center">
                                  <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                      <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="minus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $transactionID .'" disabled="disabled"><span class="fa fa-minus"></span></button>
                                    </div>
                                    <input style="text-align: center;" type="text" value="1" id="count-product-number['. $id .']" name="count-product-number['. $id .']" min="1" max="10" class="input-number form-control form-control-sm" placeholder="" aria-label="" aria-describedby="basic-addon1" data-field="count-product-number['. $id .']" data-qty="'. $qty .'" data-transactionid="'. $transactionID .'">
                                    <div class="input-group-append">
                                      <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="plus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $transactionID .'"><span class="fa fa-plus"></span></button>
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
                                    <input type="text" data-parsley-type="number" class="form-control" name="cost_price_product['. $id .']" id="cost_price_product['. $id .']" value="'.$costprice.'" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                      <button class="btn btn-outline-info cost_price_btn" type="button" data-id="cost_price_product['. $id .']" data-trx="'. $transactionID .'">Cost Price</button>
                                    </div>
                                  </div>
    
                                  <div class="input-group mb-3">
                                     <div class="input-group-prepend">
                                         <span class="input-group-text">Rp.</span>
                                       </div>
                                    <input type="text" data-parsley-type="number" class="form-control" name="selling_price_product['. $id .']" id="selling_price_product['. $id .']" value="'.$price.'" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                      <button class="btn btn-outline-info selling_price_btn" type="button" data-id="selling_price_product['. $id .']" data-trx="'. $transactionID .'">Selling Price</button>
                                    </div>
                                  </div>
                               
                            </div>
                            
                            <div class="important-notes">
                               <div class="note">
                                  <form id="remarks_florist" data-parsley-validate="" novalidate="">
                                     <div class="form-group">
                                        <textarea class="form-control remarks-florist-tambahan" name="isi_remarks['. $id .']" row="5" required="" placeholder="remarks florist" data-id="'. $id .'">'.$remkarsfloris.'</textarea>
                                     </div>
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
        } else {
            echo 'Failed!';
        }
    }
    
    
}
if($_GET['type'] == 'cekimagesid') {
    $id = $_POST['product_id'];

    $data = $config->getData('*', 'products', "product_id = '".$id."'");

    if($data['id']) {
        

        $product_id = $_POST['product_id'];
        $transactionID = $_POST['transactionID'];
        $name_product = $_POST['name_product'];
        $cost_price = $_POST['cost_price'];
        $selling_price = $_POST['selling_price'];
        $full_desc = $_POST['full_desc'];
        $remkarsfloris = $_POST['remkarsfloris'];
        $images = strtolower(str_replace(" ", "_", $name_product)).'.jpg';
        $permalink = str_replace(' ', '_', strtolower($name_product));
        $created_at = $config->getDate('Y-m-d H:m:s');

        $data = $config->getData('*', 'products', "product_id = '".$id."'");

            if($data['id']) {
                $udpate = $config->runQuery("UPDATE products SET name_product ='".$name_product."', cost_price ='".$cost_price."', selling_price ='".$selling_price."', full_desc ='".$full_desc."', images ='".$images."', permalink ='".$permalink."', created_at ='".$created_at."', admin_id ='".$admin."' WHERE product_id ='".$product_id."' ");
                $udpate->execute();
                $logs = $config->saveLogs($product_id, $admin, 'u', 'update custom products');
            } else {
                $sql = "INSERT INTO products (product_id, name_product, cost_price, selling_price, full_desc, images, permalink, created_at, admin_id) 
                VALUES (:product_id, :name_product, :cost_price, :selling_price, :full_desc, :images, :permalink, :created_at, :admin_id)";
                $stmt = $config->runQuery($sql);
                $stmt->execute(array(
                    ':product_id' => $product_id,
                    ':name_product' => $name_product,
                    ':cost_price' => $cost_price,
                    ':selling_price' => $selling_price,
                    ':full_desc' => $full_desc,
                    ':images' => $images,
                    ':permalink' => $permalink,
                    ':created_at' => $created_at,
                    ':admin_id' => $admin
                ));
            
                $reff = $config->lastInsertId();
                $logs = $config->saveLogs($reff, $admin, 'c', 'new custom products');

                if ($stmt) {
                    //  $config->actionMsg('c', 'products');
            
                    
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
            
                    $reff = $config->lastInsertId();
                    $logs = $config->saveLogs($reff, $admin, 'c', 'add product checkout');
                    if($cek){
                        //echo $config->actionMsg('c', 'detail_trxs');
            
                        //insert to transaction total 
                        $transactionIDd = $config->getData('grandTotal', 'transaction', " transactionID = '". $transactionID ."'");
            
                        $grandTotal = $transactionIDd['grandTotal'] + $selling_price;
            
                        $transaction = $config->runQuery("UPDATE transaction SET grandTotal = :a WHERE transactionID = :b ");
                        $transaction->execute(array(':a' => $grandTotal, ':b' => $transactionID));
                        //
            
                        $prod = $config->ProductsJoin('transaction_details.id, transaction_details.id_product,  transaction_details.product_price, transaction_details.product_cost,  transaction_details.product_qty, transaction_details.florist_remarks, products.product_id, products.name_product,
                            products.cost_price, products.selling_price, products.note, products.images, products.permalink',
                            'transaction_details', 'LEFT JOIN products ON products.product_id = transaction_details.id_product', "WHERE transaction_details.id_trx = '". $transactionID ."'");
            
                        $data = ''; $proQty = '';
                        $images = ''; $title = ''; $id = ''; $qty = ''; $cost = ''; $selling = ''; $price =''; $remarks='';
                        while ($row = $prod->fetch(PDO::FETCH_LAZY)) {
                            $images = $row['images'];
                            $title = $row['name_product'];
                            $id = $row['id'];
                            $qty = $row['qty'];
                            $cost = $config->formatPrice($row['cost_price']);
                            $selling = $config->formatPrice($row['selling_price']);
                            $costprice = $row['product_cost'];
                            $price = $row['product_price'];
                            $remarks = $row['florist_remarks'];
            
                            if($qty >= 1){
                                $proQty = 'disabled';
                            }
            
                            //bawa data
                            //totalBarang
                            $barang =  $config->runQuery("SELECT id FROM transaction_details WHERE id_trx = :trx");
                            $barang->execute(array(':trx' => $transactionID));
                            $totalBarang = $barang->rowCount();
            
                            //total transaction
                            $transaction = $config->getData('SUM(product_price) as price, SUM(product_qty) as qty', 'transaction_details', " id_trx = '". $transactionID ."' ");
                        
                            $total = $config->formatPrice($transaction['price'] * $transaction['qty']);
            
                            $data = '<li class="list-group-item" id="ListProduct-'. $id .'">
                            <div class="checkout-content">
                                <div class="chekcout-img">
                                    <picture>
                                    <a href="'. $config->url() .'assets/images/product/'. $images .'" data-toggle="lightbox" data-gallery="example-gallery">
                                        <img src="'. $config->url() .'assets/images/product/'. $images .'" class="img-fluid img-thumbnail" width="50%">
                                    </a>
                                </picture>
                                </div>
                                <div class="checkout-sometext" style="width: 120%">
                                    <div class="title">'. $title .' <div class="pull-right"><button class="btn btn-sm btn-danger deleteListProduct" type="button" data-id="'. $id .'"><span class="fa fa-trash"></span></button></div></div>
                                    <div class="count-product">
                                    
                                    <div class="center">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                            <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="minus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $transactionID .'" disabled="disabled"><span class="fa fa-minus"></span></button>
                                            </div>
                                            <input style="text-align: center;" type="text" value="1" id="count-product-number['. $id .']" name="count-product-number['. $id .']" min="1" max="10" class="input-number form-control form-control-sm" placeholder="" aria-label="" aria-describedby="basic-addon1" data-field="count-product-number['. $id .']" data-qty="'. $qty .'" data-transactionid="'. $transactionID .'">
                                            <div class="input-group-append">
                                            <button class="btn btn-sm btn-outline-secondary btn-number-count" type="button" data-type="plus" data-field="count-product-number['. $id .']" data-id="selling_price_product['. $id .']" data-trx="'. $transactionID .'"><span class="fa fa-plus"></span></button>
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
                                            <input type="text" data-parsley-type="number" class="form-control" name="cost_price_product['. $id .']" id="cost_price_product['. $id .']" value="'.$costprice.'" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                            <button class="btn btn-outline-info cost_price_btn" type="button" data-id="cost_price_product['. $id .']" data-trx="'. $transactionID .'">Cost Price</button>
                                            </div>
                                        </div>
            
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" data-parsley-type="number" class="form-control" name="selling_price_product['. $id .']" id="selling_price_product['. $id .']" value="'.$price.'" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                            <button class="btn btn-outline-info selling_price_btn" type="button" data-id="selling_price_product['. $id .']" data-trx="'. $transactionID .'">Selling Price</button>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    
                                    <div class="important-notes">
                                    <div class="note">
                                        <form id="remarks_florist" data-parsley-validate="" novalidate="">
                                            <div class="form-group">
                                                <textarea class="form-control remarks-florist-tambahan" name="isi_remarks['. $id .']" row="5" required="" placeholder="remarks florist" data-id="'. $id .'">'.$remkarsfloris.'</textarea>
                                            </div>
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
                } else {
                    echo 'Failed!';
                }
            }
    } else {
        echo 'NO';
        $input = $config->runQuery("INSERT INTO products (product_id) values ('".$id."')");
        $input->execute();
    }
}