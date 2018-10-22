<?php 

session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();
require_once '../../assets/vendors/PHPExcel/Classes/PHPExcel.php';
$Excel = new PHPExcel();

if($_GET['type'] == 'kurir') {
    

    /** Include PHPExcel */
   
    $daterange = $_GET['date_range'];
    $adminid = $_GET['status_paid'];
    $rangeArray = explode("_",$daterange); 

    $status_paid = 'pay_kurirs.kurir_id = '.$adminid.' AND ';
    if($adminid == 99 || $adminid == 0) {
        $status_paid = '';
    }
    
    $DataQuery = " SELECT pay_kurirs.id as payChargeID, pay_kurirs.no_trx, pay_kurirs.kurir_id, pay_kurirs.charge_id, pay_kurirs.remarks, pay_kurirs.total, pay_kurirs.weight, pay_kurirs.status, pay_kurirs.created_at, kurirs.nama_kurir, delivery_charges.price, villages.name, users.name as admin, transaction.delivery_date 
    FROM pay_kurirs left JOIN kurirs ON kurirs.id = pay_kurirs.kurir_id
    left JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
    left JOIN villages ON villages.id = delivery_charges.id_kelurahan
    left JOIN users ON users.id = delivery_charges.admin_id 
    LEFT JOIN transaction ON transaction.transactionID = pay_kurirs.no_trx
    WHERE ";
    $startDate = $rangeArray[0]. ' 00:00:00';
    $endsDate = $rangeArray[1]. ' 23:59:59';

    $DataQuery .= $status_paid." pay_kurirs.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' ORDER BY pay_kurirs.created_at DESC ";
    // var_dump($DataQuery);
    $data = $config->runQuery($DataQuery);
    $data->execute();

    $stylecenter = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 14,
        )
    );
    $header = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 12,
        )
    );

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'LAPORAN PEMBAYARAN KURIR BUNGA DAVI')
    ->mergeCells('A1:I1')
    ->getStyle("A1:I1")->applyFromArray($stylecenter);

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'NO')
    ->setCellValue('B3', 'Transaction Number')
    ->setCellValue('C3', 'Nama Kurir')
    ->setCellValue('D3', 'Nama Kelurahan')
    ->setCellValue('E3', 'Delivery Charge')
    ->setCellValue('F3', 'Remarsk')
    ->setCellValue('G3', 'Notes')
    ->setCellValue('H3', 'Total')
    ->setCellValue('I3', 'Delivery Date')
    ->getStyle("A3:I3")->applyFromArray($header);

    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('A')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('B')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('C')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('D')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('E')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('F')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('G')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('H')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('I')->setAutoSize(true);

    $Excel->getActiveSheet()->freezePane('A4');
    $nomor = 1;
    $loop = 4;
    while($row = $data->fetch(PDO::FETCH_LAZY)) {

        $Excel->getActiveSheet()
        ->setCellValue('A'.$loop, $nomor++)
        ->setCellValue('B'.$loop, $row['no_trx'])
        ->setCellValue('C'.$loop, $row['nama_kurir'])
        ->setCellValue('D'.$loop, $row['name'])
        ->setCellValue('E'.$loop, $row['price'])
        ->setCellValue('F'.$loop, $row['weight'])
        ->setCellValue('G'.$loop, $row['remarks'])
        ->setCellValue('H'.$loop, $row['price'] + $row['weight'])
        ->setCellValue('I'.$loop, $row['delivery_date']);

        $loop++;
    }
   
    $filename = str_replace(' ', '_', 'Laporan Pembayaran Kurir '.$daterange);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
    $writer->save('php://output');
}