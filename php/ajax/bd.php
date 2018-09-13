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
	
	$Dates = $config->_rangedate($dateFrom, $dateTo);

	foreach($Dates as $key => $val) {
		$TimeSlot = $config->getData('COUNT(ID) as ID', 'time_slots', "DateSlots = '". $val ."' ");
		if($TimeSlot['ID'] > 0) {
			die(json_encode(['response' => 'Error', 'msg' => 'Dates Already In Database!']));
		} else {
			$stmt = $config->runQuery("INSERT INTO time_slots (DateSlots, TimeSlots, Price, Description, IsActive, Status, AdminID) VALUES (:a, :b, :c, :d, :e, :f, :g)");
			$stmt->execute(array(
				':a'	=> $val,
				':b'	=> $b,
				':c'	=> $b,
				':d'	=> $b,
				':e'	=> 0,
				':f'	=> 1,
				':g'	=> $admin
			));
		}
	}

	$logs = $config->saveLogs('0', $admin, 'c', 'new time slots');
	die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('c', 'time_slots')]));
}

if($_GET['type'] == 'updatetimeslot'){
	$a = $_POST['ID'];

	$data = $config->getData('IsActive', 'time_slots', " ID =". $a);
	
	$status = 0;
	if($data['IsActive'] == 0) { $status = 1; }

	$update = $config->runQuery("UPDATE time_slots SET IsActive = :a WHERE ID = :b ");
	$update->execute(array(':a' => $status, ':b' => $a));

	if($update) {
		$logs = $config->saveLogs($a, $admin, 'u', 'update time slots');
		die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('u', 'time_slots')]));
	} else {
		die(json_encode(['response' => 'Error', 'msg' => 'Error Data!']));
	}
}

if($_GET['type'] == 'deletetimeslot'){
	$a = $_POST['ID'];

	$update = $config->runQuery("UPDATE time_slots SET Status = 0 WHERE ID = :a ");
	$update->execute(array(':a' => $a));

	if($update) {
		$logs = $config->saveLogs($a, $admin, 'd', 'Delete time slots');
		die(json_encode(['response' => 'OK', 'msg' => $config->actionMsg('d', 'time_slots')]));
	} else {
		die(json_encode(['response' => 'Error', 'msg' => 'Error Data!']));
	}
}