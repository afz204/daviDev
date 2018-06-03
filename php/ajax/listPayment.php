<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 14/04/2018
 * Time: 01.16
 */

session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();

if($_GET['type'] == 'kasOut'){
    $outKas = $config->ProductsJoin('kas_outs.id, kas_outs.nama, kas_outs.total, kas_outs.ket, kas_outs.created_at, kas_outs.status, users.name', 'kas_outs',
        'INNER JOIN users ON users.id = kas_outs.admin_id', "WHERE DATE(kas_outs.created_at)= CURDATE() AND kas_outs.status ='' ");

    $request = $_REQUEST;
    $colom = array(
        0   => 'id',
        1   => 'nama',
        2   => 'total',
        3   => 'ket',
        4   => 'created_at',
        5   => 'status'
    );

    $totalData = $outKas->fetchAll();
    $totalData = count($totalData);
    
    $logs = $config->saveLogs('0', $admin, 'f', 'get data kas_outs');

    $data = array();

    while ($row = $outKas->fetch(PDO::FETCH_LAZY)){
        $subdata = array();
        $subdata[]  = $row[0];
        $subdata[]  = $row[1];
        $subdata[]  = $row[2];
        $subdata[]  = $row[3];
        $subdata[]  = $row[4];
        $subdata[]  = $row[5];
        $data = $subdata;
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalData),
        'data'              => $data
    );
    echo json_encode($json_data);
}