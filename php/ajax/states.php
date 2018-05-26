<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 14.57
 */

require '../../config/api.php';
$config = new Admin();

$id = $_POST['id'];

if($_GET['type'] == 'provinsi'){

    $stmt = $config->runQuery('SELECT * FROM regencies WHERE province_id = :id');
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());

}elseif($_GET['type'] == 'kota'){

    $stmt = $config->runQuery('SELECT * FROM districts WHERE regency_id = :id');
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());

}
elseif($_GET['type'] == 'kecamatan'){

    $stmt = $config->runQuery('SELECT * FROM villages WHERE district_id = :id');
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());

}
elseif($_GET['type'] == 'subCat'){
    $stmt = $config->runQuery('SELECT * FROM categories WHERE parent_id = :id');
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());
}

