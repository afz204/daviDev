<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 17.27
 */
session_start();
require '../../config/api.php';
$config = new Admin();

$admin = $config->adminID();

if($_GET['type'] == 'newCard'){
	$a = $_POST['head'];
	$b = $_POST['template'];
	$c = $_POST['isi'];

	$stmt = $config->runQuery("INSERT INTO card_messages (level1, level2, level3, admin_id) VALUES (:a, :b, :c, :d)");
	$stmt->execute(array(
		':a'	=> $b,
		':b'	=> $a,
		':c'	=> $c,
		':d'	=> $admin
	));
	$reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new card messages');

	if($stmt){
		echo $config->actionMsg('c', 'card_messages');
	}else{
		echo 'Failed!';
	}
}

if($_GET['type'] == 'newTimeSlot'){
	$a = $_POST['date_range'];
	$b = $_POST['values'];

	$a = explode('_', $a);
	$dateFrom = $a[0];
	$dateTo = $a[1];
	//check data

	$cek = $config->runQuery("SELECT ");

	$stmt = $config->runQuery("INSERT INTO time_slots (dateFrom, dateTo, value, status, admin_id) VALUES (:a, :b, :c, :d, :e)");
	$stmt->execute(array(
		':a'	=> $dateFrom,
		':b'	=> $dateTo,
		':c'	=> $b,
		':d'	=> '1',
		':e'	=> $admin
	));

	$reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new time slots');

    if($stmt){
    	echo $config->actionMsg('c', 'time_slots');
    }else{
    	echo 'Failed!';
    }
}