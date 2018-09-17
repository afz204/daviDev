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
    echo json_encode(['error'=>'Images Product Name unset.']);
    // or you can throw an exception
    return; // terminate
}



$imagesid = empty($_POST['imagesid']) ? '' : $_POST['imagesid'];
$imagesName = empty($_POST['imagesname']) ? '' : $_POST['imagesname'];

$title = strtolower(str_replace(" ", "_", $imagesName));
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
    $target = "../../assets/images/product/". $title . '.jpg';
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

    $stmt = $config->runQuery('UPDATE products SET images = :images where product_id = :code');
    $stmt->execute(array(
        ':images' => $title . '.jpg',
        ':code'   => $imagesid
    ));
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