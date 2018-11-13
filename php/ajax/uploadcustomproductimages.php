<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/05/2018
 * Time: 17.26
 */
session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();

if (empty($_FILES['images'])) {
    echo json_encode(['error'=>'No files found for upload.']);
    // or you can throw an exception
    return; // terminate
}else{
    $images = $_FILES['images'];
}

if(empty($_POST['imagesid'])){
    echo json_encode(['error'=>'Images Product Code unset.']);
    // or you can throw an exception
    return; // terminate
}
if(empty($_POST['imagesname'])){
    echo json_encode(['error'=>'Isi Nama Product.']);
    // or you can throw an exception
    return; // terminate
}
if(empty($_POST['costProduct'])){
    echo json_encode(['error'=>'Isi Cost Price.']);
    // or you can throw an exception
    return; // terminate
}
if(empty($_POST['sellProduct'])){
    echo json_encode(['error'=>'Isi Selling Price.']);
    // or you can throw an exception
    return; // terminate
}
if(empty($_POST['transactionID'])){
    echo json_encode(['error'=>'Trx Undefined.']);
    // or you can throw an exception
    return; // terminate
}



$imagesid = empty($_POST['imagesid']) ? '' : $_POST['imagesid'];
$imagesName = empty($_POST['imagesname']) ? '' : $_POST['imagesname'];
$costProduct = empty($_POST['costProduct']) ? '' : $_POST['costProduct'];
$sellProduct = empty($_POST['sellProduct']) ? '' : $_POST['sellProduct'];
$shortDesc = empty($_POST['shortDesc']) ? '' : $_POST['shortDesc'];
$remkarsfloris = empty($_POST['remkarsfloris']) ? '' : $_POST['remkarsfloris'];
$transactionID = empty($_POST['transactionID']) ? '' : $_POST['transactionID'];

$title =$imagesName;
// $title = strtolower(str_replace(" ", "_", $imagesName));
// a flag to see if everything is ok
$success = null;

// file paths to store
$paths= [];

// get file names
$filenames = $images['name'];

// loop and process files
for($i=0; $i < count($filenames); $i++){
    $string = str_replace(" ", "_", $filenames[$i]);
    //$ext = explode('.', basename($filenames[$i]));
    //$target = "../../assets/images/product" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
    $target = "../../assets/images/product/". $imagesid . '.jpg';
    if(move_uploaded_file($images['tmp_name'][$i], $target)) {
        $success = true;
        $paths[] = $target;
    } else {
        $success = false;
        break;
    }
}

// check and process based on successful status
if ($success === true) {
    // call the function to save all data to database
    // code for the following function `save_data` is not
    // mentioned in this example
    //save_data($imagesid, $paths);

    // store a successful response (default at least an empty array). You
    // could return any additional response info you need to the plugin for
    // advanced implementations.
    $output = [];
    // for example you can get the list of files uploaded this way
    // $output = ['uploaded' => $paths];
    $output = "OK";
    $nameproduct = $imagesid.''.$imagesName;
    $images = $imagesid.'.jpg';
    $permalink = str_replace(' ', '_', strtolower($nameproduct));
    $created_at = $config->getDate('Y-m-d H:m:s');

    $sql = "INSERT INTO products (product_id, name_product, cost_price, selling_price, full_desc, images, permalink, created_at, admin_id) 
    VALUES ('".$imagesid."', '".$title."', '".$costProduct."', '".$sellProduct."', '".$shortDesc."', '".$images."', '".$permalink."', '".$created_at."', '".$admin."')";
    $stmt = $config->runQuery($sql);
    $stmt->execute();
    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new custom products');

    if($stmt) {
        $cek = $config->runQuery("INSERT INTO transaction_details (id_trx, id_product, product_name, product_price, product_cost, product_qty, florist_remarks) VALUES (:a, :b, :c, :d, :e, :f, :g) ");
        $cek->execute(array(
            ':a' => $transactionID,
            ':b' => $imagesid,
            ':c' => $title,
            ':d' => $sellProduct,
            ':e' => $costProduct,
            ':f' => '1',
            ':g' => $remkarsfloris
        ));
        if(!$cek) {
            $output = ['error' => 'Error transaction_details'];
        }
    } else {
        $output = ['error' => 'Error Input Product'];
    }

} elseif ($success === false) {
    $output = ['error'=>'Error while uploading images. Contact the system administrator'];
    // delete any uploaded files
    foreach ($paths as $file) {
        unlink($file);
    }
} else {
    $output = ['error'=>'No files were processed.'];
}

// return a json encoded response for plugin to process successfully
echo json_encode($output);