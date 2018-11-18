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

if($_GET['type'] == 'cardmsg') {
    $TransactionID = $_POST['TransactionID'];
    $CardFrom = $_POST['CardFrom'];
    $CardTo = $_POST['CardTo'];
    $Msg = $_POST['Msg'];

    $query = "UPDATE transaction SET card_from = :card_from, card_to = :card_to, card_isi = :card_isi, updated_by = :updated_by WHERE transactionID = :transactionID ";
    $update = $config->runQuery($query);
    $update->execute([
        ':card_from'    => $CardFrom,
        ':card_to'    => $CardTo,
        ':card_isi'    => $Msg,
        ':updated_by'    => $admin,
        ':transactionID'    => $TransactionID,
    ]);

    if($update) {
        echo $config->actionMsg('u', 'transaction card msg');
        $logs = $config->saveLogs($TransactionID, $admin, 'u', 'update transaction card msg.');
    } else {
        echo 'Failed !';
    }
    
}

if($_GET['type'] == 'alamatempat') {
    $TransactionID = $_POST['TransactionID'];
    $alamat_penerima = $_POST['alamat_penerima'];
    $kelurahan_id = $_POST['kelurahan_id'];
    $delivery_date = $_POST['delivery_date'];
    $delivery_time = $_POST['time_slot'];
    $delivery_charge = $_POST['delivery_charge'];
    $delivery_marks = $_POST['delivery_marks'];

    $arrtimecharge = [
        1 => 0,
        2 => 0,
        3 => 100000,
        4 => 200000,
        5 => 50000
    ];
    $data = $config->getData('*', 'transaction', "transactionID = '".$TransactionID."' ");
    $deliverycharge = $config->getData('*', 'delivery_charges', "id = '".$kelurahan_id."' ");

    $tmpgrandtotal = $data['grandTotal'] - $data['delivery_charge'] - $data['delivery_charge_time'];
    $newGrandTotal = $tmpgrandtotal + $delivery_charge + $arrtimecharge[$delivery_time];


    $query = "UPDATE transaction SET alamat_penerima = :alamat_penerima, kelurahan_id = :kelurahan_id, delivery_date = :delivery_date, delivery_time = :delivery_time, delivery_charge = :delivery_charge, delivery_charge_time = :delivery_charge_time, grandTotal = :grandTotal, updated_by = :updated_by, delivery_marks = :delivery_marks WHERE transactionID = :transactionID ";
    $update = $config->runQuery($query);
    $update->execute([
        ':alamat_penerima'    => $alamat_penerima,
        ':kelurahan_id'    => $deliverycharge['id_kelurahan'],
        ':delivery_date'    => $delivery_date,
        ':delivery_time'    => $delivery_time,
        ':delivery_charge'    => $delivery_charge,
        ':delivery_marks'    => $delivery_marks,
        ':delivery_charge_time'    => $arrtimecharge[$delivery_time],
        ':grandTotal'    => $newGrandTotal,
        ':updated_by'    => $admin,
        ':transactionID'    => $TransactionID,
    ]);

    if($update) {
        echo $config->actionMsg('u', 'transaction alamat penerima');
        $logs = $config->saveLogs($TransactionID, $admin, 'u', 'update transaction alamat penerima.');
    } else {
        echo 'Failed !';
    }
    
}

if($_GET['type'] == 'penerima') {
    $TransactionID = $_POST['TransactionID'];
    $nama_penerima = $_POST['nama_penerima'];
    $email = $_POST['email'];
    $hp_penerima = $_POST['hp_penerima'];

    $query = "UPDATE transaction SET nama_penerima = :nama_penerima, email = :email, hp_penerima = :hp_penerima, updated_by = :updated_by WHERE transactionID = :transactionID ";
    $update = $config->runQuery($query);
    $update->execute([
        ':nama_penerima'    => $nama_penerima,
        ':email'    => $email,
        ':hp_penerima'    => $hp_penerima,
        ':updated_by'    => $admin,
        ':transactionID'    => $TransactionID,
    ]);

    if($update) {
        echo $config->actionMsg('u', 'transaction detail penerima');
        $logs = $config->saveLogs($TransactionID, $admin, 'u', 'update transaction detail penerima.');
    } else {
        echo 'Failed !';
    }
    
}

if($_GET['type'] == 'customer') {
    $TransactionID = $_POST['TransactionID'];
    $invoice_name = $_POST['invoice_name'];

    $query = "UPDATE transaction SET invoice_name = :invoice_name, updated_by = :updated_by WHERE transactionID = :transactionID ";
    $update = $config->runQuery($query);
    $update->execute([
        ':invoice_name'    => $invoice_name,
        ':updated_by'    => $admin,
        ':transactionID'    => $TransactionID,
    ]);

    if($update) {
        echo $config->actionMsg('u', 'transaction customer');
        $logs = $config->saveLogs($TransactionID, $admin, 'u', 'update transaction customer.');
    } else {
        echo 'Failed !';
    }
    
}

if($_GET['type'] == 'hapusproduct') {
    $ProductID = $_POST['ProductID'];

    $product = $config->getData('*', 'transaction_details', "id = ".$ProductID);
    
    $costprice = $product['product_cost'];
    $sellprice = $product['product_price'];
    $TransactionID = $product['id_trx'];
    
    $data = $config->getData('*', 'transaction', "transactionID = '".$TransactionID."' ");

    $oldtotalcost = $data['TotalCostPrice'];
    $oldtotalsell = $data['TotalSellingPrice'];
    $oldgrandTotal = $data['grandTotal'];
    $delivery_charge = $data['delivery_charge'];
    $delivery_charge_time = $data['delivery_charge_time'];

    $tmptotalcost = $oldtotalcost - $costprice;
    $tmptotalsell = $oldtotalsell - $sellprice;

    $tmpGrandTotal = $tmptotalsell + $delivery_charge + $delivery_charge_time;
    
    $remove = $config->delRecord('transaction_details', 'id', $ProductID);
    if($remove) {
        $query = "UPDATE transaction SET TotalCostPrice = :TotalCostPrice, TotalSellingPrice = :TotalSellingPrice, grandTotal = :grandTotal, updated_by = :updated_by WHERE transactionID = :transactionID ";
        $update = $config->runQuery($query);
        $update->execute([
            ':TotalCostPrice'    => $tmptotalcost,
            ':TotalSellingPrice'    => $tmptotalsell,
            ':grandTotal'    => $tmpGrandTotal,
            ':updated_by'    => $admin,
            ':transactionID'    => $TransactionID,
        ]);

        if($update) {
            echo $config->actionMsg('u', 'transaction');
            $logs = $config->saveLogs($TransactionID, $admin, 'u', 'update transaction product.');
        }
    } else {
        echo 'Failed !';
    }
}