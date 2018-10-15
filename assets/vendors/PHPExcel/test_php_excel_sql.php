<?php 

/**
 * digunakan untuk generate file excel.
 * sebagai contoh di goblooge.com
 * 
 * @author 		: Nurul Huda
 * @copyright 	: goblooge@gmail.com
 * @license 	: LGPLv2
 * @database 	: - goblooge.keluarga
 * @since		: 16 Sept 2016
 * @version		: 1.0.0
 * 
 * */

//ini adalah require yang dibutuhkan cukup merequire file pertama di PHP Excel. 
//sesuaikan dengan Path Milik anda
	require_once "Classes/PHPExcel.php"; 
/*start - BLOCK PROPERTIES FILE EXCEL*/
	$file = new PHPExcel ();
	$file->getProperties ()->setCreator ( "Goblooge" );
	$file->getProperties ()->setLastModifiedBy ( "Nurul Huda" );
	$file->getProperties ()->setTitle ( "Data Keluarga" );
	$file->getProperties ()->setSubject ( "Inheritance Keluarga" );
	$file->getProperties ()->setDescription ( "Data Inheritance Keluarga" );
	$file->getProperties ()->setKeywords ( "Keluarga Nurul Huda" );
	$file->getProperties ()->setCategory ( "Keluarga" );
/*end - BLOCK PROPERTIES FILE EXCEL*/

/*start - BLOCK SETUP SHEET*/
	$file->createSheet ( NULL,0);
	$file->setActiveSheetIndex ( 0 );
	$sheet = $file->getActiveSheet ( 0 );
	//memberikan title pada sheet
	$sheet->setTitle ( "Database Keluarga" );
/*end - BLOCK SETUP SHEET*/

/*start - BLOCK HEADER*/
	$sheet	->setCellValue ( "A1", "Nama" )
			->setCellValue ( "B1", "Alamat" )
			->setCellValue ( "C1", "Ayah" )
			->setCellValue ( "D1", "Ibu" )
			->setCellValue ( "E1", "Hobi" );
/*end - BLOCK HEADER*/

/* start - BLOCK MEMASUKAN DATABASE*/
	//ganti dengan database anda
	$link = mysqli_connect("localhost", "root", "123456", "goblooge"); 
	$result=mysqli_query($link,"SELECT * FROM keluarga");
	$nomor=1;
	while($row=mysqli_fetch_array($result)){
		$nomor++;
		$sheet	->setCellValue ( "A".$nomor, $row["nama"] )
				->setCellValue ( "B".$nomor, $row["alamat"] )
				->setCellValue ( "C".$nomor, $row["ayah"] )
				->setCellValue ( "D".$nomor, $row["ibu"] )
				->setCellValue ( "E".$nomor, $row["hobi"] );
	}
/* end - BLOCK MEMASUKAN DATABASE*/

/* start - BLOCK MEMBUAT LINK DOWNLOAD*/
	header ( 'Content-Type: application/vnd.ms-excel' );
	//namanya adalah keluarga.xls
	header ( 'Content-Disposition: attachment;filename="Keluarga.xls"' ); 
	header ( 'Cache-Control: max-age=0' );
	$writer = PHPExcel_IOFactory::createWriter ( $file, 'Excel5' );
	$writer->save ( 'php://output' );
/* start - BLOCK MEMBUAT LINK DOWNLOAD*/

?>
