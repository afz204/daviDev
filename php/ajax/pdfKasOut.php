<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 10/04/2018
 * Time: 14.09
 */

$a = $_GET['admin'];
$u = $_GET['user'];

require '../../config/api.php';
$config = new Admin();

$admin = $config->getAdmin($a);
$admin = $admin->fetch(PDO::FETCH_LAZY);

$admin = $admin['name'];

$user = $config->getAdmin($u);
$user = $user->fetch(PDO::FETCH_LAZY);

$user = $user['name'];

$tanggal = $config->getDate('d M Y');

$stmt = $config->runQuery("SELECT kas.id, kas.nama, kas.qty, kas.harga, kas.satuan, kas.ket, kas.created_at, kas.report_at, kas.status, users.name, cat.content as category, subcat.category as subCategory FROM kas_outs AS kas INNER JOIN users ON users.id = kas.admin_id
LEFT OUTER JOIN satuans AS cat ON cat.id = kas.type
LEFT OUTER JOIN satuans AS subcat ON subcat.id = kas.sub_type WHERE kas.admin_id = :admin AND kas.status = '1' AND DATE(kas.report_at)= CURDATE()");
$stmt->execute(array(
    ':admin' => $u
));


require_once("../../assets/vendors/fpdf17/fpdf.php");

$pdf = new FPDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', 'A4', 0);
$pdf->Image('../../assets/images/logo.png', 10, 8, 50);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(276, 10, 'DAILY REPORT PAYMENT', 0, 0, 'C');
$pdf->Ln(15);
$pdf->Cell(0, 2, " ", "B");
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(35, 7, 'Nama Karyawan', 0, 0, 'L');
$pdf->Cell(10, 7, ':', 0, 0, 'L');
$pdf->Cell(30, 7, ucfirst($user), 0, 0, 'L');
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
$pdf->Cell(75, $h, 'Nama Pengeluaran', 1, 0, 'C', true);
$pdf->Cell(15, $h, 'Qty', 1, 0, 'C', true);
$pdf->Cell(30, $h, 'Harga', 1, 0, 'C', true);
$pdf->Cell(32, $h, 'Total', 1, 0, 'C', true);
$pdf->Cell(80, $h, 'Keterangan', 1, 0, 'C', true);
$pdf->Cell(30, $h, 'Action', 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(255);
$lebar = 110;
$fontSize = 12;

$i = 1;
while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
    $jumlah = number_format($row['qty'] * $row['harga'], 0, ',', '.');
    $cellWidth = 80;
    $cellHeight = 10;
    if ($pdf->GetStringWidth($row['ket']) > $cellWidth) {
        $textLength = strlen($row['ket']);
        $errMargin = 10;
        $startChar = 0;
        $maxChar = 0;
        $textArray = array();
        $tmpString = "";

        while ($startChar < $textLength) {
            while ($pdf->GetStringWidth($tmpString) < ($cellWidth - $errMargin) &&
                ($startChar + $maxChar) < $textLength) {
                $maxChar++;
                $tmpString = substr($row['ket'], $startChar, $maxChar);
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
    $pdf->Cell(75, $he, ucfirst($row['nama']), 1, 0, 'L', true);
    $pdf->Cell(15, $he, ucfirst($row['qty']), 1, 0, 'C', true);
    $pdf->Cell(30, $he, ucfirst(number_format($row['harga'], '0', '.', '.')), 1, 0, 'R', true);
    $pdf->Cell(32, $he, $jumlah, 1, 0, 'R', true);

    $xPos = $pdf->GetX();
    $yPos = $pdf->GetY();
    $pdf->MultiCell($cellWidth, $cellHeight, ucfirst($row['ket']), 1);
    $pdf->SetXY($xPos + $cellHeight, $yPos);
// $pdf->Cell(110, $h, $row['ket'], 1, 0, 'C',true);
    $pdf->SetX(257);
    $pdf->Cell(30, $he, '', 1, 0);
    $pdf->Ln();
}
// $pdf->Ln(10);
// $pdf->SetFont('Times', '', 12);
// $pdf->Cell(460,5, 'Mengetahui,', 0,0,'C');
// $pdf->Ln(25);
// $pdf->SetFont('Times', 'B', 12);
// $pdf->Cell(460,5, 'Nama Karyawan', 0,0,'C');
// $pdf->Ln();
// $pdf->SetY(-15);
// $pdf->SetFont('Arial', '',8);
// $pdf->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}',0,0,'C');
$tanggal = str_replace(' ', '_', $tanggal);
$fileName = str_replace(' ', '', $user);
$fileName = 'KAS_OUT_REPORT_' .$fileName. '_'. $tanggal .'.pdf';


$pdf->Output($fileName, 'I');
?>
