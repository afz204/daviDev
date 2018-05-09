<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 10/04/2018
 * Time: 14.09
 */
$k = $_GET['id'];
$admin = $_GET['admin'];

require '../../config/api.php';
$config = new Admin();

$admin = $config->getAdmin($admin);
$admin = $admin->fetch(PDO::FETCH_LAZY);

$admin = $admin['name'];

$user = $config->getKurir($k);
$user = $user->fetch(PDO::FETCH_LAZY);

$kurir = $user['nama_kurir'];

$tanggal = $config->getDate('d M Y');

$stmt = $config->ProductsJoin('pay_kurirs.id, pay_kurirs.no_trx, pay_kurirs.kurir_id, pay_kurirs.charge_id, pay_kurirs.created_at, kurirs.nama_kurir, delivery_charges.id_kelurahan, delivery_charges.price, villages.name', 
'pay_kurirs', 'INNER JOIN kurirs ON kurirs.id = pay_kurirs.kurir_id
INNER JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan', "WHERE pay_kurirs.kurir_id = " .$k. " AND DATE(pay_kurirs.report_at)= CURDATE()");


require_once("../../assets/vendors/fpdf17/fpdf.php");

    $pdf = new FPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('L', 'A4', 0);
    $pdf->Image('../../assets/images/logo.png', 10, 8, 50);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(276, 10, 'DAILY REPORT KURIR', 0, 0, 'C');
    $pdf->Ln(15);
    $pdf->Cell(0, 2, " ", "B");
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(35, 7, 'Nama Kurir', 0, 0, 'L');
    $pdf->Cell(10, 7, ':', 0, 0, 'L');
    $pdf->Cell(30, 7, ucfirst($kurir), 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 7, 'Tanggal', 0, 0, 'L');
    $pdf->Cell(10, 7, ':', 0, 0, 'L');
    $pdf->Cell(30, 7, $tanggal, 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(35, 7, 'Report By', 0, 0, 'L');
    $pdf->Cell(10, 7, ':', 0, 0, 'L');
    $pdf->Cell(30, 7, ucfirst($admin), 0, 0, 'L');
    $pdf->Ln(15);
    
    $h = 10;
    $left = 0;
    $top = 80;
    #tableheader
    $pdf->SetFont('Times', 'B', 14);
    $pdf->SetFillColor(200, 200, 200);
    
    $pdf->Cell(15, $h, 'NO', 1, 0, 'C', true);
    $pdf->Cell(90, $h, 'Nama Kelurahan', 1, 0, 'C', true);
    $pdf->Cell(50, $h, 'Delivery Charge', 1, 0, 'C', true);
    $pdf->Cell(80, $h, 'Tanggal Kirim', 1, 0, 'C', true);
    $pdf->Cell(44, $h, 'No. Invoice', 1, 0, 'C', true);
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetFillColor(255);
    $lebar = 110;
    $fontSize = 12;
    
    $i = 1;
    while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
        $jumlah = number_format($row['price'], 0, ',', '.');
        $cellWidth = 80;
        $cellHeight = 10;
        if ($pdf->GetStringWidth($row['created_at']) > $cellWidth) {
            $textLength = strlen($row['created_at']);
            $errMargin = 10;
            $startChar = 0;
            $maxChar = 0;
            $textArray = array();
            $tmpString = "";
    
            while ($startChar < $textLength) {
                while ($pdf->GetStringWidth($tmpString) < ($cellWidth - $errMargin) &&
                    ($startChar + $maxChar) < $textLength) {
                    $maxChar++;
                    $tmpString = substr($row['created_at'], $startChar, $maxChar);
                }
                $startChar = $startChar + $maxChar;
                array_push($textArray, $tmpString);
    
                $maxChar = 0;
                $tmpString = '';
            }
    
            $line = count($textArray);
    
    
        } else {
            $line = 1;
        }
    
        $he = $line * $cellHeight;
        $pdf->Cell(15, $he, $i++, 1, 0, 'C', true);
        $pdf->Cell(90, $he, ucfirst($row['name']), 1, 0, 'L', true);
        $pdf->Cell(50, $he, $jumlah, 1, 0, 'R', true);
    
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->MultiCell($cellWidth, $cellHeight, ucfirst($row['created_at']), 1, 'C');
        $pdf->SetXY($xPos + $cellHeight, $yPos);
    // $pdf->Cell(110, $h, $row['ket'], 1, 0, 'C',true);
        $pdf->SetX(245);
        $pdf->Cell(44, $he, $row['no_trx'], 1, 0);
        $pdf->Ln();
    }
    $pdf->Ln(10);
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(460,5, 'Mengetahui,', 0,0,'C');
    $pdf->Ln(25);
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(460,5, ucfirst($kurir), 0,0,'C');
    $pdf->Ln();
    // $pdf->SetY(250);
    // $pdf->SetFont('Arial', '',8);
    // $pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}',0,0,'C');


    $tanggal = str_replace(' ', '_', $tanggal);
    $fileName = str_replace(' ', '', ucfirst($kurir));
    $fileName = 'PAY_KURIR_REPORT_' .$fileName. '_'. $tanggal .'.pdf';
    
    
    $pdf->Output($fileName, 'I');
//         $update = $config->runQuery("UPDATE pay_kurirs SET status = '2' WHERE kurir_id = :kurir ");
//         $update->execute(array(
//             ':kurir' => $k
//         ));

// if($update){

    

// }else{
//     echo 'errorrr!!!!';
// }



?>