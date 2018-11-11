<?php 

session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();
require_once '../../assets/vendors/PHPExcel/Classes/PHPExcel.php';
$Excel = new PHPExcel();

if($_GET['type'] == 'kasbesar') {
    

    /** Include PHPExcel */
   
    $daterange = $_GET['date_range'];
    $rangeArray = explode("_",$daterange); 
    
    $DataQuery = " SELECT kas_besar.id, kas_besar.type, kas_besar.total, kas_besar.title, kas_besar.ket, kas_besar.status, kas_besar.admin_id, kas_besar.status, kas_besar.created_at, users.name FROM kas_besar LEFT JOIN users ON users.id = kas_besar.admin_id WHERE ";
    $startDate = $rangeArray[0]. ' 00:00:00';
    $endsDate = $rangeArray[1]. ' 23:59:59';

    $DataQuery .= " kas_besar.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' ORDER BY kas_besar.id ASC ";
    // var_dump($DataQuery);
    $data = $config->runQuery($DataQuery);
    $data->execute();
    $TotalData = $config->getData("COUNT(*) AS TotalData", 'kas_besar', " kas_besar.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' ");
    $SumDebit = $config->getData("SUM(total) as TotalDebit", 'kas_besar', " kas_besar.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' AND type LIKE '%debit%' ");
    $SumKredit = $config->getData("SUM(total) as TotalKredit", 'kas_besar', " kas_besar.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' AND type LIKE '%kredit%' ");
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
        ->setCellValue('A1', 'LAPORAN KAS BESAR BUNGA DAVI')
        ->mergeCells('A1:H1')
        ->getStyle("A1:H1")->applyFromArray($stylecenter);

    $Excel->setActiveSheetIndex(0)
        ->setCellValue('A3', 'NO')
        ->setCellValue('B3', 'Status')
        ->setCellValue('C3', 'Nama Kegiatan')
        ->setCellValue('D3', 'Keterangan')
        ->setCellValue('E3', 'Type')
        ->setCellValue('F3', 'Total Biaya')
        ->setCellValue('G3', 'Admin')
        ->setCellValue('H3', 'Created Date')
        ->getStyle("A3:H3")->applyFromArray($header);

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

    $Excel->getActiveSheet()->freezePane('A4');
    $nomor = 1;
    $loop = 4;
    while($row = $data->fetch(PDO::FETCH_LAZY)) {
        if($row['status'] == '1'){
            $st12 = 'PRODUKSI';
        }elseif($row['status'] == '2'){
            $st12 = 'KURIR';
        }elseif($row['status'] == '3'){
            $st12 = 'DLL';
        }else{
            $st12 = 'DEBIT';
        }

        if($row['type'] == 'debit'){
            $types = 'DEBIT';
        }else{
            $types = 'KREDIT';
        }

        $Excel->getActiveSheet()
        ->setCellValue('A'.$loop, $nomor++)
        ->setCellValue('B'.$loop, $st12)
        ->setCellValue('C'.$loop, $row['title'])
        ->setCellValue('D'.$loop, $row['ket'])
        ->setCellValue('E'.$loop, $types)
        ->setCellValue('F'.$loop, $row['total'])
        ->setCellValue('G'.$loop, $row['name'])
        ->setCellValue('H'.$loop, $row['created_at']);

        $loop++;
    }

    $page = $TotalData['TotalData'] + 5;
    $Excel->setActiveSheetIndex(0)
    ->setCellValue('D'.$page, 'TOTAL DEBIT :')
    ->setCellValue('E'.($page), '=')
    ->setCellValue('F'.($page), $SumDebit['TotalDebit'])
    ->setCellValue('D'.($page + 1) , 'TOTAL KREDIT :')
    ->setCellValue('E'.($page + 1), '=')
    ->setCellValue('F'.($page  + 1 ), $SumKredit['TotalKredit'])
    ->setCellValue('D'.($page + 2 ), 'SELISIH :')
    ->setCellValue('E'.($page + 2), '=')
    ->setCellValue('F'.($page + 2 ), $SumDebit['TotalDebit'] - $SumKredit['TotalKredit'])
    ;
   
    $filename = str_replace(' ', '_', 'Laporan Kas Besar '.$daterange);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
    $writer->save('php://output');
}